<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ssg_event_attendances', function (Blueprint $table) {
            $table->foreignId('scanned_by_user_id')->nullable()->after('scanned_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ssg_event_attendances', function (Blueprint $table) {
            $table->dropForeign(['scanned_by_user_id']);
            $table->dropColumn('scanned_by_user_id');
        });
    }
};
