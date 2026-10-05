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
        Schema::table('announcements', function (Blueprint $table) {
            // Add date_when field if it doesn't exist
            if (!Schema::hasColumn('announcements', 'date_when')) {
                $table->date('date_when')->nullable()->after('target_id');
            }

            // Remove start_at and end_at fields only if they exist
            if (Schema::hasColumn('announcements', 'start_at')) {
                $table->dropColumn('start_at');
            }
            if (Schema::hasColumn('announcements', 'end_at')) {
                $table->dropColumn('end_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Remove date_when field only if it exists
            if (Schema::hasColumn('announcements', 'date_when')) {
                $table->dropColumn('date_when');
            }

            // Restore start_at and end_at fields only if they don't exist
            if (!Schema::hasColumn('announcements', 'start_at')) {
                $table->timestamp('start_at')->nullable()->after('target_id');
            }
            if (!Schema::hasColumn('announcements', 'end_at')) {
                $table->timestamp('end_at')->nullable()->after('start_at');
            }
        });
    }
};
