<?php
$content = file_get_contents(__DIR__ . '/../inc/woocommerce.php');
if (strpos($content, 'woocommerce_thankyou') !== false) {
    echo "Found woocommerce_thankyou in inc/woocommerce.php\n";
}
$content = file_get_contents(__DIR__ . '/../functions.php');
if (strpos($content, 'woocommerce_thankyou') !== false) {
    echo "Found woocommerce_thankyou in functions.php\n";
}
