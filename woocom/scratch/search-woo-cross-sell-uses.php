<?php
$lines = file(__DIR__ . '/../inc/woocommerce.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'woocom_get_cart_cross_sell_html') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
