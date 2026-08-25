<?php
// Search for custom callbacks for comments or reviews in the theme
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'wp_list_comments') !== false || strpos($content, 'commentlist') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
