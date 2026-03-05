<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete old permissions and roles
        Permission::query()->delete();
        Role::query()->delete();

        // Create clean permissions
        $permissions = [
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view loan applications',
            'review loan applications',
            'approve loans',
            'reject loans',
            'edit loan amounts',
            'disburse loans',
            'view repayments',
            'record repayments',
            'view reports',
            'manage admins',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Super Admin - gets all permissions
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin/Staff - limited permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'view loan applications',
            'review loan applications',
            'view repayments',
            'record repayments',
            'view reports',
        ]);
    }
}