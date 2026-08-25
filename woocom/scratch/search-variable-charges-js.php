<?php
$lines = file(__DIR__ . '/../woocommerce/single-product/add-to-cart/variable.php');
foreach ($lines as $num => $line) {
    if (preg_match('/(charges|packaging|courier)/i', $line)) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
