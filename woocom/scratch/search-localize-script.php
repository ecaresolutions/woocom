<?php
$lines = file(__DIR__ . '/../functions.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'wp_localize_script') !== false || strpos($line, 'woocom_ajax') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
