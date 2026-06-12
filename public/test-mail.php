<?php
// public/test-mail.php

header('Content-Type: text/plain; charset=utf-8');

$projectRoot = realpath(__DIR__ . '/../') . '/';

try {
    echo "Starting mail test...\n";
    
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
    echo "Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "From Address: " . config('mail.from.address') . "\n\n";

    echo "Attempting to send email via Mail facade...\n";
    
    \Illuminate\Support\Facades\Mail::raw('This is a test email to verify the SMTP mail configuration on the hosted server.', function ($message) {
        $message->to('musiimentaagnes9@gmail.com')
                ->subject('Magna Credit - Test SMTP Mail');
    });

    echo "✅ Success! Email sent successfully without errors.\n";

} catch (\Throwable $e) {
    echo "❌ Mail sending failed!\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    echo "Error File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
