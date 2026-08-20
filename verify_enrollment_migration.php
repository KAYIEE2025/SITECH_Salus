<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

echo "=== ENROLLMENT MIGRATION VERIFICATION ===" . PHP_EOL . PHP_EOL;

// Check if table exists
$tableExists = \Illuminate\Support\Facades\Schema::hasTable('student_enrollments');
echo "student_enrollments table exists: " . ($tableExists ? "YES" : "NO") . PHP_EOL;

if ($tableExists) {
    // Get column listing
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('student_enrollments');
    $columnNames = array();
    foreach ($columns as $column) {
        $columnNames[] = $column['name'];
    }
    echo "Columns: " . implode(', ', $columnNames) . PHP_EOL;
    
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
    echo "Foreign keys: " . implode(', ', $fkNames) . PHP_EOL;
    
    // Count records
    $enrollmentCount = \Illuminate\Support\Facades\DB::table('student_enrollments')->count();
    echo "Enrollment records: " . $enrollmentCount . PHP_EOL;
}

echo PHP_EOL;

// Check if students table still has enrollment columns
$studentsColumns = \Illuminate\Support\Facades\Schema::getColumnListing('students');
$studentColumnsList = array();
foreach ($studentsColumns as $column) {
    $studentColumnsList[] = $column['name'];
}
echo "Students table columns count: " . count($studentColumnsList) . PHP_EOL;
echo "Students table still has year_level_id: " . (in_array('year_level_id', $studentColumnsList) ? "YES" : "NO") . PHP_EOL;
echo "Students table still has section_id: " . (in_array('section_id', $studentColumnsList) ? "YES" : "NO") . PHP_EOL;
echo "Students table still has school_year: " . (in_array('school_year', $studentColumnsList) ? "YES" : "NO") . PHP_EOL;
echo "Students table still has semester: " . (in_array('semester', $studentColumnsList) ? "YES" : "NO") . PHP_EOL;
echo "Students table still has status: " . (in_array('status', $studentColumnsList) ? "YES" : "NO") . PHP_EOL;
echo "Students table still has encoded_by: " . (in_array('encoded_by', $studentColumnsList) ? "YES" : "NO") . PHP_EOL;
echo "Students table still has encoded_at: " . (in_array('encoded_at', $studentColumnsList) ? "YES" : "NO") . PHP_EOL;

echo PHP_EOL;

// Count students
$studentCount = \Illuminate\Support\Facades\DB::table('students')->count();
echo "Total students: " . $studentCount . PHP_EOL;

// Count students with enrollment data
$studentsWithEnrollmentData = \Illuminate\Support\Facades\DB::table('students')
    ->whereNotNull('year_level_id')
    ->whereNotNull('section_id')
    ->whereNotNull('school_year')
    ->whereNotNull('semester')
    ->count();
echo "Students with enrollment data: " . $studentsWithEnrollmentData . PHP_EOL;

echo PHP_EOL . "=== VERIFICATION COMPLETE ===" . PHP_EOL;
