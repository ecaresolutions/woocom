<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['js', 'php'])) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'checkout-qty-plus') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
