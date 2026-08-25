<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'js') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'location.reload') !== false || strpos($content, 'window.location') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
