<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove 'course' from the target_type enum in announcements table
        // This is for high school SIS where courses are not used
        DB::statement("ALTER TABLE announcements MODIFY COLUMN target_type ENUM('all', 'year_level', 'section') NOT NULL DEFAULT 'all'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore 'course' to the target_type enum for rollback
        DB::statement("ALTER TABLE announcements MODIFY COLUMN target_type ENUM('all', 'course', 'year_level', 'section') NOT NULL DEFAULT 'all'");
    }
};
