<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── SSG Events ────────────────────────────────────────────────
        Schema::create('ssg_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('venue', 100)->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->enum('status', ['Upcoming', 'Ongoing', 'Done'])->default('Upcoming');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('event_date');
            $table->index('status');
        });

        // ── SSG Event Attendance ──────────────────────────────────────
        // One row per student per event.
        // Created for ALL students when event is opened for scanning.
        // is_present flips to true when SSG scans the student's QR.
        Schema::create('ssg_event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ssg_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_present')->default(false);
            $table->timestamp('scanned_at')->nullable();

            // Snapshot of fine at event creation time
            $table->decimal('applicable_fine', 8, 2)->default(0.00);

            // Actual fine = 0 if present, applicable_fine if absent
            // Computed automatically: 0 when scanned, applicable_fine otherwise
            // Stored for fast portal queries
            $table->decimal('actual_fine', 8, 2)->default(0.00);

            $table->timestamps();

            $table->unique(['ssg_event_id', 'student_id']);   // one record per student per event
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ssg_event_attendances');
        Schema::dropIfExists('ssg_events');
    }
};
