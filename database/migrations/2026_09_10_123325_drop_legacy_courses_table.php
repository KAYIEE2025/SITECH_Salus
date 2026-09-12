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
        // Safely drop the legacy courses table if it exists
        // This table was created for a college-style system but is not used
        // in the current high school SIS structure
        if (Schema::hasTable('courses')) {
            Schema::dropIfExists('courses');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the courses table if needed for rollback
        // This recreates the original courses table structure
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('code', 20)->unique();   // e.g. BSIT, BSCS
                $table->string('name');                  // e.g. Bachelor of Science in IT
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }
};
