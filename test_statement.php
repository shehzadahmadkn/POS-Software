<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = new \Illuminate\Http\Request(['type' => 'group', 'id' => 1, 'from' => '2026-08-01', 'to' => '2026-08-11']);
try {
    $res = app(App\Http\Controllers\AccountController::class)->statement($request);
    echo $res->content();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
