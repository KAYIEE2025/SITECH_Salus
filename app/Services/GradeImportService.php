<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\GradeImport;
use App\Models\GradingComponent;
use App\Models\ScoreItem;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\StudyLoad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GradeImportService
{
    public function importGrades($file, ClassSchedule $classSchedule, $teacherId, $replaceExisting = false)
    {
        try {
            DB::beginTransaction();

            // Store the file
            $filePath = $file->store('grade_imports', 'public');
            $originalFilename = $file->getClientOriginalName();

            // Create grade import record
            $gradeImport = GradeImport::create([
                'class_schedule_id' => $classSchedule->id,
                'teacher_id' => $teacherId,
                'file_path' => $filePath,
                'original_filename' => $originalFilename,
                'status' => 'processing',
                'imported_at' => now(),
            ]);

            // Read the Excel file
            $data = Excel::toArray([], $file);
            $rows = $data[0];

            // Validate basic structure
            if (count($rows) < 2) {
                throw new \Exception('Excel file is empty or invalid.');
            }

            // Parse header row to identify grading components
            $headerRow = $rows[0];
            $components = $this->parseHeaderRow($headerRow);

            if (empty($components)) {
                throw new \Exception('No valid grading components found in header row.');
            }

            // If replace existing, delete old data
            if ($replaceExisting) {
                $this->deleteExistingGrades($classSchedule->id, $teacherId);
            }

            // Create grading components and score items
            $componentMap = [];
            $itemMap = [];

            foreach ($components as $index => $component) {
                $gradingComponent = GradingComponent::create([
                    'class_schedule_id' => $classSchedule->id,
                    'grade_import_id' => $gradeImport->id,
                    'name' => $component['name'],
                    'weight' => $component['weight'],
                    'order' => $index + 1,
                ]);

                $componentMap[$component['name']] = $gradingComponent->id;

                foreach ($component['items'] as $itemIndex => $item) {
                    $scoreItem = ScoreItem::create([
                        'grading_component_id' => $gradingComponent->id,
                        'name' => $item['name'],
                        'max_score' => $item['max_score'],
                        'order' => $itemIndex + 1,
                    ]);

                    $itemMap[$item['name']] = $scoreItem->id;
                }
            }

            // Get students enrolled in this class
            $enrolledStudents = StudyLoad::where('class_schedule_id', $classSchedule->id)
                ->with('student')
                ->get()
                ->keyBy('student.student_number');

            // Process student rows
            $studentCount = 0;
            $missingStudents = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                if (empty($row[0])) {
                    continue; // Skip empty rows
                }

                $studentNumber = trim($row[0]);

                if (!$enrolledStudents->has($studentNumber)) {
                    $missingStudents[] = $studentNumber;
                    continue;
                }

                $student = $enrolledStudents[$studentNumber]->student;
                $studentCount++;

                // Process scores for each item
                $colIndex = 1;
                foreach ($components as $component) {
                    foreach ($component['items'] as $item) {
                        if (isset($row[$colIndex]) && $row[$colIndex] !== '') {
                            $score = floatval($row[$colIndex]);

                            StudentScore::updateOrCreate(
                                [
                                    'student_id' => $student->id,
                                    'score_item_id' => $itemMap[$item['name']],
                                ],
                                [
                                    'score' => $score,
                                ]
                            );
                        }
                        $colIndex++;
                    }
                }
            }

            // Update grade import status
            $gradeImport->update([
                'status' => 'done',
            ]);

            DB::commit();

            $message = "Imported grades for {$studentCount} students.";
            if (!empty($missingStudents)) {
                $message .= " Warning: " . count($missingStudents) . " students not found in class: " . implode(', ', array_slice($missingStudents, 0, 5));
            }

            return [
                'success' => true,
                'message' => $message,
                'student_count' => $studentCount,
                'missing_students' => $missingStudents,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($gradeImport)) {
                $gradeImport->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function parseHeaderRow($headerRow)
    {
        $components = [];
        $currentComponent = null;
        $currentItems = [];

        // Expected format: Component Name (Weight%) or Item Name (Max Score)
        // Example: "Written Works (30%)" or "WW1 (20)"

        foreach ($headerRow as $cell) {
            if (empty($cell)) {
                continue;
            }

            $cell = trim($cell);

            // Check if it's a component (contains %)
            if (preg_match('/^(.+)\s*\((\d+(?:\.\d+)?)%\)$/', $cell, $matches)) {
                // Save previous component if exists
                if ($currentComponent !== null) {
                    $components[] = [
                        'name' => $currentComponent,
                        'weight' => $currentWeight,
                        'items' => $currentItems,
                    ];
                }

                $currentComponent = trim($matches[1]);
                $currentWeight = floatval($matches[2]);
                $currentItems = [];
            }
            // Check if it's a score item (contains number without %)
            elseif (preg_match('/^(.+)\s*\((\d+(?:\.\d+)?)\)$/', $cell, $matches)) {
                if ($currentComponent !== null) {
                    $currentItems[] = [
                        'name' => trim($matches[1]),
                        'max_score' => floatval($matches[2]),
                    ];
                }
            }
            // Otherwise, treat as a simple item name
            elseif ($currentComponent !== null) {
                $currentItems[] = [
                    'name' => $cell,
                    'max_score' => 100, // Default max score
                ];
            }
        }

        // Don't forget the last component
        if ($currentComponent !== null) {
            $components[] = [
                'name' => $currentComponent,
                'weight' => $currentWeight,
                'items' => $currentItems,
            ];
        }

        return $components;
    }

    private function deleteExistingGrades($classScheduleId, $teacherId)
    {
        // Delete student scores
        $gradeImportIds = GradeImport::where('class_schedule_id', $classScheduleId)
            ->where('teacher_id', $teacherId)
            ->pluck('id');

        if ($gradeImportIds->isNotEmpty()) {
            $componentIds = GradingComponent::whereIn('grade_import_id', $gradeImportIds)
                ->pluck('id');

            if ($componentIds->isNotEmpty()) {
                $itemIds = ScoreItem::whereIn('grading_component_id', $componentIds)
                    ->pluck('id');

                if ($itemIds->isNotEmpty()) {
                    StudentScore::whereIn('score_item_id', $itemIds)->delete();
                }

                ScoreItem::whereIn('grading_component_id', $componentIds)->delete();
            }

            GradingComponent::whereIn('grade_import_id', $gradeImportIds)->delete();
        }

        GradeImport::where('class_schedule_id', $classScheduleId)
            ->where('teacher_id', $teacherId)
            ->delete();
    }
}
