<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::with(['role', 'position'])->get();
echo "Total Users: " . $users->count() . "\n";

foreach ($users as $user) {
    echo "ID: {$user->user_id} | Name: {$user->full_name} | Email: {$user->email} | Role: " . ($user->role ? $user->role->role_name : 'No Role') . "\n";
    if ($user->position) {
        echo "  Position: {$user->position->position_type} | Area: {$user->position->area_code}\n";
    } else {
        echo "  Position: NONE\n";
    }
    
    // List permissions
    if ($user->role) {
        $permissions = $user->role->permissions->pluck('permission_name')->toArray();
        echo "  Permissions: " . implode(', ', $permissions) . "\n";
    }
    echo "---------------------------------------------------\n";
}
