<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$superAdmin = \App\Models\User::whereHas('role', function($q){ $q->where('role_name', 'SUPER_ADMIN'); })->first();
if (!$superAdmin) die("No Super Admin found\n");

auth()->login($superAdmin);

echo "Testing routes for SUPER_ADMIN:\n";

$routesToTest = [
    'dashboard' => [],
    'admin.users.index' => [],
    'admin.users.create' => [],
    'admin.roles.index' => [],
    'admin.permissions.index' => [],
    'admin.settings.index' => [],
    'admin.audit-log.index' => [],
];

foreach ($routesToTest as $routeName => $params) {
    try {
        $url = route($routeName, $params, false);
        $request = Illuminate\Http\Request::create($url, 'GET');
        
        $response = app()->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
        
        echo str_pad($routeName, 25) . " => Status: " . $response->getStatusCode() . "\n";
        if ($response->getStatusCode() === 500) {
            echo substr($response->getContent(), 0, 500) . "\n";
        }
    } catch (\Exception $e) {
        echo str_pad($routeName, 25) . " => FAILED: " . $e->getMessage() . "\n";
    }
}
