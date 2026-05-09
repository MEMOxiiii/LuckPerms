<?php

/**
 * Build script for LuckPerms PHAR
 */

$pharPath = 'LuckPerms.phar';

// Remove old PHAR if it exists
if(file_exists($pharPath)) {
    unlink($pharPath);
}

$phar = new Phar($pharPath);
$phar->setSignatureAlgorithm(Phar::SHA512);

// Add src directory
$srcDir = new RecursiveDirectoryIterator('src');
$iter = new RecursiveIteratorIterator($srcDir, RecursiveIteratorIterator::LEAVES_ONLY);

foreach($iter as $file) {
    $filePath = $file->getPathname();
    if(is_file($filePath)) {
        $localPath = substr($filePath, 0, 4) === './src' ? substr($filePath, 2) : 'src/' . basename($filePath);
        $phar[$localPath] = file_get_contents($filePath);
    }
}

// Add plugin.yml
if(file_exists('plugin.yml')) {
    $phar['plugin.yml'] = file_get_contents('plugin.yml');
}

// Add resources
$resourcesDir = 'resources';
if(is_dir($resourcesDir)) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($resourcesDir, RecursiveIteratorIterator::LEAVES_ONLY));
    foreach($iter as $file) {
        if(is_file($file)) {
            $localPath = substr($file->getPathname(), 0, 2) === '.\'' ? substr($file->getPathname(), 2) : $file->getPathname();
            $phar[$localPath] = file_get_contents($file);
        }
    }
}

// Set stub
$stub = '<?php require "phar://LuckPerms.phar/src/LuckPerms.php"; __HALT_COMPILER();';
$phar->setStub($stub);

echo "✓ PHAR built successfully: $pharPath\n";
