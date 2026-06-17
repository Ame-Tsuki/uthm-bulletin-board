<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== SYSTEM MODERATION SERVICE ===\n";
print_r(app(App\Services\ModerationService::class)->moderate("bodoh", 1));
