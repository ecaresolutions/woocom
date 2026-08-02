<?php
$lines = file(__DIR__ . '/../woocommerce/single-product/add-to-cart/variable.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'single_variation_wrap') !== false || strpos($line, 'premium-product-actions') !== false || strpos($line, 'style') !== false) {
        if ($num >= 235 && $num <= 280) {
            echo ($num + 1) . ': ' . trim($line) . "\n";
        }
    }
}
