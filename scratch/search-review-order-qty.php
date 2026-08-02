<?php
$lines = file(__DIR__ . '/../woocommerce/checkout/review-order.php');
foreach ($lines as $num => $line) {
    if (preg_match('/(qty|quantity|plus|minus|input)/i', $line)) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
