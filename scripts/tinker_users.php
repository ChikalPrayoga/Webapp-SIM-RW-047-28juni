<?php
$users = \App\Models\User::with('role')->get();
foreach($users as $user) {
    $roleName = $user->role ? $user->role->role_name : 'NO_ROLE';
    echo "User: {$user->username} | Role: {$roleName} | Email: {$user->email}\n";
}
