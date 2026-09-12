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
        // Update class_schedules indexes
        Schema::table('class_schedules', function (Blueprint $table) {
            // Add new term-based indexes first
            $table->index(['teacher_id', 'school_year', 'term']);
            $table->index(['section_id', 'school_year', 'term']);
        });

        // Drop old semester-based indexes using raw SQL
        DB::statement("ALTER TABLE class_schedules DROP INDEX IF EXISTS class_schedules_teacher_id_school_year_semester_index");
        DB::statement("ALTER TABLE class_schedules DROP INDEX IF EXISTS class_schedules_section_id_school_year_semester_index");

        // Update study_loads unique constraint
        Schema::table('study_loads', function (Blueprint $table) {
            // Add new term-based unique constraint
            $table->unique(['student_id', 'class_schedule_id', 'school_year', 'term'], 'study_loads_unique_term');
        });

        // Drop old semester-based unique constraint using raw SQL
        DB::statement("ALTER TABLE study_loads DROP INDEX IF EXISTS study_loads_unique");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert class_schedules indexes
        Schema::table('class_schedules', function (Blueprint $table) {
            // Restore old semester-based indexes
            $table->index(['teacher_id', 'school_year', 'semester']);
            $table->index(['section_id', 'school_year', 'semester']);
        });

        // Drop new term-based indexes using raw SQL
        DB::statement("ALTER TABLE class_schedules DROP INDEX IF EXISTS class_schedules_teacher_id_school_year_term_index");
        DB::statement("ALTER TABLE class_schedules DROP INDEX IF EXISTS class_schedules_section_id_school_year_term_index");

        // Revert study_loads unique constraint
        Schema::table('study_loads', function (Blueprint $table) {
            // Restore old semester-based unique constraint
            $table->unique(['student_id', 'class_schedule_id', 'school_year', 'semester'], 'study_loads_unique');
        });

        // Drop new term-based unique constraint using raw SQL
        DB::statement("ALTER TABLE study_loads DROP INDEX IF EXISTS study_loads_unique_term");
    }
};
