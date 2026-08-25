<?php
$lines = file(__DIR__ . '/../inc/woocommerce.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'woocommerce_single_variation') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
