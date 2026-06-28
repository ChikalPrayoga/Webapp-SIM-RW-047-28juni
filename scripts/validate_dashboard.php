<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

echo "--- Self Review Dashboard Controller ---\n";

function testDashboardFor($email, $role) {
    echo "\nTesting Dashboard as $role ($email)...\n";
    $user = User::where('email', $email)->first();
    if (!$user) {
        echo "User not found.\n";
        return;
    }
    
    auth()->login($user);
    
    try {
        $controller = new \App\Http\Controllers\DashboardController();
        $view = $controller->index();
        $data = $view->getData();
        
        echo "Success! Metrics:\n";
        echo "- Total Warga: " . $data['metrics']['total_warga'] . "\n";
        echo "- Total KK: " . $data['metrics']['total_kk'] . "\n";
        echo "- Total Surat: " . $data['metrics']['total_surat'] . "\n";
        echo "- Surat Pending: " . $data['metrics']['surat_pending'] . "\n";
        echo "- Total Laporan: " . $data['metrics']['total_laporan'] . "\n";
        
        echo "- Recent Surat Count: " . count($data['recentSurat']) . "\n";
        echo "- Recent Laporan Count: " . count($data['recentLaporan']) . "\n";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
}

testDashboardFor('admin@simrw.com', 'Super Admin');
testDashboardFor('rw@simrw.com', 'Ketua RW');
testDashboardFor('rt01@simrw.com', 'Ketua RT 01');

echo "\n--- Validation Complete ---\n";
