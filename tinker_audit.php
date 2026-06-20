<?php
echo "1. DB_CONNECTION: " . config('database.default') . "\n";
echo "2. Database Active: " . DB::connection()->getDatabaseName() . "\n";
echo "3. Count roles: " . \App\Models\Role::count() . "\n";
echo "3. Count permissions: " . \App\Models\Permission::count() . "\n";
echo "3. Count role_permissions: " . DB::table('role_permissions')->count() . "\n";

$roles = ['SUPER_ADMIN', 'KETUA_RW', 'KETUA_RT'];
echo "4. Mappings:\n";
foreach($roles as $r) {
    $role = \App\Models\Role::where('role_name', $r)->first();
    if($role) {
        $perms = $role->permissions->pluck('permission_name')->join(', ');
        echo "   Role {$r} has permissions: " . ($perms ?: 'NONE') . "\n";
    } else {
        echo "   Role {$r} NOT FOUND\n";
    }
}

echo "7. Simulasi KETUA_RW:\n";
$rw = \App\Models\User::whereHas('role', function($q){ $q->where('role_name', 'KETUA_RW'); })->first();
if($rw) {
    echo "   RW username: " . $rw->username . "\n";
    echo "   can(viewAny AnggotaKeluarga)? " . ($rw->can('viewAny', \App\Models\AnggotaKeluarga::class) ? 'Yes' : 'No') . "\n";
    echo "   can(viewAny KartuKeluarga)? " . ($rw->can('viewAny', \App\Models\KartuKeluarga::class) ? 'Yes' : 'No') . "\n";
    echo "   can(viewAny PengajuanSurat)? " . ($rw->can('viewAny', \App\Models\PengajuanSurat::class) ? 'Yes' : 'No') . "\n";
    echo "   can(viewAny LogLaporanAspirasi)? " . ($rw->can('viewAny', \App\Models\LogLaporanAspirasi::class) ? 'Yes' : 'No') . "\n";
} else {
    echo "   RW NOT FOUND\n";
}

echo "7. Simulasi KETUA_RT:\n";
$rt = \App\Models\User::whereHas('role', function($q){ $q->where('role_name', 'KETUA_RT'); })->first();
if($rt) {
    echo "   RT username: " . $rt->username . "\n";
    echo "   can(viewAny AnggotaKeluarga)? " . ($rt->can('viewAny', \App\Models\AnggotaKeluarga::class) ? 'Yes' : 'No') . "\n";
    echo "   can(viewAny KartuKeluarga)? " . ($rt->can('viewAny', \App\Models\KartuKeluarga::class) ? 'Yes' : 'No') . "\n";
    echo "   can(viewAny PengajuanSurat)? " . ($rt->can('viewAny', \App\Models\PengajuanSurat::class) ? 'Yes' : 'No') . "\n";
    echo "   can(viewAny LogLaporanAspirasi)? " . ($rt->can('viewAny', \App\Models\LogLaporanAspirasi::class) ? 'Yes' : 'No') . "\n";
} else {
    echo "   RT NOT FOUND\n";
}
exit();
