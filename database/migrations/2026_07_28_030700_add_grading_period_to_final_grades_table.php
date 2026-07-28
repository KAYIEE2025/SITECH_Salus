<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the grading period used by teacher submission and registrar approval.
     *
     * This is nullable for existing legacy grade records. New submissions
     * populate the field before they can be approved by the Registrar.
     */
    public function up(): void
    {
        Schema::table('final_grades', function (Blueprint $table) {
            $table->unsignedTinyInteger('grading_period')
                ->nullable()
                ->after('class_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::table('final_grades', function (Blueprint $table) {
            $table->dropColumn('grading_period');
        });
    }
};
