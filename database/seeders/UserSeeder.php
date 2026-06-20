<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('role_name', 'SUPER_ADMIN')->first();

        if ($superAdminRole) {
            User::firstOrCreate(
                ['email' => 'admin@simrw047.com'],
                [
                    'role_id' => $superAdminRole->role_id,
                    'username' => 'superadmin',
                    'password' => Hash::make('password123'),
                    'full_name' => 'Super Administrator',
                    'status' => 'ACTIVE'
                ]
            );
        }
    }
}
