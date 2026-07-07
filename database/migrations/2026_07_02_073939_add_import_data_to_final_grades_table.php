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
        Schema::table('final_grades', function (Blueprint $table) {
            // Add columns for Excel import data (only those that don't exist)
            if (!Schema::hasColumn('final_grades', 'student_name')) {
                $table->string('student_name')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('final_grades', 'lrn')) {
                $table->string('lrn')->nullable()->after('student_name');
            }
            if (!Schema::hasColumn('final_grades', 'imported_data')) {
                $table->json('imported_data')->nullable()->after('remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_grades', function (Blueprint $table) {
            $table->dropColumn([
                'student_name',
                'lrn',
                'imported_data'
            ]);
        });
    }
};
