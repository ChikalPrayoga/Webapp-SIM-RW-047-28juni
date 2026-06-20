<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password123'); // Konsisten dengan UserSeeder

        // 1. Akun Sekretaris
        $sekretarisRole = Role::where('role_name', 'SEKRETARIS_RW')->first();
        if ($sekretarisRole) {
            User::updateOrCreate(
                ['email' => 'sekretaris@rw047.com'],
                [
                    'role_id' => $sekretarisRole->role_id,
                    'username' => 'sekretaris',
                    'full_name' => 'Sekretaris RW 047',
                    'password' => $password,
                    'status' => 'ACTIVE'
                ]
            );
        }

        // 2. Akun Bendahara
        $bendaharaRole = Role::where('role_name', 'BENDAHARA_RW')->first();
        if ($bendaharaRole) {
            User::updateOrCreate(
                ['email' => 'bendahara@rw047.com'],
                [
                    'role_id' => $bendaharaRole->role_id,
                    'username' => 'bendahara',
                    'full_name' => 'Bendahara RW 047',
                    'password' => $password,
                    'status' => 'ACTIVE'
                ]
            );
        }
    }
}
