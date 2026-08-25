<?php
/**
 * Global and Archive Page Custom CSS for Woom-1 Theme
 *
 * @package Woocom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global Custom CSS — Colors + Navigation
 */
add_action('wp_head', 'woocom_global_custom_css', 10);
function woocom_global_custom_css() {
    // ── Transient cache: rebuild only after a settings change or 24 h ──────────
    delete_transient( 'woocom_global_css_vars' );
    $css = false;
    if ( false === $css ) {
        $primary   = get_option('woocom_primary_color') ?: '#2563EB';
        $secondary = get_option('woocom_secondary_color') ?: '#2563EB';
        if ($secondary === '#F7A501' || empty($secondary)) {
            $secondary = $primary;
        }
        $main_background = get_option('woocom_main_background_color', '#FBF9F5') ?: '#FBF9F5';
        $product_add_to_cart_color = get_option('product_add_to_cart_button_color', $primary) ?: $primary;
        if ($product_add_to_cart_color === '#F7A501') {
            $product_add_to_cart_color = $primary;
        }
        $product_buy_now_color     = get_option('product_buy_now_button_color', $primary) ?: $primary;
        $product_whatsapp_color    = get_option('product_whatsapp_button_color', '#25D366') ?: '#25D366';
        $product_call_color        = get_option('product_call_button_color', '#1e3a8a') ?: '#1e3a8a';
        $nav_bg    = get_option('nav_bg_color', '#000000');
        $nav_text  = get_option('nav_text_color', '#ffffff');
        $nav_hover = get_option('nav_hover_color', '#F7A501');
        if ($nav_hover === '#F7A501' || empty($nav_hover)) {
            $nav_hover = $primary;
        }
        $nav_pad   = (int) get_option('nav_vertical_padding', '12');
        $font_bengali = get_option('woocom_font_bengali', 'Noto Serif Bengali');
        $font_english = get_option('woocom_font_english', 'Inter');

        $ticker = array(
            'bg'        => get_option('ticker_bg_color', '#1E5D02') ?: '#1E5D02',
            'color'     => get_option('ticker_text_color', '#ffffff') ?: '#ffffff',
            'speed'     => intval(get_option('ticker_speed', '20')) ?: 20,
            'font_size' => intval(get_option('ticker_font_size', '14')) ?: 14,
            'padding'   => intval(get_option('ticker_padding', '8')),
        );

        ob_start();
        ?>
    <style>
        :root {
            --color-primary: <?php echo esc_attr($primary); ?>;
            --color-secondary: <?php echo esc_attr($secondary); ?>;
            --main-background-color: <?php echo esc_attr($main_background); ?>;
            --product-add-to-cart-bg: <?php echo esc_attr($product_add_to_cart_color); ?>;
            --product-buy-now-bg: <?php echo esc_attr($product_buy_now_color); ?>;
            --product-whatsapp-bg: <?php echo esc_attr($product_whatsapp_color); ?>;
            --product-call-bg: <?php echo esc_attr($product_call_color); ?>;
            --nav-bg: <?php echo esc_attr($nav_bg); ?>;
            --nav-text: <?php echo esc_attr($nav_text); ?>;
            --nav-hover: <?php echo esc_attr($nav_hover); ?>;
            --font-bengali: '<?php echo esc_attr($font_bengali); ?>';
            --font-english: '<?php echo esc_attr($font_english); ?>';
            --ticker-bg: <?php echo esc_attr($ticker['bg']); ?>;
            --ticker-color: <?php echo esc_attr($ticker['color']); ?>;
            --ticker-speed: <?php echo esc_attr($ticker['speed']); ?>;
            --ticker-font-size: <?php echo esc_attr($ticker['font_size']); ?>;
            --ticker-padding: <?php echo esc_attr($ticker['padding']); ?>;
        }

        body,
        body h1,
        body h2,
        body h3,
        body h4,
        body h5,
        body h6,
        body p,
        body a,
        body span,
        body div,
        body li,
        body ul,
        body ol,
        body td,
        body th,
        body label,
        body button,
        body input,
        body select,
        body textarea {
            font-family: var(--font-bengali), var(--font-english), "Open Sans", serif, sans-serif !important;
        }

        /* Ensure form elements and specific components also respect the exclusion */
        #wpadminbar * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
        }

        body,
        body.bg-gray-50,
        #page,
        #primary,
        #main,
        main.site-main,
        .site-main,
        .woocommerce-page-shell,
        .page-content-shell,
        .site-main > .bg-gray-50,
        .site-main > .bg-\[\#F9F9F9\],
        #page > .bg-gray-50,
        .woocommerce-page #primary,
        .woocommerce-page #main,
        .woocommerce-page main,
        .woocommerce-page .site-main,
        .woocommerce-page .bg-gray-50,
        .woocommerce-checkout .checkout-page-wrapper,
        .checkout-page-wrapper,
        .thank-you-page-wrapper,
        .top-selling-products {
            background-color: var(--main-background-color) !important;
        }

        /* ===== Navigation Bar ===== */
        .desktop-nav {
            background-color: var(--nav-bg) !important;
        }
        .desktop-nav ul {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 32px !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .desktop-nav li {
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }
        .desktop-nav .menu-item > a {
            color: var(--nav-text) !important;
            padding-top: <?php echo $nav_pad; ?>px !important;
            padding-bottom: <?php echo $nav_pad; ?>px !important;
            display: flex !important;
            align-items: center;
            gap: 6px;
            transition: color 0.3s ease;
            text-decoration: none;
        }
        .desktop-nav .menu-item > a:hover,
        .desktop-nav .current-menu-item > a,
        .desktop-nav .current-menu-ancestor > a {
            color: var(--nav-hover) !important;
        }
        /* Dropdown */
        .desktop-nav .menu-item-has-children {
            position: relative;
        }
        .desktop-nav .sub-menu {
            position: absolute !important;
            top: 100%;
            left: 0;
            background: white;
            min-width: 240px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(15px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 9999;
            border-radius: 0 0 12px 12px;
            border-top: 3px solid var(--nav-hover);
            padding: 10px 0;
            list-style: none;
            margin: 0;
        }
        .desktop-nav .menu-item-has-children:hover > .sub-menu {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        .desktop-nav .sub-menu .menu-item {
            border-bottom: 1px solid #f1f5f9;
        }
        .desktop-nav .sub-menu .menu-item:last-child {
            border-bottom: none;
        }
        .desktop-nav .sub-menu .menu-item a {
            color: #334155 !important;
            padding: 11px 22px !important;
            display: block !important;
            font-size: 14px !important;
            font-weight: 600;
            text-transform: none !important;
            letter-spacing: normal;
            transition: all 0.2s ease;
        }
        .desktop-nav .sub-menu .menu-item a:hover {
            background: #f8fafc;
            color: var(--nav-hover) !important;
            padding-left: 28px !important;
        }

        /* ===== Announcement Ticker ===== */
        .woocom-ticker-container {
            overflow: hidden;
            background-color: var(--ticker-bg);
            color: var(--ticker-color);
            padding: calc(var(--ticker-padding) * 1px) 0;
            font-size: calc(var(--ticker-font-size) * 1px);
            width: 100%;
            display: flex;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .woocom-ticker-content {
            display: flex;
            white-space: nowrap;
            animation: woocom-ticker-marquee calc(var(--ticker-speed) * 1s) linear infinite;
            will-change: transform;
        }
        .woocom-ticker-track {
            display: flex;
            align-items: center;
        }
        .woocom-ticker-item {
            display: inline-flex;
            align-items: center;
            padding: 0 1.5rem;
            font-weight: 700;
        }
        .woocom-ticker-item svg {
            margin-right: 0.5rem;
            flex-shrink: 0;
            width: 1.25em;
            height: 1.25em;
        }
        @keyframes woocom-ticker-marquee {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }
        .woocom-ticker-container:hover .woocom-ticker-content {
            animation-play-state: paused;
        }

        /* Accessibility: prefers-reduced-motion */
        @media (prefers-reduced-motion: reduce) {
            .woocom-ticker-content {
                animation: none !important;
                white-space: normal !important;
                flex-wrap: wrap !important;
                justify-content: center !important;
            }
            .woocom-ticker-track:last-child {
                display: none !important;
            }
        }

        /* Mobile Responsive Spacing and Font Adjustments */
        @media (max-width: 768px) {
            .woocom-ticker-container {
                font-size: clamp(12px, 3.5vw, calc(var(--ticker-font-size) * 0.9 * 1px)) !important;
                padding: calc(var(--ticker-padding) * 0.8 * 1px) 0 !important;
            }
            .woocom-ticker-item {
                padding: 0 1rem !important;
            }
        }
    </style>
        <?php
        $css = ob_get_clean();
        set_transient( 'woocom_global_css_vars', $css, DAY_IN_SECONDS );
    }

    echo $css; // Already escaped — values were esc_attr()'d before storing.
}

/**
 * Styling WooCommerce Orderby Select
 */
add_action('wp_head', 'woocom_archive_custom_css', 999);
function woocom_archive_custom_css() {
    if ( ! function_exists( 'is_shop' ) ) {
        return;
    }
    if (is_shop() || is_product_category() || is_product_tag() || is_checkout()) {
        ?>
        <style>
            .woocommerce-ordering select {
                background-color: #F9FAFB !important;
                border: 1px solid #E5E7EB !important;
                border-radius: 8px !important;
                padding: 8px 36px 8px 12px !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                color: #374151 !important;
                cursor: pointer !important;
                appearance: none !important;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
                background-repeat: no-repeat !important;
                background-position: right 12px center !important;
                background-size: 16px !important;
            }
            .woocommerce-ordering select:focus {
                outline: none !important;
                border-color: var(--color-secondary) !important;
                ring: 2px !important;
            }
            /* Grid layout fix for WooCommerce */
            .woocommerce ul.products.columns-4::before, 
            .woocommerce ul.products.columns-4::after { display: none !important; }
            .woocommerce ul.products { display: grid !important; margin: 0 !important; padding: 0 !important; }
            .woocommerce ul.products li.product { width: 100% !important; margin: 0 !important; float: none !important; }

            /* Checkout Refinements */
            .phone-prefix-88 .woocommerce-input-wrapper {
                position: relative;
                display: flex !important;
                align-items: center;
            }
            .phone-prefix-88 .woocommerce-input-wrapper::before {
                content: "88";
                position: absolute;
                left: 12px;
                font-size: 13px;
                font-weight: 600;
                color: #374151;
                padding-right: 8px;
                border-right: 1px solid #E5E7EB;
                line-height: 1;
            }
            .phone-prefix-88 .woocommerce-input-wrapper input {
                padding-left: 45px !important;
            }
            .woocommerce-checkout .form-row {
                margin-bottom: 1rem !important;
            }
            .woocommerce-checkout select {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                background-size: 16px;
                padding-right: 36px !important;
            }
            #billing-address-toggle .bg-secondary {
                background-color: var(--color-secondary) !important;
                border-color: var(--color-secondary) !important;
                position: relative;
            }
            #billing-address-toggle .bg-secondary::after {
                content: "";
                position: absolute;
                width: 8px;
                height: 8px;
                background: white;
                border-radius: 50%;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            /* Global Checkout Background */
            body.woocommerce-checkout,
            .woocommerce-checkout .checkout-page-wrapper,
            .woocommerce-checkout #primary,
            .woocommerce-checkout #main {
                background-color: var(--main-background-color) !important;
                background: var(--main-background-color) !important;
            }

            .checkout-page-wrapper > .checkout-content-container {
                width: 100% !important;
                max-width: 1320px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                box-sizing: border-box !important;
            }

            /* Payment Area Specific White Reset */
            .woocommerce-checkout #payment, 
            .woocommerce-checkout .woocommerce-checkout-payment,
            .woocommerce-checkout #payment ul.payment_methods,
            .woocommerce-checkout #payment div.payment_box {
                background-color: white !important;
                background: white !important;
                border: none !important;
                box-shadow: none !important;
            }

            .woocommerce-checkout #payment ul.payment_methods {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                grid-auto-flow: dense !important;
                gap: 8px !important;
                margin: 0 !important;
                padding: 0 !important;
                list-style: none !important;
                width: 100% !important;
            }
            .woocommerce-checkout #payment ul.payment_methods > li {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            /* Hide any non-li children of the payment list that might break grid */
            .woocommerce-checkout #payment ul.payment_methods > *:not(li) {
                display: none !important;
            }
            .woocommerce-checkout #payment ul.payment_methods::before,
            .woocommerce-checkout #payment ul.payment_methods::after {
                display: none !important;
                content: none !important;
            }
            .woocommerce-billing-fields h3,
            .woocommerce-info,
            #payment-custom-area #place_order,
            #payment-custom-area .place-order {
                display: none !important;
            }
            
            /* Modernized Error/Message Styling */
            .woocommerce-error, 
            .woocommerce-message {
                display: block !important;
                background: #fff !important;
                border: none !important;
                border-left: 4px solid #ef4444 !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
                border-radius: 8px !important;
                padding: 15px 20px !important;
                margin-bottom: 25px !important;
                color: #ef4444 !important;
                font-size: 14px !important;
                list-style: none !important;
            }
            .woocommerce-message {
                border-left-color: #10b981 !important;
                color: #10b981 !important;
            }
            .woocommerce-error li {
                margin: 5px 0 !important;
                padding: 0 !important;
            }
            @media (max-width: 1023px) {
                /* === Critical: prevent horizontal scroll on checkout mobile === */
                html.woocommerce-checkout-page,
                html {
                    overflow-x: hidden !important;
                }
                body.woocommerce-checkout {
                    overflow-x: hidden !important;
                    max-width: 100vw !important;
                    position: relative !important;
                }
                /* Prevent any child from pushing beyond viewport */
                body.woocommerce-checkout *:not(#cart-drawer):not(.fixed):not([style*="width:100vw"]) {
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                }
                /* WooCommerce review order table overflow */
                .woocommerce-checkout-review-order,
                .woocommerce-checkout-review-order-table {
                    overflow-x: auto !important;
                    max-width: 100% !important;
                }
            }

            @media (max-width: 767px) {
                .woocommerce-checkout #payment ul.payment_methods {
                    grid-template-columns: 1fr !important;
                }
                .checkout-page-wrapper {
                    padding-top: 1.5rem !important;
                    padding-bottom: 1.5rem !important;
                }
                #order_review .flex.items-center.p-4 {
                    padding: 0.75rem !important;
                    gap: 0.75rem !important;
                }
                #order_review .w-16.h-16 {
                    width: 3.5rem !important;
                    height: 3.5rem !important;
                }
                #order_review h4 {
                    font-size: 0.875rem !important;
                }
                #order_review .text-xs {
                    font-size: 0.75rem !important;
                }
                
                /* Mobile checkout uses the submit button as the bottom sticky action. */
                body.woocommerce-checkout.has-sticky-checkout-mobile,
                body.woocom-checkout-page.has-sticky-checkout-mobile {
                    padding-bottom: 74px !important;
                }
                body.has-sticky-checkout-mobile .checkout-page-wrapper {
                    padding-bottom: 5rem !important;
                }
                body.woocommerce-checkout .mobile-bottom-navigation,
                body.woocom-checkout-page .mobile-bottom-navigation {
                    display: none !important;
                }
                body.woocommerce-checkout.has-sticky-checkout-mobile .checkout-place-order,
                body.woocom-checkout-page.has-sticky-checkout-mobile .checkout-place-order,
                body.has-sticky-checkout-mobile .checkout-page-wrapper .checkout-place-order {
                    position: fixed !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    z-index: 160 !important;
                    padding: 7px 12px calc(7px + env(safe-area-inset-bottom)) !important;
                    background: rgba(255, 255, 255, 0.96) !important;
                    border-top: 1px solid rgba(226, 232, 240, 0.9) !important;
                    box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.12) !important;
                }
                body.woocommerce-checkout.has-sticky-checkout-mobile .checkout-place-order .checkout-terms-row,
                body.woocom-checkout-page.has-sticky-checkout-mobile .checkout-place-order .checkout-terms-row,
                body.has-sticky-checkout-mobile .checkout-page-wrapper .checkout-place-order .checkout-terms-row {
                    display: none !important;
                }
                body.woocommerce-checkout.has-sticky-checkout-mobile .checkout-place-order #place_order,
                body.woocom-checkout-page.has-sticky-checkout-mobile .checkout-place-order #place_order,
                body.has-sticky-checkout-mobile .checkout-page-wrapper .checkout-place-order #place_order {
                    min-height: 44px !important;
                    padding-top: 0.75rem !important;
                    padding-bottom: 0.75rem !important;
                    border-radius: 8px !important;
                }
            }
        </style>
        <?php
    }
}

/**
 * Global Custom CSS for Search Focus Ring overrides
 */
add_action('wp_head', 'woocom_global_search_focus_css', 9999);
function woocom_global_search_focus_css() {
    ?>
    <style>
        /* Modern Premium Search Focus Ring styling */
        #desktop-search-input:focus,
        #mobile-header-search-input:focus,
        #mobile-search-input:focus,
        input[type="search"]:focus {
            outline: none !important;
            box-shadow: none !important;
            border-color: var(--color-primary, #2563EB) !important;
        }
    </style>
    <?php
}
