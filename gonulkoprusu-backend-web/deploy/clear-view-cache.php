<?php
header('Content-Type: text/plain; charset=utf-8');

$scriptDir = __DIR__;
$candidateRoots = [
    $scriptDir,
    dirname($scriptDir),
    dirname($scriptDir) . '/public',
];

$viewDirs = [];
foreach ($candidateRoots as $root) {
    $viewDir = $root . '/storage/framework/views';
    if (is_dir($viewDir)) {
        $realPath = realpath($viewDir) ?: $viewDir;
        $viewDirs[$realPath] = $viewDir;
    }
}

$artisanOutput = null;
foreach ($candidateRoots as $root) {
    if (!file_exists($root . '/vendor/autoload.php') || !file_exists($root . '/bootstrap/app.php')) {
        continue;
    }

    try {
        require_once $root . '/vendor/autoload.php';
        $app = require $root . '/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $artisanOutput = \Illuminate\Support\Facades\Artisan::output();
    } catch (Throwable $e) {
        $artisanOutput = 'Artisan view:clear calistirilamadi: ' . $e->getMessage() . "\n";
    }

    break;
}

$cleared = 0;
foreach ($viewDirs as $viewDir) {
    foreach (glob($viewDir . '/*.php') ?: [] as $file) {
        if (is_file($file) && @unlink($file)) {
            $cleared++;
        }
    }
}

if ($artisanOutput !== null) {
    echo $artisanOutput;
}

echo "Silinen derlenmis view: $cleared\n";
echo "View onbellegi temizlendi.\n";
