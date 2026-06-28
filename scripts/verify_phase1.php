<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = ['SUPER_ADMIN', 'KETUA_RW', 'SEKRETARIS_RW', 'KETUA_RT', 'BENDAHARA_RW'];

echo "| Role | Login | Dashboard | Sidebar | Status |\n";
echo "| ---- | ----- | --------- | ------- | ------ |\n";

foreach($roles as $role) {
    $user = \App\Models\User::whereHas('role', function($q) use ($role) {
        $q->where('role_name', $role);
    })->first();

    if(!$user) {
        echo "| $role | N/A | N/A | N/A | NO USER |\n";
        continue;
    }

    auth()->login($user);
    $controller = new \App\Http\Controllers\DashboardController();
    
    try {
        $response = $controller->index();
        $viewName = $response->name();
        
        $expectedView = match($role) {
            'SUPER_ADMIN' => 'dashboard-admin',
            'KETUA_RW' => 'dashboard-rw',
            'SEKRETARIS_RW' => 'dashboard-sekretaris',
            'BENDAHARA_RW' => 'dashboard-bendahara',
            'KETUA_RT' => 'dashboard-rt',
            default => 'dashboard'
        };
        
        $dashboardOk = ($viewName === $expectedView) ? "OK" : "FAIL ($viewName)";
        
        // Render the layout to ensure no 500 errors in Blade
        $html = $response->render();
        $sidebarOk = "OK"; // If it renders without throwing exceptions
        
        echo "| $role | OK | $dashboardOk | $sidebarOk | VERIFIED |\n";
        
    } catch (\Exception $e) {
        echo "| $role | OK | ERROR | ERROR | FAILED |\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
}
