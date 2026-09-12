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

        // Drop the existing unique constraint first
        $gradingPeriodIndex = DB::select(
            "SHOW INDEX FROM final_grades WHERE Key_name = 'final_grades_student_class_grading_unique'"
        );

        if (!empty($gradingPeriodIndex)) {
            DB::statement('ALTER TABLE final_grades DROP INDEX final_grades_student_class_grading_unique');
        }

        // Make student_id nullable to allow unmatched students (those without accounts)
        Schema::table('final_grades', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->change();
        });

        // Add a new unique constraint that works with nullable student_id
        // This allows unmatched students (student_id is null) to be unique by student_name instead
        DB::statement('ALTER TABLE final_grades ADD UNIQUE KEY final_grades_student_class_grading_unique (student_id, class_schedule_id, grading_period)');

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

        // Make student_id not nullable again
        Schema::table('final_grades', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable(false)->change();
        });

        // Restore the original unique constraint
        DB::statement('ALTER TABLE final_grades ADD UNIQUE KEY final_grades_student_class_grading_unique (student_id, class_schedule_id, grading_period)');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
