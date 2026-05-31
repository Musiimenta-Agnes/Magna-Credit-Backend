<?php
// public/extract.php

// Ensure this script is only accessed with the correct token
if (!isset($_GET['token']) || $_GET['token'] !== getenv('DEPLOY_TOKEN') && $_GET['token'] !== $_ENV['DEPLOY_TOKEN']) {
    // If getenv/$_ENV fails because dotenv isn't loaded yet, try parsing the .env file directly
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

// Ensure the zip file exists
$zipFile = __DIR__ . '/../release.zip';
if (!file_exists($zipFile)) {
    http_response_code(404);
    die(json_encode(['error' => 'release.zip not found']));
}

// Unzip the file
$zip = new ZipArchive;
$res = $zip->open($zipFile);
if ($res === TRUE) {
    // Extract everything to the root folder (one level above public)
    $zip->extractTo(__DIR__ . '/../');
    $zip->close();
    
    // Delete the zip file after successful extraction
    unlink($zipFile);
    
    // Now that files are extracted, boot Laravel to run migrations
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    // Put site in maintenance mode
    $kernel->call('down');
    
    // Clear caches and run migrations
    $kernel->call('optimize:clear');
    $kernel->call('migrate', ['--force' => true]);
    
    // Bring site back up
    $kernel->call('up');
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Unzipped and migrated successfully!',
        'migrations' => \Illuminate\Support\Facades\Artisan::output()
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to open zip file']);
}
