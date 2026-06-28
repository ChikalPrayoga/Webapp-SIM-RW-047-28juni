<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;

$errors = [];

// 1. Check Routes and Controller Methods
$routes = Route::getRoutes()->getRoutes();
$validRouteNames = [];
foreach ($routes as $route) {
    $action = $route->getAction();
    if (isset($action['as'])) {
        $validRouteNames[] = $action['as'];
    }
    if (isset($action['controller'])) {
        $controllerAction = explode('@', $action['controller']);
        if (count($controllerAction) == 2) {
            $controller = $controllerAction[0];
            $method = $controllerAction[1];
            if (!class_exists($controller)) {
                $errors[] = "Route Error: Controller class not found: $controller";
            } elseif (!method_exists($controller, $method)) {
                $errors[] = "Route Error: Controller method not found: $controller@$method";
            }
        }
    }
}

// 2. Check Blade files for invalid route() calls
$viewsDir = resource_path('views');
$files = File::allFiles($viewsDir);
foreach ($files as $file) {
    $content = file_get_contents($file->getPathname());
    // Find all route('name') calls
    preg_match_all("/route\(['\"]([^'\"]+)['\"]/", $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $routeName) {
            // Ignore routes with parameters in the name string if any, though usually parameters are second arg
            if (!in_array($routeName, $validRouteNames) && !str_contains($routeName, '*')) {
                $errors[] = "Blade Error: Invalid route name '$routeName' in " . $file->getRelativePathname();
            }
        }
    }
}

// 3. Check Controllers for invalid route() calls
$controllersDir = app_path('Http/Controllers');
$files = File::allFiles($controllersDir);
foreach ($files as $file) {
    $content = file_get_contents($file->getPathname());
    // Find all route('name') calls
    preg_match_all("/route\(['\"]([^'\"]+)['\"]/", $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $routeName) {
            if (!in_array($routeName, $validRouteNames)) {
                $errors[] = "Controller Error: Invalid route name '$routeName' in " . $file->getRelativePathname();
            }
        }
    }
    // Find all view('name') calls
    preg_match_all("/view\(['\"]([^'\"]+)['\"]/", $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $viewName) {
            if (!View::exists($viewName)) {
                $errors[] = "Controller Error: Missing view '$viewName' in " . $file->getRelativePathname();
            }
        }
    }
}

echo json_encode(['errors' => $errors], JSON_PRETTY_PRINT);
