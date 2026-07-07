<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY status ENUM('Pending Student Account', 'Account Created', 'Active', 'Inactive', 'Graduated', 'Dropped') NOT NULL DEFAULT 'Pending Student Account'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE students SET status = 'Active' WHERE status = 'Pending Student Account'");
            DB::statement("ALTER TABLE students MODIFY status ENUM('Active', 'Inactive', 'Graduated', 'Dropped') NOT NULL DEFAULT 'Active'");
        }
    }
};
