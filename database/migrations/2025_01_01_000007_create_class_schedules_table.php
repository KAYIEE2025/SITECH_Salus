<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // A class schedule = one subject taught by one teacher
        // to one section at specific days/time/room.
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('room', 50);                    // Room 301, Lab 2 …
            $table->json('days');                          // ["Mon","Wed","Fri"]
            $table->time('time_start');
            $table->time('time_end');
            $table->enum('semester', ['1st', '2nd', 'Summer']);
            $table->string('school_year', 20);             // 2025-2026
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['teacher_id', 'school_year', 'semester']);
            $table->index(['section_id', 'school_year', 'semester']);
        });
    }

    public function down(): void { Schema::dropIfExists('class_schedules'); }
};
