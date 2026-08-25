<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['php', 'js', 'css'])) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'Inside Dhaka') !== false || strpos($content, 'custom-shipping-ui') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
