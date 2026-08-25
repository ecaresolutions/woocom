<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        if (strpos($file->getPathname(), 'checkout') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
