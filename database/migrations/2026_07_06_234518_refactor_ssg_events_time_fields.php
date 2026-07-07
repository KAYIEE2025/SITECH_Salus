<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

            // Make status nullable for backward compatibility
            $table->string('status', 20)->nullable()->change();
        });

        // Migrate existing data
        // For events with event_time, use it as event_start_time
        // Set event_end_time to event_start_time + 2 hours as default
        DB::statement('
            UPDATE ssg_events
            SET event_start_time = event_time,
                event_end_time = DATE_ADD(event_time, INTERVAL 2 HOUR)
            WHERE event_time IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ssg_events', function (Blueprint $table) {
            $table->dropColumn(['event_start_time', 'event_end_time']);
            $table->string('status', 20)->nullable(false)->change();
        });
    }
};
