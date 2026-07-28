<?php
/**
 * Unified Theme Settings Page
 *
 * @package Woocom
 */

// Add Unified Menu Item
add_action('admin_menu', 'woocom_unified_theme_settings_menu', 10);
function woocom_unified_theme_settings_menu() {
    add_menu_page(
        'Woocom Settings',
        'Woocom Settings',
        'manage_options',
        'woocom-settings',
        'woocom_unified_theme_settings_page',
        'dashicons-admin-generic',
        60
    );

    // Add submenus for better access if needed, but we use tabs for everything
    add_submenu_page(
        'woocom-settings',
        'Theme Settings',
        'Theme Settings',
        'manage_options',
        'woocom-settings',
        'woocom_unified_theme_settings_page'
    );
}

// Enqueue Media Scripts for Image Uploads
add_action('admin_enqueue_scripts', 'woocom_admin_settings_scripts');
function woocom_admin_settings_scripts($hook) {
    if (strpos($hook, 'woocom-settings') === false) return;
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

add_filter('wp_redirect', 'woocom_keep_settings_tab_after_save', 10, 2);
function woocom_keep_settings_tab_after_save($location, $status) {
    if (
        !is_admin() ||
        empty($_POST['option_page']) ||
        $_POST['option_page'] !== 'woocom-settings-group' ||
        empty($_POST['woocom_active_tab'])
    ) {
        return $location;
    }

    $allowed_tabs = array('branding', 'header', 'ticker', 'banners', 'layout', 'collections', 'cart', 'contact', 'footer', 'language', 'analytics');
    $active_tab = sanitize_key(wp_unslash($_POST['woocom_active_tab']));

    if (!in_array($active_tab, $allowed_tabs, true)) {
        $active_tab = 'branding';
    }

    return add_query_arg(
        array(
            'page' => 'woocom-settings',
            'tab' => $active_tab,
            'settings-updated' => 'true',
        ),
        admin_url('admin.php')
    );
}

// Register All Settings in one group
add_action('admin_init', 'woocom_register_unified_settings');
function woocom_register_unified_settings() {
    // General Settings
    register_setting('woocom-settings-group', 'theme_logo');
    register_setting('woocom-settings-group', 'footer_logo');
    register_setting('woocom-settings-group', 'woocom_primary_color');
    register_setting('woocom-settings-group', 'woocom_secondary_color');
    register_setting('woocom-settings-group', 'woocom_main_background_color', array(
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#FBF9F5',
    ));
    register_setting('woocom-settings-group', 'contact_phone');
    register_setting('woocom-settings-group', 'contact_email');
    register_setting('woocom-settings-group', 'contact_address');
    register_setting('woocom-settings-group', 'social_facebook');
    register_setting('woocom-settings-group', 'social_instagram');
    register_setting('woocom-settings-group', 'social_twitter');
    register_setting('woocom-settings-group', 'social_youtube');

    // Font Settings
    register_setting('woocom-settings-group', 'woocom_font_bengali');
    register_setting('woocom-settings-group', 'woocom_font_english');

    // Header & Navigation
    register_setting('woocom-settings-group', 'sticky_header');
    register_setting('woocom-settings-group', 'nav_bg_color');
    register_setting('woocom-settings-group', 'nav_text_color');
    register_setting('woocom-settings-group', 'nav_hover_color');
    register_setting('woocom-settings-group', 'nav_vertical_padding');

    // Homepage Banners (legacy individual options kept for backward compat)
    register_setting('woocom-settings-group', 'hero_banner_1');
    register_setting('woocom-settings-group', 'hero_banner_1_link');
    register_setting('woocom-settings-group', 'hero_banner_2');
    register_setting('woocom-settings-group', 'hero_banner_2_link');
    register_setting('woocom-settings-group', 'hero_side_banner');
    register_setting('woocom-settings-group', 'hero_side_banner_link');
    register_setting('woocom-settings-group', 'promo_banner_1');
    register_setting('woocom-settings-group', 'promo_banner_1_link');
    register_setting('woocom-settings-group', 'promo_banner_2');
    register_setting('woocom-settings-group', 'promo_banner_2_link');
    // Dynamic hero slides (JSON array)
    register_setting('woocom-settings-group', 'woocom_hero_slides', 'woocom_sanitize_hero_slides');

    // Homepage Layout Visibility
    register_setting('woocom-settings-group', 'show_hero_section');
    register_setting('woocom-settings-group', 'show_featured_categories');
    register_setting('woocom-settings-group', 'show_top_selling');
    register_setting('woocom-settings-group', 'show_category_sections');
    register_setting('woocom-settings-group', 'show_combo_offers');
    register_setting('woocom-settings-group', 'woocom_show_just_for_you');
    register_setting('woocom-settings-group', 'show_dual_banners');

    // Content Settings
    register_setting('woocom-settings-group', 'woocom_combo_title');
    register_setting('woocom-settings-group', 'woocom_combo_image');
    register_setting('woocom-settings-group', 'woocom_top_selling_title');
    register_setting('woocom-settings-group', 'woocom_top_selling_image');
    register_setting('woocom-settings-group', 'woocom_featured_orderby');
    register_setting('woocom-settings-group', 'woocom_featured_order');
    register_setting('woocom-settings-group', 'woocom_latest_orderby');
    register_setting('woocom-settings-group', 'woocom_latest_order');
    register_setting('woocom-settings-group', 'woocom_just_for_you_title');
    register_setting('woocom-settings-group', 'woocom_just_for_you_image');

    // Product Collections (Arrays)
    register_setting('woocom-settings-group', 'woocom_featured_categories');
    register_setting('woocom-settings-group', 'woocom_category_sections');
    register_setting('woocom-settings-group', 'woocom_combo_bundles', 'woocom_sanitize_combo_bundles');
    // Cart & Checkout
    register_setting('woocom-settings-group', 'enable_cart_drawer');
    register_setting('woocom-settings-group', 'cart_drawer_floating_visibility');
    register_setting('woocom-settings-group', 'cart_drawer_title');
    register_setting('woocom-settings-group', 'cart_promo_enabled');
    register_setting('woocom-settings-group', 'cart_promo_title');
    register_setting('woocom-settings-group', 'cart_promo_min_amount');
    register_setting('woocom-settings-group', 'show_cross_sell');
    register_setting('woocom-settings-group', 'cross_sell_title');
    register_setting('woocom-settings-group', 'cross_sell_autoslide');
    register_setting('woocom-settings-group', 'checkout_button_shake');
    register_setting('woocom-settings-group', 'sticky_checkout_mobile');
    register_setting('woocom-settings-group', 'product_add_to_cart_button_color');
    register_setting('woocom-settings-group', 'product_buy_now_button_color');
    register_setting('woocom-settings-group', 'product_whatsapp_button_color');
    register_setting('woocom-settings-group', 'product_call_button_color');
    register_setting('woocom-settings-group', 'variation_unavailable_message');
    // Product Action Buttons Text (Language)
    register_setting('woocom-settings-group', 'woocom_text_add_to_cart', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_text_buy_now', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_text_see_details', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_text_stock_out', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_text_pre_order', 'sanitize_text_field');

    // Footer Settings
    register_setting('woocom-settings-group', 'woocom_footer_information_title', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_footer_shop_title', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_footer_support_title', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_footer_policy_title', 'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_footer_information_links', 'woocom_sanitize_footer_links');
    register_setting('woocom-settings-group', 'woocom_footer_shop_links', 'woocom_sanitize_footer_links');
    register_setting('woocom-settings-group', 'woocom_footer_support_links', 'woocom_sanitize_footer_links');
    register_setting('woocom-settings-group', 'woocom_footer_policy_links', 'woocom_sanitize_footer_links');

    // Contact — WhatsApp number (international format for wa.me links)
    register_setting('woocom-settings-group', 'woocom_whatsapp_number', 'sanitize_text_field');

    // Analytics Settings
    register_setting('woocom-settings-group', 'woocom_enable_gtm');
    register_setting('woocom-settings-group', 'woocom_gtm_id',     'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_enable_ga4');
    register_setting('woocom-settings-group', 'woocom_ga4_id',     'sanitize_text_field');
    register_setting('woocom-settings-group', 'woocom_enable_pixel');
    register_setting('woocom-settings-group', 'woocom_pixel_id',   'sanitize_text_field');

    // Ticker Settings
    register_setting('woocom-settings-group', 'ticker_enabled');
    register_setting('woocom-settings-group', 'ticker_text');
    register_setting('woocom-settings-group', 'ticker_bg_color', array('default' => '#1E5D02'));
    register_setting('woocom-settings-group', 'ticker_text_color', array('default' => '#ffffff'));
    register_setting('woocom-settings-group', 'ticker_speed', array('default' => '20', 'sanitize_callback' => 'woocom_sanitize_ticker_speed'));
    register_setting('woocom-settings-group', 'ticker_font_size', array('default' => '14'));
    register_setting('woocom-settings-group', 'ticker_padding', array('default' => '8'));
    register_setting('woocom-settings-group', 'ticker_icon', array('default' => 'mango'));
}

function woocom_sanitize_ticker_speed($val) {
    $val = intval($val);
    if ($val < 3) return 3;
    if ($val > 120) return 120;
    return $val;
}

function woocom_sanitize_footer_links($links) {
    $sanitized = array();

    if (!is_array($links)) {
        return $sanitized;
    }

    foreach ($links as $link) {
        $label = isset($link['label']) ? sanitize_text_field($link['label']) : '';
        $url = isset($link['url']) ? esc_url_raw($link['url']) : '';

        if ($label === '' && $url === '') {
            continue;
        }

        $sanitized[] = array(
            'label' => $label,
            'url' => $url,
        );
    }

    return $sanitized;
}

function woocom_sanitize_combo_bundles( $bundles ) {
    if ( ! is_array( $bundles ) ) return array();
    $sanitized = array();
    foreach ( $bundles as $bundle ) {
        if ( ! is_array( $bundle ) ) continue;
        $title    = sanitize_text_field( isset( $bundle['title'] )  ? $bundle['title']  : '' );
        $price    = sanitize_text_field( isset( $bundle['price'] )  ? $bundle['price']  : '' );
        $image    = esc_url_raw( isset( $bundle['image'] )           ? $bundle['image']  : '' );
        $products = array();
        if ( ! empty( $bundle['products'] ) && is_array( $bundle['products'] ) ) {
            $products = array_values( array_filter( array_map( 'absint', $bundle['products'] ) ) );
        }
        if ( empty( $title ) && empty( $products ) ) continue;
        $sanitized[] = array( 'title' => $title, 'price' => $price, 'image' => $image, 'products' => $products );
    }
    return $sanitized;
}

function woocom_get_default_footer_links($group = '') {
    $defaults = array(
        'information' => array(
            array('label' => 'About us', 'url' => home_url('/about-us/')),
            array('label' => 'Contact us', 'url' => home_url('/contact/')),
            array('label' => 'Company Information', 'url' => home_url('/company-information/')),
            array('label' => 'Our Stories', 'url' => home_url('/our-stories/')),
            array('label' => 'Terms & Conditions', 'url' => home_url('/terms-conditions/')),
            array('label' => 'Privacy Policy', 'url' => home_url('/privacy-policy/')),
            array('label' => 'Careers', 'url' => home_url('/careers/')),
        ),
        'shop' => array(
            array('label' => 'Oil & Ghee', 'url' => home_url('/product-category/oil-ghee/')),
            array('label' => 'Honey', 'url' => home_url('/product-category/honey/')),
            array('label' => 'Dates', 'url' => home_url('/product-category/dates/')),
            array('label' => 'Spices', 'url' => home_url('/product-category/spices/')),
            array('label' => 'Nuts & Seeds', 'url' => home_url('/product-category/nuts-seeds/')),
            array('label' => 'Beverage', 'url' => home_url('/product-category/beverage/')),
            array('label' => 'Functional Foods', 'url' => home_url('/product-category/functional-foods/')),
        ),
        'support' => array(
            array('label' => 'Support Center', 'url' => home_url('/support-center/')),
            array('label' => 'How to Order', 'url' => home_url('/how-to-order/')),
            array('label' => 'Order Tracking', 'url' => home_url('/order-tracking/')),
            array('label' => 'Payment', 'url' => home_url('/payment/')),
            array('label' => 'Shipping', 'url' => home_url('/shipping/')),
            array('label' => 'FAQ', 'url' => home_url('/faq/')),
        ),
        'policy' => array(
            array('label' => 'Happy Return', 'url' => home_url('/happy-return/')),
            array('label' => 'Refund Policy', 'url' => home_url('/return-policy/')),
            array('label' => 'Exchange', 'url' => home_url('/exchange/')),
            array('label' => 'Cancellation', 'url' => home_url('/cancellation/')),
            array('label' => 'Pre-Order', 'url' => home_url('/pre-order/')),
            array('label' => 'Extra Discount', 'url' => home_url('/extra-discount/')),
        ),
    );

    return $group && isset($defaults[$group]) ? $defaults[$group] : $defaults;
}

function woocom_get_default_footer_titles($group = '') {
    $defaults = array(
        'information' => 'Information',
        'shop'        => 'Shop By',
        'support'     => 'Support',
        'policy'      => 'Consumer Policy',
    );

    return $group && isset($defaults[$group]) ? $defaults[$group] : $defaults;
}

function woocom_get_footer_title($group) {
    $option_map = array(
        'information' => 'woocom_footer_information_title',
        'shop'        => 'woocom_footer_shop_title',
        'support'     => 'woocom_footer_support_title',
        'policy'      => 'woocom_footer_policy_title',
    );

    if (!isset($option_map[$group])) {
        return '';
    }

    $title = get_option($option_map[$group], woocom_get_default_footer_titles($group));
    return $title !== '' ? $title : woocom_get_default_footer_titles($group);
}

function woocom_get_footer_links($group) {
    $option_map = array(
        'information' => 'woocom_footer_information_links',
        'shop' => 'woocom_footer_shop_links',
        'support' => 'woocom_footer_support_links',
        'policy' => 'woocom_footer_policy_links',
    );

    if (!isset($option_map[$group])) {
        return array();
    }

    $links = get_option($option_map[$group], woocom_get_default_footer_links($group));

    if (!is_array($links) || empty($links)) {
        $links = woocom_get_default_footer_links($group);
    }

    return array_filter($links, function($link) {
        return !empty($link['label']);
    });
}

// Unified Settings Page Output
function woocom_unified_theme_settings_page() {
    $allowed_tabs = array('branding', 'header', 'ticker', 'banners', 'layout', 'collections', 'cart', 'contact', 'footer', 'language', 'analytics');
    $active_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'branding';
    if (!in_array($active_tab, $allowed_tabs, true)) {
        $active_tab = 'branding';
    }
    
    // Get Categories & Products for selections
    $categories = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false));
    $products = get_posts(array('post_type' => 'product', 'posts_per_page' => 100, 'post_status' => 'publish'));

    ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        :root {
            --admin-primary: #1E5D02;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
        }
        .woocom-admin-wrap {
            margin: 30px 20px 30px 0;
            max-width: 1260px;
            font-family: 'Inter', -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .woocom-admin-header {
            background: linear-gradient(135deg, var(--admin-primary) 0%, #154502 100%);
            padding: 45px 40px;
            border-radius: 24px 24px 0 0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(30, 93, 2, 0.15);
        }
        .woocom-admin-header::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .woocom-admin-header h1 {
            color: white;
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .woocom-admin-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .woocom-admin-container {
            display: grid;
            grid-template-columns: 240px 1fr;
            background: white;
            border-radius: 0 0 16px 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            min-height: 600px;
        }
        /* Sidebar Navigation */
        .woocom-admin-sidebar {
            background: #f8fafc;
            padding: 25px 0;
            border-right: 1px solid var(--border);
        }
        .woocom-admin-nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 15px 30px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            margin-bottom: 4px;
        }
        .woocom-admin-nav-item:hover {
            background: #f1f5f9;
            color: var(--admin-primary);
            padding-left: 34px;
        }
        .woocom-admin-nav-item.active {
            background: white;
            color: var(--admin-primary);
            border-left-color: var(--admin-primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .woocom-admin-nav-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
            opacity: 0.8;
        }
        .woocom-admin-nav-item.active i {
            opacity: 1;
        }

        /* Content Area */
        .woocom-admin-content {
            padding: 40px;
            background: white;
        }
        .section-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
        }
        .section-header h2 {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        .section-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Form Elements */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 240px 1fr;
            align-items: start;
            gap: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            padding-top: 10px;
        }
        .form-input-text {
            width: 100%;
            max-width: 500px;
            padding: 10px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-input-text:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(30, 93, 2, 0.1);
            outline: none;
        }
        
        /* Modern Switch Toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1; transition: .4s; border-radius: 34px;
        }
        .slider:before {
            position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px;
            background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        input:checked + .slider { background-color: var(--admin-primary); }
        input:checked + .slider:before { transform: translateX(24px); }

        /* Grid for Categories/Products */
        .selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 15px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            max-height: 400px;
            overflow-y: auto;
        }
        .selection-item {
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
        }
        .selection-item:hover { border-color: var(--admin-primary); transform: translateY(-1px); }
        .selection-item span { font-size: 13px; font-weight: 500; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }

        /* Banner Preview */
        .banner-preview {
            width: 100%;
            max-width: 500px;
            min-height: 140px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 12px;
            overflow: hidden;
            border: 2px dashed #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .banner-preview:hover {
            border-color: var(--admin-primary);
            background: #f1f5f9;
        }
        .banner-preview img { 
            max-width: 100%; 
            max-height: 250px; 
            object-fit: contain; 
            display: block;
        }
        .size-hint {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
            font-style: italic;
        }

        /* Submit Area */
        .submit-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        .woocom-save-btn {
            background: var(--admin-primary) !important;
            border: none !important;
            padding: 14px 40px !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 15px rgba(30, 93, 2, 0.2) !important;
            transition: all 0.3s ease !important;
        }
        .woocom-save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 93, 2, 0.3) !important;
        }

        /* View Site Button */
        .view-site-btn {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            text-decoration: none !important;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .view-site-btn:hover {
            background: white;
            color: var(--admin-primary) !important;
            border-color: white;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }
        .view-site-btn svg {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }
        .view-site-btn:hover svg {
            transform: rotate(45deg);
        }

        /* Responsive Previews */
        .banner-preview img, .logo-preview img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }
        .logo-preview {
            max-width: 200px;
            padding: 15px;
            background: #f1f5f9;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
            margin-bottom: 10px;
        }
    </style>

    <div class="woocom-admin-wrap">
        <div class="woocom-admin-header">
            <div>
                <h1>Theme Control Center</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.8; font-weight: 500;">Master Dashboard for Woocom Theme v<?php echo WOOCOM_VERSION; ?> by <strong>Ecare Solution</strong></p>
            </div>
            <div class="header-actions">
                <a href="<?php echo home_url(); ?>" target="_blank" class="view-site-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                    View Site
                </a>
            </div>
        </div>

        <div class="woocom-admin-container">
            <div class="woocom-admin-sidebar">
                <a href="?page=woocom-settings&tab=branding" class="woocom-admin-nav-item <?php echo $active_tab == 'branding' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-art"></span> Branding & Colors
                </a>
                <a href="?page=woocom-settings&tab=header" class="woocom-admin-nav-item <?php echo $active_tab == 'header' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-menu-alt3"></span> Header & Nav
                </a>
                <a href="?page=woocom-settings&tab=ticker" class="woocom-admin-nav-item <?php echo $active_tab == 'ticker' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-megaphone"></span> Announcement Ticker
                </a>
                <a href="?page=woocom-settings&tab=banners" class="woocom-admin-nav-item <?php echo $active_tab == 'banners' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-images-alt2"></span> Home Banners
                </a>
                <a href="?page=woocom-settings&tab=layout" class="woocom-admin-nav-item <?php echo $active_tab == 'layout' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-layout"></span> Home Sections
                </a>
                <a href="?page=woocom-settings&tab=collections" class="woocom-admin-nav-item <?php echo $active_tab == 'collections' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-archive"></span> Product Collections
                </a>
                <a href="?page=woocom-settings&tab=cart" class="woocom-admin-nav-item <?php echo $active_tab == 'cart' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-cart"></span> Cart & Checkout
                </a>
                <a href="?page=woocom-settings&tab=contact" class="woocom-admin-nav-item <?php echo $active_tab == 'contact' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-phone"></span> Contact & Social
                </a>
                <a href="?page=woocom-settings&tab=footer" class="woocom-admin-nav-item <?php echo $active_tab == 'footer' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-editor-kitchensink"></span> Footer
                </a>
                <a href="?page=woocom-settings&tab=language" class="woocom-admin-nav-item <?php echo $active_tab == 'language' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-translation"></span> Language & Texts
                </a>
                <a href="?page=woocom-settings&tab=analytics" class="woocom-admin-nav-item <?php echo $active_tab == 'analytics' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-chart-bar"></span> Analytics & Tracking
                </a>
            </div>

            <div class="woocom-admin-content">
                <form method="post" action="options.php">
                    <?php settings_fields('woocom-settings-group'); ?>
                    <input type="hidden" name="woocom_active_tab" value="<?php echo esc_attr($active_tab); ?>">

                    <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : ?>
                        <div class="notice notice-success is-dismissible" style="margin: 0 0 24px; border-left-color: #16a34a;">
                            <p><strong>Settings saved successfully.</strong></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($active_tab == 'branding') : ?>
                        <div class="section-header">
                            <h2>Visual Identity</h2>
                            <p>Manage your site logo and global brand colors.</p>
                        </div>
                        <div class="form-grid">
                            <div class="form-row">
                                <div class="form-label">
                                    Site Logo
                                    <span class="size-hint">Recommended: 200 × 60 px</span>
                                </div>
                                <td>
                                    <div class="logo-preview" id="theme_logo_preview">
                                        <?php if(get_option('theme_logo')): ?>
                                            <img src="<?php echo esc_url(get_option('theme_logo')); ?>" style="max-height: 80px; object-fit: contain;">
                                        <?php else: ?>
                                            <span class="dashicons dashicons-format-image" style="font-size: 48px; color: #cbd5e1;"></span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="theme_logo" id="theme_logo_url" value="<?php echo esc_attr(get_option('theme_logo')); ?>">
                                    <button type="button" class="button upload_image_button" data-target="theme_logo">Upload Logo</button>
                                    <button type="button" class="button remove_image_button" data-target="theme_logo" style="color: #ef4444; margin-left: 5px;">Remove</button>
                                    <span class="size-hint">Recommended: 200x80px (Transparent PNG)</span>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">
                                    Footer Logo
                                    <span class="size-hint">Leave empty to use Site Logo</span>
                                </div>
                                <td>
                                    <div class="logo-preview" id="footer_logo_preview">
                                        <?php if(get_option('footer_logo')): ?>
                                            <img src="<?php echo esc_url(get_option('footer_logo')); ?>" style="max-height: 80px; object-fit: contain;">
                                        <?php else: ?>
                                            <span class="dashicons dashicons-format-image" style="font-size: 48px; color: #cbd5e1;"></span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="footer_logo" id="footer_logo_url" value="<?php echo esc_attr(get_option('footer_logo')); ?>">
                                    <button type="button" class="button upload_image_button" data-target="footer_logo">Upload Footer Logo</button>
                                    <button type="button" class="button remove_image_button" data-target="footer_logo" style="color: #ef4444; margin-left: 5px;">Remove</button>
                                    <span class="size-hint">Recommended: 200x80px (Transparent PNG)</span>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Primary Color</div>
                                <td><input type="text" name="woocom_primary_color" value="<?php echo esc_attr(get_option('woocom_primary_color', '#1E5D02')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Secondary Color</div>
                                <td><input type="text" name="woocom_secondary_color" value="<?php echo esc_attr(get_option('woocom_secondary_color', '#F7A501')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">
                                    Main Background Color
                                    <span class="size-hint">Applies to all page content areas</span>
                                </div>
                                <td><input type="text" name="woocom_main_background_color" value="<?php echo esc_attr(get_option('woocom_main_background_color', '#FBF9F5')); ?>" class="wp-color-picker-field"></td>
                            </div>

                            <hr style="grid-column: 1 / -1; border: 1px solid #f1f5f9; margin: 10px 0;">

                            <div class="form-row">
                                <div class="form-label">Bengali Font Family</div>
                                <td>
                                    <select name="woocom_font_bengali" class="form-input-text" style="width: 100%; max-width: 400px;">
                                        <?php 
                                        $b_fonts = array(
                                            'Noto Serif Bengali' => 'Noto Serif Bengali',
                                            'Hind Siliguri' => 'Hind Siliguri',
                                            'Baloo Da 2' => 'Baloo Da 2'
                                        );
                                        $current_b = get_option('woocom_font_bengali', 'Noto Serif Bengali');
                                        foreach($b_fonts as $label => $val): ?>
                                            <option value="<?php echo esc_attr($val); ?>" <?php selected($current_b, $val); ?>><?php echo esc_html($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description">Select the primary font for Bengali text.</p>
                                </td>
                            </div>

                            <div class="form-row">
                                <div class="form-label">English Font Family</div>
                                <td>
                                    <select name="woocom_font_english" class="form-input-text" style="width: 100%; max-width: 400px;">
                                        <?php 
                                        $e_fonts = array(
                                            'Inter' => 'Inter',
                                            'Poppins' => 'Poppins',
                                            'Open Sans' => 'Open Sans',
                                            'Roboto' => 'Roboto'
                                        );
                                        $current_e = get_option('woocom_font_english', 'Inter');
                                        foreach($e_fonts as $label => $val): ?>
                                            <option value="<?php echo esc_attr($val); ?>" <?php selected($current_e, $val); ?>><?php echo esc_html($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description">Select the primary font for English text and numbers.</p>
                                </td>
                            </div>
                        </div>

                    <?php elseif ($active_tab == 'header') : ?>
                        <div class="section-header">
                            <h2>Header & Navigation</h2>
                            <p>Configure navigation behavior, colors, and spacing.</p>
                        </div>
                        <div class="form-grid">
                            <div class="form-row">
                                <div class="form-label">Sticky Navigation</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="sticky_header" value="1" <?php checked(1, get_option('sticky_header', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <p class="description" style="margin-top: 8px;">Keep the navigation bar visible at the top while scrolling down.</p>
                                </td>
                            </div>

                            <hr style="grid-column: 1 / -1; border: 1px solid #f1f5f9; margin: 10px 0;">

                            <div class="form-row">
                                <div class="form-label">Nav Background Color</div>
                                <td><input type="text" name="nav_bg_color" value="<?php echo esc_attr(get_option('nav_bg_color', '#000000')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Nav Text Color</div>
                                <td><input type="text" name="nav_text_color" value="<?php echo esc_attr(get_option('nav_text_color', '#ffffff')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Nav Hover/Active Color</div>
                                <td><input type="text" name="nav_hover_color" value="<?php echo esc_attr(get_option('nav_hover_color', '#F7A501')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Nav Vertical Padding (px)</div>
                                <td>
                                    <input type="number" name="nav_vertical_padding" value="<?php echo esc_attr(get_option('nav_vertical_padding', '12')); ?>" class="form-input-text" style="max-width: 120px;">
                                    <p class="description">Increase this to make the navigation bar thicker.</p>
                                </td>
                            </div>
                        </div>

                    <?php elseif ($active_tab == 'ticker') : ?>
                        <div class="section-header">
                            <h2>Announcement Ticker</h2>
                            <p>Configure the scrolling announcement bar on your homepage.</p>
                        </div>
                        <div class="form-grid">
                            <div class="form-row">
                                <div class="form-label">Enable Ticker</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="ticker_enabled" value="1" <?php checked(1, get_option('ticker_enabled', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">
                                    Ticker Text
                                    <span class="size-hint">Enter one message per line.</span>
                                </div>
                                <td>
                                    <textarea name="ticker_text" class="form-input-text" rows="6" placeholder="ডেলিভারির সময় প্রোডাক্ট দেখে নিতে পারবেন&#10;সিজন ফ্রেশ মধু চলে এসেছে&#10;আমাদের বাগানের ফ্রেশ আমের প্রি-অর্ডার চলছে"><?php echo esc_textarea(get_option('ticker_text')); ?></textarea>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Background Color</div>
                                <td><input type="text" name="ticker_bg_color" value="<?php echo esc_attr(get_option('ticker_bg_color', '#1E5D02')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Text Color</div>
                                <td><input type="text" name="ticker_text_color" value="<?php echo esc_attr(get_option('ticker_text_color', '#ffffff')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">
                                    Scroll Speed (Seconds)
                                    <span class="size-hint">Lower value = Faster scrolling. (3 to 120 seconds)</span>
                                </div>
                                <td>
                                    <input type="number" name="ticker_speed" value="<?php echo esc_attr(get_option('ticker_speed', '20')); ?>" class="form-input-text" style="max-width: 120px;" min="3" max="120">
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Font Size (px)</div>
                                <td>
                                    <input type="number" name="ticker_font_size" value="<?php echo esc_attr(get_option('ticker_font_size', '14')); ?>" class="form-input-text" style="max-width: 120px;" min="10" max="30">
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Vertical Padding (px)</div>
                                <td>
                                    <input type="number" name="ticker_padding" value="<?php echo esc_attr(get_option('ticker_padding', '8')); ?>" class="form-input-text" style="max-width: 120px;" min="0" max="40">
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Separator Icon</div>
                                <td>
                                    <?php $current_icon = get_option('ticker_icon', 'mango'); ?>
                                    <select name="ticker_icon" class="form-input-text" style="max-width: 200px;">
                                        <option value="mango" <?php selected($current_icon, 'mango'); ?>>Mango 🥭</option>
                                        <option value="star" <?php selected($current_icon, 'star'); ?>>Star ⭐</option>
                                        <option value="gift" <?php selected($current_icon, 'gift'); ?>>Gift 🎁</option>
                                        <option value="bell" <?php selected($current_icon, 'bell'); ?>>Bell 🔔</option>
                                        <option value="none" <?php selected($current_icon, 'none'); ?>>None</option>
                                    </select>
                                </td>
                            </div>
                        </div>

                    <?php elseif ($active_tab == 'banners') : ?>
                        <div class="section-header">
                            <h2>Homepage Banners</h2>
                            <p>Upload high-quality banners for your homepage sliders and promotions.</p>
                        </div>
                        <div class="form-grid">

                            <?php /* ════ DYNAMIC HERO SLIDER ════ */ ?>
                            <div style="margin-bottom: 30px;">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                                    <div>
                                        <strong style="font-size:15px; color:#253D4E;">Main Slider Slides</strong>
                                        <span style="display:block; font-size:12px; color:#94a3b8; margin-top:2px;">Recommended size: 1200 × 450 px &nbsp;|&nbsp; Add as many slides as you need</span>
                                    </div>
                                </div>

                                <?php
                                /* Load existing slides — migrate from old hero_banner_1/2 if needed */
                                $raw_slides = get_option('woocom_hero_slides', '');
                                $slides = array();
                                if ($raw_slides) {
                                    $slides = json_decode($raw_slides, true) ?: array();
                                }
                                if (empty($slides)) {
                                    // Migrate legacy options
                                    $b1 = get_option('hero_banner_1', '');
                                    $b2 = get_option('hero_banner_2', '');
                                    if ($b1) $slides[] = array('image' => $b1, 'link' => get_option('hero_banner_1_link', ''));
                                    if ($b2) $slides[] = array('image' => $b2, 'link' => get_option('hero_banner_2_link', ''));
                                    if (empty($slides)) {
                                        $slides = array(array('image' => '', 'link' => ''), array('image' => '', 'link' => ''));
                                    }
                                }
                                ?>

                                <div id="hero-slides-list" style="display:flex; flex-direction:column; gap:16px;">
                                    <?php foreach($slides as $i => $slide): ?>
                                    <div class="hero-slide-row" data-index="<?php echo $i; ?>" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:16px; display:flex; align-items:center; gap:16px; position:relative;">
                                        <!-- Drag handle -->
                                        <div class="slide-drag-handle" title="Drag to reorder" style="cursor:grab; color:#cbd5e1; flex-shrink:0; display:flex; flex-direction:column; gap:3px; padding:4px;">
                                            <span style="display:block;width:18px;height:2px;background:currentColor;border-radius:2px;"></span>
                                            <span style="display:block;width:18px;height:2px;background:currentColor;border-radius:2px;"></span>
                                            <span style="display:block;width:18px;height:2px;background:currentColor;border-radius:2px;"></span>
                                        </div>
                                        <!-- Preview -->
                                        <div class="slide-preview" style="width:120px;height:52px;border-radius:8px;overflow:hidden;background:#e2e8f0;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:1px dashed #cbd5e1;">
                                            <?php if(!empty($slide['image'])): ?>
                                                <img src="<?php echo esc_url($slide['image']); ?>" style="width:100%;height:100%;object-fit:cover;">
                                            <?php else: ?>
                                                <span class="dashicons dashicons-format-image" style="font-size:28px;color:#cbd5e1;"></span>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Hidden URL input -->
                                        <input type="hidden" class="slide-image-url" name="hero_slide_image[]" value="<?php echo esc_attr($slide['image'] ?? ''); ?>">
                                        <!-- Buttons + Link -->
                                        <div style="flex:1; min-width:0;">
                                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px; flex-wrap:wrap;">
                                                <button type="button" class="button slide-upload-btn" style="font-size:12px;">
                                                    <span class="dashicons dashicons-upload" style="font-size:14px;line-height:1.6;"></span> Upload Image
                                                </button>
                                                <button type="button" class="button slide-remove-img-btn" style="font-size:12px;color:#ef4444;">Remove Image</button>
                                                <span style="font-size:12px;color:#94a3b8;margin-left:4px;">Slide <?php echo $i + 1; ?></span>
                                            </div>
                                            <input type="text" class="slide-link-url" name="hero_slide_link[]" value="<?php echo esc_attr($slide['link'] ?? ''); ?>" placeholder="Link URL (https://...)" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 12px;font-size:13px;outline:none;">
                                        </div>
                                        <!-- Delete slide -->
                                        <button type="button" class="slide-delete-btn" title="Remove this slide" style="flex-shrink:0;background:#fff;border:1px solid #fecaca;color:#ef4444;border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:16px;line-height:1;">
                                            <span class="dashicons dashicons-trash" style="font-size:15px;line-height:2.2;"></span>
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Add Slide Button -->
                                <button type="button" id="hero-add-slide" style="margin-top:14px;display:flex;align-items:center;gap:8px;background:#fff;border:2px dashed #e2e8f0;color:#64748b;border-radius:12px;padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;width:100%;justify-content:center;transition:all 0.2s;">
                                    <span style="font-size:20px;line-height:1;">+</span> Add Another Slide
                                </button>

                                <!-- Hidden JSON field that gets submitted -->
                                <input type="hidden" name="woocom_hero_slides" id="woocom_hero_slides_json" value="<?php echo esc_attr($raw_slides ?: json_encode($slides)); ?>">
                            </div>

                            <hr style="border:none;border-top:2px solid #f1f5f9;margin:4px 0 24px;">

                            <?php /* ════ OTHER BANNERS (side, promo) ════ */ ?>
                            <?php 
                            $other_banners = array(
                                'hero_side_banner' => array('label' => 'Right Side Mini Banner', 'size' => '400 × 400 px'),
                                'promo_banner_1'   => array('label' => 'Dual Banner - Left',     'size' => '600 × 250 px'),
                                'promo_banner_2'   => array('label' => 'Dual Banner - Right',    'size' => '600 × 250 px'),
                            );
                            foreach($other_banners as $id => $data):
                                $val      = get_option($id);
                                $link_id  = $id . '_link';
                                $link_val = get_option($link_id);
                            ?>
                            <div class="form-row" style="padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
                                <div class="form-label">
                                    <?php echo $data['label']; ?>
                                    <span class="size-hint">Recommended: <?php echo $data['size']; ?></span>
                                </div>
                                <td>
                                    <div class="banner-preview" id="<?php echo $id; ?>_preview" style="<?php echo ($id === 'hero_side_banner') ? 'max-width: 200px; height: 200px;' : ''; ?>">
                                        <?php if($val): ?>
                                            <img src="<?php echo esc_url($val); ?>">
                                        <?php else: ?>
                                            <span class="dashicons dashicons-format-image" style="font-size: 48px; color: #cbd5e1;"></span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>_url" value="<?php echo esc_attr($val); ?>">
                                    <button type="button" class="button upload_image_button" data-target="<?php echo $id; ?>">Upload Image</button>
                                    <button type="button" class="button remove_image_button" data-target="<?php echo $id; ?>" style="color: #ef4444; margin-left: 5px;">Remove</button>
                                    <div style="margin-top: 10px;">
                                        <input type="text" name="<?php echo $link_id; ?>" value="<?php echo esc_attr($link_val); ?>" class="form-input-text" placeholder="Link URL (https://...)">
                                    </div>
                                </td>
                            </div>
                            <?php endforeach; ?>

                        </div>

                        <?php /* ════ SLIDER JS ════ */ ?>
                        <script>
                        (function($){
                            var slideIndex = <?php echo count($slides); ?>;

                            /* ── Serialize all slide rows into the hidden JSON field ── */
                            function serializeSlides() {
                                var slides = [];
                                $('#hero-slides-list .hero-slide-row').each(function(){
                                    slides.push({
                                        image : $(this).find('.slide-image-url').val(),
                                        link  : $(this).find('.slide-link-url').val()
                                    });
                                });
                                $('#woocom_hero_slides_json').val(JSON.stringify(slides));
                            }

                            /* ── Build a new empty slide row ── */
                            function buildSlideRow(idx) {
                                return $('<div class="hero-slide-row" data-index="'+idx+'" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;display:flex;align-items:center;gap:16px;position:relative;">' +
                                    '<div class="slide-drag-handle" title="Drag to reorder" style="cursor:grab;color:#cbd5e1;flex-shrink:0;display:flex;flex-direction:column;gap:3px;padding:4px;">' +
                                        '<span style="display:block;width:18px;height:2px;background:currentColor;border-radius:2px;"></span>' +
                                        '<span style="display:block;width:18px;height:2px;background:currentColor;border-radius:2px;"></span>' +
                                        '<span style="display:block;width:18px;height:2px;background:currentColor;border-radius:2px;"></span>' +
                                    '</div>' +
                                    '<div class="slide-preview" style="width:120px;height:52px;border-radius:8px;overflow:hidden;background:#e2e8f0;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:1px dashed #cbd5e1;">' +
                                        '<span class="dashicons dashicons-format-image" style="font-size:28px;color:#cbd5e1;"></span>' +
                                    '</div>' +
                                    '<input type="hidden" class="slide-image-url" name="hero_slide_image[]" value="">' +
                                    '<div style="flex:1;min-width:0;">' +
                                        '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">' +
                                            '<button type="button" class="button slide-upload-btn" style="font-size:12px;"><span class="dashicons dashicons-upload" style="font-size:14px;line-height:1.6;"></span> Upload Image</button>' +
                                            '<button type="button" class="button slide-remove-img-btn" style="font-size:12px;color:#ef4444;">Remove Image</button>' +
                                            '<span style="font-size:12px;color:#94a3b8;margin-left:4px;">Slide '+(idx+1)+'</span>' +
                                        '</div>' +
                                        '<input type="text" class="slide-link-url" name="hero_slide_link[]" value="" placeholder="Link URL (https://...)" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 12px;font-size:13px;outline:none;">' +
                                    '</div>' +
                                    '<button type="button" class="slide-delete-btn" title="Remove this slide" style="flex-shrink:0;background:#fff;border:1px solid #fecaca;color:#ef4444;border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:16px;line-height:1;">' +
                                        '<span class="dashicons dashicons-trash" style="font-size:15px;line-height:2.2;"></span>' +
                                    '</button>' +
                                '</div>');
                            }

                            /* ── Add Slide ── */
                            $('#hero-add-slide').on('click', function(){
                                var $row = buildSlideRow(slideIndex);
                                $('#hero-slides-list').append($row);
                                bindRow($row);
                                slideIndex++;
                                serializeSlides();
                            });

                            /* ── Bind events to a row ── */
                            function bindRow($row) {
                                /* Upload */
                                $row.find('.slide-upload-btn').on('click', function(){
                                    var $r = $(this).closest('.hero-slide-row');
                                    var frame = wp.media({ title: 'Select Slide Image', multiple: false });
                                    frame.on('select', function(){
                                        var att = frame.state().get('selection').first().toJSON();
                                        $r.find('.slide-image-url').val(att.url);
                                        $r.find('.slide-preview').html('<img src="'+att.url+'" style="width:100%;height:100%;object-fit:cover;">');
                                        serializeSlides();
                                    });
                                    frame.open();
                                });

                                /* Remove image */
                                $row.find('.slide-remove-img-btn').on('click', function(){
                                    var $r = $(this).closest('.hero-slide-row');
                                    $r.find('.slide-image-url').val('');
                                    $r.find('.slide-preview').html('<span class="dashicons dashicons-format-image" style="font-size:28px;color:#cbd5e1;"></span>');
                                    serializeSlides();
                                });

                                /* Delete row */
                                $row.find('.slide-delete-btn').on('click', function(){
                                    if ($('#hero-slides-list .hero-slide-row').length <= 1) {
                                        alert('At least one slide is required.');
                                        return;
                                    }
                                    $row.fadeOut(200, function(){ $(this).remove(); serializeSlides(); });
                                });

                                /* Link change */
                                $row.find('.slide-link-url').on('input', serializeSlides);
                            }

                            /* ── Bind existing rows ── */
                            $('#hero-slides-list .hero-slide-row').each(function(){
                                bindRow($(this));
                            });

                            /* ── Serialize before form submit ── */
                            $('form').on('submit', serializeSlides);

                            /* ── Sortable drag-and-drop ── */
                            if ($.fn.sortable) {
                                $('#hero-slides-list').sortable({
                                    handle: '.slide-drag-handle',
                                    axis: 'y',
                                    update: serializeSlides
                                });
                            }

                            /* ── Add hover effect to + button ── */
                            $('#hero-add-slide').hover(
                                function(){ $(this).css({background:'#f0f9ff', borderColor:'#93c5fd', color:'#2563eb'}); },
                                function(){ $(this).css({background:'#fff', borderColor:'#e2e8f0', color:'#64748b'}); }
                            );

                        })(jQuery);
                        </script>

                    <?php elseif ($active_tab == 'layout') : ?>
                        <div class="section-header">
                            <h2>Section Visibility</h2>
                            <p>Enable or disable specific sections on your homepage.</p>
                        </div>
                        <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <?php 
                            $visibility = array(
                                'show_hero_section' => 'Hero Slider & Side Banner',
                                'show_featured_categories' => 'Featured Categories Strip',
                                'show_top_selling' => 'Top Selling Section',
                                'show_category_sections' => 'Category Product Carousels',
                                'show_combo_offers' => 'Combo Deals Section',
                                'woocom_show_just_for_you' => 'Just For You Product Grid',
                                'show_dual_banners' => 'Dual Promo Banners'
                            );
                            foreach($visibility as $id => $label): ?>
                            <div style="background: #f8fafc; padding: 15px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border);">
                                <span style="font-weight: 600;"><?php echo $label; ?></span>
                                <label class="switch">
                                    <input type="checkbox" name="<?php echo $id; ?>" value="1" <?php checked(1, get_option($id, 1)); ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="margin-top: 30px;">
                            <div class="form-row">
                                <div class="form-label">Combo Title & Image</div>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 250px; max-width: 350px;">
                                            <input type="text" name="woocom_combo_title" value="<?php echo esc_attr(get_option('woocom_combo_title', 'Exclusive Combo Deals')); ?>" class="form-input-text" style="max-width: 100%;">
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 8px;">
                                            <div id="woocom_combo_image_preview" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px; background: #fff; border: 1px solid #cbd5e1;">
                                                <?php if(get_option('woocom_combo_image')): ?>
                                                    <img src="<?php echo esc_url(get_option('woocom_combo_image')); ?>" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="dashicons dashicons-format-image" style="font-size: 20px; color: #94a3b8; height:20px; width:20px;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <input type="hidden" name="woocom_combo_image" id="woocom_combo_image_url" value="<?php echo esc_attr(get_option('woocom_combo_image')); ?>">
                                            <button type="button" class="button upload_image_button" data-target="woocom_combo_image">Upload</button>
                                            <button type="button" class="button remove_image_button" data-target="woocom_combo_image" style="color: #ef4444;">Remove</button>
                                        </div>
                                    </div>
                                </td>
                            </div>
                            <div class="form-row" style="margin-top: 15px;">
                                <div class="form-label">Top Selling Title & Image</div>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 250px; max-width: 350px;">
                                            <input type="text" name="woocom_top_selling_title" value="<?php echo esc_attr(get_option('woocom_top_selling_title', 'Top Selling Products')); ?>" class="form-input-text" style="max-width: 100%;">
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 8px;">
                                            <div id="woocom_top_selling_image_preview" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px; background: #fff; border: 1px solid #cbd5e1;">
                                                <?php if(get_option('woocom_top_selling_image')): ?>
                                                    <img src="<?php echo esc_url(get_option('woocom_top_selling_image')); ?>" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="dashicons dashicons-format-image" style="font-size: 20px; color: #94a3b8; height:20px; width:20px;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <input type="hidden" name="woocom_top_selling_image" id="woocom_top_selling_image_url" value="<?php echo esc_attr(get_option('woocom_top_selling_image')); ?>">
                                            <button type="button" class="button upload_image_button" data-target="woocom_top_selling_image">Upload</button>
                                            <button type="button" class="button remove_image_button" data-target="woocom_top_selling_image" style="color: #ef4444;">Remove</button>
                                        </div>
                                    </div>
                                </td>
                            </div>
                            <div class="form-row" style="margin-top: 15px;">
                                <div class="form-label">Featured Products Order</div>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                        <div>
                                            <label style="font-size: 12px; color: #64748b; display: block; margin-bottom: 4px;">Order By</label>
                                            <select name="woocom_featured_orderby" class="form-input-text" style="min-width: 180px;">
                                                <option value="menu_order" <?php selected(get_option('woocom_featured_orderby', 'menu_order'), 'menu_order'); ?>>Manual Sorting (Menu Order)</option>
                                                <option value="date" <?php selected(get_option('woocom_featured_orderby'), 'date'); ?>>Date Created (Newest First)</option>
                                                <option value="title" <?php selected(get_option('woocom_featured_orderby'), 'title'); ?>>Alphabetical (Title)</option>
                                                <option value="price" <?php selected(get_option('woocom_featured_orderby'), 'price'); ?>>Price</option>
                                                <option value="sales" <?php selected(get_option('woocom_featured_orderby'), 'sales'); ?>>Best Selling (Sales)</option>
                                                <option value="rand" <?php selected(get_option('woocom_featured_orderby'), 'rand'); ?>>Random</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; color: #64748b; display: block; margin-bottom: 4px;">Sort Direction</label>
                                            <select name="woocom_featured_order" class="form-input-text" style="min-width: 120px;">
                                                <option value="ASC" <?php selected(get_option('woocom_featured_order', 'ASC'), 'ASC'); ?>>Ascending</option>
                                                <option value="DESC" <?php selected(get_option('woocom_featured_order'), 'DESC'); ?>>Descending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <p style="font-size: 12px; color: #64748b; margin-top: 8px; margin-bottom: 0;">
                                        💡 <strong>Manual Sorting</strong> ব্যবহার করতে চাইলে <a href="<?php echo esc_url(admin_url('edit.php?post_type=product&orderby=menu_order&order=asc')); ?>" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: underline;">এখানে ক্লিক করুন</a>। এটি প্রোডাক্ট পেজের এমন একটি ভিউ ওপেন করবে যেখানে আপনি মাউস দিয়ে ড্র্যাগ অ্যান্ড ড্রপ করে প্রোডাক্টগুলো নিজের মতো সাজাতে পারবেন।
                                    </p>
                                </td>
                            </div>
                            <div class="form-row" style="margin-top: 15px;">
                                <div class="form-label">Latest Products Order</div>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                        <div>
                                            <label style="font-size: 12px; color: #64748b; display: block; margin-bottom: 4px;">Order By</label>
                                            <select name="woocom_latest_orderby" class="form-input-text" style="min-width: 180px;">
                                                <option value="menu_order" <?php selected(get_option('woocom_latest_orderby', 'date'), 'menu_order'); ?>>Manual Sorting (Menu Order)</option>
                                                <option value="date" <?php selected(get_option('woocom_latest_orderby', 'date'), 'date'); ?>>Date Created (Newest First)</option>
                                                <option value="title" <?php selected(get_option('woocom_latest_orderby', 'date'), 'title'); ?>>Alphabetical (Title)</option>
                                                <option value="price" <?php selected(get_option('woocom_latest_orderby', 'date'), 'price'); ?>>Price</option>
                                                <option value="sales" <?php selected(get_option('woocom_latest_orderby', 'date'), 'sales'); ?>>Best Selling (Sales)</option>
                                                <option value="rand" <?php selected(get_option('woocom_latest_orderby', 'date'), 'rand'); ?>>Random</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; color: #64748b; display: block; margin-bottom: 4px;">Sort Direction</label>
                                            <select name="woocom_latest_order" class="form-input-text" style="min-width: 120px;">
                                                <option value="ASC" <?php selected(get_option('woocom_latest_order', 'DESC'), 'ASC'); ?>>Ascending</option>
                                                <option value="DESC" <?php selected(get_option('woocom_latest_order', 'DESC'), 'DESC'); ?>>Descending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <p style="font-size: 12px; color: #64748b; margin-top: 8px; margin-bottom: 0;">
                                        💡 <strong>Manual Sorting</strong> ব্যবহার করতে চাইলে <a href="<?php echo esc_url(admin_url('edit.php?post_type=product&orderby=menu_order&order=asc')); ?>" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: underline;">এখানে ক্লিক করুন</a>। এটি প্রোডাক্ট পেজের এমন একটি ভিউ ওপেন করবে যেখানে আপনি মাউস দিয়ে ড্র্যাগ অ্যান্ড ড্রপ করে প্রোডাক্টগুলো নিজের মতো সাজাতে পারবেন।
                                    </p>
                                </td>
                            </div>
                            <div class="form-row" style="margin-top: 15px;">
                                <div class="form-label">Just For You Title & Image</div>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 250px; max-width: 350px;">
                                            <input type="text" name="woocom_just_for_you_title" value="<?php echo esc_attr(get_option('woocom_just_for_you_title', 'Just For You')); ?>" class="form-input-text" style="max-width: 100%;">
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 8px;">
                                            <div id="woocom_just_for_you_image_preview" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px; background: #fff; border: 1px solid #cbd5e1;">
                                                <?php if(get_option('woocom_just_for_you_image')): ?>
                                                    <img src="<?php echo esc_url(get_option('woocom_just_for_you_image')); ?>" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="dashicons dashicons-format-image" style="font-size: 20px; color: #94a3b8; height:20px; width:20px;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <input type="hidden" name="woocom_just_for_you_image" id="woocom_just_for_you_image_url" value="<?php echo esc_attr(get_option('woocom_just_for_you_image')); ?>">
                                            <button type="button" class="button upload_image_button" data-target="woocom_just_for_you_image">Upload</button>
                                            <button type="button" class="button remove_image_button" data-target="woocom_just_for_you_image" style="color: #ef4444;">Remove</button>
                                        </div>
                                    </div>
                                </td>
                            </div>
                        </div>

                    <?php elseif ($active_tab == 'collections') : ?>
                        <div class="section-header">
                            <h2>Product Collections</h2>
                            <p>Choose which categories or products appear in specific homepage sections.</p>
                        </div>

                        <!-- Featured Categories Selection -->
                        <div style="margin-bottom: 40px;">
                            <h3 style="margin-bottom: 15px;">Featured Categories (Strip)</h3>
                            <div class="selection-grid">
                                <?php
                                $saved_featured = (array) get_option('woocom_featured_categories', array());
                                foreach($categories as $cat): ?>
                                <label class="selection-item">
                                    <span><?php echo $cat->name; ?></span>
                                    <input type="checkbox" name="woocom_featured_categories[]" value="<?php echo $cat->term_id; ?>" <?php checked(in_array($cat->term_id, $saved_featured)); ?>>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Category Sections -->
                        <div style="margin-bottom: 40px;">
                            <h3 style="margin-bottom: 15px;">Individual Category Product Sections</h3>
                            <div class="selection-grid">
                                <?php
                                $saved_sections = (array) get_option('woocom_category_sections', array());
                                foreach($categories as $cat): ?>
                                <label class="selection-item">
                                    <span><?php echo $cat->name; ?></span>
                                    <input type="checkbox" name="woocom_category_sections[]" value="<?php echo $cat->term_id; ?>" <?php checked(in_array($cat->term_id, $saved_sections)); ?>>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Top Selling Note -->
                        <div style="background:#f1f5f9;padding:24px 28px;border-radius:14px;border:2px dashed var(--admin-primary);margin-bottom:40px;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                                <div style="width:36px;height:36px;border-radius:8px;background:var(--admin-primary);display:flex;align-items:center;justify-content:center;color:white;flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </div>
                                <h3 style="margin:0;color:var(--admin-primary);font-size:16px;font-weight:700;">Top Selling Section (Featured Products Only)</h3>
                            </div>
                            <p style="margin:0;color:#475569;font-size:13px;line-height:1.7;">Mark products as <strong>Featured</strong> in <strong>Products → All Products</strong> (Star icon). The theme auto-shows the latest 4 featured products.</p>
                        </div>

                        <!-- Combo Bundle Builder -->
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                                <div>
                                    <h3 style="margin:0 0 4px 0;font-size:16px;font-weight:700;color:#0f172a;">Combo Bundles</h3>
                                    <p style="margin:0;font-size:13px;color:#64748b;">Group multiple products into a single combo deal shown on the homepage.</p>
                                </div>
                                <button type="button" id="add-combo-bundle" class="button button-primary" style="background:var(--admin-primary);border-color:var(--admin-primary);">+ Add Bundle</button>
                            </div>

                            <div id="combo-bundles-container">
                                <?php
                                $saved_bundles = (array) get_option('woocom_combo_bundles', array());
                                if ( empty($saved_bundles) ) : ?>
                                <p id="combo-empty-msg" style="color:#94a3b8;text-align:center;padding:30px;background:#f8fafc;border-radius:12px;border:2px dashed #e2e8f0;">
                                    No combo bundles yet. Click "+ Add Bundle" to create your first bundle.
                                </p>
                                <?php else : ?>
                                    <p id="combo-empty-msg" style="display:none;color:#94a3b8;text-align:center;padding:30px;background:#f8fafc;border-radius:12px;border:2px dashed #e2e8f0;">
                                        No combo bundles yet. Click "+ Add Bundle" to create your first bundle.
                                    </p>
                                <?php endif; ?>

                                <?php foreach ($saved_bundles as $b_index => $bundle) :
                                    $b_title    = isset($bundle['title'])    ? $bundle['title']    : '';
                                    $b_price    = isset($bundle['price'])    ? $bundle['price']    : '';
                                    $b_image    = isset($bundle['image'])    ? $bundle['image']    : '';
                                    $b_products = isset($bundle['products']) ? (array) $bundle['products'] : array();
                                ?>
                                <div class="combo-bundle-item" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:16px;" data-index="<?php echo esc_attr($b_index); ?>">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #e2e8f0;">
                                        <h4 style="margin:0;color:#334155;font-size:14px;font-weight:700;">Combo Bundle #<?php echo $b_index + 1; ?></h4>
                                        <button type="button" class="remove-combo-bundle button" style="color:#ef4444;border-color:#ef4444;">✕ Remove</button>
                                    </div>
                                    <div style="display:grid;grid-template-columns:160px 1fr;gap:20px;">
                                        <div>
                                            <div class="banner-preview" id="combo_bundle_<?php echo esc_attr($b_index); ?>_preview" style="max-width:160px;min-height:120px;">
                                                <?php if ($b_image) : ?>
                                                    <img src="<?php echo esc_url($b_image); ?>">
                                                <?php else : ?>
                                                    <span class="dashicons dashicons-format-image" style="font-size:36px;color:#cbd5e1;"></span>
                                                <?php endif; ?>
                                            </div>
                                            <input type="hidden" name="woocom_combo_bundles[<?php echo esc_attr($b_index); ?>][image]" id="combo_bundle_<?php echo esc_attr($b_index); ?>_url" value="<?php echo esc_attr($b_image); ?>">
                                            <button type="button" class="button upload_image_button" data-target="combo_bundle_<?php echo esc_attr($b_index); ?>" style="margin-top:8px;width:100%;font-size:12px;">📷 Image</button>
                                            <button type="button" class="button remove_image_button" data-target="combo_bundle_<?php echo esc_attr($b_index); ?>" style="color:#ef4444;margin-top:4px;width:100%;font-size:12px;">Remove</button>
                                        </div>
                                        <div>
                                            <div style="margin-bottom:12px;">
                                                <label style="display:block;font-weight:600;margin-bottom:6px;color:#334155;font-size:13px;">Bundle Title</label>
                                                <input type="text" name="woocom_combo_bundles[<?php echo esc_attr($b_index); ?>][title]" value="<?php echo esc_attr($b_title); ?>" class="form-input-text" placeholder="e.g. Family Pack Combo">
                                            </div>
                                            <div style="margin-bottom:14px;">
                                                <label style="display:block;font-weight:600;margin-bottom:6px;color:#334155;font-size:13px;">Combo Price (৳)</label>
                                                <input type="number" name="woocom_combo_bundles[<?php echo esc_attr($b_index); ?>][price]" value="<?php echo esc_attr($b_price); ?>" class="form-input-text" style="max-width:180px;" placeholder="999" min="0">
                                            </div>
                                            <div>
                                                <label style="display:block;font-weight:600;margin-bottom:8px;color:#334155;font-size:13px;">Include Products</label>
                                                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;max-height:200px;overflow-y:auto;background:white;border:1px solid #e2e8f0;border-radius:8px;padding:12px;">
                                                    <?php foreach ($products as $prod) : ?>
                                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#475569;padding:4px 0;">
                                                        <input type="checkbox" name="woocom_combo_bundles[<?php echo esc_attr($b_index); ?>][products][]" value="<?php echo esc_attr($prod->ID); ?>" <?php checked(in_array($prod->ID, $b_products)); ?>>
                                                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo esc_html($prod->post_title); ?></span>
                                                    </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <script>
                        (function($) {
                            var woocomProducts = <?php echo wp_json_encode(array_map(function($p) {
                                return array('id' => $p->ID, 'title' => $p->post_title);
                            }, $products)); ?>;

                            function getBundleCount() {
                                return $('#combo-bundles-container .combo-bundle-item').length;
                            }

                            function buildBundleHTML(index) {
                                var productsHTML = woocomProducts.map(function(p) {
                                    return '<label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#475569;padding:4px 0;">' +
                                        '<input type="checkbox" name="woocom_combo_bundles[' + index + '][products][]" value="' + p.id + '">' +
                                        '<span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + $('<div>').text(p.title).html() + '</span></label>';
                                }).join('');

                                return '<div class="combo-bundle-item" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:16px;" data-index="' + index + '">' +
                                    '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #e2e8f0;">' +
                                        '<h4 style="margin:0;color:#334155;font-size:14px;font-weight:700;">Combo Bundle #' + (index + 1) + '</h4>' +
                                        '<button type="button" class="remove-combo-bundle button" style="color:#ef4444;border-color:#ef4444;">✕ Remove</button>' +
                                    '</div>' +
                                    '<div style="display:grid;grid-template-columns:160px 1fr;gap:20px;">' +
                                        '<div>' +
                                            '<div class="banner-preview" id="combo_bundle_' + index + '_preview" style="max-width:160px;min-height:120px;">' +
                                                '<span class="dashicons dashicons-format-image" style="font-size:36px;color:#cbd5e1;"></span>' +
                                            '</div>' +
                                            '<input type="hidden" name="woocom_combo_bundles[' + index + '][image]" id="combo_bundle_' + index + '_url" value="">' +
                                            '<button type="button" class="button upload_image_button" data-target="combo_bundle_' + index + '" style="margin-top:8px;width:100%;font-size:12px;">📷 Image</button>' +
                                            '<button type="button" class="button remove_image_button" data-target="combo_bundle_' + index + '" style="color:#ef4444;margin-top:4px;width:100%;font-size:12px;">Remove</button>' +
                                        '</div>' +
                                        '<div>' +
                                            '<div style="margin-bottom:12px;">' +
                                                '<label style="display:block;font-weight:600;margin-bottom:6px;color:#334155;font-size:13px;">Bundle Title</label>' +
                                                '<input type="text" name="woocom_combo_bundles[' + index + '][title]" value="" class="form-input-text" placeholder="e.g. Family Pack Combo">' +
                                            '</div>' +
                                            '<div style="margin-bottom:14px;">' +
                                                '<label style="display:block;font-weight:600;margin-bottom:6px;color:#334155;font-size:13px;">Combo Price (৳)</label>' +
                                                '<input type="number" name="woocom_combo_bundles[' + index + '][price]" value="" class="form-input-text" style="max-width:180px;" placeholder="999" min="0">' +
                                            '</div>' +
                                            '<div>' +
                                                '<label style="display:block;font-weight:600;margin-bottom:8px;color:#334155;font-size:13px;">Include Products</label>' +
                                                '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;max-height:200px;overflow-y:auto;background:white;border:1px solid #e2e8f0;border-radius:8px;padding:12px;">' +
                                                    productsHTML +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>';
                            }

                            $('#add-combo-bundle').on('click', function() {
                                var index = getBundleCount();
                                $('#combo-bundles-container').append(buildBundleHTML(index));
                                $('#combo-empty-msg').hide();
                            });

                            $(document).on('click', '.remove-combo-bundle', function() {
                                $(this).closest('.combo-bundle-item').remove();
                                // Re-index remaining bundles
                                $('#combo-bundles-container .combo-bundle-item').each(function(i) {
                                    $(this).attr('data-index', i);
                                    $(this).find('h4').text('Combo Bundle #' + (i + 1));
                                    $(this).find('[name*="woocom_combo_bundles["]').each(function() {
                                        var name = $(this).attr('name').replace(/woocom_combo_bundles\[\d+\]/, 'woocom_combo_bundles[' + i + ']');
                                        $(this).attr('name', name);
                                    });
                                    $(this).find('[id*="combo_bundle_"]').each(function() {
                                        var id = $(this).attr('id').replace(/combo_bundle_\d+/, 'combo_bundle_' + i);
                                        $(this).attr('id', id);
                                    });
                                    $(this).find('[data-target*="combo_bundle_"]').each(function() {
                                        var target = $(this).attr('data-target').replace(/combo_bundle_\d+/, 'combo_bundle_' + i);
                                        $(this).attr('data-target', target);
                                    });
                                });
                                if (getBundleCount() === 0) $('#combo-empty-msg').show();
                            });
                        }(jQuery));
                        </script>
                    <?php elseif ($active_tab == 'footer') : ?>
                        <div class="section-header">
                            <h2>Footer Settings</h2>
                            <p>Manage footer quick links and their destination URLs.</p>
                        </div>
                        <div class="form-grid">
                            <?php
                            $footer_groups = array(
                                'information' => array('title' => 'Information', 'title_option' => 'woocom_footer_information_title', 'option' => 'woocom_footer_information_links', 'rows' => 8),
                                'shop' => array('title' => 'Shop By', 'title_option' => 'woocom_footer_shop_title', 'option' => 'woocom_footer_shop_links', 'rows' => 8),
                                'support' => array('title' => 'Support', 'title_option' => 'woocom_footer_support_title', 'option' => 'woocom_footer_support_links', 'rows' => 7),
                                'policy' => array('title' => 'Consumer Policy', 'title_option' => 'woocom_footer_policy_title', 'option' => 'woocom_footer_policy_links', 'rows' => 7),
                            );

                            foreach ($footer_groups as $group_key => $group_data) :
                                $saved_title = get_option($group_data['title_option'], $group_data['title']);
                                $saved_links = get_option($group_data['option'], woocom_get_default_footer_links($group_key));
                                $saved_links = is_array($saved_links) ? array_values($saved_links) : array();
                                $row_count = max($group_data['rows'], count($saved_links) + 1);
                            ?>
                            <div class="form-row" style="padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;">
                                <div class="form-label"><?php echo esc_html($group_data['title']); ?></div>
                                <td>
                                    <div style="display: grid; gap: 12px; max-width: 780px;" class="footer-link-group" data-option="<?php echo esc_attr($group_data['option']); ?>">
                                        <input type="text" name="<?php echo esc_attr($group_data['title_option']); ?>" value="<?php echo esc_attr($saved_title); ?>" class="form-input-text" placeholder="Footer column title">
                                        <div class="footer-link-rows" style="display: grid; gap: 10px;">
                                        <?php for ($i = 0; $i < $row_count; $i++) :
                                            $label = isset($saved_links[$i]['label']) ? $saved_links[$i]['label'] : '';
                                            $url = isset($saved_links[$i]['url']) ? $saved_links[$i]['url'] : '';
                                        ?>
                                        <div class="footer-link-row" style="display: grid; grid-template-columns: minmax(180px, 1fr) minmax(220px, 1.4fr); gap: 10px;">
                                            <input type="text" name="<?php echo esc_attr($group_data['option']); ?>[<?php echo esc_attr($i); ?>][label]" value="<?php echo esc_attr($label); ?>" class="form-input-text" placeholder="Link text">
                                            <input type="text" name="<?php echo esc_attr($group_data['option']); ?>[<?php echo esc_attr($i); ?>][url]" value="<?php echo esc_attr($url); ?>" class="form-input-text" placeholder="https://example.com/page">
                                        </div>
                                        <?php endfor; ?>
                                        </div>
                                        <button type="button" class="button footer-add-link-row" style="justify-self:start;">+ Add item</button>
                                    </div>
                                    <p class="description">Empty rows will be ignored. Use full URLs or relative paths like /about-us/.</p>
                                </td>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ($active_tab == 'cart') : ?>
                        <div class="section-header">
                            <h2>Cart & Checkout Settings</h2>
                            <p>Configure the side cart drawer and checkout experience.</p>
                        </div>
                        <div class="form-grid">
                            <div class="form-row">
                                <div class="form-label">Enable Cart Drawer</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="enable_cart_drawer" value="1" <?php checked(1, get_option('enable_cart_drawer', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Floating Cart Visibility</div>
                                <td>
                                    <?php $cart_floating_visibility = get_option('cart_drawer_floating_visibility', 'hide_mobile'); ?>
                                    <select name="cart_drawer_floating_visibility" class="form-input-text">
                                        <option value="show_all" <?php selected($cart_floating_visibility, 'show_all'); ?>>Show on PC & Mobile</option>
                                        <option value="hide_mobile" <?php selected($cart_floating_visibility, 'hide_mobile'); ?>>Hide on Mobile</option>
                                        <option value="hide_desktop" <?php selected($cart_floating_visibility, 'hide_desktop'); ?>>Hide on PC</option>
                                        <option value="hide_all" <?php selected($cart_floating_visibility, 'hide_all'); ?>>Hide on PC & Mobile</option>
                                    </select>
                                    <p class="description">Controls the floating side cart opener only. Header and bottom cart buttons stay available.</p>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Cart Drawer Title</div>
                                <td><input type="text" name="cart_drawer_title" value="<?php echo esc_attr(get_option('cart_drawer_title', 'Shopping Cart')); ?>" class="form-input-text"></td>
                            </div>

                            <hr style="grid-column: 1 / -1; border: 1px solid #f1f5f9; margin: 10px 0;">

                            <div class="form-row">
                                <div class="form-label">Enable Free Gift Progress</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="cart_promo_enabled" value="1" <?php checked(1, get_option('cart_promo_enabled', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Promo Unlock Message</div>
                                <td><input type="text" name="cart_promo_title" value="<?php echo esc_attr(get_option('cart_promo_title', 'Get a Free Gift!')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Min Amount for Promo (৳)</div>
                                <td><input type="number" name="cart_promo_min_amount" value="<?php echo esc_attr(get_option('cart_promo_min_amount', 3000)); ?>" class="form-input-text"></td>
                            </div>

                            <hr style="grid-column: 1 / -1; border: 1px solid #f1f5f9; margin: 10px 0;">

                            <div class="form-row">
                                <div class="form-label">Show Cross-sell Section</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="show_cross_sell" value="1" <?php checked(1, get_option('show_cross_sell', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Cross-sell Title</div>
                                <td><input type="text" name="cross_sell_title" value="<?php echo esc_attr(get_option('cross_sell_title', 'You May Also Like')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Auto-slide Cross-sell</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="cross_sell_autoslide" value="1" <?php checked(1, get_option('cross_sell_autoslide', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </div>

                            <hr style="grid-column: 1 / -1; border: 1px solid #f1f5f9; margin: 10px 0;">

                            <div class="form-row">
                                <div class="form-label">Shake Checkout Button</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="checkout_button_shake" value="1" <?php checked(1, get_option('checkout_button_shake', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Sticky Checkout (Mobile)</div>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="sticky_checkout_mobile" value="1" <?php checked(1, get_option('sticky_checkout_mobile', 1)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </div>

                            <hr style="grid-column: 1 / -1; border: 1px solid #f1f5f9; margin: 10px 0;">

                            <div class="form-row">
                                <div class="form-label">Add to Cart Button Color</div>
                                <td><input type="text" name="product_add_to_cart_button_color" value="<?php echo esc_attr(get_option('product_add_to_cart_button_color', get_option('woocom_secondary_color', '#F7A501'))); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Buy Now Button Color</div>
                                <td><input type="text" name="product_buy_now_button_color" value="<?php echo esc_attr(get_option('product_buy_now_button_color', get_option('woocom_primary_color', '#1E5D02'))); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">WhatsApp Button Color</div>
                                <td><input type="text" name="product_whatsapp_button_color" value="<?php echo esc_attr(get_option('product_whatsapp_button_color', '#25D366')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Call Button Color</div>
                                <td><input type="text" name="product_call_button_color" value="<?php echo esc_attr(get_option('product_call_button_color', '#1e3a8a')); ?>" class="wp-color-picker-field"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Unavailable Variation Message</div>
                                <td>
                                    <textarea name="variation_unavailable_message" class="form-input-text" rows="3"><?php echo esc_textarea(get_option('variation_unavailable_message', 'Sorry, this product is unavailable. Please choose a different combination.')); ?></textarea>
                                </td>
                            </div>
                        </div>

                    <?php elseif ($active_tab == 'contact') : ?>
                        <div class="section-header">
                            <h2>Contact & Social</h2>
                            <p>Set your business contact details and social media profile links.</p>
                        </div>
                        <div class="form-grid">
                            <div class="form-row">
                                <div class="form-label">Phone Number</div>
                                <td>
                                    <input type="text" name="contact_phone" value="<?php echo esc_attr(get_option('contact_phone')); ?>" class="form-input-text" placeholder="01XXXXXXXXX">
                                    <p class="description">Used for the Call button on product pages.</p>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">WhatsApp Number</div>
                                <td>
                                    <input type="text" name="woocom_whatsapp_number" value="<?php echo esc_attr(get_option('woocom_whatsapp_number')); ?>" class="form-input-text" placeholder="8801XXXXXXXXX">
                                    <p class="description">International format without + sign, e.g. <strong>8801711111111</strong>. Used for the WhatsApp button on product pages.</p>
                                </td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Email Address</div>
                                <td><input type="email" name="contact_email" value="<?php echo esc_attr(get_option('contact_email')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Physical Address</div>
                                <td><textarea name="contact_address" class="form-input-text" rows="3"><?php echo esc_textarea(get_option('contact_address')); ?></textarea></td>
                            </div>
                            <hr>
                            <div class="form-row">
                                <div class="form-label">Facebook URL</div>
                                <td><input type="text" name="social_facebook" value="<?php echo esc_attr(get_option('social_facebook')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Instagram URL</div>
                                <td><input type="text" name="social_instagram" value="<?php echo esc_attr(get_option('social_instagram')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Twitter/X URL</div>
                                <td><input type="text" name="social_twitter" value="<?php echo esc_attr(get_option('social_twitter')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">YouTube URL</div>
                                <td><input type="text" name="social_youtube" value="<?php echo esc_attr(get_option('social_youtube')); ?>" class="form-input-text"></td>
                            </div>
                        </div>
                    <?php elseif ($active_tab == 'language') : ?>
                        <div class="section-header">
                            <h2>Language & Texts</h2>
                            <p>Edit product action labels and stock request text used across the shop.</p>
                        </div>
                        <div class="form-grid">
                            <div class="form-row">
                                <div class="form-label">Add to Cart Text</div>
                                <td><input type="text" name="woocom_text_add_to_cart" value="<?php echo esc_attr(get_option('woocom_text_add_to_cart', 'Add To Cart')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Buy Now Text</div>
                                <td><input type="text" name="woocom_text_buy_now" value="<?php echo esc_attr(get_option('woocom_text_buy_now', 'Buy Now')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">See Details Text</div>
                                <td><input type="text" name="woocom_text_see_details" value="<?php echo esc_attr(get_option('woocom_text_see_details', 'See Details')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Stock Out Text</div>
                                <td><input type="text" name="woocom_text_stock_out" value="<?php echo esc_attr(get_option('woocom_text_stock_out', 'Out of stock')); ?>" class="form-input-text"></td>
                            </div>
                            <div class="form-row">
                                <div class="form-label">Pre Order Text</div>
                                <td><input type="text" name="woocom_text_pre_order" value="<?php echo esc_attr(get_option('woocom_text_pre_order', 'Pre Order')); ?>" class="form-input-text"></td>
                            </div>
                        </div>
                    <?php elseif ($active_tab == 'analytics') : ?>
                        <div class="section-header">
                            <h2>Analytics & Tracking</h2>
                            <p>Configure Google Tag Manager, Google Analytics 4, and Meta Pixel for your store.</p>
                        </div>

                        <!-- GTM -->
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:24px 28px;margin-bottom:24px;">
                            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#4285F4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                </div>
                                <div>
                                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0f172a;">Google Tag Manager</h3>
                                    <p style="margin:0;font-size:13px;color:#64748b;">Recommended: manage GA4 & Pixel from one place</p>
                                </div>
                                <div style="margin-left:auto;">
                                    <label class="switch">
                                        <input type="checkbox" name="woocom_enable_gtm" value="1" <?php checked(1, get_option('woocom_enable_gtm', 0)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-label" style="padding-top:10px;">Container ID</div>
                                <td>
                                    <input type="text" name="woocom_gtm_id" value="<?php echo esc_attr(get_option('woocom_gtm_id', '')); ?>" class="form-input-text" placeholder="GTM-XXXXXXX">
                                    <p class="description" style="margin-top:6px;">Find this in your GTM account under Container settings.</p>
                                </td>
                            </div>
                        </div>

                        <!-- GA4 -->
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:24px 28px;margin-bottom:24px;">
                            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#EA4335;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                </div>
                                <div>
                                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0f172a;">Google Analytics 4</h3>
                                    <p style="margin:0;font-size:13px;color:#64748b;">Direct GA4 — use only if NOT managing via GTM</p>
                                </div>
                                <div style="margin-left:auto;">
                                    <label class="switch">
                                        <input type="checkbox" name="woocom_enable_ga4" value="1" <?php checked(1, get_option('woocom_enable_ga4', 0)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-label" style="padding-top:10px;">Measurement ID</div>
                                <td>
                                    <input type="text" name="woocom_ga4_id" value="<?php echo esc_attr(get_option('woocom_ga4_id', '')); ?>" class="form-input-text" placeholder="G-XXXXXXXXXX">
                                    <p class="description" style="margin-top:6px;">Found in GA4 → Admin → Data Streams → your stream.</p>
                                </td>
                            </div>
                        </div>

                        <!-- Meta Pixel -->
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:24px 28px;margin-bottom:24px;">
                            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                                <div style="width:38px;height:38px;border-radius:10px;background:#1877F2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </div>
                                <div>
                                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#0f172a;">Meta Pixel</h3>
                                    <p style="margin:0;font-size:13px;color:#64748b;">Facebook/Instagram ad conversion tracking</p>
                                </div>
                                <div style="margin-left:auto;">
                                    <label class="switch">
                                        <input type="checkbox" name="woocom_enable_pixel" value="1" <?php checked(1, get_option('woocom_enable_pixel', 0)); ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-label" style="padding-top:10px;">Pixel ID</div>
                                <td>
                                    <input type="text" name="woocom_pixel_id" value="<?php echo esc_attr(get_option('woocom_pixel_id', '')); ?>" class="form-input-text" placeholder="1234567890123456">
                                    <p class="description" style="margin-top:6px;">Found in Meta Events Manager → your Pixel → Settings.</p>
                                </td>
                            </div>
                        </div>

                        <!-- Tracked Events Info -->
                        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:20px 24px;">
                            <h4 style="margin:0 0 12px 0;color:#166534;font-size:14px;font-weight:700;">Automatically Tracked Events</h4>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <?php
                                $events = array(
                                    array('PageView', 'সব পেজ'),
                                    array('ViewContent / view_item', 'প্রোডাক্ট পেজ'),
                                    array('AddToCart / add_to_cart', 'কার্টে যোগ'),
                                    array('InitiateCheckout / begin_checkout', 'চেকআউট শুরু'),
                                    array('Purchase / purchase', 'অর্ডার সম্পন্ন'),
                                );
                                foreach ($events as $ev) : ?>
                                <div style="display:flex;align-items:center;gap:8px;background:white;padding:8px 12px;border-radius:8px;border:1px solid #dcfce7;">
                                    <span style="color:#16a34a;">✓</span>
                                    <div>
                                        <span style="font-size:12px;font-weight:600;color:#166534;"><?php echo $ev[0]; ?></span>
                                        <span style="font-size:11px;color:#64748b;margin-left:6px;"><?php echo $ev[1]; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php endif; ?>

                    <!-- Important: Persist all options regardless of active tab -->
                    <?php 
                    $all_fields = array(
                        'branding' => array('theme_logo', 'footer_logo', 'woocom_primary_color', 'woocom_secondary_color', 'woocom_main_background_color', 'woocom_font_bengali', 'woocom_font_english'),
                        'header' => array('sticky_header', 'nav_bg_color', 'nav_text_color', 'nav_hover_color', 'nav_vertical_padding'),
                        'banners' => array('hero_banner_1', 'hero_banner_1_link', 'hero_banner_2', 'hero_banner_2_link', 'hero_side_banner', 'hero_side_banner_link', 'promo_banner_1', 'promo_banner_1_link', 'promo_banner_2', 'promo_banner_2_link', 'woocom_hero_slides'),
                        'layout' => array('show_hero_section', 'show_featured_categories', 'show_top_selling', 'show_category_sections', 'show_combo_offers', 'woocom_show_just_for_you', 'show_dual_banners', 'woocom_combo_title', 'woocom_combo_image', 'woocom_top_selling_title', 'woocom_top_selling_image', 'woocom_featured_orderby', 'woocom_featured_order', 'woocom_latest_orderby', 'woocom_latest_order', 'woocom_just_for_you_title', 'woocom_just_for_you_image'),
                        'collections' => array('woocom_featured_categories', 'woocom_category_sections', 'woocom_combo_bundles'),
                        'cart' => array('enable_cart_drawer', 'cart_drawer_floating_visibility', 'cart_drawer_title', 'cart_promo_enabled', 'cart_promo_title', 'cart_promo_min_amount', 'show_cross_sell', 'cross_sell_title', 'cross_sell_autoslide', 'checkout_button_shake', 'sticky_checkout_mobile', 'product_add_to_cart_button_color', 'product_buy_now_button_color', 'product_whatsapp_button_color', 'product_call_button_color', 'variation_unavailable_message'),
                        'contact' => array('contact_phone', 'woocom_whatsapp_number', 'contact_email', 'contact_address', 'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube'),
                        'footer'    => array('woocom_footer_information_title', 'woocom_footer_shop_title', 'woocom_footer_support_title', 'woocom_footer_policy_title', 'woocom_footer_information_links', 'woocom_footer_shop_links', 'woocom_footer_support_links', 'woocom_footer_policy_links'),
                        'language' => array('woocom_text_add_to_cart', 'woocom_text_buy_now', 'woocom_text_see_details', 'woocom_text_stock_out', 'woocom_text_pre_order'),
                        'analytics' => array('woocom_enable_gtm', 'woocom_gtm_id', 'woocom_enable_ga4', 'woocom_ga4_id', 'woocom_enable_pixel', 'woocom_pixel_id'),
                        'ticker' => array('ticker_enabled', 'ticker_text', 'ticker_bg_color', 'ticker_text_color', 'ticker_speed', 'ticker_font_size', 'ticker_padding', 'ticker_icon'),
                    );

                    $boolean_fields = array('show_hero_section','show_featured_categories','show_top_selling','show_category_sections','show_combo_offers','woocom_show_just_for_you','show_dual_banners','sticky_header','enable_cart_drawer','cart_promo_enabled','show_cross_sell','cross_sell_autoslide','checkout_button_shake','sticky_checkout_mobile','woocom_enable_gtm','woocom_enable_ga4','woocom_enable_pixel','ticker_enabled');
                    foreach ($all_fields as $tab => $fields) {
                        if ($active_tab !== $tab) {
                            foreach ($fields as $field) {
                                $val = get_option($field);
                                if (is_array($val)) {
                                    foreach ($val as $index => $v) {
                                        if (is_array($v)) {
                                            foreach ($v as $sub_key => $sub_val) {
                                                if (is_array($sub_val)) {
                                                    foreach ($sub_val as $sub_sub_val) {
                                                        echo '<input type="hidden" name="' . esc_attr($field) . '[' . esc_attr($index) . '][' . esc_attr($sub_key) . '][]" value="' . esc_attr($sub_sub_val) . '">';
                                                    }
                                                } else {
                                                    echo '<input type="hidden" name="' . esc_attr($field) . '[' . esc_attr($index) . '][' . esc_attr($sub_key) . ']" value="' . esc_attr($sub_val) . '">';
                                                }
                                            }
                                        } else {
                                            echo '<input type="hidden" name="' . esc_attr($field) . '[]" value="' . esc_attr($v) . '">';
                                        }
                                    }
                                } elseif (in_array($field, $boolean_fields)) {
                                    $bool_val = ($val === '1' || $val === 1) ? '1' : '0';
                                    echo '<input type="hidden" name="' . esc_attr($field) . '" value="' . esc_attr($bool_val) . '">';
                                } elseif ($val !== '' && $val !== false) {
                                    echo '<input type="hidden" name="' . esc_attr($field) . '" value="' . esc_attr($val) . '">';
                                }
                            }
                        }
                    }
                    ?>

                    <div class="submit-footer">
                        <?php submit_button('Save Theme Settings', 'primary woocom-save-btn'); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Color Picker
            $('.wp-color-picker-field').wpColorPicker();

            // Image Upload
            var mediaUploader;
            $('.upload_image_button').click(function (e) {
                e.preventDefault();
                var button = $(this);
                var target = button.data('target');
                var preview = $('#' + target + '_preview');
                var input = $('#' + target + '_url');

                if (mediaUploader) { mediaUploader.off('select'); }
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: 'Select Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                mediaUploader.on('select', function () {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    input.val(attachment.url);
                    preview.html('<img src="' + attachment.url + '">');
                    // Special case for logo
                    if (target === 'theme_logo') {
                        preview.find('img').css('max-height', '80px');
                    }
                });
                mediaUploader.open();
            });

            $('.remove_image_button').click(function (e) {
                e.preventDefault();
                var button = $(this);
                var target = button.data('target');
                $('#' + target + '_url').val('');
                $('#' + target + '_preview').html('<span class="dashicons dashicons-format-image" style="font-size: 48px; color: #cbd5e1;"></span>');
            });

            $('.footer-add-link-row').on('click', function () {
                var group = $(this).closest('.footer-link-group');
                var option = group.data('option');
                var rows = group.find('.footer-link-rows');
                var index = rows.find('.footer-link-row').length;
                var row = $('<div class="footer-link-row" style="display: grid; grid-template-columns: minmax(180px, 1fr) minmax(220px, 1.4fr); gap: 10px;"></div>');

                row.append('<input type="text" name="' + option + '[' + index + '][label]" value="" class="form-input-text" placeholder="Link text">');
                row.append('<input type="text" name="' + option + '[' + index + '][url]" value="" class="form-input-text" placeholder="https://example.com/page">');
                rows.append(row);
                row.find('input:first').focus();
            });
        });
    </script>
    <?php
}

/**
 * Helper function to get featured categories
 */
function woocom_get_featured_categories() {
    $category_ids = get_option('woocom_featured_categories', array());
    if (is_array($category_ids)) { $category_ids = array_filter($category_ids); }
    if (empty($category_ids)) { return array(); }
    return get_terms(array('taxonomy' => 'product_cat', 'include' => $category_ids, 'hide_empty' => false, 'orderby' => 'include'));
}

/**
 * Helper function to get category sections
 */
function woocom_get_category_sections() {
    $category_ids = get_option('woocom_category_sections', array());
    if (is_array($category_ids)) { $category_ids = array_filter($category_ids); }
    if (empty($category_ids)) { return array(); }
    return get_terms(array('taxonomy' => 'product_cat', 'include' => $category_ids, 'hide_empty' => false, 'orderby' => 'include'));
}

/**
 * Helper function to get top selling products
 */
function woocom_get_top_selling_products() {
    $orderby = get_option('woocom_featured_orderby', 'menu_order');
    $order   = get_option('woocom_featured_order', 'ASC');

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'orderby'        => $orderby,
        'order'          => $order,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            ),
        ),
    );

    if ( 'price' === $orderby ) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = '_price';
    }
    if ( 'sales' === $orderby ) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'total_sales';
        $args['order']    = 'DESC';
    }

    return new WP_Query($args);
}

/**
 * Helper function to get combo bundles
 */
function woocom_get_combo_bundles() {
    $bundles = get_option( 'woocom_combo_bundles', array() );
    if ( ! is_array( $bundles ) ) return array();
    return array_values( array_filter( $bundles, function( $b ) {
        return ! empty( $b['products'] );
    } ) );
}
