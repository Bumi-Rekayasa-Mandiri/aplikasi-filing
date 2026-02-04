<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::firstOrCreate(['name' => 'manage roles']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo('manage roles');
    }
}
