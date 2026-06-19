<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\YearLevel;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create roles (Spatie) ───────────────────────────────
        // 6 roles only — no Registrar Staff
        $roles = [
            'Super Admin',
            'Admin',
            'Registrar',
            'Teacher',
            'SSG',
            'Student',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // ── 2. Create Super Admin account ─────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@salus.edu'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('SIT@superadmin2025'),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // ── Seed grade levels ──────────────────────────────────────
        $levels = [
            ['level' => 7,  'name' => 'Grade 7'],
            ['level' => 8,  'name' => 'Grade 8'],
            ['level' => 9,  'name' => 'Grade 9'],
            ['level' => 10, 'name' => 'Grade 10'],
            ['level' => 11, 'name' => 'Grade 11'],
            ['level' => 12, 'name' => 'Grade 12'],
        ];

        foreach ($levels as $yr) {
            \App\Models\YearLevel::firstOrCreate(['level' => $yr['level']], $yr);
        }

        $this->command->info('Done! Super Admin: superadmin@salus.edu / SIT@superadmin2025');
    }
}
