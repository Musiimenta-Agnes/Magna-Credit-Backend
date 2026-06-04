<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $loansOfficerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'loans_officer']);

        // 2. Seed Super Admin User (Full Access)
        $superAdmin = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Super Admin',
                'phone' => '1234567890',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        $superAdmin->syncRoles([$superAdminRole]);

        // 3. Seed Admin User (All access except AdminUserResource)
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'General Admin',
                'phone' => '1234567891',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 4. Seed Loans Officer User (View-only access except AdminUserResource)
        $loansOfficer = User::updateOrCreate(
            ['email' => 'officer@example.com'],
            [
                'name' => 'Loans Officer',
                'phone' => '1234567892',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        $loansOfficer->syncRoles([$loansOfficerRole]);
    }
}
