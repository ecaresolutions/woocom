<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['css', 'php'])) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'single_variation_wrap') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
