<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::where('email', env('SUPER_ADMIN_EMAIL'))->delete();

        $superAdmin = User::create([
            'name' => env('SUPER_ADMIN_NAME'),
            'email' => env('SUPER_ADMIN_EMAIL'),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
        ]);

        $role = Role::findByName('super-admin');
        $superAdmin->assignRole($role);
    }
}
