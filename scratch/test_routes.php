<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();

$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use Illuminate\Http\Request;

$users = User::with(['role', 'position'])->get();

foreach ($users as $user) {
    $roleName = $user->role ? $user->role->role_name : 'UNKNOWN';
    echo "=== Testing routes for user #{$user->user_id}: {$user->full_name} ({$roleName}) ===\n";
    
    auth()->login($user);
    
    $paths = ['/dashboard', '/warga', '/kk', '/letters', '/complaints'];
    
    foreach ($paths as $path) {
        $request = Request::create($path, 'GET');
        
        try {
            $response = $httpKernel->handle($request);
            $status = $response->getStatusCode();
            echo "  GET {$path} -> Status: {$status}\n";
            $content = $response->getContent();
            if (preg_match('/<title>(.*?)<\/title>/s', $content, $matches)) {
                echo "    Title: " . trim($matches[1]) . "\n";
            } else {
                echo "    Content Snippet: " . substr(strip_tags(cleanHtml($content)), 0, 100) . "\n";
            }
            $httpKernel->terminate($request, $response);
        } catch (\Exception $e) {
            echo "  GET {$path} -> EXCEPTION: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
    auth()->logout();
}

function cleanHtml($html) {
    return preg_replace('/\s+/', ' ', $html);
}
