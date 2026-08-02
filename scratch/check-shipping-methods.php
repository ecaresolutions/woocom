<?php
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/../../../../wp-load.php');

$shipping_zones = WC_Shipping_Zones::get_zones();
echo "Shipping Zones:\n";
foreach ($shipping_zones as $zone) {
    echo "Zone ID: " . $zone['zone_id'] . " - Name: " . $zone['zone_name'] . "\n";
    $methods = $zone['shipping_methods'];
    foreach ($methods as $method) {
        echo "  - Method: " . $method->id . " (Instance ID: " . $method->instance_id . ") - Title: " . $method->title . " - Enabled: " . $method->enabled . "\n";
        if (isset($method->cost)) {
            echo "    Cost: " . $method->cost . "\n";
        }
    }
}

// Also check the rest of the world zone
$default_zone = WC_Shipping_Zones::get_zone(0);
echo "Default Zone (Rest of the World):\n";
$methods = $default_zone->get_shipping_methods();
foreach ($methods as $method) {
    echo "  - Method: " . $method->id . " (Instance ID: " . $method->instance_id . ") - Title: " . $method->title . " - Enabled: " . $method->enabled . "\n";
    if (isset($method->cost)) {
        echo "    Cost: " . $method->cost . "\n";
    }
}
