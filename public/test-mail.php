<?php
// public/test-mail.php

header('Content-Type: text/plain; charset=utf-8');

$projectRoot = realpath(__DIR__ . '/../') . '/';

function checkConnection($host, $port, $timeout = 3) {
    echo "Checking connection to $host:$port ... ";
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$fp) {
        echo "❌ FAILED ($errno: $errstr)\n";
        return false;
    } else {
        echo "✅ SUCCESS\n";
        fclose($fp);
        return true;
    }
}

try {
    echo "Starting SMTP port connectivity tests...\n";
    checkConnection('smtp.gmail.com', 587);
    checkConnection('smtp.gmail.com', 465);
    checkConnection('smtp.gmail.com', 25);
    checkConnection('127.0.0.1', 25);
    echo "\n";

    echo "Starting Laravel mail test...\n";
    // Boot Laravel
    require $projectRoot . 'vendor/autoload.php';
    $app = require $projectRoot . 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "Laravel booted successfully.\n";
    echo "Current mail configuration:\n";
    echo "Mailer: " . config('mail.default') . "\n";
    echo "Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "Username: " . config('mail.mailers.smtp.username') . "\n\n";

    echo "Attempting to send test email...\n";
    \Illuminate\Support\Facades\Mail::raw('This is a test email from the hosted server.', function ($message) {
        $message->to('musiimentaagnes9@gmail.com')
                ->subject('Magna Credit - Live Server Test');
    });

    echo "✅ Success! Email sent successfully.\n";

} catch (\Throwable $e) {
    echo "❌ Mail sending failed!\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    echo "Error File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
