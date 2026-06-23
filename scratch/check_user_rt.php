<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
echo "Username: " . $user->username . "\n";
echo "rt_code property exists: " . (property_exists($user, 'rt_code') ? 'yes' : 'no') . "\n";
try {
    echo "rt_code value: " . var_export($user->rt_code, true) . "\n";
} catch (\Exception $e) {
    echo "rt_code value error: " . $e->getMessage() . "\n";
}

$ref = new ReflectionClass(\App\Models\User::class);
echo "Has getRtCodeAttribute: " . ($ref->hasMethod('getRtCodeAttribute') ? 'yes' : 'no') . "\n";
echo "Has rt_code magic attribute? " . (isset($user->rt_code) ? 'yes' : 'no') . "\n";
