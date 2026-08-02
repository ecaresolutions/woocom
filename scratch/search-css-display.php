<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['css', 'php', 'js'])) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'single_add_to_cart_button') !== false && strpos($content, 'display') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
