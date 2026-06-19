<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Grade Imports ─────────────────────────────────────────────
        // Each row = one Excel file uploaded by a teacher for one subject/class.
        // The system reads the Excel structure (columns = grading components)
        // and populates grading_components, score_items, and student_scores
        // automatically from the file.
        Schema::create('grade_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path');                // stored Excel file path
            $table->string('original_filename');        // e.g. "BSIT4A_WebSystems.xlsx"
            $table->enum('status', [
                'processing',   // file is being parsed
                'done',         // successfully parsed, scores loaded
                'failed',       // parse error
            ])->default('processing');
            $table->text('error_message')->nullable();  // if status = failed
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->index(['class_schedule_id', 'teacher_id']);
        });

        // ── Grading Components ────────────────────────────────────────
        // Parsed from the Excel headers.
        // e.g. Written Works (30%), Performance Tasks (50%), Quarterly Exam (20%)
        // These are created automatically when the Excel is imported.
        Schema::create('grading_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_import_id')
                  ->nullable()                          // null = manually created (future use)
                  ->constrained()
                  ->nullOnDelete();
            $table->string('name', 100);                // Written Works, Performance Tasks …
            $table->decimal('weight', 5, 2);            // 30.00, 50.00, 20.00 (must total 100)
            $table->unsignedSmallInteger('order')->default(1);
            $table->timestamps();

            $table->index('class_schedule_id');
        });

        // ── Score Items ───────────────────────────────────────────────
        // Individual columns inside each component (read from Excel headers).
        // e.g. WW1 (max 20), WW2 (max 20), PT1 (max 50), QE (max 40)
        Schema::create('score_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_component_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name', 50);                 // WW1, WW2, PT1, QE …
            $table->unsignedSmallInteger('max_score');
            $table->unsignedSmallInteger('order')->default(1);
            $table->timestamps();

            $table->index('grading_component_id');
        });

        // ── Student Scores ────────────────────────────────────────────
        // Populated automatically from the imported Excel.
        // Teachers can also edit scores directly in the system after import.
        // NOTE: scores are ONLY visible to the teacher — never passed to Registrar.
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('score_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 6, 2)->default(0);
            $table->timestamps();

            // One score per student per item
            $table->unique(['student_id', 'score_item_id']);
        });

        // ── Final Grades ──────────────────────────────────────────────
        // Auto-computed by the system from student_scores.
        // Follows a submission → approval → visible workflow.
        //
        // VISIBILITY RULES:
        //  - Teacher sees: all grades + all scores at all times
        //  - Registrar sees: subject name + student name + final grade + remarks ONLY
        //                    (no score breakdown)
        //  - Student sees: subject name + final grade + remarks
        //                  BUT ONLY after status = 'approved'
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();

            // ── Computed grade ─────────────────────────────────────
            $table->decimal('final_grade', 5, 2)->nullable();  // e.g. 91.25
            $table->enum('remarks', ['Passed', 'Failed', 'Incomplete', 'Dropped'])
                  ->nullable();
            $table->timestamp('computed_at')->nullable();

            // ── Submission / Approval workflow ─────────────────────
            // draft      = teacher is still working on scores
            // submitted  = teacher submitted grades to Registrar
            // approved   = Registrar approved → student can now see grade
            // rejected   = Registrar sent back to teacher for correction
            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
            ])->default('draft');

            // When teacher submitted to Registrar
            $table->timestamp('submitted_at')->nullable();

            // Registrar who reviewed
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // When Registrar approved or rejected
            $table->timestamp('reviewed_at')->nullable();

            // Reason for rejection (shown to teacher so they know what to fix)
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            // One grade record per student per subject
            $table->unique(['student_id', 'class_schedule_id']);

            $table->index('status');    // fast filtering for Registrar's pending list
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_grades');
        Schema::dropIfExists('student_scores');
        Schema::dropIfExists('score_items');
        Schema::dropIfExists('grading_components');
        Schema::dropIfExists('grade_imports');
    }
};
