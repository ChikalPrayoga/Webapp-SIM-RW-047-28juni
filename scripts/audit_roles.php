<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Models\User;

$targetRoles = ['SUPER_ADMIN', 'KETUA_RW', 'SEKRETARIS_RW', 'BENDAHARA_RW', 'KETUA_RT', 'WARGA'];

echo "--- VERIFIKASI ROLE ---\n";
foreach($targetRoles as $rName) {
    $role = Role::where('role_name', $rName)->first();
    echo $rName . ": " . ($role ? "ADA" : "TIDAK ADA") . "\n";
}

echo "\n--- VERIFIKASI USER DEMO ---\n";
$users = User::with('role')->get();
foreach($targetRoles as $rName) {
    if($rName === 'WARGA') continue; // Only checking demo users for admin/pengurus
    $demoUsers = $users->filter(function($u) use ($rName) {
        return $u->role && $u->role->role_name === $rName;
    });

    if($demoUsers->isEmpty()) {
        echo "Belum ada akun demo untuk: " . $rName . "\n";
    } else {
        foreach($demoUsers as $u) {
            echo "Role: " . $rName . " | Nama: " . $u->name . " | Email: " . $u->email . "\n";
        }
    }
}
