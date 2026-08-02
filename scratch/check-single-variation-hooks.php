<?php
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/../../../../wp-load.php');

global $wp_filter;
$tag = 'woocommerce_single_variation';
if (isset($wp_filter[$tag])) {
    echo "Hooks for $tag:\n";
    foreach ($wp_filter[$tag]->callbacks as $priority => $callbacks) {
        echo "Priority: $priority\n";
        foreach ($callbacks as $id => $callback) {
            echo "  - Callback: " . $id . "\n";
        }
    }
} else {
    echo "No hooks for $tag\n";
}
