<?php
$dir = dirname(__DIR__);
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($files as $file) {
    if ($file->isDir()) continue;
    $content = file_get_contents($file->getPathname());
    if (strpos($content, 'woocom_hero_slides') !== false) {
        echo "Found in: " . $file->getPathname() . "\n";
    }
}
