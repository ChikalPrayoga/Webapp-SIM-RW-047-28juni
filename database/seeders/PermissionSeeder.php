<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Resident Management
            'view_residents', 'create_residents', 'edit_residents', 'delete_residents', 'approve_resident_changes',
            // Complaint Management
            'view_complaints', 'update_complaints',
            // Letter Management
            'view_letters', 'approve_rt_letters', 'approve_rw_letters', 'complete_letters',
            // Financial Management
            'view_finances', 'manage_finances',
            // Information Management
            'manage_information',
            // System
            'manage_system',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['permission_name' => $perm], ['description' => "Allows user to {$perm}"]);
        }
    }
}
