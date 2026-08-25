<?php
$lines = file(__DIR__ . '/../style.css');
foreach ($lines as $num => $line) {
    if (strpos($line, 'single_add_to_cart_button') !== false || strpos($line, 'variation') !== false || strpos($line, 'buy_now') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
