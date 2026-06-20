<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'SUPER_ADMIN', 'description' => 'System Administrator with full access'],
            ['role_name' => 'KETUA_RW', 'description' => 'Ketua RW with executive access'],
            ['role_name' => 'SEKRETARIS_RW', 'description' => 'Sekretaris RW with administrative access'],
            ['role_name' => 'BENDAHARA_RW', 'description' => 'Bendahara RW with financial access'],
            ['role_name' => 'KETUA_RT', 'description' => 'Ketua RT with local territory access'],
            ['role_name' => 'WARGA', 'description' => 'Regular citizen access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['role_name' => $role['role_name']], $role);
        }
    }
}
