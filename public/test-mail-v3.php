<?php
// public/test-mail-v3.php

header('Content-Type: text/plain; charset=utf-8');

$projectRoot = realpath(__DIR__ . '/../') . '/';

try {
    echo "Booting Laravel...\n";
    require $projectRoot . 'vendor/autoload.php';
    $app = require $projectRoot . 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "Laravel booted.\n\n";
} catch (\Throwable $e) {
    die("❌ Laravel boot failed: " . $e->getMessage());
}

function testMailConfiguration($name, $configSettings) {
    echo "--------------------------------------------------\n";
    echo "Testing Configuration: $name\n";
    echo "--------------------------------------------------\n";
    
    // Apply configuration at runtime
    foreach ($configSettings as $key => $value) {
        config([$key => $value]);
    }
    
    // Reset the mail manager
    app()->forgetInstance('mail.manager');
    
    try {
        \Illuminate\Support\Facades\Mail::raw("This is a test email sent using $name configuration on the live server.", function ($message) use ($name) {
            $message->to('musiimentaagnes9@gmail.com')
                    ->subject('Magna Credit - Mail Test (' . $name . ')');
        });
        echo "✅ SUCCESS! Email sent without exceptions.\n\n";
        return true;
    } catch (\Throwable $e) {
        echo "❌ FAILED!\n";
        echo "Error: " . $e->getMessage() . "\n\n";
        return false;
    }
}

// Test Configuration: SMTP using s11.my-control-panel.com
// We will test if we can establish a connection. Note: This test will fail if authentication is required but we pass nulls,
// but we want to see if we get past the "SSL/TLS Certificate mismatch" error first!
testMailConfiguration('SMTP via Hostname on Port 587 (No Auth)', [
    'mail.default' => 'smtp',
    'mail.mailers.smtp.host' => 's11.my-control-panel.com',
    'mail.mailers.smtp.port' => 587,
    'mail.mailers.smtp.encryption' => 'tls',
    'mail.mailers.smtp.username' => null,
    'mail.mailers.smtp.password' => null,
    'mail.from.address' => 'noreply@magnacredited.com',
    'mail.from.name' => 'Magna App Test',
]);

testMailConfiguration('SMTP via Hostname on Port 465 (No Auth)', [
    'mail.default' => 'smtp',
    'mail.mailers.smtp.host' => 's11.my-control-panel.com',
    'mail.mailers.smtp.port' => 465,
    'mail.mailers.smtp.encryption' => 'ssl',
    'mail.mailers.smtp.username' => null,
    'mail.mailers.smtp.password' => null,
    'mail.from.address' => 'noreply@magnacredited.com',
    'mail.from.name' => 'Magna App Test',
]);
