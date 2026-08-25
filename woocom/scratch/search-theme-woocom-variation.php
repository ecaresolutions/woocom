<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['css', 'php'])) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'woocommerce-variation') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
