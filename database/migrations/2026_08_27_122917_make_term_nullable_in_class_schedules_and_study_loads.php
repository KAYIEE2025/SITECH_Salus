<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make term nullable in class_schedules table
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropIndex('class_schedules_school_year_term_index');
            $table->dropColumn('term');
        });

        // Make term nullable in study_loads table
        Schema::table('study_loads', function (Blueprint $table) {
            $table->dropUnique('study_loads_unique');
            $table->dropColumn('term');
        });

        // Add term back as nullable for grading purposes only
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->nullable()->after('school_year');
        });

        Schema::table('study_loads', function (Blueprint $table) {
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->nullable()->after('school_year');
            $table->unique(['student_id', 'class_schedule_id', 'school_year'], 'study_loads_unique');
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
            $table->dropUnique('study_loads_unique');
            $table->dropColumn('term');
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->after('school_year');
            $table->unique(['student_id', 'class_schedule_id', 'school_year', 'term'], 'study_loads_unique');
        });
    }
};
