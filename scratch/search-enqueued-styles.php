<?php
$lines = file(__DIR__ . '/../functions.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'wp_enqueue_style') !== false || strpos($line, 'wp_enqueue_script') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
