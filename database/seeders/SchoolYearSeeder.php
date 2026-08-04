<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolYears = [
            ['name' => '2024-2025', 'is_active' => false],
            ['name' => '2025-2026', 'is_active' => true],
            ['name' => '2026-2027', 'is_active' => false],
        ];

        foreach ($schoolYears as $schoolYear) {
            SchoolYear::create($schoolYear);
        }
    }
}
