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
        // SAFETY CHECK: Ensure no records have semester data that hasn't been migrated to term
        // This prevents data loss in existing databases while allowing fresh installations
        $classSchedulesWithUnmigratedSemester = DB::table('class_schedules')
            ->whereNotNull('semester')
            ->whereNull('term')
            ->count();

        $studyLoadsWithUnmigratedSemester = DB::table('study_loads')
            ->whereNotNull('semester')
            ->whereNull('term')
            ->count();

        if ($classSchedulesWithUnmigratedSemester > 0 || $studyLoadsWithUnmigratedSemester > 0) {
            throw new \Exception('Migration safety check failed: Found records with semester data that has not been migrated to term. Run the term migration first.');
        }

        // Remove old semester-based index from student_enrollments if it exists
        // Use raw SQL to safely drop index only if it exists
        DB::statement("ALTER TABLE student_enrollments DROP INDEX IF EXISTS student_enrollments_school_year_semester_index");

        // Remove semester column from class_schedules if it exists
        if (Schema::hasColumn('class_schedules', 'semester')) {
            Schema::table('class_schedules', function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }

        // Remove semester column from study_loads if it exists
        if (Schema::hasColumn('study_loads', 'semester')) {
            Schema::table('study_loads', function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore semester column to class_schedules
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->enum('semester', ['1st', '2nd', 'Summer'])->nullable()->after('school_year');
        });

        // Restore semester column to study_loads
        Schema::table('study_loads', function (Blueprint $table) {
            $table->enum('semester', ['1st', '2nd', 'Summer'])->nullable()->after('school_year');
        });

        // Restore old semester-based index to student_enrollments
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->index(['school_year', 'semester'], 'student_enrollments_school_year_semester_index');
        });

        // NOTE: This down() migration restores the semester columns but does NOT restore
        // the historical semester data that was migrated to term. The term columns contain
        // the current academic period data. To fully restore semester functionality, you would
        // need to run the term-to-semester migration and manually map the term values back to
        // semester values. This is a one-way migration from semester to term.
    }
};
