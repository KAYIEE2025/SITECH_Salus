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
            // Add date_when field
            $table->date('date_when')->nullable()->after('target_id');

            // Remove start_at and end_at fields
            $table->dropColumn(['start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Remove date_when field
            $table->dropColumn('date_when');

            // Restore start_at and end_at fields
            $table->timestamp('start_at')->nullable()->after('target_id');
            $table->timestamp('end_at')->nullable()->after('start_at');
        });
    }
};
