<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Check if permissions already exist for 'api' guard
        $viewUsersPermission = Permission::firstOrCreate(['name' => 'view users', 'guard_name' => 'api']);
        $manageUsersPermission = Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'api']);
        $deleteUsersPermission = Permission::firstOrCreate(['name' => 'delete users', 'guard_name' => 'api']);

        // Check if roles already exist for 'api' guard
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'api']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'api']);
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'api']);

        // Assign Permissions to Roles
        $superAdminRole->givePermissionTo([$viewUsersPermission, $manageUsersPermission, $deleteUsersPermission]);
        $adminRole->givePermissionTo([$viewUsersPermission, $manageUsersPermission]);
        $userRole->givePermissionTo([$viewUsersPermission]);

        // Assign Roles to Users (for example)
        $user = \App\Models\User::find(1); // Get a user from the database
        if ($user) {
            $user->assignRole('SuperAdmin'); // Assign SuperAdmin role to user
        }

        $admin = \App\Models\User::find(2);
        if ($admin) {
            $admin->assignRole('Admin'); // Assign Admin role to user
        }

        $normalUser = \App\Models\User::find(3);
        if ($normalUser) {
            $normalUser->assignRole('User'); // Assign User role to user
        }
    }
}
