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
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Admin',
                'phone' => '1234567890',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
        $user->assignRole($role);
    }
}
