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
        // Drop the custom_roles table as it's no longer needed
        // Authorization is now handled by Spatie Permission
        Schema::dropIfExists('custom_roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the custom_roles table if needed for rollback
        Schema::create('custom_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
