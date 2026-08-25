<?php
$lines = file(__DIR__ . '/../inc/custom-css.php');
foreach ($lines as $num => $line) {
    if (preg_match('/(single-variation|single_variation|woocommerce-variation)/i', $line)) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
