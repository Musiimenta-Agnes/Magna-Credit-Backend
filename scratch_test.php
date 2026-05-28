<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $className = 'Filament\Widgets\StatsOverviewWidget';
    if (class_exists($className)) {
        echo "Class exists!\n";
    } else {
        echo "Class does not exist!\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
