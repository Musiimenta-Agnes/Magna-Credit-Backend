<?php
// public/test-mail-v2.php

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
    
    // Reset the mail manager instance only, so it re-reads config
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

// Configuration 1: Sendmail driver
testMailConfiguration('Sendmail Driver', [
    'mail.default' => 'sendmail',
    'mail.from.address' => 'noreply@magnacredited.com',
    'mail.from.name' => 'Magna App Test',
]);

// Configuration 2: Local SMTP Port 25
testMailConfiguration('Local SMTP Port 25 (No Auth)', [
    'mail.default' => 'smtp',
    'mail.mailers.smtp.host' => 'localhost',
    'mail.mailers.smtp.port' => 25,
    'mail.mailers.smtp.encryption' => null,
    'mail.mailers.smtp.username' => null,
    'mail.mailers.smtp.password' => null,
    'mail.from.address' => 'noreply@magnacredited.com',
    'mail.from.name' => 'Magna App Test',
]);

// Configuration 3: Local SMTP Port 587
testMailConfiguration('Local SMTP Port 587 (No Auth)', [
    'mail.default' => 'smtp',
    'mail.mailers.smtp.host' => 'localhost',
    'mail.mailers.smtp.port' => 587,
    'mail.mailers.smtp.encryption' => null,
    'mail.mailers.smtp.username' => null,
    'mail.mailers.smtp.password' => null,
    'mail.from.address' => 'noreply@magnacredited.com',
    'mail.from.name' => 'Magna App Test',
]);
