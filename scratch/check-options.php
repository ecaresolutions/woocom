<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/wp-load.php';

echo "hero_banner_1: " . get_option('hero_banner_1') . "\n";
echo "hero_banner_2: " . get_option('hero_banner_2') . "\n";
echo "hero_side_banner: " . get_option('hero_side_banner') . "\n";
echo "woocom_hero_slides: " . get_option('woocom_hero_slides') . "\n";

$slides_decoded = json_decode(get_option('woocom_hero_slides'), true);
if (is_array($slides_decoded)) {
    echo "Decoded Slides:\n";
    print_r($slides_decoded);
}
