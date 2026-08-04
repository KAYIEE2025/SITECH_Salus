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
        Schema::create('grade_submission_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('school_year');
            $table->integer('grading_period');
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->unique(['school_year', 'grading_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_submission_schedules');
    }
};
