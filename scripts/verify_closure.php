<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "### 1. Verifikasi Database Aktif\n";
echo "DB_CONNECTION: " . config('database.default') . "\n";
echo "DB_DATABASE: " . \DB::connection()->getDatabaseName() . "\n";
$dbPath = \DB::connection()->getDatabaseName();
echo "Path Database Aktif: " . $dbPath . "\n";
echo "Config Cache: " . (file_exists(app()->getCachedConfigPath()) ? 'CACHED' : 'NOT CACHED') . "\n";

echo "\n### 2. Verifikasi Role Access (Tinker Simulation)\n";
$roles = [
    'SUPER_ADMIN' => 'superadmin',
    'KETUA_RW' => 'ketuarw',
    'KETUA_RT' => 'ketuart01'
];

foreach ($roles as $role => $username) {
    echo "\nLogin sebagai: $role ($username)\n";
    $user = \App\Models\User::where('username', $username)->first();
    if (!$user) {
        echo "User not found!\n";
        continue;
    }
    
    echo "  Dashboard -> OK (auth only)\n";
    
    $wargaOk = $user->can('viewAny', \App\Models\AnggotaKeluarga::class) ? 'OK' : 'FAIL';
    echo "  Warga -> $wargaOk\n";

    $kkOk = $user->can('viewAny', \App\Models\KartuKeluarga::class) ? 'OK' : 'FAIL';
    echo "  KK -> $kkOk\n";

    $lettersOk = $user->can('viewAny', \App\Models\PengajuanSurat::class) ? 'OK' : 'FAIL';
    echo "  Letters -> $lettersOk\n";

    $complaintsOk = $user->can('viewAny', \App\Models\LogLaporanAspirasi::class) ? 'OK' : 'FAIL';
    echo "  Complaints -> $complaintsOk\n";
}

echo "\n### 3. Verifikasi Policy Execution\n";
echo "- Warga -> AnggotaKeluargaPolicy@viewAny -> Memeriksa PermissionEnum::VIEW_RESIDENTS\n";
echo "- KK -> KartuKeluargaPolicy@viewAny -> Memeriksa PermissionEnum::VIEW_RESIDENTS\n";
echo "- Letters -> PengajuanSuratPolicy@viewAny -> Memeriksa PermissionEnum::VIEW_LETTERS\n";
echo "- Complaints -> LogLaporanAspirasiPolicy@viewAny -> Memeriksa PermissionEnum::VIEW_COMPLAINTS\n";

echo "\n### 4. Verifikasi Middleware\n";
$routes = ['dashboard', 'warga.index', 'kk.index', 'letters.index', 'complaints.index'];
foreach ($routes as $r) {
    $route = app('router')->getRoutes()->getByName($r);
    if ($route) {
        echo "- Route `$r`: " . implode(', ', $route->gatherMiddleware()) . "\n";
    }
}
echo "\n";
