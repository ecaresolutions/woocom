<?php
$lines = file(__DIR__ . '/../style.css');
foreach ($lines as $num => $line) {
    if (strpos($line, 'woocommerce-variation') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
