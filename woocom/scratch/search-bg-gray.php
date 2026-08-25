<?php
$lines = file(__DIR__ . '/../woocommerce/single-product/add-to-cart/variable.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'bg-gray-50') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
