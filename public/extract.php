<?php
// public/extract.php — Pull Deployment Webhook

set_time_limit(300);
header('Content-Type: text/html; charset=utf-8');
ob_implicit_flush(true);
if (ob_get_level()) ob_end_flush();

$projectRoot = realpath(__DIR__ . '/../') . '/';

// ── Authentication ──────────────────────────────────────────────────
$token = $_GET['token'] ?? '';
$validToken = null;

// Try getenv / $_ENV first
$validToken = getenv('DEPLOY_TOKEN') ?: ($_ENV['DEPLOY_TOKEN'] ?? null);

// Fallback: read directly from .env file
if (!$validToken) {
    $envFile = $projectRoot . '.env';
    if (file_exists($envFile)) {
        $envContents = file_get_contents($envFile);
        if (preg_match('/^DEPLOY_TOKEN=(.*)$/m', $envContents, $matches)) {
            $validToken = trim($matches[1]);
        }
    }
}

if (!$validToken || $token !== $validToken) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

function logStep(string $msg): void {
    echo $msg . "<br>\n";
    flush();
}

logStep("🚀 Starting Pull Deployment...");

// ── 1. Download ZIP from GitHub ─────────────────────────────────────
$zipUrl  = 'https://github.com/Musiimenta-Agnes/Magna-Credit-Backend/archive/refs/heads/master.zip';
$zipFile = $projectRoot . 'release.zip';

logStep("📥 Downloading release from GitHub...");

$ctx = stream_context_create(['http' => [
    'timeout' => 120,
    'user_agent' => 'MagnaCredit-Deploy/1.0'
]]);
$zipData = @file_get_contents($zipUrl, false, $ctx);
if ($zipData === false) {
    http_response_code(500);
    die("❌ Failed to download zip from GitHub.");
}
file_put_contents($zipFile, $zipData);
logStep("✅ Downloaded " . round(strlen($zipData) / 1024) . " KB");

// ── 2. Extract ZIP ──────────────────────────────────────────────────
logStep("📦 Unzipping files...");
$zip = new ZipArchive;
if ($zip->open($zipFile) !== true) {
    http_response_code(500);
    die("❌ Failed to open zip file.");
}
$zip->extractTo($projectRoot);
$zip->close();

$extractDir = $projectRoot . 'Magna-Credit-Backend-master';

// ── 3. Preserve .env — NEVER overwrite the live .env ────────────────
// Remove .env and .env.example from extracted folder so they don't clobber the live config
@unlink($extractDir . '/.env');
@unlink($extractDir . '/.env.example');

logStep("📂 Moving files out of subdirectory...");
exec('cp -a ' . escapeshellarg($extractDir . '/.') . ' ' . escapeshellarg($projectRoot) . ' 2>&1', $cpOut);
exec('rm -rf ' . escapeshellarg($extractDir) . ' 2>&1');
@unlink($zipFile);

logStep("✅ Files extracted successfully.");

// ── 4. Composer Install ─────────────────────────────────────────────
logStep("🎵 Running Composer Install...");

$composerPhar = $projectRoot . 'composer.phar';

// Only download composer.phar if it doesn't exist or is older than 7 days
if (!file_exists($composerPhar) || filemtime($composerPhar) < time() - 604800) {
    logStep("   ↳ Downloading composer.phar...");
    $composerData = @file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar');
    if ($composerData === false) {
        die("❌ Failed to download composer.phar");
    }
    file_put_contents($composerPhar, $composerData);
}

$output = [];
$return_var = 0;
$composerCmd = 'cd ' . escapeshellarg($projectRoot)
    . ' && export COMPOSER_HOME=' . escapeshellarg($projectRoot)
    . ' && php composer.phar install --optimize-autoloader --no-dev --no-interaction 2>&1';
exec($composerCmd, $output, $return_var);

logStep("<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>");

if ($return_var !== 0) {
    die("❌ Composer install failed (exit code: $return_var). Cannot continue.");
}
logStep("✅ Composer install succeeded.");

// ── 5. Storage directories & symlink ────────────────────────────────
logStep("📁 Ensuring storage directories exist...");
$storageDirs = [
    $projectRoot . 'storage/framework/cache/data',
    $projectRoot . 'storage/framework/sessions',
    $projectRoot . 'storage/framework/views',
    $projectRoot . 'storage/logs',
];
foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Create storage symlink if missing
$storageLink = $projectRoot . 'public/storage';
if (!file_exists($storageLink)) {
    @symlink($projectRoot . 'storage/app/public', $storageLink);
    logStep("   ↳ Created storage symlink.");
}

// ── 6. Laravel Artisan Commands ─────────────────────────────────────
logStep("⚙️ Running Laravel setup...");

try {
    require $projectRoot . 'vendor/autoload.php';
    $app = require $projectRoot . 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Clear all caches
    $kernel->call('optimize:clear');
    logStep("   ↳ Caches cleared.");

    // Run database migrations
    logStep("🗃️ Running migrations...");
    $kernel->call('migrate', ['--force' => true]);
    $migrationOutput = \Illuminate\Support\Facades\Artisan::output();
    logStep("<pre>" . htmlspecialchars($migrationOutput) . "</pre>");

    // Rebuild optimized caches for production
    $kernel->call('config:cache');
    $kernel->call('route:cache');
    $kernel->call('view:cache');
    logStep("   ↳ Caches rebuilt for production.");

    logStep("🎉 <strong>Deployment Complete!</strong>");

} catch (\Throwable $e) {
    logStep("❌ <strong>Fatal Error:</strong> " . htmlspecialchars($e->getMessage()));
    logStep("<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>");
}
