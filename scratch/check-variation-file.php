<?php
$file = __DIR__ . '/../woocommerce/single-product/add-to-cart/variation.php';
if (file_exists($file)) {
    echo "variation.php exists! Size: " . filesize($file) . " bytes\n";
    echo file_get_contents($file);
} else {
    echo "variation.php does NOT exist!\n";
}
