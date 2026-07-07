<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_number', 20)->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->text('address')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_contact', 20)->nullable();
            $table->string('guardian_relationship', 50)->nullable();
            $table->foreignId('year_level_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->string('school_year', 20);
            $table->enum('semester', ['1st', '2nd', 'Summer']);
            $table->enum('status', ['Pending Student Account', 'Account Created', 'Active', 'Inactive', 'Graduated', 'Dropped'])->default('Pending Student Account');
            $table->string('photo_path')->nullable();
            $table->string('qr_code_value')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->foreignId('encoded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('encoded_at')->nullable();
            $table->timestamps();

            $table->index(['year_level_id', 'section_id']);
            $table->index('student_number');
        });
    }

    public function down(): void { Schema::dropIfExists('students'); }
};
