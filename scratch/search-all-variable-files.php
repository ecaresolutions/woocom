<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../../../');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getFilename() === 'variable.php') {
        echo $file->getPathname() . "\n";
    }
}
