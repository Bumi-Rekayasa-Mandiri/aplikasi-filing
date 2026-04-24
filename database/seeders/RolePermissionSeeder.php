<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Surat
            'view surat', 'create surat', 'edit surat',
            'delete surat', 'submit surat',
            'approve surat', 'reject surat',

            // Arsip
            'view arsip', 'create arsip', 'edit arsip',
            'delete arsip', 'manage arsip',

            // Sertifikat
            'view sertifikat', 'create sertifikat', 'edit sertifikat',
            'delete sertifikat', 'manage sertifikat',

            // Roles & Users
            'view roles', 'create roles', 'manage roles',
            'view users', 'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());


        // Admin

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'view surat', 'create surat', 'edit surat', 'delete surat',
            'view arsip', 'create arsip', 'edit arsip',
            'view sertifikat', 'create sertifikat', 'edit sertifikat',
        ]);
    }
}