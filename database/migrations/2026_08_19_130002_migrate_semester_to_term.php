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
        // Migrate existing semester data to term format for high school system
        // This preserves existing data while transitioning to the new term system
        
        // Map old semester values to new term values
        // For high school: 1st -> Term 1, 2nd -> Term 2, Summer -> Term 3 (or keep as is if Summer still needed)
        
        DB::statement("
            UPDATE student_enrollments 
            SET term = CASE 
                WHEN semester = '1st' THEN 'Term 1'
                WHEN semester = '2nd' THEN 'Term 2'
                WHEN semester = 'Summer' THEN 'Term 3'
                ELSE NULL
            END
            WHERE term IS NULL AND semester IS NOT NULL
        ");
        
        DB::statement("
            UPDATE students 
            SET term = CASE 
                WHEN semester = '1st' THEN 'Term 1'
                WHEN semester = '2nd' THEN 'Term 2'
                WHEN semester = 'Summer' THEN 'Term 3'
                ELSE NULL
            END
            WHERE term IS NULL AND semester IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the migration by clearing term values
        // The semester field is kept for backward compatibility
        DB::statement("UPDATE student_enrollments SET term = NULL");
        DB::statement("UPDATE students SET term = NULL");
    }
};
