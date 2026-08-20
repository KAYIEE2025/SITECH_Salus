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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('year_level_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->string('school_year', 20);
            $table->enum('semester', ['1st', '2nd', 'Summer']);
            $table->enum('status', ['Pending Student Account', 'Account Created', 'Active', 'Inactive', 'Graduated', 'Dropped'])->default('Pending Student Account');
            $table->foreignId('encoded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('encoded_at')->nullable();
            $table->timestamps();

            // Ensure a student can only have one enrollment per school year and semester
            $table->unique(['student_id', 'school_year', 'semester'], 'student_enrollments_unique');

            // Indexes for common queries
            $table->index('student_id');
            $table->index('school_year');
            $table->index('year_level_id');
            $table->index('section_id');
            $table->index('status');
            $table->index(['school_year', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
