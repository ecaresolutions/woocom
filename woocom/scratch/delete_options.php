<?php
require_once 'd:/laragon/www/woocom/wp-load.php';

$options_to_delete = array(
    'woocom_footer_information_links',
    'woocom_footer_shop_links',
    'woocom_footer_support_links',
    'woocom_footer_policy_links'
);

foreach ($options_to_delete as $option) {
    delete_option($option);
    echo "Deleted option: {$option}\n";
}
