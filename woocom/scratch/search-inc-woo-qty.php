<?php
$lines = file(__DIR__ . '/../inc/woocommerce.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'woocom_update_cart_qty') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
