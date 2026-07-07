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
            $table->decimal('initial_grade', 5, 2)->nullable()->after('final_grade');
            $table->decimal('transmuted_grade', 5, 2)->nullable()->after('initial_grade');
            $table->decimal('quarterly_grade', 5, 2)->nullable()->after('transmuted_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_grades', function (Blueprint $table) {
            $table->dropColumn(['initial_grade', 'transmuted_grade', 'quarterly_grade']);
        });
    }
};
