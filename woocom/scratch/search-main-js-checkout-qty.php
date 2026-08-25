<?php
$lines = file(__DIR__ . '/../assets/js/main.js');
foreach ($lines as $num => $line) {
    if (strpos($line, 'checkout-qty-plus') !== false || strpos($line, 'checkout-qty-minus') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
