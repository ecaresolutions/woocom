<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'contact_address') !== false && basename($file->getPathname()) !== 'search-settings.php') {
            echo $file->getPathname() . "\n";
        }
    }
}
