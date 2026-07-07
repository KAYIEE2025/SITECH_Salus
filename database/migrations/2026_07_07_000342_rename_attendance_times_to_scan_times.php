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
            $table->renameColumn('attendance_start_time', 'scan_start_time');
            $table->renameColumn('attendance_end_time', 'scan_end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ssg_events', function (Blueprint $table) {
            $table->renameColumn('scan_start_time', 'attendance_start_time');
            $table->renameColumn('scan_end_time', 'attendance_end_time');
        });
    }
};
