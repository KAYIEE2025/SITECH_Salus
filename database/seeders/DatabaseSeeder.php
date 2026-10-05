<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\YearLevel;
use Spatie\Permission\Models\Role;
use Database\Seeders\LegacyStudentSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create system roles
        $roles = [
            'Super Admin',
            'Admin',
            'Registrar',
            'Teacher',
            'SSG',
            'Student',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // 2. Create Super Admin from .env
        $superAdminEmail = env('SUPERADMIN_EMAIL');
        $superAdminPassword = env('SUPERADMIN_PASSWORD');

        if (!$superAdminEmail || !$superAdminPassword) {
            throw new \RuntimeException(
                'SUPERADMIN_EMAIL and SUPERADMIN_PASSWORD must be set in the .env file.'
            );
        }

        $superAdmin = User::firstOrCreate(
            ['email' => $superAdminEmail],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($superAdminPassword),
                'is_active' => true,
            ]
        );

        $superAdmin->assignRole('Super Admin');

        // 3. Seed year levels
        $levels = [
            ['level' => 7,  'name' => 'Grade 7'],
            ['level' => 8,  'name' => 'Grade 8'],
            ['level' => 9,  'name' => 'Grade 9'],
            ['level' => 10, 'name' => 'Grade 10'],
            ['level' => 11, 'name' => 'Grade 11'],
            ['level' => 12, 'name' => 'Grade 12'],
        ];

        foreach ($levels as $yearLevel) {
            YearLevel::firstOrCreate(
                ['level' => $yearLevel['level']],
                $yearLevel
            );
        }

        // 4. Seed legacy students from private Excel file
        $this->call([
            LegacyStudentSeeder::class,
        ]);

        $this->command->info(
            "Database seeding completed. Super Admin: {$superAdminEmail}"
        );
    }
}