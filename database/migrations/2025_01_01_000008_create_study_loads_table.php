<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Enrolls a student in a specific class schedule (subject + teacher + section).
        Schema::create('study_loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();
            $table->enum('semester', ['1st', '2nd', 'Summer']);
            $table->string('school_year', 20);
            $table->timestamps();

            // A student can only be enrolled once per class per semester
           $table->unique(['student_id', 'class_schedule_id', 'school_year', 'semester'], 'study_loads_unique');
        });
    }

    public function down(): void { Schema::dropIfExists('study_loads'); }
};