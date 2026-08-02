<?php
$lines = file(__DIR__ . '/../woocommerce/single-product/add-to-cart/variable.php');
foreach ($lines as $num => $line) {
    if (preg_match('/(bg-|border|rounded)/', $line) && !preg_match('/(premium-|style>)/', $line)) {
        if ($num >= 100 && $num <= 300) {
            echo ($num + 1) . ': ' . trim($line) . "\n";
        }
    }
}
