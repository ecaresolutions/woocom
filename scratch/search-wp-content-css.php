<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../../../../wp-content/');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'css') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'single_variation') !== false && strpos($content, 'background') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
