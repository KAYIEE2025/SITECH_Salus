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
        // Drop the unique constraint (this will fail due to FK dependency, so we handle it differently)
        // Instead, we'll use raw SQL to drop and recreate
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE final_grades DROP INDEX final_grades_student_id_class_schedule_id_unique');
        DB::statement('ALTER TABLE final_grades ADD UNIQUE KEY final_grades_student_class_grading_unique (student_id, class_schedule_id, grading_period)');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE final_grades DROP INDEX final_grades_student_class_grading_unique');
        DB::statement('ALTER TABLE final_grades ADD UNIQUE KEY final_grades_student_id_class_schedule_id_unique (student_id, class_schedule_id)');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
