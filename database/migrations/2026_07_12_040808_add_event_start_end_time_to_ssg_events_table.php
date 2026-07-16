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
        Schema::table('ssg_events', function (Blueprint $table) {
            // Add event start and end times
            $table->time('event_start_time')->nullable()->after('event_date');
            $table->time('event_end_time')->nullable()->after('event_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ssg_events', function (Blueprint $table) {
            $table->dropColumn(['event_start_time', 'event_end_time']);
        });
    }
};
