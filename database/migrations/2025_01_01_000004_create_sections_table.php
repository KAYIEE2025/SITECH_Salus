<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);             // A, B, C, Rizal, Bonifacio, San Antonio …
            $table->foreignId('year_level_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'year_level_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('sections'); }
};
