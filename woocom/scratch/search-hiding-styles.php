<?php
$lines = file(__DIR__ . '/../woocommerce/single-product/add-to-cart/variable.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'display: none') !== false || strpos($line, 'single_add_to_cart_button') !== false || strpos($line, 'single_variation') !== false) {
        if ($num >= 300) {
            echo ($num + 1) . ': ' . trim($line) . "\n";
        }
    }
}
