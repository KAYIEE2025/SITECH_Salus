<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('school_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->date('event_end_date')->nullable(); // for multi-day events
            $table->string('color', 10)->default('#1a5c35'); // calendar dot color
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('event_date');
        });
    }

    public function down(): void { Schema::dropIfExists('school_events'); }
};
