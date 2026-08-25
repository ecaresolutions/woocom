<?php
require_once 'd:/laragon/www/woocom/wp-load.php';

$pages_to_create = array(
    'about-us' => array(
        'title'   => 'About Us',
        'content' => '<p>Welcome to Jashori Food! We are dedicated to providing safe, healthy, and reliable organic foods directly to your doorstep. Our mission is to connect farmers with families, ensuring premium quality food items for everyone.</p>',
    ),
    'faq' => array(
        'title'   => 'FAQ',
        'content' => '<h2>Frequently Asked Questions</h2>
                     <h3>How do I place an order?</h3>
                     <p>Simply select your favorite products, add them to your cart, and click Checkout. Fill in your delivery details and choose your payment method.</p>
                     <h3>What are your delivery hours?</h3>
                     <p>We deliver from 9:00 AM to 8:00 PM every day.</p>
                     <h3>What is your helpline number?</h3>
                     <p>You can call us directly at +8801700934555.</p>',
    ),
    'return-policy' => array(
        'title'   => 'Return Policy',
        'content' => '<h2>Return & Refund Policy</h2>
                     <p>At Jashori Food, customer satisfaction is our top priority. If you receive any damaged or incorrect product, you can request a return or exchange within 24 hours of delivery.</p>
                     <p>Please contact us at +8801700934555 or email support@jashorifood.com with your order details to process your refund or replacement.</p>',
    ),
);

foreach ($pages_to_create as $slug => $page) {
    $existing = get_page_by_path($slug);
    if (!$existing) {
        $post_id = wp_insert_post(array(
            'post_title'   => $page['title'],
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_name'    => $slug,
        ));
        echo "Created page: {$page['title']} (ID: {$post_id})\n";
    } else {
        echo "Page already exists: {$page['title']}\n";
    }
}
