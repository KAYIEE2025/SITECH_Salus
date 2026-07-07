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
            $table->time('attendance_start_time')->nullable()->after('event_time');
            $table->time('attendance_end_time')->nullable()->after('attendance_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ssg_events', function (Blueprint $table) {
            $table->dropColumn(['attendance_start_time', 'attendance_end_time']);
        });
    }
};
