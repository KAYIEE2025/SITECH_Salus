<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only proceed if student_enrollments table exists and students table has enrollment data
        if (!Schema::hasTable('student_enrollments') || !Schema::hasTable('students')) {
            return;
        }

        // Check if there are any students with enrollment data to migrate
        $studentsWithEnrollmentData = DB::table('students')
            ->whereNotNull('year_level_id')
            ->whereNotNull('section_id')
            ->whereNotNull('school_year')
            ->whereNotNull('semester')
            ->count();

        if ($studentsWithEnrollmentData === 0) {
            return; // No data to migrate
        }

        // Get all students with enrollment data
        $students = DB::table('students')
            ->select([
                'id',
                'year_level_id',
                'section_id',
                'school_year',
                'semester',
                'status',
                'encoded_by',
                'encoded_at',
                'created_at'
            ])
            ->whereNotNull('year_level_id')
            ->whereNotNull('section_id')
            ->whereNotNull('school_year')
            ->whereNotNull('semester')
            ->orderBy('id')
            ->get();

        $createdCount = 0;
        $duplicateCount = 0;
        $errorCount = 0;

        foreach ($students as $student) {
            try {
                // Check for potential duplicate enrollment
                $existingEnrollment = DB::table('student_enrollments')
                    ->where('student_id', $student->id)
                    ->where('school_year', $student->school_year)
                    ->where('semester', $student->semester)
                    ->first();

                if ($existingEnrollment) {
                    // Skip duplicates - log the conflict
                    $duplicateCount++;
                    continue;
                }

                // Insert enrollment record
                $createdTimestamp = $student->encoded_at ?? $student->created_at ?? now();
                
                DB::table('student_enrollments')->insert([
                    'student_id' => $student->id,
                    'year_level_id' => $student->year_level_id,
                    'section_id' => $student->section_id,
                    'school_year' => $student->school_year,
                    'semester' => $student->semester,
                    'status' => $student->status ?? 'Pending Student Account',
                    'encoded_by' => $student->encoded_by,
                    'encoded_at' => $student->encoded_at,
                    'created_at' => $createdTimestamp,
                    'updated_at' => now(),
                ]);

                $createdCount++;
            } catch (\Exception $e) {
                $errorCount++;
                // Log error but continue with other records
                \Log::error('Failed to backfill enrollment for student', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log migration results for verification
        \Log::info('Student Enrollment Backfill Results', [
            'students_checked' => $students->count(),
            'enrollments_created' => $createdCount,
            'duplicates_skipped' => $duplicateCount,
            'errors' => $errorCount,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For data safety, we don't automatically delete backfilled data
        // If rollback is needed, manual intervention is required to identify
        // and remove only the specific records created by this migration
    }
};
