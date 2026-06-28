<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$paginator = new Illuminate\Pagination\LengthAwarePaginator([], 10, 2);
echo $paginator->links()->toHtml();
