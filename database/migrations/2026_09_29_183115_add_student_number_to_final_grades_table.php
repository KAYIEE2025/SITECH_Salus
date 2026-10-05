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
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop the existing unique constraint
        $existingIndex = DB::select(
            "SHOW INDEX FROM final_grades WHERE Key_name = 'final_grades_student_class_grading_unique'"
        );

        if (!empty($existingIndex)) {
            DB::statement('ALTER TABLE final_grades DROP INDEX final_grades_student_class_grading_unique');
        }

        // Add student_number column for Excel imports
        Schema::table('final_grades', function (Blueprint $table) {
            $table->string('student_number', 20)->nullable()->after('student_name');
        });

        // Add index on student_number for lookups
        Schema::table('final_grades', function (Blueprint $table) {
            $table->index('student_number');
        });

        // Create a new unique constraint that works with both student_id and student_number
        // This allows records with student_id (existing students) to be unique by student_id
        // and records without student_id (Excel-only students) to be unique by student_number
        DB::statement('ALTER TABLE final_grades ADD UNIQUE KEY final_grades_student_class_grading_unique (student_id, student_number, class_schedule_id, grading_period)');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop the unique constraint
        DB::statement('ALTER TABLE final_grades DROP INDEX final_grades_student_class_grading_unique');

        // Drop the student_number column and its index
        Schema::table('final_grades', function (Blueprint $table) {
            $table->dropIndex(['student_number']);
            $table->dropColumn('student_number');
        });

        // Restore the original unique constraint
        DB::statement('ALTER TABLE final_grades ADD UNIQUE KEY final_grades_student_class_grading_unique (student_id, class_schedule_id, grading_period)');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
