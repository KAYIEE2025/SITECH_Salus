<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\StudyLoad;
use App\Models\Student;
use App\Models\FinalGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassSchedule::where('teacher_id', auth()->id())
            ->with(['subject', 'section.yearLevel'])
            ->withCount('studyLoads')
            ->orderBy('school_year', 'desc')
            ->orderBy('section_id')
            ->get();

        $sectionGroups = $classes
            ->groupBy(fn (ClassSchedule $class) => $class->section_id ?? 'unassigned')
            ->map(function ($sectionClasses) {
                $section = $sectionClasses->first()->section;

                return [
                    'section' => $section,
                    'classes' => $sectionClasses
                        ->sortBy(fn (ClassSchedule $class) => implode('|', [
                            $class->school_year,
                            $class->subject->name ?? '',
                        ]))
                        ->values(),
                    'student_count' => $sectionClasses->max('study_loads_count') ?? 0,
                ];
            })
            ->sortBy(fn ($group) => ($group['section']->yearLevel->level ?? 999) . '-' . ($group['section']->name ?? 'Unassigned'))
            ->values();

        return view('teacher.classes.index', compact('sectionGroups'));
    }

    public function showStudents(ClassSchedule $classSchedule)
    {
        // Verify the class belongs to the logged-in teacher
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $students = StudyLoad::where('class_schedule_id', $classSchedule->id)
            ->with('student')
            ->get()
            ->sortBy('student.last_name');

        return view('teacher.classes.students', compact('classSchedule', 'students'));
    }

    public function manageGrades(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $classSchedule->load(['subject', 'section.yearLevel']);

        $students = StudyLoad::where('class_schedule_id', $classSchedule->id)
            ->with('student')
            ->get()
            ->sortBy('student.last_name');

        // Check for existing grades and their status
        $existingGrades = FinalGrade::where('class_schedule_id', $classSchedule->id)->get();
        $gradeStatus = null;
        $gradeTimeline = collect();
        
        // Check which terms are approved and their individual statuses
        $approvedTerms = [
            'term_1' => false,
            'term_2' => false,
            'term_3' => false,
        ];
        
        $termStatus = [
            'term_1' => null,
            'term_2' => null,
            'term_3' => null,
        ];
        
        if ($existingGrades->isNotEmpty()) {
            // Set overall gradeStatus for backward compatibility with existing UI elements
            $gradeStatus = $existingGrades->first()->status;
            
            // Check each term for approval and status
            foreach ($existingGrades as $grade) {
                // Track approved terms
                if ($grade->status === 'approved') {
                    if ($grade->term_1) $approvedTerms['term_1'] = true;
                    if ($grade->term_2) $approvedTerms['term_2'] = true;
                    if ($grade->term_3) $approvedTerms['term_3'] = true;
                }
                
                // Track individual term statuses (use the most recent status for each term)
                if ($grade->term_1 && $termStatus['term_1'] !== 'approved') {
                    $termStatus['term_1'] = $grade->status;
                }
                if ($grade->term_2 && $termStatus['term_2'] !== 'approved') {
                    $termStatus['term_2'] = $grade->status;
                }
                if ($grade->term_3 && $termStatus['term_3'] !== 'approved') {
                    $termStatus['term_3'] = $grade->status;
                }
            }
            
            // Get grade history for timeline
            $gradeTimeline = \App\Models\GradeHistory::whereHas('finalGrade', function($query) use ($classSchedule) {
                $query->where('class_schedule_id', $classSchedule->id);
            })
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->groupBy(function($history) {
                return $history->created_at->format('Y-m-d H:i:s');
            })
            ->map(function($group) {
                $first = $group->first();
                return [
                    'timestamp' => $first->created_at,
                    'action' => ucfirst($first->action),
                    'status' => ucfirst($first->status),
                    'description' => $first->description,
                    'user' => $first->user->name ?? 'System',
                    'rejection_reason' => $first->rejection_reason,
                ];
            })
            ->values();
        }

        // Check if all terms are approved
        $allTermsApproved = $approvedTerms['term_1'] && $approvedTerms['term_2'] && $approvedTerms['term_3'];

        return view('teacher.classes.grades', compact(
            'classSchedule', 
            'students', 
            'existingGrades', 
            'gradeStatus', 
            'gradeTimeline',
            'approvedTerms',
            'termStatus',
            'allTermsApproved'
        ));
    }

    public function processImport(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240',
            'grading_period' => 'required|in:1,2,3',
        ]);

        $gradingPeriod = $request->grading_period;
        $termField = 'term_' . $gradingPeriod;

        // Check if this grading period is already approved
        $alreadyApproved = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('status', 'approved')
            ->whereNotNull($termField)
            ->exists();

        if ($alreadyApproved) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', "Term {$gradingPeriod} grades for this class have already been approved and cannot be modified.");
        }

        try {
            $file = $request->file('excel_file');
            
            // Use optimized reader with readDataOnly to reduce memory usage
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            
            // Load the spreadsheet
            $spreadsheet = $reader->load($file->getPathname());
            
            // Find the worksheet named exactly "SUMMARY OF GRADES"
            $worksheetName = 'SUMMARY OF GRADES';
            $worksheet = $spreadsheet->getSheetByName($worksheetName);
            
            if ($worksheet === null) {
                return redirect()
                    ->route('teacher.classes.grades', $classSchedule)
                    ->with('error', 'Worksheet named "SUMMARY OF GRADES" not found in the Excel file.');
            }
            
            // Get dimensions
            $highestRow = $worksheet->getHighestDataRow();
            $highestColumn = $worksheet->getHighestColumn();
            
            // Log worksheet information
            \Log::info('Excel Worksheet Selected', [
                'worksheet_name' => $worksheetName,
                'highest_row' => $highestRow,
                'highest_column' => $highestColumn,
            ]);
            
            // PHASE 2: Detect header row and column mapping
            $headerDetection = $this->detectHeaderRowAndColumns($worksheet, $highestRow, $highestColumn);
            
            if ($headerDetection['header_row'] === -1) {
                return redirect()
                    ->route('teacher.classes.grades', $classSchedule)
                    ->with('error', 'Header row not found in the worksheet.');
            }
            
            // Log header detection results
            \Log::info('Header Detection Results', [
                'header_row' => $headerDetection['header_row'],
                'column_mapping' => $headerDetection['column_mapping'],
            ]);
            
            // PHASE 6A: Read all learners, match with students, and store in session for preview
            $allLearners = $this->readAllLearners($worksheet, $headerDetection['header_row'], $headerDetection['column_mapping'], $highestRow);
            
            if (empty($allLearners)) {
                return redirect()
                    ->route('teacher.classes.grades', $classSchedule)
                    ->with('error', 'No learner data found after header row.');
            }
            
            // Get enrolled students for matching
            $enrolledStudents = StudyLoad::where('class_schedule_id', $classSchedule->id)
                ->with('student')
                ->get();
            
            // Match learners with enrolled students
            $matchedLearners = $this->matchLearnersWithStudents($allLearners, $enrolledStudents);
            
            // Log matching results
            \Log::info('Student Matching Results', [
                'total_learners' => count($allLearners),
                'matched_count' => count(array_filter($matchedLearners, fn($m) => $m['match_status'] === 'matched')),
                'unmatched_count' => count(array_filter($matchedLearners, fn($m) => $m['match_status'] === 'unmatched')),
                'ambiguous_count' => count(array_filter($matchedLearners, fn($m) => $m['match_status'] === 'ambiguous')),
                'matching_details' => $matchedLearners,
            ]);
            
            // Map grading period to term column
            $gradingPeriod = $request->grading_period;
            $termColumn = 'term_' . $gradingPeriod;
            
            // Store data in session for preview (no database save yet)
            session([
                'import_preview_' . $classSchedule->id => [
                    'all_learners' => $allLearners,
                    'matched_learners' => $matchedLearners,
                    'grading_period' => $gradingPeriod,
                    'term_column' => $termColumn,
                ],
            ]);
            
            // Redirect to preview page
            return redirect()->route('teacher.classes.import-summary-preview', $classSchedule);
            
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'Unable to read the Excel file. Please ensure it is a valid .xlsx or .xls file.');
        } catch (\Exception $e) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'Error processing the grading sheet: ' . $e->getMessage());
        }
    }

    private function detectHeaderRowAndColumns($worksheet, $highestRow, $highestColumn)
    {
        $headerRowIndex = -1;
        $columnMapping = [
            'learners_name' => null,
            'term_1' => null,
            'term_2' => null,
            'term_3' => null,
            'final_grade' => null,
            'descriptor' => null,
            'remarks' => null,
        ];
        
        // Convert highest column to numeric index
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        
        // Search for header row in first 30 rows
        $searchLimit = min(30, $highestRow);
        for ($row = 1; $row <= $searchLimit; $row++) {
            $hasRequiredColumns = false;
            $foundColumns = 0;
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $value = $cell->getCalculatedValue();
                
                if (is_string($value)) {
                    $valueUpper = strtoupper(trim($value));
                    
                    // Check for required columns
                    if (strpos($valueUpper, 'LEARNERS') !== false && strpos($valueUpper, 'NAME') !== false) {
                        $columnMapping['learners_name'] = $col;
                        $foundColumns++;
                    }
                    
                    if (strpos($valueUpper, 'TERM') !== false && strpos($valueUpper, '1') !== false) {
                        $columnMapping['term_1'] = $col;
                        $foundColumns++;
                    }
                    
                    if (strpos($valueUpper, 'TERM') !== false && strpos($valueUpper, '2') !== false) {
                        $columnMapping['term_2'] = $col;
                        $foundColumns++;
                    }
                    
                    if (strpos($valueUpper, 'TERM') !== false && strpos($valueUpper, '3') !== false) {
                        $columnMapping['term_3'] = $col;
                        $foundColumns++;
                    }
                    
                    if (strpos($valueUpper, 'FINAL') !== false && strpos($valueUpper, 'GRADE') !== false) {
                        $columnMapping['final_grade'] = $col;
                        $foundColumns++;
                    }
                    
                    if (strpos($valueUpper, 'DESCRIPTOR') !== false) {
                        $columnMapping['descriptor'] = $col;
                        $foundColumns++;
                    }
                    
                    if (strpos($valueUpper, 'REMARK') !== false) {
                        $columnMapping['remarks'] = $col;
                        $foundColumns++;
                    }
                }
            }
            
            // If we found at least 3 required columns, consider this the header row
            if ($foundColumns >= 3) {
                $headerRowIndex = $row;
                break;
            }
        }
        
        return [
            'header_row' => $headerRowIndex,
            'column_mapping' => $columnMapping,
        ];
    }

    private function readAllLearners($worksheet, $headerRow, $columnMapping, $highestRow)
    {
        $allLearners = [];
        
        // Start from the row after header and read until the end
        $startRow = $headerRow + 1;
        
        for ($row = $startRow; $row <= $highestRow; $row++) {
            // Extract learners name to check if it's a valid learner row
            $learnersName = $columnMapping['learners_name'] !== null 
                ? trim($worksheet->getCellByColumnAndRow($columnMapping['learners_name'], $row)->getCalculatedValue()) 
                : '';
            
            // Skip empty rows or non-learner rows
            if (empty($learnersName) || !$this->isValidLearnerRow($learnersName)) {
                continue;
            }
            
            // Extract data for all required columns
            $learnerData = [
                'learners_name' => $learnersName,
                'term_1' => $columnMapping['term_1'] !== null 
                    ? $worksheet->getCellByColumnAndRow($columnMapping['term_1'], $row)->getCalculatedValue() 
                    : null,
                'term_2' => $columnMapping['term_2'] !== null 
                    ? $worksheet->getCellByColumnAndRow($columnMapping['term_2'], $row)->getCalculatedValue() 
                    : null,
                'term_3' => $columnMapping['term_3'] !== null 
                    ? $worksheet->getCellByColumnAndRow($columnMapping['term_3'], $row)->getCalculatedValue() 
                    : null,
                'final_grade' => $columnMapping['final_grade'] !== null 
                    ? $worksheet->getCellByColumnAndRow($columnMapping['final_grade'], $row)->getCalculatedValue() 
                    : null,
                'descriptor' => $columnMapping['descriptor'] !== null 
                    ? trim($worksheet->getCellByColumnAndRow($columnMapping['descriptor'], $row)->getCalculatedValue()) 
                    : '',
                'remarks' => $columnMapping['remarks'] !== null 
                    ? trim($worksheet->getCellByColumnAndRow($columnMapping['remarks'], $row)->getCalculatedValue()) 
                    : '',
            ];
            
            $allLearners[] = $learnerData;
        }
        
        return $allLearners;
    }

    private function matchLearnersWithStudents($allLearners, $enrolledStudents)
    {
        $matchedLearners = [];
        $firstUnmatchedLogged = false;
        
        foreach ($allLearners as $learner) {
            $excelName = $learner['learners_name'];
            $parsedName = $this->parseExcelName($excelName);
            
            // Pass original Excel name for debug logging (only for first unmatched)
            $matchedStudent = $this->matchStudentByName($parsedName, $enrolledStudents, !$firstUnmatchedLogged ? $excelName : null);
            
            // Handle ambiguous matches
            if ($matchedStudent && isset($matchedStudent['ambiguous']) && $matchedStudent['ambiguous']) {
                $matchResult = [
                    'excel_name' => $excelName,
                    'matched_student' => null,
                    'student_id' => null,
                    'match_status' => 'ambiguous',
                    'ambiguous_matches' => $matchedStudent['matches'],
                ];
            } elseif ($matchedStudent) {
                $matchResult = [
                    'excel_name' => $excelName,
                    'matched_student' => $matchedStudent['student_name'],
                    'student_id' => $matchedStudent['student_id'],
                    'match_status' => 'matched',
                ];
            } else {
                $matchResult = [
                    'excel_name' => $excelName,
                    'matched_student' => null,
                    'student_id' => null,
                    'match_status' => 'unmatched',
                ];
                $firstUnmatchedLogged = true;
            }
            
            $matchedLearners[] = $matchResult;
        }
        
        return $matchedLearners;
    }

    private function saveGradesForPeriod($matchedLearners, $allLearners, $classSchedule, $termColumn, $gradingPeriod)
    {
        try {
            DB::beginTransaction();
            
            $savedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $skippedApprovedCount = 0;
            $skippedAmbiguousCount = 0;
            
            foreach ($matchedLearners as $index => $matched) {
                // Skip unmatched or ambiguous learners
                if ($matched['match_status'] !== 'matched') {
                    $skippedCount++;
                    if ($matched['match_status'] === 'ambiguous') {
                        $skippedAmbiguousCount++;
                    }
                    continue;
                }
                
                $studentId = $matched['student_id'];
                $learnerData = $allLearners[$index];
                $gradeValue = $learnerData[$termColumn] ?? null;
                
                // Check if grade already exists
                $existingGrade = FinalGrade::where('student_id', $studentId)
                    ->where('class_schedule_id', $classSchedule->id)
                    ->first();
                
                // Check if the specific grading period is already approved (locked)
                $termField = 'term_' . $gradingPeriod;
                if ($existingGrade && $existingGrade->status === 'approved' && $existingGrade->$termField) {
                    $skippedApprovedCount++;
                    continue;
                }
                
                // Prepare grade data based on grading period
                $gradeData = [
                    'student_id' => $studentId,
                    'class_schedule_id' => $classSchedule->id,
                    'student_name' => $learnerData['learners_name'],
                    'computed_at' => now(),
                ];
                
                // Only set status to draft if this is a new record
                // If updating an existing approved record, keep the approved status
                if (!$existingGrade) {
                    $gradeData['status'] = 'draft';
                }
                
                // Map term to appropriate field
                if ($gradingPeriod == 1) {
                    $gradeData['term_1'] = $gradeValue;
                } elseif ($gradingPeriod == 2) {
                    $gradeData['term_2'] = $gradeValue;
                } elseif ($gradingPeriod == 3) {
                    $gradeData['term_3'] = $gradeValue;
                }
                
                // Add remarks if descriptor is available
                if (!empty($learnerData['remarks'])) {
                    $gradeData['remarks'] = $learnerData['remarks'];
                } elseif (!empty($learnerData['descriptor'])) {
                    $gradeData['remarks'] = $learnerData['descriptor'];
                }
                
                if ($existingGrade) {
                    // Update existing draft record
                    $existingGrade->update($gradeData);
                    $updatedCount++;
                } else {
                    // Create new record
                    FinalGrade::create($gradeData);
                    $savedCount++;
                }
            }
            
            DB::commit();
            
            $message = "Quarter {$gradingPeriod} grades saved: {$savedCount} new, {$updatedCount} updated";
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} skipped";
            }
            if ($skippedAmbiguousCount > 0) {
                $message .= " ({$skippedAmbiguousCount} ambiguous)";
            }
            if ($skippedApprovedCount > 0) {
                $message .= ", {$skippedApprovedCount} skipped (approved/submitted)";
            }
            
            return [
                'success' => true,
                'message' => $message,
                'saved_count' => $savedCount,
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'skipped_approved_count' => $skippedApprovedCount,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error saving grades: ' . $e->getMessage(),
            ];
        }
    }

    private function isValidLearnerRow($learnersName)
    {
        if (empty($learnersName)) {
            return false;
        }
        
        $nameUpper = strtoupper(trim($learnersName));
        
        // Skip non-learner rows
        $nonLearnerPatterns = [
            'LEARNERS\' NAMES',
            'LEARNERS NAMES',
            'HIGHEST POSSIBLE SCORE',
            'MALE',
            'FEMALE',
            'TOTAL',
            'AVERAGE',
            'GENERAL',
            'REMARKS',
            'SUBTOTAL',
        ];
        
        foreach ($nonLearnerPatterns as $pattern) {
            if (strpos($nameUpper, $pattern) !== false) {
                return false;
            }
        }
        
        // Valid learner names should have a reasonable length
        if (strlen($nameUpper) < 3) {
            return false;
        }
        
        return true;
    }

    private function extractClassInfoOptimized($worksheet, $highestRow, $highestColumn)
    {
        $classInfo = [
            'school_year' => '',
            'grade_level' => '',
            'section' => '',
            'subject' => '',
            'subject_code' => '',
            'teacher_name' => '',
        ];
        
        // Convert highest column to numeric index
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        
        // Read only the first 10 rows for class info (SALUS template)
        $maxRow = min(10, $highestRow);
        
        for ($row = 1; $row <= $maxRow; $row++) {
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $value = $cell->getCalculatedValue();
                
                if (is_string($value)) {
                    $valueLower = strtolower($value);
                    
                    // Check if this cell contains a label
                    if (stripos($valueLower, 'school year') !== false || stripos($valueLower, 'sy') !== false) {
                        $nextCell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $classInfo['school_year'] = trim($nextCell->getCalculatedValue());
                    }
                    
                    if (stripos($valueLower, 'grade') !== false && stripos($valueLower, 'level') !== false) {
                        $nextCell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $classInfo['grade_level'] = trim($nextCell->getCalculatedValue());
                    }
                    
                    if (stripos($valueLower, 'section') !== false) {
                        $nextCell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $classInfo['section'] = trim($nextCell->getCalculatedValue());
                    }
                    
                    if (stripos($valueLower, 'subject') !== false) {
                        $nextCell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $classInfo['subject'] = trim($nextCell->getCalculatedValue());
                    }
                    
                    if (stripos($valueLower, 'code') !== false) {
                        $nextCell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $classInfo['subject_code'] = trim($nextCell->getCalculatedValue());
                    }
                    
                    if (stripos($valueLower, 'teacher') !== false || stripos($valueLower, 'adviser') !== false) {
                        $nextCell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $classInfo['teacher_name'] = trim($nextCell->getCalculatedValue());
                    }
                }
            }
        }
        
        return $classInfo;
    }

    private function extractStudentRecordsOptimized($worksheet, $highestRow, $highestColumn)
    {
        $studentRecords = [];
        
        // Convert highest column to numeric index
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        
        // Find the header row (SALUS template typically has headers around row 8-12)
        $headerRowIndex = -1;
        $columnMapping = [
            'student_number' => null,
            'student_name' => null,
            'initial_grade' => null,
            'quarterly_grade' => null,
            'remarks' => null,
        ];
        
        // Search for header row in first 20 rows
        $searchLimit = min(20, $highestRow);
        for ($row = 1; $row <= $searchLimit; $row++) {
            $hasStudentColumns = false;
            $rowHeaders = [];
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $value = $cell->getCalculatedValue();
                
                if (is_string($value)) {
                    $valueLower = strtolower($value);
                    $rowHeaders[$col] = $value;
                    
                    if (stripos($valueLower, 'student') !== false || 
                        stripos($valueLower, 'name') !== false || 
                        stripos($valueLower, 'lrn') !== false ||
                        stripos($valueLower, 'number') !== false) {
                        $hasStudentColumns = true;
                    }
                    
                    // Map columns based on header names
                    if (stripos($valueLower, 'student') !== false && (stripos($valueLower, 'number') !== false || stripos($valueLower, 'lrn') !== false)) {
                        $columnMapping['student_number'] = $col;
                    } elseif (stripos($valueLower, 'name') !== false && stripos($valueLower, 'student') === false) {
                        $columnMapping['student_name'] = $col;
                    } elseif (stripos($valueLower, 'initial') !== false && stripos($valueLower, 'grade') !== false) {
                        $columnMapping['initial_grade'] = $col;
                    } elseif (stripos($valueLower, 'term') !== false || stripos($valueLower, 'quarterly') !== false) {
                        $columnMapping['quarterly_grade'] = $col;
                    } elseif (stripos($valueLower, 'descriptor') !== false || stripos($valueLower, 'remark') !== false) {
                        $columnMapping['remarks'] = $col;
                    }
                }
            }
            
            if ($hasStudentColumns) {
                $headerRowIndex = $row;
                \Log::info('Header row found at row ' . $row . ':', $rowHeaders);
                break;
            }
        }
        
        if ($headerRowIndex === -1) {
            return [];
        }
        
        // Debug: Log column mapping
        \Log::info('Column mapping found:', [
            'header_row' => $headerRowIndex,
            'mapping' => $columnMapping,
        ]);
        
        // Extract student data row by row after header
        for ($row = $headerRowIndex + 1; $row <= $highestRow; $row++) {
            // Check if row is empty
            $isEmpty = true;
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $value = $cell->getCalculatedValue();
                if (!empty($value) && $value !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            
            if ($isEmpty) {
                continue;
            }
            
            // Extract student name to check if it's a valid student row
            $studentName = $columnMapping['student_name'] !== null 
                ? trim($worksheet->getCellByColumnAndRow($columnMapping['student_name'], $row)->getCalculatedValue()) 
                : '';
            
            // Skip non-student rows
            if (!$this->isValidStudentRow($studentName)) {
                continue;
            }
            
            // Extract only needed columns
            $studentNumber = $columnMapping['student_number'] !== null 
                ? trim($worksheet->getCellByColumnAndRow($columnMapping['student_number'], $row)->getCalculatedValue()) 
                : '';
            
            // Skip if no identifying information
            if (empty($studentName)) {
                continue;
            }
            
            // Extract grade values
            $initialGradeValue = $columnMapping['initial_grade'] !== null 
                ? $worksheet->getCellByColumnAndRow($columnMapping['initial_grade'], $row)->getCalculatedValue() 
                : null;
            $quarterlyGradeValue = $columnMapping['quarterly_grade'] !== null 
                ? $worksheet->getCellByColumnAndRow($columnMapping['quarterly_grade'], $row)->getCalculatedValue() 
                : null;
            $remarksValue = $columnMapping['remarks'] !== null 
                ? trim($worksheet->getCellByColumnAndRow($columnMapping['remarks'], $row)->getCalculatedValue()) 
                : '';
            
            // Debug: Log first few student records
            if (count($studentRecords) < 3) {
                \Log::info('Student record extraction:', [
                    'row' => $row,
                    'student_name' => $studentName,
                    'initial_grade_col' => $columnMapping['initial_grade'],
                    'initial_grade_value' => $initialGradeValue,
                    'quarterly_grade_col' => $columnMapping['quarterly_grade'],
                    'quarterly_grade_value' => $quarterlyGradeValue,
                    'remarks_col' => $columnMapping['remarks'],
                    'remarks_value' => $remarksValue,
                ]);
            }
            
            $studentRecords[] = [
                'student_number' => $studentNumber,
                'student_name' => $studentName,
                'initial_grade' => $initialGradeValue,
                'quarterly_grade' => $quarterlyGradeValue,
                'remarks' => $remarksValue,
            ];
        }
        
        return $studentRecords;
    }

    private function isValidStudentRow($studentName)
    {
        if (empty($studentName)) {
            return false;
        }
        
        $nameUpper = strtoupper(trim($studentName));
        
        // Skip non-student rows
        $nonStudentPatterns = [
            'LEARNERS\' NAMES',
            'LEARNERS NAMES',
            'HIGHEST POSSIBLE SCORE',
            'MALE',
            'FEMALE',
            'TOTAL',
            'AVERAGE',
            'GENERAL',
            'REMARKS',
        ];
        
        foreach ($nonStudentPatterns as $pattern) {
            if (strpos($nameUpper, $pattern) !== false) {
                return false;
            }
        }
        
        // Valid student names should contain a comma (LASTNAME, FIRSTNAME format)
        // or at least have a reasonable length and structure
        if (strlen($nameUpper) < 3) {
            return false;
        }
        
        return true;
    }

    private function parseExcelName($excelName)
    {
        // Parse LASTNAME, REMAINING_NAME format
        if (strpos($excelName, ',') !== false) {
            $parts = explode(',', $excelName, 2);
            $lastName = trim($parts[0]);
            $remainingName = trim($parts[1] ?? '');
        } else {
            // Fallback: treat entire string as last name
            $lastName = trim($excelName);
            $remainingName = '';
        }
        
        // Normalize last name (uppercase, remove periods, collapse spaces, trim)
        $lastName = strtoupper(preg_replace('/[.]/', '', preg_replace('/\s+/', '', $lastName)));
        
        // Normalize remaining name (uppercase, remove periods, collapse spaces, trim)
        $remainingName = strtoupper(preg_replace('/[.]/', '', preg_replace('/\s+/', ' ', trim($remainingName))));
        
        return [
            'last_name' => $lastName,
            'remaining_name' => $remainingName,
        ];
    }

    private function matchStudentByName($parsedName, $enrolledStudents, $originalExcelName = null)
    {
        $matches = [];
        
        // Get normalized parsed name (already uppercase, no periods from parseExcelName)
        $normalizedLastName = $parsedName['last_name'];
        $normalizedRemainingName = $parsedName['remaining_name'];
        
        // Track first unmatched student for debug logging
        $firstUnmatchedLogged = false;
        
        foreach ($enrolledStudents as $studyLoad) {
            $student = $studyLoad->student;
            
            // Normalize database last name (uppercase, remove periods, collapse spaces, trim)
            $dbLastName = strtoupper(preg_replace('/[.]/', '', preg_replace('/\s+/', '', $student->last_name ?? '')));
            
            // Build database remaining name from first_name + ' ' + middle_name
            $dbRemainingName = trim(($student->first_name ?? '') . ' ' . ($student->middle_name ?? ''));
            
            // Normalize database remaining name (uppercase, remove periods, collapse spaces, trim)
            $dbRemainingName = strtoupper(preg_replace('/[.]/', '', preg_replace('/\s+/', ' ', $dbRemainingName)));
            
            // Debug logging for first unmatched student
            if (!$firstUnmatchedLogged && $originalExcelName !== null) {
                \Log::info('DEBUG: First Unmatched Student Comparison', [
                    'excel_raw' => $originalExcelName,
                    'excel_trimmed' => trim($originalExcelName),
                    'excel_last_name' => $normalizedLastName,
                    'excel_remaining_name' => $normalizedRemainingName,
                    'excel_last_length' => strlen($normalizedLastName),
                    'excel_last_hex' => bin2hex($normalizedLastName),
                    'excel_remaining_length' => strlen($normalizedRemainingName),
                    'excel_remaining_hex' => bin2hex($normalizedRemainingName),
                    'db_raw_last' => $student->last_name ?? '',
                    'db_raw_first' => $student->first_name ?? '',
                    'db_raw_middle' => $student->middle_name ?? '',
                    'db_trimmed_last' => trim($student->last_name ?? ''),
                    'db_trimmed_first' => trim($student->first_name ?? ''),
                    'db_trimmed_middle' => trim($student->middle_name ?? ''),
                    'db_last_name' => $dbLastName,
                    'db_remaining_name' => $dbRemainingName,
                    'db_last_length' => strlen($dbLastName),
                    'db_last_hex' => bin2hex($dbLastName),
                    'db_remaining_length' => strlen($dbRemainingName),
                    'db_remaining_hex' => bin2hex($dbRemainingName),
                    'comparison' => [
                        'last_name_match' => $normalizedLastName === $dbLastName,
                        'remaining_name_match' => $normalizedRemainingName === $dbRemainingName,
                        'excel_last' => $normalizedLastName,
                        'db_last' => $dbLastName,
                        'excel_remaining' => $normalizedRemainingName,
                        'db_remaining' => $dbRemainingName,
                    ],
                ]);
                $firstUnmatchedLogged = true;
            }
            
            // Rule 1: Compare surname exactly
            if ($normalizedLastName !== $dbLastName) {
                continue;
            }
            
            // Rule 2: Compare remaining name exactly
            if ($normalizedRemainingName !== $dbRemainingName) {
                continue;
            }
            
            // Both last name and remaining name match
            $matches[] = [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'student_name' => trim($student->last_name . ', ' . $student->first_name . ' ' . $student->middle_name),
            ];
        }
        
        // Rule 5: If more than one student matches, report as ambiguous
        if (count($matches) > 1) {
            return [
                'ambiguous' => true,
                'matches' => $matches,
            ];
        }
        
        // Single match found
        if (count($matches) === 1) {
            return $matches[0];
        }
        
        // No match found
        return null;
    }

    private function matchStudentsInRecords($studentRecords, $enrolledStudents)
    {
        foreach ($studentRecords as &$record) {
            if (!empty($record['student_name'])) {
                $parsedName = $this->parseExcelName($record['student_name']);
                $matchedStudent = $this->matchStudentByName($parsedName, $enrolledStudents);
                
                if ($matchedStudent) {
                    $record['student_id'] = $matchedStudent['student_id'];
                    $record['student_number'] = $matchedStudent['student_number'];
                    $record['matched_name'] = $matchedStudent['student_name'];
                } else {
                    $record['student_id'] = null;
                    $record['student_number'] = null;
                    $record['matched_name'] = null;
                }
            } else {
                $record['student_id'] = null;
                $record['student_number'] = null;
                $record['matched_name'] = null;
            }
        }
        
        return $studentRecords;
    }

    private function validateStudentRecords($studentRecords, $enrolledStudents)
    {
        $validatedRecords = [];
        $studentIds = [];
        $totalStudents = count($studentRecords);
        $validCount = 0;
        $invalidCount = 0;

        foreach ($studentRecords as $index => $record) {
            $validationStatus = 'valid';
            $validationMessages = [];

            // Check for missing student name
            if (empty($record['student_name'])) {
                $validationStatus = 'invalid';
                $validationMessages[] = 'Missing Student Name';
            }

            // Check if student was found in database
            if (empty($record['student_id'])) {
                $validationStatus = 'invalid';
                $validationMessages[] = 'Student not found in database';
            }

            // Check for duplicate students
            if (!empty($record['student_id'])) {
                if (in_array($record['student_id'], $studentIds)) {
                    $validationStatus = 'invalid';
                    $validationMessages[] = 'Duplicate Student';
                }
                $studentIds[] = $record['student_id'];
            }

            // Check for empty quarterly grade
            if (empty($record['quarterly_grade']) && empty($record['initial_grade'])) {
                $validationStatus = 'invalid';
                $validationMessages[] = 'Missing Grade';
            }

            // Add validation status to record
            $record['validation_status'] = $validationStatus;
            $record['validation_messages'] = $validationMessages;

            if ($validationStatus === 'valid') {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $validatedRecords[] = $record;
        }

        $summary = [
            'total_students' => $totalStudents,
            'valid_count' => $validCount,
            'invalid_count' => $invalidCount,
            'has_invalid_records' => $invalidCount > 0,
        ];

        return [
            'records' => $validatedRecords,
            'summary' => $summary,
        ];
    }

    public function saveGrades(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $importedStudentRecords = session('imported_student_records_' . $classSchedule->id);
        $validationSummary = session('imported_validation_summary_' . $classSchedule->id);

        if (empty($importedStudentRecords)) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'No imported data found. Please upload a grading sheet first.');
        }

        try {
            DB::beginTransaction();

            $savedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;
            $warnings = [];

            foreach ($importedStudentRecords as $record) {
                // Skip invalid records
                if (($record['validation_status'] ?? 'valid') !== 'valid') {
                    $skippedCount++;
                    continue;
                }

                $studentId = $record['student_id'] ?? null;
                
                // Skip if student not found in database
                if (!$studentId) {
                    $skippedCount++;
                    continue;
                }

                // Check if grade already exists
                $existingGrade = FinalGrade::where('student_id', $studentId)
                    ->where('class_schedule_id', $classSchedule->id)
                    ->first();

                // If record is submitted or approved, skip it
                if ($existingGrade && in_array($existingGrade->status, ['submitted', 'approved'])) {
                    $skippedCount++;
                    $warnings[] = "Student {$record['student_name']} ({$record['student_number']}) grades already submitted/approved and cannot be modified.";
                    continue;
                }

                // Prepare grade data
                $gradeData = [
                    'student_id' => $studentId,
                    'class_schedule_id' => $classSchedule->id,
                    'student_name' => $record['student_name'] ?? '',
                    'written_works' => null,
                    'performance_tasks' => null,
                    'quarterly_assessment' => null,
                    'initial_grade' => $record['initial_grade'] ?? null,
                    'quarterly_grade' => $record['quarterly_grade'] ?? null,
                    'final_grade' => $record['quarterly_grade'] ?? null, // Use quarterly as final for now
                    'remarks' => $record['remarks'] ?? '',
                    'imported_data' => $record,
                    'status' => 'draft',
                    'computed_at' => now(),
                ];

                if ($existingGrade) {
                    // Update existing draft record
                    $existingGrade->update($gradeData);
                    $existingGrade->recordHistory('updated', 'draft', "Updated draft grade for student {$record['student_name']}");
                    $updatedCount++;
                } else {
                    // Create new record
                    $newGrade = FinalGrade::create($gradeData);
                    $newGrade->recordHistory('imported', 'draft', "Imported grade for student {$record['student_name']}");
                    $savedCount++;
                }
            }

            DB::commit();

            // Clear session data
            session()->forget('imported_class_info_' . $classSchedule->id);
            session()->forget('imported_student_records_' . $classSchedule->id);
            session()->forget('imported_validation_summary_' . $classSchedule->id);

            // Store import summary for confirmation page
            session([
                'import_save_summary_' . $classSchedule->id => [
                    'total_students' => $validationSummary['total_students'],
                    'saved_count' => $savedCount,
                    'updated_count' => $updatedCount,
                    'skipped_count' => $skippedCount,
                    'error_count' => $errorCount,
                    'warnings' => $warnings,
                ],
            ]);

            return redirect()->route('teacher.classes.grades-save-confirmation', $classSchedule);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'Error saving grades: ' . $e->getMessage());
        }
    }

    public function importSummaryPreview(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $previewData = session('import_preview_' . $classSchedule->id);

        if (empty($previewData)) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'No import data found. Please upload an Excel file first.');
        }

        $allLearners = $previewData['all_learners'];
        $matchedLearners = $previewData['matched_learners'];
        $gradingPeriod = $previewData['grading_period'];
        $termColumn = $previewData['term_column'];

        // Combine learner data with match status for display
        $displayData = [];
        foreach ($allLearners as $index => $learner) {
            $matched = $matchedLearners[$index];
            $displayData[] = [
                'student_name' => $learner['learners_name'],
                'matched_student' => $matched['matched_student'],
                'quarter_grade' => $learner[$termColumn] ?? null,
                'remarks' => $learner['remarks'] ?? $learner['descriptor'] ?? '',
                'match_status' => $matched['match_status'],
                'ambiguous_matches' => $matched['ambiguous_matches'] ?? null,
            ];
        }

        $totalParsed = count($allLearners);
        $matchedCount = count(array_filter($matchedLearners, fn($m) => $m['match_status'] === 'matched'));
        $unmatchedCount = count(array_filter($matchedLearners, fn($m) => $m['match_status'] === 'unmatched'));
        $ambiguousCount = count(array_filter($matchedLearners, fn($m) => $m['match_status'] === 'ambiguous'));

        return view('teacher.classes.import-summary-preview', compact(
            'classSchedule',
            'displayData',
            'totalParsed',
            'matchedCount',
            'unmatchedCount',
            'ambiguousCount',
            'gradingPeriod'
        ));
    }

    public function confirmImportSummary(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $previewData = session('import_preview_' . $classSchedule->id);

        if (empty($previewData)) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'No import data found. Please upload an Excel file first.');
        }

        $allLearners = $previewData['all_learners'];
        $matchedLearners = $previewData['matched_learners'];
        $gradingPeriod = $previewData['grading_period'];
        $termColumn = $previewData['term_column'];

        // Check if this grading period is already approved
        $termField = 'term_' . $gradingPeriod;
        $alreadyApproved = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('status', 'approved')
            ->whereNotNull($termField)
            ->exists();

        if ($alreadyApproved) {
            // Clear session data
            session()->forget('import_preview_' . $classSchedule->id);
            
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', "Term {$gradingPeriod} grades for this class have already been approved and cannot be modified.");
        }

        // Save grades for matched students
        $saveResult = $this->saveGradesForPeriod($matchedLearners, $allLearners, $classSchedule, $termColumn, $gradingPeriod);

        // Clear session data
        session()->forget('import_preview_' . $classSchedule->id);

        if ($saveResult['success']) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('success', $saveResult['message']);
        } else {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', $saveResult['message']);
        }
    }

    public function cancelImportSummary(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Clear session data
        session()->forget('import_preview_' . $classSchedule->id);

        return redirect()
            ->route('teacher.classes.grades', $classSchedule)
            ->with('info', 'Import cancelled.');
    }

    public function saveConfirmation(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $saveSummary = session('import_save_summary_' . $classSchedule->id);

        return view('teacher.classes.save-confirmation', compact('classSchedule', 'saveSummary'));
    }

    public function submitGrades(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Get all draft grades for this class
            $draftGrades = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'draft')
                ->get();

            if ($draftGrades->isEmpty()) {
                return redirect()
                    ->route('teacher.classes.grades', $classSchedule)
                    ->with('error', 'No draft grades found to submit.');
            }

            // Determine which grading period is being submitted
            $firstGrade = $draftGrades->first();
            $gradingPeriod = null;
            if ($firstGrade->term_1 && !$firstGrade->term_2 && !$firstGrade->term_3) {
                $gradingPeriod = 1;
            } elseif ($firstGrade->term_2 && !$firstGrade->term_3) {
                $gradingPeriod = 2;
            } elseif ($firstGrade->term_3) {
                $gradingPeriod = 3;
            }

            if (!$gradingPeriod) {
                return redirect()
                    ->route('teacher.classes.grades', $classSchedule)
                    ->with('error', 'Unable to determine which grading period is being submitted.');
            }

            // Check if this specific grading period is already submitted
            $termField = 'term_' . $gradingPeriod;
            $alreadySubmitted = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'submitted')
                ->whereNotNull($termField)
                ->exists();
            
            if ($alreadySubmitted) {
                return redirect()
                    ->route('teacher.classes.grades', $classSchedule)
                    ->with('error', "Term {$gradingPeriod} grades for this class have already been submitted and are pending review.");
            }

            // Update all draft grades to submitted status
            $submittedCount = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'draft')
                ->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

            // Record history for each grade
            $draftGrades->each(function ($grade) use ($gradingPeriod) {
                $grade->recordHistory('submitted', 'submitted', "Term {$gradingPeriod} grades submitted for Registrar approval");
            });

            DB::commit();

            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('success', "Grades successfully submitted to the Registrar. {$submittedCount} student grades are now waiting for approval.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'Error submitting grades: ' . $e->getMessage());
        }
    }
}
