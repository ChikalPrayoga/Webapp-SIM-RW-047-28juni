<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\LogLaporanAspirasi;
use App\Models\PengajuanSurat;
use App\Models\AnggotaKeluarga;
use App\Models\KartuKeluarga;

$users = User::with(['role', 'position'])->get();

echo "=== FULL AUTHORIZATION SELF-REVIEW ===\n\n";

foreach ($users as $user) {
    $roleName = $user->role ? $user->role->role_name : 'UNKNOWN';
    echo "--- User #{$user->user_id}: {$user->full_name} ({$roleName}) ---\n";
    
    auth()->login($user);
    
    // Test Dashboard
    try {
        $ctrl = new \App\Http\Controllers\DashboardController();
        $result = $ctrl->index();
        echo "  /dashboard         -> OK\n";
    } catch (\Exception $e) {
        echo "  /dashboard         -> FAIL: " . $e->getMessage() . "\n";
    }
    
    // Test viewAny policies
    $checks = [
        ['/warga', AnggotaKeluarga::class, 'viewAny'],
        ['/kk', KartuKeluarga::class, 'viewAny'],
        ['/letters', PengajuanSurat::class, 'viewAny'],
        ['/complaints', LogLaporanAspirasi::class, 'viewAny'],
    ];
    
    foreach ($checks as [$path, $modelClass, $ability]) {
        try {
            $allowed = $user->can($ability, $modelClass);
            $status = $allowed ? 'OK' : 'DENIED (403)';
            echo "  {$path}" . str_pad('', 18 - strlen($path)) . "-> {$status}\n";
        } catch (\Exception $e) {
            echo "  {$path}" . str_pad('', 18 - strlen($path)) . "-> ERROR: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    auth()->logout();
}

echo "=== SELF-REVIEW COMPLETE ===\n";
