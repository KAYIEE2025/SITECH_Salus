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
        // This migration is meant to make existing term columns nullable for grading purposes
        // In a fresh database, term columns are already nullable (added as nullable in earlier migrations)
        // Only proceed if there are existing records that would be affected
        
        $classSchedulesCount = DB::table('class_schedules')->count();
        $studyLoadsCount = DB::table('study_loads')->count();
        
        // Skip migration for fresh/empty databases
        if ($classSchedulesCount === 0 && $studyLoadsCount === 0) {
            return;
        }
        
        // Make term nullable in class_schedules table
        Schema::table('class_schedules', function (Blueprint $table) {
            // Use raw SQL to safely drop index only if it exists
            DB::statement("ALTER TABLE class_schedules DROP INDEX IF EXISTS class_schedules_school_year_term_index");
            $table->dropColumn('term');
        });

        // Make term nullable in study_loads table
        Schema::table('study_loads', function (Blueprint $table) {
            // Drop the semester-based unique constraint if it exists
            DB::statement("ALTER TABLE study_loads DROP INDEX IF EXISTS study_loads_unique");
            $table->dropColumn('term');
        });

        // Add term back as nullable for grading purposes only
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->nullable()->after('school_year');
        });

        Schema::table('study_loads', function (Blueprint $table) {
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->nullable()->after('school_year');
            // Restore the term-based unique constraint
            $table->unique(['student_id', 'class_schedule_id', 'school_year', 'term'], 'study_loads_unique_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropColumn('term');
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->after('school_year');
        });

        Schema::table('study_loads', function (Blueprint $table) {
            // Drop the unique constraint without term
            DB::statement("ALTER TABLE study_loads DROP INDEX IF EXISTS study_loads_unique");
            $table->dropColumn('term');
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->after('school_year');
            // Restore the term-based unique constraint
            $table->unique(['student_id', 'class_schedule_id', 'school_year', 'term'], 'study_loads_unique_term');
        });
    }
};
