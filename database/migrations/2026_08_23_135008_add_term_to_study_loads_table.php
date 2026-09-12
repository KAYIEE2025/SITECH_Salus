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
        Schema::table('study_loads', function (Blueprint $table) {
            // Add term field for high school system
            $table->enum('term', ['Term 1', 'Term 2', 'Term 3'])->nullable()->after('school_year');

            // Add index for term queries
            $table->index(['school_year', 'term']);
        });

        // Migrate existing semester data to term format
        DB::statement("
            UPDATE study_loads
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
        Schema::table('study_loads', function (Blueprint $table) {
            $table->dropIndex(['school_year', 'term']);
            $table->dropColumn('term');
        });
    }
};
