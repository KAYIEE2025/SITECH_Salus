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
        Schema::table('grade_submission_reopening_requests', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->after('reviewed_by')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            $table->text('remarks')->nullable()->after('temporary_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_submission_reopening_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at', 'remarks']);
        });
    }
};
