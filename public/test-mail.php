<?php
// public/test-mail.php

header('Content-Type: text/plain; charset=utf-8');

function checkConnection($host, $port, $timeout = 2) {
    echo "Checking connection to $host:$port ... ";
    $start = microtime(true);
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    $elapsed = round(microtime(true) - $start, 3);
    if (!$fp) {
        echo "❌ FAILED ($errno: $errstr) in {$elapsed}s\n";
        return false;
    } else {
        echo "✅ SUCCESS in {$elapsed}s\n";
        fclose($fp);
        return true;
    }
}

echo "SMTP PORT CONNECTIVITY TESTS\n";
echo "=============================\n";
checkConnection('smtp.gmail.com', 587);
checkConnection('smtp.gmail.com', 465);
checkConnection('smtp.gmail.com', 25);
checkConnection('127.0.0.1', 25);
checkConnection('localhost', 25);
checkConnection('localhost', 587);
echo "=============================\n";
echo "Tests completed.\n";
