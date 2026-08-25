<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../woocommerce');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile()) {
        echo str_replace(realpath(__DIR__ . '/../'), '', $file->getPathname()) . "\n";
    }
}
