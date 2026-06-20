<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$roles = ['SUPER_ADMIN', 'KETUA_RW', 'SEKRETARIS_RW', 'KETUA_RT', 'BENDAHARA_RW'];
foreach($roles as $role) {
    $user = \App\Models\User::whereHas('role', function($q) use ($role) {
        $q->where('role_name', $role);
    })->first();

    if(!$user) {
        echo "No user for $role\n";
        continue;
    }

    echo "Testing $role...\n";
    $request = Illuminate\Http\Request::create('/dashboard', 'GET');
    $app->make('auth')->login($user);
    $response = $kernel->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    if($response->getStatusCode() !== 200) {
        echo "Error: dashboard failed for $role\n";
    }
}
