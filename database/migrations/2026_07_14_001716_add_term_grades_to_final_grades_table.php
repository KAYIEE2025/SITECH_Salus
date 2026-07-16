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
            $table->decimal('term_1', 5, 2)->nullable()->after('quarterly_grade');
            $table->decimal('term_2', 5, 2)->nullable()->after('term_1');
            $table->decimal('term_3', 5, 2)->nullable()->after('term_2');
            $table->decimal('final_rating', 5, 2)->nullable()->after('term_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_grades', function (Blueprint $table) {
            $table->dropColumn(['term_1', 'term_2', 'term_3', 'final_rating']);
        });
    }
};
