<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$role = \App\Models\Role::where('role_name', 'BENDAHARA_RW')->first();
foreach($role->permissions as $p) {
    echo $p->permission_name . "\n";
}
