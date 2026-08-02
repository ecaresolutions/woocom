<?php
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/../../../../wp-load.php');

// Simulate the AJAX call
$_REQUEST['cart_item_key'] = 'dummy_key';
$_REQUEST['qty'] = 2;
$_REQUEST['nonce'] = wp_create_nonce('woocom_cart_nonce');

// Capture wp_send_json output
ob_start();
try {
    woocom_update_cart_qty();
} catch (Exception $e) {
    echo "Caught: " . $e->getMessage() . "\n";
}
$output = ob_get_clean();
echo "Output: " . $output . "\n";
