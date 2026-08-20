<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verify enrollment migration command
Artisan::command('enrollment:verify', function () {
    $this->info('=== ENROLLMENT MIGRATION VERIFICATION ===');
    $this->newLine();

    // Check if table exists
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('student_enrollments');
    $this->line('student_enrollments table exists: ' . ($tableExists ? 'YES' : 'NO'));

    if ($tableExists) {
        // Get column listing
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('student_enrollments');
        $columnNames = array();
        foreach ($columns as $column) {
            $columnNames[] = $column['name'];
        }
        $this->line('Columns: ' . implode(', ', $columnNames));
        
        // Check foreign keys
        $foreignKeys = \Illuminate\Support\Facades\DB::select(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.KEY_COLUMN_USAGE 
             WHERE TABLE_NAME = 'student_enrollments' 
             AND CONSTRAINT_SCHEMA = DATABASE() 
             AND CONSTRAINT_NAME != 'PRIMARY'"
        );
        $fkNames = array();
        foreach ($foreignKeys as $fk) {
            $fkNames[] = $fk->CONSTRAINT_NAME;
        }
        $this->line('Foreign keys: ' . implode(', ', $fkNames));
        
        // Count records
        $enrollmentCount = \Illuminate\Support\Facades\DB::table('student_enrollments')->count();
        $this->line('Enrollment records: ' . $enrollmentCount);
    }

    $this->newLine();

    // Check if students table still has enrollment columns
    $studentsColumns = \Illuminate\Support\Facades\Schema::getColumnListing('students');
    $studentColumnsList = array();
    foreach ($studentsColumns as $column) {
        $studentColumnsList[] = $column['name'];
    }
    $this->line('Students table columns count: ' . count($studentColumnsList));
    $this->line('Students table still has year_level_id: ' . (in_array('year_level_id', $studentColumnsList) ? 'YES' : 'NO'));
    $this->line('Students table still has section_id: ' . (in_array('section_id', $studentColumnsList) ? 'YES' : 'NO'));
    $this->line('Students table still has school_year: ' . (in_array('school_year', $studentColumnsList) ? 'YES' : 'NO'));
    $this->line('Students table still has semester: ' . (in_array('semester', $studentColumnsList) ? 'YES' : 'NO'));
    $this->line('Students table still has status: ' . (in_array('status', $studentColumnsList) ? 'YES' : 'NO'));
    $this->line('Students table still has encoded_by: ' . (in_array('encoded_by', $studentColumnsList) ? 'YES' : 'NO'));
    $this->line('Students table still has encoded_at: ' . (in_array('encoded_at', $studentColumnsList) ? 'YES' : 'NO'));

    $this->newLine();

    // Count students
    $studentCount = \Illuminate\Support\Facades\DB::table('students')->count();
    $this->line('Total students: ' . $studentCount);

    // Count students with enrollment data
    $studentsWithEnrollmentData = \Illuminate\Support\Facades\DB::table('students')
        ->whereNotNull('year_level_id')
        ->whereNotNull('section_id')
        ->whereNotNull('school_year')
        ->whereNotNull('semester')
        ->count();
    $this->line('Students with enrollment data: ' . $studentsWithEnrollmentData);

    $this->newLine();
    $this->info('=== VERIFICATION COMPLETE ===');
})->purpose('Verify the enrollment migration and backfill results');
