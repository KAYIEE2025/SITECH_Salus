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
        Schema::table('student_enrollments', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique('student_enrollments_unique');
            
            // Add new unique constraint using term instead of semester
            $table->unique(['student_id', 'school_year', 'term'], 'student_enrollments_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('student_enrollments_unique');
            
            // Restore the old unique constraint using semester
            $table->unique(['student_id', 'school_year', 'semester'], 'student_enrollments_unique');
        });
    }
};
