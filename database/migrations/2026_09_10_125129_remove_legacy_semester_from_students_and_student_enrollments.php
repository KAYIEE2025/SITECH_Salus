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
        // Remove semester column from students table if it exists
        if (Schema::hasColumn('students', 'semester')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }

        // Remove semester column from student_enrollments table if it exists
        if (Schema::hasColumn('student_enrollments', 'semester')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }

        // Drop any remaining semester-based index from student_enrollments if it exists
        // This was referenced in historical migrations but may still exist
        DB::statement("ALTER TABLE student_enrollments DROP INDEX IF EXISTS student_enrollments_school_year_semester_index");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore semester column to students table
        if (!Schema::hasColumn('students', 'semester')) {
            Schema::table('students', function (Blueprint $table) {
                $table->enum('semester', ['1st', '2nd', 'Summer'])->nullable()->after('school_year');
            });
        }

        // Restore semester column to student_enrollments table
        if (!Schema::hasColumn('student_enrollments', 'semester')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->enum('semester', ['1st', '2nd', 'Summer'])->nullable()->after('school_year');
            });
        }

        // Restore the semester-based index to student_enrollments
        // This index was removed in the up() migration
        // Use try-catch to handle if index already exists
        try {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->index(['school_year', 'semester'], 'student_enrollments_school_year_semester_index');
            });
        } catch (\Exception $e) {
            // Index may already exist, continue
        }
    }
};
