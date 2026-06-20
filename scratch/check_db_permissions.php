<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE TABLE COUNTS ===\n";
$tables = ['roles', 'permissions', 'role_permissions', 'users'];
foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "Table '{$table}': {$count} rows\n";
    } catch (\Exception $e) {
        echo "Table '{$table}': ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n=== ROLE PERMISSIONS PIVOT TABLE DATA ===\n";
try {
    $rows = DB::table('role_permissions')
        ->join('roles', 'role_permissions.role_id', '=', 'roles.role_id')
        ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.permission_id')
        ->select('roles.role_name', 'permissions.permission_name')
        ->get();
    
    foreach ($rows as $row) {
        echo "Role: {$row->role_name} -> Permission: {$row->permission_name}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
