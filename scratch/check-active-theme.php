<?php
// Load WordPress bootstrap
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/../../../../wp-load.php');

echo "Active stylesheet (theme folder): " . get_stylesheet() . "\n";
echo "Active template (parent theme): " . get_template() . "\n";
