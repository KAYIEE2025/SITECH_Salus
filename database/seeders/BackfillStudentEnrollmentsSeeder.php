<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillStudentEnrollmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting student enrollment backfill...');

        $totalStudents = Student::count();
        $enrollmentsCreated = 0;
        $studentsWithoutEnrollmentData = 0;
        $duplicateConflicts = 0;
        $migrationErrors = 0;

        $this->command->info("Found {$totalStudents} students to process.");

        Student::chunk(100, function ($students) use (&$enrollmentsCreated, &$studentsWithoutEnrollmentData, &$duplicateConflicts, &$migrationErrors) {
            foreach ($students as $student) {
                try {
                    // Check if student has enrollment data to migrate
                    if (!$student->year_level_id || !$student->section_id || !$student->school_year) {
                        $studentsWithoutEnrollmentData++;
                        $this->command->warn("Student {$student->student_number} ({$student->full_name}) has no enrollment data to migrate.");
                        continue;
                    }

                    // Check for duplicate enrollment (student_id + school_year + semester)
                    $existingEnrollment = StudentEnrollment::where('student_id', $student->id)
                        ->where('school_year', $student->school_year)
                        ->where('semester', $student->semester)
                        ->first();

                    if ($existingEnrollment) {
                        $duplicateConflicts++;
                        $this->command->warn("Duplicate enrollment found for student {$student->student_number} in {$student->school_year} {$student->semester}. Skipping.");
                        continue;
                    }

                    // Create enrollment record
                    StudentEnrollment::create([
                        'student_id' => $student->id,
                        'year_level_id' => $student->year_level_id,
                        'section_id' => $student->section_id,
                        'school_year' => $student->school_year,
                        'semester' => $student->semester,
                        'status' => $student->status,
                        'encoded_by' => $student->encoded_by,
                        'encoded_at' => $student->encoded_at,
                    ]);

                    $enrollmentsCreated++;
                    $this->command->line("Created enrollment for student {$student->student_number} in {$student->school_year} {$student->semester}.");

                } catch (\Exception $e) {
                    $migrationErrors++;
                    $this->command->error("Failed to migrate enrollment for student {$student->student_number}: {$e->getMessage()}");
                }
            }
        });

        $this->command->newLine();
        $this->command->info('=== BACKFILL REPORT ===');
        $this->command->info("Students checked: {$totalStudents}");
        $this->command->info("Enrollments created: {$enrollmentsCreated}");
        $this->command->info("Students without enrollment data: {$studentsWithoutEnrollmentData}");
        $this->command->info("Duplicate enrollment conflicts: {$duplicateConflicts}");
        $this->command->info("Migration errors: {$migrationErrors}");
        $this->command->info('======================');
    }
}
