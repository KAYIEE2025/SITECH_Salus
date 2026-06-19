<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');

            // Target audience
            // target_type: 'all' | 'course' | 'year_level' | 'section'
            $table->enum('target_type', ['all', 'course', 'year_level', 'section'])
                  ->default('all');
            // target_id: FK to the corresponding table row (null when target_type = 'all')
            $table->unsignedBigInteger('target_id')->nullable();

            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('announcements'); }
};
