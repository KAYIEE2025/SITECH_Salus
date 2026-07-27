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
            if (!Schema::hasColumn('ssg_events', 'event_start_time')) {
                $table->time('event_start_time')->nullable()->after('event_date');
            }

            if (!Schema::hasColumn('ssg_events', 'event_end_time')) {
                $table->time('event_end_time')->nullable()->after('event_start_time');
            }
        });
    }
};
