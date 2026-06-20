<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define the permission matrix per role
        $matrix = [
            'KETUA_RW' => [
                'view_residents',
                'create_residents',
                'edit_residents',
                'view_complaints',
                'update_complaints',
                'resolve_complaints',
                'view_letters',
                'approve_rw_letters',
                'complete_letters',
                'view_finances',
                'manage_information',
                'view_audit_logs',
            ],
            'KETUA_RT' => [
                'view_residents',
                'create_residents',
                'edit_residents',
                'view_complaints',
                'update_complaints',
                'resolve_complaints',
                'view_letters',
                'approve_rt_letters',
                'complete_letters',
            ],
            'SEKRETARIS_RW' => [
                'view_residents',
                'create_residents',
                'edit_residents',
                'view_complaints',
                'view_letters',
                'view_finances',
                'manage_information',
            ],
            'BENDAHARA_RW' => [
                'view_residents',
                'view_complaints',
                'view_letters',
                'view_finances',
                'manage_finances',
            ],
            'WARGA' => [
                'submit_complaints',
                'submit_letters',
            ],
        ];

        foreach ($matrix as $roleName => $permissionNames) {
            $role = Role::where('role_name', $roleName)->first();
            if (!$role) {
                $this->command->warn("Role '{$roleName}' not found, skipping.");
                continue;
            }

            $permissionIds = Permission::whereIn('permission_name', $permissionNames)
                ->pluck('permission_id')
                ->toArray();

            // Sync permissions (replaces existing, avoids duplicates)
            $role->permissions()->syncWithoutDetaching($permissionIds);

            $this->command->info("Assigned " . count($permissionIds) . " permissions to {$roleName}.");
        }
    }
}
