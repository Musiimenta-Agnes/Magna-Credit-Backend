<?php
// public/extract.php

// Ensure this script is only accessed with the correct token
if (!isset($_GET['token']) || $_GET['token'] !== getenv('DEPLOY_TOKEN') && $_GET['token'] !== $_ENV['DEPLOY_TOKEN']) {
    $envFile = __DIR__ . '/../.env';
    $validToken = null;
    if (file_exists($envFile)) {
        $envContents = file_get_contents($envFile);
        if (preg_match('/^DEPLOY_TOKEN=(.*)$/m', $envContents, $matches)) {
            $validToken = trim($matches[1]);
        }
    }
    
    if ($validToken === null || $_GET['token'] !== $validToken) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized']));
    }
}

echo "Starting Pull Deployment...<br>";

// Download the zip file from GitHub
$zipUrl = 'https://github.com/Musiimenta-Agnes/Magna-Credit-Backend/archive/refs/heads/master.zip';
$zipFile = __DIR__ . '/../release.zip';

echo "Downloading release from GitHub...<br>";
$zipData = file_get_contents($zipUrl);
if ($zipData === false) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to download zip from GitHub']));
}
file_put_contents($zipFile, $zipData);

echo "Unzipping files...<br>";
$zip = new ZipArchive;
$res = $zip->open($zipFile);
if ($res === TRUE) {
    $extractDir = __DIR__ . '/../Magna-Credit-Backend-master';
    $zip->extractTo(__DIR__ . '/../');
    $zip->close();
    
    echo "Moving files out of subdirectory...<br>";
    // Copy all files from the extracted subdirectory up one level
    exec('cp -a ' . $extractDir . '/* ' . __DIR__ . '/../');
    exec('cp -a ' . $extractDir . '/.[a-zA-Z0-9]* ' . __DIR__ . '/../ 2>/dev/null'); // Copy hidden files
    exec('rm -rf ' . $extractDir);
    
    unlink($zipFile);
    
    echo "Running Composer Install...<br>";
    // Run composer install to ensure livewire/flux gets installed correctly
    exec('cd ' . __DIR__ . '/../ && composer install --optimize-autoloader --no-dev --no-interaction');

    echo "Running Laravel Migrations...<br>";
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    $kernel->call('optimize:clear');
    $kernel->call('migrate', ['--force' => true]);
    
    echo "Deployment Complete!<br>";
    echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to open zip file']);
}
