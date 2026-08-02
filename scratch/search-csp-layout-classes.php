<?php
$lines = file(__DIR__ . '/../woocommerce/content-single-product.php');
foreach ($lines as $num => $line) {
    if (preg_match('/(bg-|border|rounded)/', $line)) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
