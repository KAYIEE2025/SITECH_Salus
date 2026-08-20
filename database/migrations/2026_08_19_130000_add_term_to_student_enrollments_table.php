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
            // Add term field for high school system
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->nullable()->after('school_year');
            
            // Add index for term queries
            $table->index(['school_year', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropIndex(['school_year', 'term']);
            $table->dropColumn('term');
        });
    }
};
