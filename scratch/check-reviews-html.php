<?php
define('ABSPATH', dirname(dirname(dirname(dirname(__DIR__)))) . '/');
// Look at the contents of WooCommerce core's single-product-reviews.php if it exists in plugins
$plugin_path = ABSPATH . 'wp-content/plugins/woocommerce/templates/single-product-reviews.php';
if (file_exists($plugin_path)) {
    echo file_get_contents($plugin_path);
} else {
    // Search the plugins directory for it
    $dir = new RecursiveDirectoryIterator(ABSPATH . 'wp-content/plugins');
    $iterator = new RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() === 'single-product-reviews.php') {
            echo "Found in: " . $file->getPathname() . "\n";
            echo file_get_contents($file->getPathname());
            break;
        }
    }
}
