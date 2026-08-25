<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'woocommerce_after_variations_table') !== false && strpos($content, 'add_action') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
