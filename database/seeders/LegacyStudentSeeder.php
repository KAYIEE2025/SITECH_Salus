<?php

namespace Database\Seeders;

use App\Models\LegacyStudent;
use App\Utilities\NameParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LegacyStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder reads the private Excel file "SALUS STUDENTS.xlsx" from the project root
     * and populates the legacy_students table with master identity data for old students.
     * 
     * The Excel file is NOT committed to Git and must be provided separately.
     */
    public function run(): void
    {
        $this->command->info('LegacyStudentSeeder - Starting...');
        
        $excelFile = base_path('SALUS STUDENTS.xlsx');
        
        // Check if Excel file exists
        if (!file_exists($excelFile)) {
            $this->command->error("Excel file not found: {$excelFile}");
            $this->command->error('Please place "SALUS STUDENTS.xlsx" in the project root directory.');
            return;
        }

        $this->command->info("Loading Excel file: {$excelFile}");

        try {
            $spreadsheet = IOFactory::load($excelFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header row
            $header = array_shift($rows);
            
            $totalSourceRecords = count($rows);
            $createdCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $parseWarningCount = 0;
            $errorCount = 0;
            $errors = [];
            $parseWarnings = [];
            
            // Track student numbers for duplicate detection
            $studentNumbers = [];
            $duplicates = [];

            $this->command->info("Processing {$totalSourceRecords} records...");

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // Excel row number (1-indexed + header)
                
                // Skip empty rows
                if (empty($row[0]) && empty($row[1])) {
                    $skippedCount++;
                    continue;
                }

                $studentNumber = trim($row[0] ?? '');
                $fullName = trim($row[1] ?? '');

                // Validate required fields
                if (empty($studentNumber)) {
                    $errorCount++;
                    $errors[] = "Row {$rowNumber}: Missing student number";
                    continue;
                }

                if (empty($fullName)) {
                    $errorCount++;
                    $errors[] = "Row {$rowNumber}: Missing full name for student number {$studentNumber}";
                    continue;
                }

                // Validate student number format
                if (!preg_match('/^\d{8}-\d{6}$/', $studentNumber)) {
                    $errorCount++;
                    $errors[] = "Row {$rowNumber}: Invalid student number format: {$studentNumber}";
                    continue;
                }

                // Check for duplicates in source file
                if (isset($studentNumbers[$studentNumber])) {
                    $duplicates[] = $studentNumber;
                    $errorCount++;
                    $errors[] = "Row {$rowNumber}: Duplicate student number: {$studentNumber}";
                    continue;
                }
                $studentNumbers[$studentNumber] = true;

                // Parse name
                $parsedName = NameParser::parse($fullName);

                // Prepare data for upsert
                $studentData = [
                    'student_number' => $studentNumber,
                    'full_name' => $parsedName['full_name'],
                    'first_name' => $parsedName['first_name'],
                    'middle_name' => $parsedName['middle_name'],
                    'last_name' => $parsedName['last_name'],
                    'middle_initial' => $parsedName['middle_initial'],
                    'is_parsed' => $parsedName['is_parsed'],
                    'parse_notes' => $parsedName['parse_notes'],
                ];

                // Use updateOrCreate for idempotency
                $legacyStudent = LegacyStudent::updateOrCreate(
                    ['student_number' => $studentNumber],
                    $studentData
                );

                if ($legacyStudent->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }

                // Track parse warnings
                if (!$parsedName['is_parsed']) {
                    $parseWarningCount++;
                    $parseWarnings[] = "Student {$studentNumber}: {$parsedName['parse_notes']}";
                }

                // Progress indicator
                if (($index + 1) % 100 === 0) {
                    $this->command->info("Processed " . ($index + 1) . " records...");
                }
            }

            // Display summary
            $this->command->newLine();
            $this->command->info('=== LegacyStudentSeeder Summary ===');
            $this->command->info("Source records: {$totalSourceRecords}");
            $this->command->info("Created: {$createdCount}");
            $this->command->info("Updated: {$updatedCount}");
            $this->command->info("Skipped: {$skippedCount}");
            $this->command->info("Parse warnings: {$parseWarningCount}");
            $this->command->info("Errors: {$errorCount}");
            
            if (!empty($duplicates)) {
                $this->command->warn("Duplicate student numbers found: " . count(array_unique($duplicates)));
                foreach (array_unique($duplicates) as $dup) {
                    $this->command->warn("  - {$dup}");
                }
            }

            if (!empty($parseWarnings)) {
                $this->command->warn('Parse warnings (first 10):');
                foreach (array_slice($parseWarnings, 0, 10) as $warning) {
                    $this->command->warn("  - {$warning}");
                }
                if (count($parseWarnings) > 10) {
                    $this->command->warn("  ... and " . (count($parseWarnings) - 10) . " more");
                }
            }

            if (!empty($errors)) {
                $this->command->error('Errors:');
                foreach (array_slice($errors, 0, 10) as $error) {
                    $this->command->error("  - {$error}");
                }
                if (count($errors) > 10) {
                    $this->command->error("  ... and " . (count($errors) - 10) . " more");
                }
            }

            // Verify final count
            $finalCount = LegacyStudent::count();
            $this->command->info("Total legacy students in database: {$finalCount}");

            if ($errorCount === 0 && $parseWarningCount === 0) {
                $this->command->info('✓ All records processed successfully!');
            } else {
                $this->command->warn('Completed with warnings and/or errors. Please review above.');
            }

        } catch (\Exception $e) {
            $this->command->error("Error processing Excel file: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
