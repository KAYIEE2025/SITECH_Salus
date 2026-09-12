<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\SsgEvent;
use App\Models\SsgEventAttendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillSsgAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $events = SsgEvent::all();
        $totalRecordsCreated = 0;

        foreach ($students as $student) {
            foreach ($events as $event) {
                $attendance = SsgEventAttendance::firstOrCreate([
                    'ssg_event_id' => $event->id,
                    'student_id' => $student->id,
                ], [
                    'is_present' => false,
                    'scanned_at' => null,
                    'applicable_fine' => $event->fine_amount,
                    'actual_fine' => $event->fine_amount,
                ]);

                if ($attendance->wasRecentlyCreated) {
                    $totalRecordsCreated++;
                }
            }
        }

        $this->command->info("Successfully created {$totalRecordsCreated} SSG attendance records for {$students->count()} students and {$events->count()} events.");
    }
}
