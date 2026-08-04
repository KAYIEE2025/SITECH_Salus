<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\SchoolYearHelper;
use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\FinalGrade;
use App\Models\GradeImport;
use App\Models\GradeSubmissionSchedule;
use App\Models\GradeSubmissionReopeningRequest;
use App\Models\GradingComponent;
use App\Models\ScoreItem;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\StudyLoad;
use App\Services\GradeImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class GradeManagementController extends Controller
{
    protected $gradeImportService;

    public function __construct(GradeImportService $gradeImportService)
    {
        $this->gradeImportService = $gradeImportService;
    }

    /**
     * Get the grade submission schedule status for a given school year and grading period
     */
    private function getSubmissionStatus($schoolYear, $gradingPeriod)
    {
        // DEBUG: Log the values being used for the schedule lookup
        \Log::info('GRADE MANAGEMENT - getSubmissionStatus DEBUG', [
            'school_year' => $schoolYear,
            'grading_period' => $gradingPeriod,
        ]);

        $schedule = GradeSubmissionSchedule::where('school_year', $schoolYear)
            ->where('grading_period', $gradingPeriod)
            ->first();

        \Log::info('GRADE MANAGEMENT - Schedule Query Result', [
            'query_school_year' => $schoolYear,
            'query_grading_period' => $gradingPeriod,
            'schedule_found' => !is_null($schedule),
            'schedule_id' => $schedule ? $schedule->id : null,
        ]);

        if (!$schedule) {
            return [
                'status' => 'no_schedule',
                'message' => 'No grade submission schedule configured for this period.',
                'start_at' => null,
                'deadline_at' => null,
            ];
        }

        $now = Carbon::now();

        if ($now < $schedule->start_at) {
            return [
                'status' => 'scheduled',
                'message' => 'Grade submission has not started yet.',
                'start_at' => $schedule->start_at,
                'deadline_at' => $schedule->end_at,
            ];
        } elseif ($now >= $schedule->start_at && $now <= $schedule->end_at) {
            return [
                'status' => 'open',
                'message' => 'Grade submission is OPEN.',
                'start_at' => $schedule->start_at,
                'deadline_at' => $schedule->end_at,
            ];
        } else {
            return [
                'status' => 'closed',
                'message' => 'Grade submission has already closed.',
                'start_at' => $schedule->start_at,
                'deadline_at' => $schedule->end_at,
            ];
        }
    }

    public function index()
    {
        // Show selection form (School Year, Section, Subject)
        $activeSchoolYear = SchoolYearHelper::getActive();
        return view('teacher.grades.index', compact('activeSchoolYear'));
    }

    public function select(Request $request)
    {
        $request->validate([
            'school_year' => 'required',
            'section_id' => 'required|exists:sections,id',
            'class_schedule_id' => 'required|exists:class_schedules,id',
        ]);

        $classSchedule = ClassSchedule::whereKey($request->class_schedule_id)
            ->where('teacher_id', auth()->id())
            ->where('school_year', $request->school_year)
            ->where('section_id', $request->section_id)
            ->firstOrFail();

        return redirect()->route('teacher.grades.upload', $classSchedule);
    }

    public function sections(Request $request): JsonResponse
    {
        $request->validate([
            'school_year' => 'required|string',
        ]);

        $sections = ClassSchedule::query()
            ->with('section:id,name')
            ->where('teacher_id', auth()->id())
            ->where('school_year', $request->school_year)
            ->whereHas('section')
            ->get()
            ->pluck('section')
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(fn ($section) => [
                'id' => $section->id,
                'name' => $section->name,
            ]);

        return response()->json($sections);
    }

    public function subjects(Request $request): JsonResponse
    {
        $request->validate([
            'school_year' => 'required|string',
            'section_id' => 'required|exists:sections,id',
        ]);

        $subjects = ClassSchedule::query()
            ->with('subject:id,code,name')
            ->where('teacher_id', auth()->id())
            ->where('school_year', $request->school_year)
            ->where('section_id', $request->section_id)
            ->whereHas('subject')
            ->orderBy('id')
            ->get()
            ->map(fn ($schedule) => [
                'id' => $schedule->id,
                'name' => trim(($schedule->subject->code ? $schedule->subject->code . ' - ' : '') . $schedule->subject->name),
            ]);

        return response()->json($subjects);
    }

    public function upload(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Get grading period from request or default to Term 1
        $gradingPeriod = $request->query('grading_period', 1);

        // Get submission status
        $submissionStatus = $this->getSubmissionStatus($classSchedule->school_year, $gradingPeriod);

        // Check for reopening requests if status is closed
        $reopeningRequest = null;
        if ($submissionStatus['status'] === 'closed') {
            $reopeningRequest = GradeSubmissionReopeningRequest::forTeacher(auth()->id())
                ->forPeriod($classSchedule->school_year, $gradingPeriod)
                ->orderBy('created_at', 'desc')
                ->first();

            // If there's an approved request with valid temporary deadline, override status
            if ($reopeningRequest && $reopeningRequest->isApproved() && $reopeningRequest->hasTemporaryAccess()) {
                $submissionStatus['status'] = 'open';
                $submissionStatus['message'] = 'Grade submission is OPEN (Temporary Access)';
            }
        }

        // Get imported data from session if available
        $importedData = session('imported_grades_' . $classSchedule->id);

        // Check if grades have already been submitted
        $existingGrades = FinalGrade::where('class_schedule_id', $classSchedule->id)->get();

        $activeSchoolYear = SchoolYearHelper::getActive();

        return view('teacher.grades.index', compact(
            'classSchedule',
            'importedData',
            'existingGrades',
            'submissionStatus',
            'gradingPeriod',
            'reopeningRequest',
            'activeSchoolYear'
        ));
    }

    public function importPreview(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $classSchedule->load(['subject', 'section']);

        return view('teacher.grades.import-preview', compact('classSchedule'));
    }

    public function submitGrades(Request $request)
    {
        dd('SUBMIT METHOD REACHED - GradeManagementController::submitGrades');

        $request->validate([
            'class_schedule_id' => 'required',
        ]);

        $classSchedule = ClassSchedule::findOrFail($request->class_schedule_id);

        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Get imported data from session
        $importedData = session('imported_grades_' . $classSchedule->id);

        if (empty($importedData)) {
            return redirect()
                ->route('teacher.grades.upload', $classSchedule)
                ->with('error', 'No imported grades found. Please upload an Excel file first.');
        }

        try {
            DB::beginTransaction();

            // Get enrolled students for this class
            $studyLoads = StudyLoad::where('class_schedule_id', $classSchedule->id)
                ->with('student')
                ->get();

            // Create a map of LRN to student_id
            $lrnToStudentId = [];
            foreach ($studyLoads as $studyLoad) {
                if ($studyLoad->student->lrn) {
                    $lrnToStudentId[$studyLoad->student->lrn] = $studyLoad->student_id;
                }
            }

            // Process each imported grade
            foreach ($importedData as $gradeData) {
                $lrn = $gradeData['lrn'];
                
                // Match student by LRN
                if (!isset($lrnToStudentId[$lrn])) {
                    continue; // Skip unmatched students
                }

                $studentId = $lrnToStudentId[$lrn];

                // Check if grade already exists
                $existingGrade = FinalGrade::where('student_id', $studentId)
                    ->where('class_schedule_id', $classSchedule->id)
                    ->first();

                if ($existingGrade) {
                    // Update existing grade
                    $existingGrade->update([
                        'student_name' => $gradeData['student_name'],
                        'lrn' => $gradeData['lrn'],
                        'initial_grade' => is_numeric($gradeData['initial_grade']) ? $gradeData['initial_grade'] : null,
                        'transmuted_grade' => is_numeric($gradeData['transmuted_grade']) ? $gradeData['transmuted_grade'] : null,
                        'quarterly_grade' => is_numeric($gradeData['quarterly_grade']) ? $gradeData['quarterly_grade'] : null,
                        'final_grade' => is_numeric($gradeData['final_grade']) ? $gradeData['final_grade'] : null,
                        'remarks' => $gradeData['remarks'],
                        'imported_data' => $gradeData,
                        'status' => 'submitted',
                        'submitted_at' => now(),
                    ]);
                } else {
                    // Create new grade
                    FinalGrade::create([
                        'student_id' => $studentId,
                        'class_schedule_id' => $classSchedule->id,
                        'student_name' => $gradeData['student_name'],
                        'lrn' => $gradeData['lrn'],
                        'initial_grade' => is_numeric($gradeData['initial_grade']) ? $gradeData['initial_grade'] : null,
                        'transmuted_grade' => is_numeric($gradeData['transmuted_grade']) ? $gradeData['transmuted_grade'] : null,
                        'quarterly_grade' => is_numeric($gradeData['quarterly_grade']) ? $gradeData['quarterly_grade'] : null,
                        'final_grade' => is_numeric($gradeData['final_grade']) ? $gradeData['final_grade'] : null,
                        'remarks' => $gradeData['remarks'],
                        'imported_data' => $gradeData,
                        'status' => 'submitted',
                        'submitted_at' => now(),
                    ]);
                }
            }

            // Clear session data
            session()->forget('imported_grades_' . $classSchedule->id);

            DB::commit();

            return redirect()
                ->route('teacher.grades.upload', $classSchedule)
                ->with('success', 'Grades submitted successfully. Waiting for Registrar approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('teacher.grades.upload', $classSchedule)
                ->with('error', 'Error submitting grades: ' . $e->getMessage());
        }
    }

    public function processUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:5120',
            'class_schedule_id' => 'required',
            'school_year' => 'required',
            'grading_period' => 'required|integer|in:1,2,3,4',
        ]);

        $classSchedule = ClassSchedule::findOrFail($request->class_schedule_id);

        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        try {
            $file = $request->file('excel_file');
            $filePath = $file->getPathname();

            // Load Excel file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return redirect()
                    ->route('teacher.grades.upload', $classSchedule)
                    ->with('error', 'Excel file is empty or has no data rows.');
            }

            // Extract headers from first row
            $headers = array_map('trim', $rows[0]);
            $dataRows = array_slice($rows, 1);

            // Find column indices
            $columnMapping = $this->mapExcelColumns($headers);

            // Extract student data
            $importedData = [];
            foreach ($dataRows as $row) {
                $studentName = $columnMapping['student_name'] !== null ? trim($row[$columnMapping['student_name']] ?? '') : '';
                $lrn = $columnMapping['lrn'] !== null ? trim($row[$columnMapping['lrn']] ?? '') : '';
                $initialGrade = $columnMapping['initial_grade'] !== null ? trim($row[$columnMapping['initial_grade']] ?? '') : '';
                $transmutedGrade = $columnMapping['transmuted_grade'] !== null ? trim($row[$columnMapping['transmuted_grade']] ?? '') : '';
                $quarterlyGrade = $columnMapping['quarterly_grade'] !== null ? trim($row[$columnMapping['quarterly_grade']] ?? '') : '';
                $finalGrade = $columnMapping['final_grade'] !== null ? trim($row[$columnMapping['final_grade']] ?? '') : '';
                $remarks = $columnMapping['remarks'] !== null ? trim($row[$columnMapping['remarks']] ?? '') : '';

                // Skip empty rows
                if (empty($studentName) && empty($lrn)) {
                    continue;
                }

                // Auto-determine remarks if not provided
                if (empty($remarks) && !empty($finalGrade)) {
                    $remarks = is_numeric($finalGrade) && $finalGrade >= 75 ? 'Passed' : 'Failed';
                }

                $importedData[] = [
                    'student_name' => $studentName,
                    'lrn' => $lrn,
                    'initial_grade' => $initialGrade,
                    'transmuted_grade' => $transmutedGrade,
                    'quarterly_grade' => $quarterlyGrade,
                    'final_grade' => $finalGrade,
                    'remarks' => $remarks,
                ];
            }

            if (empty($importedData)) {
                return redirect()
                    ->route('teacher.grades.upload', $classSchedule)
                    ->with('error', 'No valid student data found in Excel file.');
            }

            // Store in session
            session(['imported_grades_' . $classSchedule->id => $importedData]);

            return redirect()
                ->route('teacher.grades.upload', $classSchedule)
                ->with('success', 'Excel file imported successfully. Please review the grades below.');

        } catch (\Exception $e) {
            return redirect()
                ->route('teacher.grades.upload', $classSchedule)
                ->with('error', 'Error processing Excel file: ' . $e->getMessage());
        }
    }

    private function mapExcelColumns($headers)
    {
        $mapping = [
            'student_name' => null,
            'lrn' => null,
            'initial_grade' => null,
            'transmuted_grade' => null,
            'quarterly_grade' => null,
            'final_grade' => null,
            'remarks' => null,
        ];

        foreach ($headers as $index => $header) {
            $headerLower = strtolower($header);
            
            if (stripos($headerLower, 'student') !== false || stripos($headerLower, 'name') !== false) {
                if (stripos($headerLower, 'lrn') === false) {
                    $mapping['student_name'] = $index;
                }
            }
            
            if (stripos($headerLower, 'lrn') !== false || stripos($headerLower, 'learner') !== false) {
                $mapping['lrn'] = $index;
            }
            
            if (stripos($headerLower, 'initial') !== false) {
                $mapping['initial_grade'] = $index;
            }
            
            if (stripos($headerLower, 'transmuted') !== false) {
                $mapping['transmuted_grade'] = $index;
            }
            
            if (stripos($headerLower, 'quarterly') !== false) {
                $mapping['quarterly_grade'] = $index;
            }
            
            if (stripos($headerLower, 'final') !== false && stripos($headerLower, 'grade') !== false) {
                $mapping['final_grade'] = $index;
            }
            
            if (stripos($headerLower, 'remark') !== false) {
                $mapping['remarks'] = $index;
            }
        }

        return $mapping;
    }

    public function indexOld(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $students = StudyLoad::where('class_schedule_id', $classSchedule->id)
            ->with('student')
            ->get()
            ->sortBy('student.last_name');

        // Prepare student scores in the format expected by the view
        $studentScores = [];
        foreach ($students as $studyLoad) {
            $studentId = $studyLoad->student_id;
            $studentScores[$studentId] = [
                'written_works' => [
                    'ww1' => null,
                    'ww2' => null,
                    'ww3' => null,
                    'ww4' => null,
                ],
                'performance_tasks' => [
                    'pt1' => null,
                    'pt2' => null,
                    'pt3' => null,
                    'pt4' => null,
                ],
                'quarterly_assessment' => [
                    'qa' => null,
                    'total' => null,
                ],
            ];

            // Load existing scores from JSON storage
            $finalGrade = FinalGrade::where('student_id', $studentId)
                ->where('class_schedule_id', $classSchedule->id)
                ->first();

            if ($finalGrade && $finalGrade->component_scores) {
                $componentScores = json_decode($finalGrade->component_scores, true);
                if (is_array($componentScores)) {
                    $studentScores[$studentId] = array_merge($studentScores[$studentId], $componentScores);
                }
            }
        }

        $finalGrades = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->get()
            ->keyBy('student_id');

        return view('teacher.grades.index', compact(
            'classSchedule',
            'students',
            'studentScores',
            'finalGrades'
        ));
    }

    public function import(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
            'replace_existing' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $replaceExisting = $request->has('replace_existing');

            // Load the Excel file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                throw new \Exception('Excel file is empty or has no data rows.');
            }

            // Get header row (first row)
            $headers = array_map('trim', $rows[0]);
            
            // Expected column mapping (case-insensitive)
            $columnMapping = [
                'student_number' => null,
                'ww1' => null,
                'ww2' => null,
                'ww3' => null,
                'ww4' => null,
                'pt1' => null,
                'pt2' => null,
                'pt3' => null,
                'pt4' => null,
                'qa' => null,
            ];

            // Map headers to our expected columns
            foreach ($headers as $index => $header) {
                $headerLower = strtolower($header);
                if (stripos($headerLower, 'student') !== false || stripos($headerLower, 'lrn') !== false || stripos($headerLower, 'number') !== false) {
                    $columnMapping['student_number'] = $index;
                } elseif (stripos($headerLower, 'ww1') !== false) {
                    $columnMapping['ww1'] = $index;
                } elseif (stripos($headerLower, 'ww2') !== false) {
                    $columnMapping['ww2'] = $index;
                } elseif (stripos($headerLower, 'ww3') !== false) {
                    $columnMapping['ww3'] = $index;
                } elseif (stripos($headerLower, 'ww4') !== false) {
                    $columnMapping['ww4'] = $index;
                } elseif (stripos($headerLower, 'pt1') !== false) {
                    $columnMapping['pt1'] = $index;
                } elseif (stripos($headerLower, 'pt2') !== false) {
                    $columnMapping['pt2'] = $index;
                } elseif (stripos($headerLower, 'pt3') !== false) {
                    $columnMapping['pt3'] = $index;
                } elseif (stripos($headerLower, 'pt4') !== false) {
                    $columnMapping['pt4'] = $index;
                } elseif (stripos($headerLower, 'qa') !== false || stripos($headerLower, 'quarterly') !== false) {
                    $columnMapping['qa'] = $index;
                }
            }

            if ($columnMapping['student_number'] === null) {
                throw new \Exception('Could not find Student Number column in Excel file.');
            }

            // Get students in this class
            $classStudents = StudyLoad::where('class_schedule_id', $classSchedule->id)
                ->with('student')
                ->get()
                ->keyBy('student.student_number');

            $importedCount = 0;
            $missingStudents = [];

            // Process data rows (skip header)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Get student number
                $studentNumber = trim($row[$columnMapping['student_number']] ?? '');
                
                if (empty($studentNumber)) {
                    continue;
                }

                // Check if student exists in class
                if (!$classStudents->has($studentNumber)) {
                    $missingStudents[] = $studentNumber;
                    continue;
                }

                $studyLoad = $classStudents[$studentNumber];
                $studentId = $studyLoad->student_id;

                // Check if grade is approved (locked)
                $existingGrade = FinalGrade::where('student_id', $studentId)
                    ->where('class_schedule_id', $classSchedule->id)
                    ->first();

                if ($existingGrade && $existingGrade->status === 'approved') {
                    continue; // Skip approved grades - they are locked
                }

                // Extract scores (get calculated values, ignore formulas)
                $componentScores = [
                    'written_works' => [
                        'ww1' => $columnMapping['ww1'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['ww1'] + 1) : null,
                        'ww2' => $columnMapping['ww2'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['ww2'] + 1) : null,
                        'ww3' => $columnMapping['ww3'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['ww3'] + 1) : null,
                        'ww4' => $columnMapping['ww4'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['ww4'] + 1) : null,
                    ],
                    'performance_tasks' => [
                        'pt1' => $columnMapping['pt1'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['pt1'] + 1) : null,
                        'pt2' => $columnMapping['pt2'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['pt2'] + 1) : null,
                        'pt3' => $columnMapping['pt3'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['pt3'] + 1) : null,
                        'pt4' => $columnMapping['pt4'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['pt4'] + 1) : null,
                    ],
                    'quarterly_assessment' => [
                        'qa' => $columnMapping['qa'] !== null ? $this->getCellValue($worksheet, $i + 1, $columnMapping['qa'] + 1) : null,
                        'total' => null,
                    ],
                ];

                // Automatically compute grades
                $computedGrades = $this->computeGradesFromComponentScores($componentScores);
                $finalGradeValue = $computedGrades['quarterly_grade'];
                $autoRemarks = $this->determineRemarks($finalGradeValue);

                // Save or update grade
                if ($replaceExisting) {
                    FinalGrade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'class_schedule_id' => $classSchedule->id,
                        ],
                        [
                            'component_scores' => $componentScores,
                            'initial_grade' => is_numeric($computedGrades['initial_grade']) ? $computedGrades['initial_grade'] : null,
                            'transmuted_grade' => is_numeric($computedGrades['transmuted_grade']) ? $computedGrades['transmuted_grade'] : null,
                            'quarterly_grade' => is_numeric($computedGrades['quarterly_grade']) ? $computedGrades['quarterly_grade'] : null,
                            'final_grade' => is_numeric($finalGradeValue) ? $finalGradeValue : null,
                            'remarks' => $autoRemarks,
                            'computed_at' => now(),
                            'status' => 'draft',
                        ]
                    );
                } else {
                    // Only create if doesn't exist
                    if (!$existingGrade) {
                        FinalGrade::create([
                            'student_id' => $studentId,
                            'class_schedule_id' => $classSchedule->id,
                            'component_scores' => $componentScores,
                            'initial_grade' => is_numeric($computedGrades['initial_grade']) ? $computedGrades['initial_grade'] : null,
                            'transmuted_grade' => is_numeric($computedGrades['transmuted_grade']) ? $computedGrades['transmuted_grade'] : null,
                            'quarterly_grade' => is_numeric($computedGrades['quarterly_grade']) ? $computedGrades['quarterly_grade'] : null,
                            'final_grade' => is_numeric($finalGradeValue) ? $finalGradeValue : null,
                            'remarks' => $autoRemarks,
                            'computed_at' => now(),
                            'status' => 'draft',
                        ]);
                    }
                }

                $importedCount++;
            }

            DB::commit();

            $message = "Successfully imported grades for {$importedCount} students.";
            if (!empty($missingStudents)) {
                $message .= " Warning: " . count($missingStudents) . " students not found in class: " . implode(', ', array_slice($missingStudents, 0, 5)) . (count($missingStudents) > 5 ? '...' : '');
            }

            return redirect()
                ->route('teacher.grades.index', $classSchedule)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('teacher.grades.index', $classSchedule)
                ->with('error', 'Error importing grades: ' . $e->getMessage());
        }
    }

    private function getCellValue($worksheet, $row, $col)
    {
        $cell = $worksheet->getCellByColumnAndRow($col, $row);
        
        // Get calculated value (ignores formulas, returns the result)
        $value = $cell->getCalculatedValue();
        
        // If it's a formula and calculated value is same as formula, try getOldCalculatedValue
        if ($cell->isFormula() && $value === $cell->getValue()) {
            $value = $cell->getOldCalculatedValue();
        }
        
        // Convert to float if numeric
        if (is_numeric($value)) {
            return floatval($value);
        }
        
        return null;
    }

    public function updateScore(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'score_item_id' => 'required|exists:score_items,id',
            'score' => 'required|numeric|min:0',
        ]);

        $studentScore = StudentScore::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'score_item_id' => $request->score_item_id,
            ],
            [
                'score' => $request->score,
            ]
        );

        return response()->json(['success' => true, 'score' => $studentScore->score]);
    }

    public function computeGrades(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $students = StudyLoad::where('class_schedule_id', $classSchedule->id)
            ->get();

        try {
            DB::beginTransaction();

            foreach ($students as $studyLoad) {
                $finalGrade = FinalGrade::where('student_id', $studyLoad->student_id)
                    ->where('class_schedule_id', $classSchedule->id)
                    ->first();

                if (!$finalGrade || !$finalGrade->component_scores) {
                    continue;
                }

                $componentScores = $finalGrade->component_scores;

                // Calculate Written Works average (30% weight)
                $wwScores = array_filter([
                    $componentScores['written_works']['ww1'] ?? 0,
                    $componentScores['written_works']['ww2'] ?? 0,
                    $componentScores['written_works']['ww3'] ?? 0,
                    $componentScores['written_works']['ww4'] ?? 0,
                ], function($val) { return $val !== null && $val !== ''; });
                
                $wwTotal = array_sum($wwScores);
                $wwMax = count($wwScores) * 20;
                $wwAverage = $wwMax > 0 ? ($wwTotal / $wwMax) * 100 : 0;
                $wwWeighted = $wwAverage * 0.30;

                // Calculate Performance Tasks average (50% weight)
                $ptScores = array_filter([
                    $componentScores['performance_tasks']['pt1'] ?? 0,
                    $componentScores['performance_tasks']['pt2'] ?? 0,
                    $componentScores['performance_tasks']['pt3'] ?? 0,
                    $componentScores['performance_tasks']['pt4'] ?? 0,
                ], function($val) { return $val !== null && $val !== ''; });
                
                $ptTotal = array_sum($ptScores);
                $ptMax = count($ptScores) * 50;
                $ptAverage = $ptMax > 0 ? ($ptTotal / $ptMax) * 100 : 0;
                $ptWeighted = $ptAverage * 0.50;

                // Calculate Quarterly Assessment (20% weight)
                $qaScore = $componentScores['quarterly_assessment']['qa'] ?? 0;
                $qaWeighted = ($qaScore / 100) * 100 * 0.20;

                // Calculate final grade
                $finalGradeValue = round($wwWeighted + $ptWeighted + $qaWeighted, 2);
                $remarks = $this->determineRemarks($finalGradeValue);

                // Update the final grade
                $finalGrade->update([
                    'final_grade' => $finalGradeValue,
                    'remarks' => $remarks,
                    'computed_at' => now(),
                    'status' => 'draft',
                ]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function determineRemarks($grade)
    {
        if ($grade === null) {
            return 'Incomplete';
        }

        return $grade >= 75 ? 'Passed' : 'Failed';
    }

    private function transmuteGrade($initialGrade)
    {
        if ($initialGrade === null) {
            return null;
        }

        // DepEd Transmutation Table (DO 8, s. 2015)
        $transmutationTable = [
            100 => 100,
            99 => 99,
            98 => 98,
            97 => 97,
            96 => 96,
            95 => 95,
            94 => 95,
            93 => 94,
            92 => 93,
            91 => 92,
            90 => 91,
            89 => 90,
            88 => 89,
            87 => 88,
            86 => 87,
            85 => 86,
            84 => 85,
            83 => 84,
            82 => 83,
            81 => 82,
            80 => 81,
            79 => 80,
            78 => 79,
            77 => 78,
            76 => 77,
            75 => 75,
            74 => 74,
            73 => 73,
            72 => 72,
            71 => 71,
            70 => 70,
            69 => 69,
            68 => 68,
            67 => 67,
            66 => 66,
            65 => 65,
            64 => 64,
            63 => 63,
            62 => 62,
            61 => 61,
            60 => 60,
            59 => 59,
            58 => 58,
            57 => 57,
            56 => 56,
            55 => 55,
            54 => 54,
            53 => 53,
            52 => 52,
            51 => 51,
            50 => 50,
            49 => 49,
            48 => 48,
            47 => 47,
            46 => 46,
            45 => 45,
            44 => 44,
            43 => 43,
            42 => 42,
            41 => 41,
            40 => 40,
            39 => 39,
            38 => 38,
            37 => 37,
            36 => 36,
            35 => 35,
            34 => 34,
            33 => 33,
            32 => 32,
            31 => 31,
            30 => 30,
            29 => 29,
            28 => 28,
            27 => 27,
            26 => 26,
            25 => 25,
            24 => 24,
            23 => 23,
            22 => 22,
            21 => 21,
            20 => 20,
            19 => 19,
            18 => 18,
            17 => 17,
            16 => 16,
            15 => 15,
            14 => 14,
            13 => 13,
            12 => 12,
            11 => 11,
            10 => 10,
            9 => 9,
            8 => 8,
            7 => 7,
            6 => 6,
            5 => 5,
            4 => 4,
            3 => 3,
            2 => 2,
            1 => 1,
            0 => 0,
        ];

        $roundedGrade = round($initialGrade);
        return $transmutationTable[$roundedGrade] ?? $initialGrade;
    }

    private function computeGradesFromComponentScores($componentScores)
    {
        // Calculate Written Works average (30% weight)
        $wwScores = array_filter([
            $componentScores['written_works']['ww1'] ?? 0,
            $componentScores['written_works']['ww2'] ?? 0,
            $componentScores['written_works']['ww3'] ?? 0,
            $componentScores['written_works']['ww4'] ?? 0,
        ], function($val) { return $val !== null && $val !== ''; });
        
        $wwTotal = array_sum($wwScores);
        $wwMax = count($wwScores) * 20;
        $wwAverage = $wwMax > 0 ? ($wwTotal / $wwMax) * 100 : 0;
        $wwWeighted = $wwAverage * 0.30;

        // Calculate Performance Tasks average (50% weight)
        $ptScores = array_filter([
            $componentScores['performance_tasks']['pt1'] ?? 0,
            $componentScores['performance_tasks']['pt2'] ?? 0,
            $componentScores['performance_tasks']['pt3'] ?? 0,
            $componentScores['performance_tasks']['pt4'] ?? 0,
        ], function($val) { return $val !== null && $val !== ''; });
        
        $ptTotal = array_sum($ptScores);
        $ptMax = count($ptScores) * 50;
        $ptAverage = $ptMax > 0 ? ($ptTotal / $ptMax) * 100 : 0;
        $ptWeighted = $ptAverage * 0.50;

        // Calculate Quarterly Assessment (20% weight)
        $qaScore = $componentScores['quarterly_assessment']['qa'] ?? 0;
        $qaWeighted = ($qaScore / 100) * 100 * 0.20;

        // Calculate initial grade (raw average)
        $initialGrade = round($wwWeighted + $ptWeighted + $qaWeighted, 2);

        // Transmute the grade
        $transmutedGrade = $this->transmuteGrade($initialGrade);

        // Quarterly grade is the transmuted grade
        $quarterlyGrade = $transmutedGrade;

        return [
            'initial_grade' => $initialGrade,
            'transmuted_grade' => $transmutedGrade,
            'quarterly_grade' => $quarterlyGrade,
        ];
    }

    public function saveManual(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'scores' => 'required|array',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|in:Passed,Failed,Incomplete,Dropped',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->scores as $studentId => $componentScores) {
                $remarks = $request->remarks[$studentId] ?? null;

                // Check if grade is approved (locked)
                $existingGrade = FinalGrade::where('student_id', $studentId)
                    ->where('class_schedule_id', $classSchedule->id)
                    ->first();

                if ($existingGrade && $existingGrade->status === 'approved') {
                    continue; // Skip approved grades - they are locked
                }

                // Automatically compute grades from component scores
                $computedGrades = $this->computeGradesFromComponentScores($componentScores);
                $finalGradeValue = $computedGrades['quarterly_grade'];
                $autoRemarks = $this->determineRemarks($finalGradeValue);

                // Use manual remarks if provided, otherwise use computed remarks
                $finalRemarks = $remarks ?? $autoRemarks;

                FinalGrade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_schedule_id' => $classSchedule->id,
                    ],
                    [
                        'component_scores' => $componentScores,
                        'initial_grade' => is_numeric($computedGrades['initial_grade']) ? $computedGrades['initial_grade'] : null,
                        'transmuted_grade' => is_numeric($computedGrades['transmuted_grade']) ? $computedGrades['transmuted_grade'] : null,
                        'quarterly_grade' => is_numeric($computedGrades['quarterly_grade']) ? $computedGrades['quarterly_grade'] : null,
                        'final_grade' => is_numeric($finalGradeValue) ? $finalGradeValue : null,
                        'remarks' => $finalRemarks,
                        'computed_at' => now(),
                        'status' => 'draft',
                    ]
                );
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
