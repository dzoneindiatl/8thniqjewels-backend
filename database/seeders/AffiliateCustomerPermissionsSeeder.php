<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AffiliateCustomerPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Role ID 5 = Affiliates (Vendor)
        $role = Role::findById(5, 'web');

        $permissions = Permission::where('guard_name', 'web')
            ->whereIn('name', [
                'view_user',
                'create_user',
            ])
            ->get();

        if ($permissions->count() !== 2) {
            throw new \RuntimeException(
                'Required permissions view_user and create_user must exist.'
            );
        }

        // Add these permissions without removing existing role permissions.
        $role->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
