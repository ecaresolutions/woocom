<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'woocom_update_cart_qty') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
