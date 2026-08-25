<?php
/**
 * WooFashion Theme Settings Admin Panel & Options Handler
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue Media Uploader for Theme Settings page
 */
function woofashion_admin_theme_settings_assets( $hook ) {
    if ( strpos( $hook, 'woofashion-theme-settings' ) === false ) {
        return;
    }
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'woofashion_admin_theme_settings_assets' );

/**
 * Get default theme settings
 */
function woofashion_get_default_theme_settings() {
    return array(
        'general' => array(
            'site_title'         => get_bloginfo( 'name' ),
            'site_tagline'       => get_bloginfo( 'description' ),
            'brand_name'         => 'WoocomFashion',
            'logo_url'           => '',
            'footer_logo_url'    => '',
            'logo_height'        => '65',
            'primary_color'      => '#f59e0b',
            'whatsapp_number'    => '+8801700000000',
            'whatsapp_enable'    => 'yes',
        ),
        'header' => array(
            'top_announcement'   => 'Free Shipping on orders over ৳1000 | 24/7 Dedicated Support',
            'hotline_phone'      => '+880 9612-888999',
            'support_email'      => 'support@woocomfashion.com',
            'enable_track_order' => 'yes',
        ),
        'homepage_sections' => array(
            'hero_slider'        => 'yes',
            'features_bar'       => 'yes',
            'flash_sale'         => 'yes',
            'category_slider'    => 'yes',
            'special_products'   => 'yes',
            'trending_products'  => 'yes',
            'best_selling'       => 'yes',
            'new_arrivals'       => 'yes',
            'favourite_products' => 'yes',
            'brand_marquee'      => 'yes',
            'blog_section'       => 'yes',
            'subscription'       => 'yes',
        ),
        'section_titles' => array(
            'special_title'      => 'Our Spatial Brand Products',
            'trending_title'     => 'Our Trending Products',
            'best_selling_title' => 'Our Best Selling Products',
            'new_arrivals_title' => 'Our New Arrival Products',
            'flash_sale_title'   => 'Flash Sale',
            'categories_title'   => 'Popular Categories',
            'blog_title'         => 'Latest News & Articles',
        ),
        'footer' => array(
            'about_bio'          => 'WoocomFashion is your premium destination for the latest fashion trends. Discover curated collections crafted for comfort and style.',
            'contact_address'    => '37 W 24th St, New York, NY / Dhaka, Bangladesh',
            'contact_phone'      => '+123 324 5879 39',
            'contact_email'      => 'info@WoocomFashion.com',
            'copyright_text'     => 'Copyright @ WoocomFashion 2026. All rights reserved.',
            'facebook'           => 'https://facebook.com',
            'twitter'            => 'https://twitter.com',
            'instagram'          => 'https://instagram.com',
            'linkedin'           => 'https://linkedin.com',
            'youtube'            => '',
        )
    );
}

/**
 * Get all theme settings (merged with defaults)
 */
function woofashion_get_theme_settings() {
    $saved = get_option( 'woofashion_theme_settings', array() );
    $defaults = woofashion_get_default_theme_settings();

    if ( ! is_array( $saved ) ) {
        $saved = array();
    }

    $merged = array();
    foreach ( $defaults as $group_key => $group_values ) {
        $merged[ $group_key ] = array();
        foreach ( $group_values as $key => $default_val ) {
            if ( isset( $saved[ $group_key ][ $key ] ) ) {
                $merged[ $group_key ][ $key ] = $saved[ $group_key ][ $key ];
            } else {
                $merged[ $group_key ][ $key ] = $default_val;
            }
        }
    }

    return $merged;
}

/**
 * Register Theme Settings Admin Menu
 */
function woofashion_register_admin_menu() {
    add_menu_page(
        __( 'Theme Settings', 'woofashion-spa' ),
        __( 'Theme Settings', 'woofashion-spa' ),
        'manage_options',
        'woofashion-theme-settings',
        'woofashion_render_settings_page',
        'dashicons-admin-customizer',
        59
    );
}
add_action( 'admin_menu', 'woofashion_register_admin_menu' );

/**
 * Save Theme Settings (Unified all-tab save)
 */
function woofashion_save_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Unauthorized access.', 'woofashion-spa' ) );
    }

    check_admin_referer( 'woofashion_settings_save_action', 'woofashion_settings_nonce' );

    $active_tab = sanitize_key( $_POST['active_tab'] ?? 'general' );
    $raw = isset( $_POST['woofashion_settings'] ) ? (array) $_POST['woofashion_settings'] : array();
    
    $saved = woofashion_get_theme_settings();
    $defaults = woofashion_get_default_theme_settings();

    // General & Branding
    if ( isset( $raw['general'] ) ) {
        $saved['general']['site_title']      = sanitize_text_field( $raw['general']['site_title'] ?? $defaults['general']['site_title'] );
        $saved['general']['brand_name']      = sanitize_text_field( $raw['general']['brand_name'] ?? $defaults['general']['brand_name'] );
        $saved['general']['logo_url']        = esc_url_raw( $raw['general']['logo_url'] ?? '' );
        $saved['general']['footer_logo_url'] = esc_url_raw( $raw['general']['footer_logo_url'] ?? '' );
        $saved['general']['logo_height']     = isset( $raw['general']['logo_height'] ) ? absint( $raw['general']['logo_height'] ) : 65;
        $saved['general']['primary_color']   = sanitize_hex_color( $raw['general']['primary_color'] ?? '#f59e0b' );
        $saved['general']['whatsapp_number'] = sanitize_text_field( $raw['general']['whatsapp_number'] ?? '' );
        $saved['general']['whatsapp_enable'] = isset( $raw['general']['whatsapp_enable'] ) ? 'yes' : 'no';
    }

    // Homepage Sections & Titles
    if ( isset( $raw['homepage_sections'] ) || isset( $raw['section_titles'] ) ) {
        foreach ( $defaults['homepage_sections'] as $sec_key => $val ) {
            $saved['homepage_sections'][ $sec_key ] = isset( $raw['homepage_sections'][ $sec_key ] ) ? 'yes' : 'no';
        }
        foreach ( $defaults['section_titles'] as $title_key => $title_val ) {
            $saved['section_titles'][ $title_key ] = sanitize_text_field( $raw['section_titles'][ $title_key ] ?? $title_val );
        }
    }

    // Header Settings
    if ( isset( $raw['header'] ) ) {
        $saved['header']['top_announcement']   = sanitize_text_field( $raw['header']['top_announcement'] ?? '' );
        $saved['header']['hotline_phone']      = sanitize_text_field( $raw['header']['hotline_phone'] ?? '' );
        $saved['header']['support_email']      = sanitize_email( $raw['header']['support_email'] ?? '' );
        $saved['header']['enable_track_order'] = isset( $raw['header']['enable_track_order'] ) ? 'yes' : 'no';
    }

    // Footer Settings
    if ( isset( $raw['footer'] ) ) {
        $saved['footer']['about_bio']       = sanitize_textarea_field( $raw['footer']['about_bio'] ?? '' );
        $saved['footer']['contact_address'] = sanitize_text_field( $raw['footer']['contact_address'] ?? '' );
        $saved['footer']['contact_phone']   = sanitize_text_field( $raw['footer']['contact_phone'] ?? '' );
        $saved['footer']['contact_email']   = sanitize_email( $raw['footer']['contact_email'] ?? '' );
        $saved['footer']['copyright_text']  = sanitize_text_field( $raw['footer']['copyright_text'] ?? '' );
        $saved['footer']['facebook']        = esc_url_raw( $raw['footer']['facebook'] ?? '' );
        $saved['footer']['twitter']         = esc_url_raw( $raw['footer']['twitter'] ?? '' );
        $saved['footer']['instagram']       = esc_url_raw( $raw['footer']['instagram'] ?? '' );
        $saved['footer']['linkedin']        = esc_url_raw( $raw['footer']['linkedin'] ?? '' );
        $saved['footer']['youtube']         = esc_url_raw( $raw['footer']['youtube'] ?? '' );
    }

    update_option( 'woofashion_theme_settings', $saved );

    wp_safe_redirect( add_query_arg( array( 'page' => 'woofashion-theme-settings', 'status' => 'saved', 'tab' => $active_tab ), admin_url( 'admin.php' ) ) );
    exit;
}
add_action( 'admin_post_woofashion_save_settings', 'woofashion_save_settings' );

/**
 * Render Theme Settings Admin Page
 */
function woofashion_render_settings_page() {
    $settings = woofashion_get_theme_settings();
    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
    ?>
    <div class="wrap woofashion-admin-wrap" style="max-width: 1100px; margin-top: 20px;">
        <div style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff; padding: 22px 28px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
            <div>
                <h1 style="color: #fff; margin: 0 0 5px 0; font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                    <span class="dashicons dashicons-admin-customizer" style="font-size: 26px; width: 26px; height: 26px;"></span>
                    WooFashion Theme Settings
                </h1>
                <p style="margin: 0; color: #94a3b8; font-size: 13px;">Manage Store Logo, Homepage Builder, Header, Footer, and Brand details for your React SPA storefront.</p>
            </div>
            <span style="background: #f59e0b; color: #000; font-weight: 700; padding: 4px 12px; border-radius: 20px; font-size: 12px;">v1.0.0</span>
        </div>

        <?php if ( isset( $_GET['status'] ) && $_GET['status'] === 'saved' ) : ?>
            <div class="notice notice-success is-dismissible" style="border-left-color: #10b981; padding: 12px 15px; margin-bottom: 20px; border-radius: 6px;">
                <p style="margin: 0; font-weight: 600; color: #065f46;">✓ Settings saved successfully! All tabs are updated and live on your store.</p>
            </div>
        <?php endif; ?>

        <style>
            .woofashion-tabs { display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 25px; }
            .woofashion-tab-item { padding: 12px 20px; font-size: 14px; font-weight: 600; text-decoration: none; color: #64748b; border-radius: 8px 8px 0 0; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; cursor: pointer; }
            .woofashion-tab-item:hover { color: #0f172a; background: #f1f5f9; }
            .woofashion-tab-item.active { color: #f59e0b; background: #fff; border-bottom: 3px solid #f59e0b; }
            .woofashion-card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 25px; }
            .woofashion-field { margin-bottom: 22px; }
            .woofashion-field label { display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 13.5px; }
            .woofashion-field .desc { font-size: 12px; color: #64748b; margin-top: 4px; }
            .woofashion-switch-row { display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; transition: background 0.2s ease; }
            .woofashion-switch-row:hover { background: #f1f5f9; }
            .woofashion-toggle { position: relative; display: inline-block; width: 46px; height: 24px; }
            .woofashion-toggle input { opacity: 0; width: 0; height: 0; }
            .woofashion-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 24px; }
            .woofashion-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
            input:checked + .woofashion-slider { background-color: #10b981; }
            input:checked + .woofashion-slider:before { transform: translateX(22px); }
            .woofashion-submit-btn { background: #0f172a !important; color: #fff !important; border: none !important; padding: 13px 36px !important; font-size: 14.5px !important; font-weight: 600 !important; border-radius: 6px !important; cursor: pointer !important; transition: background 0.2s ease !important; }
            .woofashion-submit-btn:hover { background: #1e293b !important; }
            .woofashion-media-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 18px; text-align: left; }
            .woofashion-media-preview img { max-height: 80px; max-width: 260px; object-fit: contain; margin-bottom: 12px; display: block; border-radius: 6px; padding: 6px; background: #fff; border: 1px solid #e2e8f0; }
            .woofashion-tab-content { display: none; }
            .woofashion-tab-content.active { display: block; }
        </style>

        <!-- Navigation Tabs (Instant Client-Side Switch) -->
        <div class="woofashion-tabs">
            <div data-tab="general" class="woofashion-tab-item <?php echo $active_tab === 'general' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-generic"></span> Logo & Branding
            </div>
            <div data-tab="homepage" class="woofashion-tab-item <?php echo $active_tab === 'homepage' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-home"></span> Homepage Builder
            </div>
            <div data-tab="header" class="woofashion-tab-item <?php echo $active_tab === 'header' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-arrow-up-alt"></span> Header Settings
            </div>
            <div data-tab="footer" class="woofashion-tab-item <?php echo $active_tab === 'footer' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-arrow-down-alt"></span> Footer Settings
            </div>
        </div>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="woofashion_settings_form">
            <input type="hidden" name="action" value="woofashion_save_settings">
            <input type="hidden" name="active_tab" id="woofashion_active_tab_input" value="<?php echo esc_attr( $active_tab ); ?>">
            <?php wp_nonce_field( 'woofashion_settings_save_action', 'woofashion_settings_nonce' ); ?>

            <!-- GENERAL & BRANDING TAB -->
            <div id="tab-content-general" class="woofashion-tab-content <?php echo $active_tab === 'general' ? 'active' : ''; ?>">
                <div class="woofashion-card">
                    <h3 style="margin-top: 0; font-size: 17px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                        🖼️ Store Logo Upload
                    </h3>

                    <!-- Header Logo -->
                    <div class="woofashion-field">
                        <label>Main / Header Logo</label>
                        <div class="woofashion-media-box">
                            <div class="woofashion-media-preview" id="header_logo_preview">
                                <?php if ( ! empty( $settings['general']['logo_url'] ) ) : ?>
                                    <img src="<?php echo esc_url( $settings['general']['logo_url'] ); ?>" alt="Logo Preview">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="header_logo_input" name="woofashion_settings[general][logo_url]" value="<?php echo esc_attr( $settings['general']['logo_url'] ); ?>">
                            <button type="button" class="button button-secondary woofashion-media-upload-btn" data-target="header_logo_input" data-preview="header_logo_preview">
                                📤 Upload / Select Header Logo
                            </button>
                            <button type="button" class="button button-link-delete woofashion-media-remove-btn" data-target="header_logo_input" data-preview="header_logo_preview" style="<?php echo empty( $settings['general']['logo_url'] ) ? 'display:none;' : ''; ?>">
                                ❌ Remove
                            </button>
                        </div>
                        <div class="desc">💡 টিপস: স্বচ্ছ ব্যাকগ্রাউন্ডযুক্ত (transparent PNG/SVG) ইমেজ ব্যবহার করুন। ইমেজের চারপাশের খালি স্পেস (padding) ক্রপ করে নিলে লোগোটি বড় ও সুন্দর দেখাবে।</div>
                    </div>

                    <!-- Footer Logo -->
                    <div class="woofashion-field">
                        <label>Footer Logo (Optional / Dark Background)</label>
                        <div class="woofashion-media-box">
                            <div class="woofashion-media-preview" id="footer_logo_preview">
                                <?php if ( ! empty( $settings['general']['footer_logo_url'] ) ) : ?>
                                    <img src="<?php echo esc_url( $settings['general']['footer_logo_url'] ); ?>" alt="Footer Logo Preview" style="background: #1e293b;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="footer_logo_input" name="woofashion_settings[general][footer_logo_url]" value="<?php echo esc_attr( $settings['general']['footer_logo_url'] ); ?>">
                            <button type="button" class="button button-secondary woofashion-media-upload-btn" data-target="footer_logo_input" data-preview="footer_logo_preview">
                                📤 Upload / Select Footer Logo
                            </button>
                            <button type="button" class="button button-link-delete woofashion-media-remove-btn" data-target="footer_logo_input" data-preview="footer_logo_preview" style="<?php echo empty( $settings['general']['footer_logo_url'] ) ? 'display:none;' : ''; ?>">
                                ❌ Remove
                            </button>
                        </div>
                        <div class="desc">If left empty, the main header logo will be used automatically in the footer.</div>
                    </div>

                    <!-- Logo Height -->
                    <div class="woofashion-field">
                        <label>Logo Display Height (px)</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="number" min="30" max="150" step="1" style="width: 100px;" name="woofashion_settings[general][logo_height]" value="<?php echo esc_attr( $settings['general']['logo_height'] ?? '65' ); ?>">
                            <span style="color: #64748b; font-size: 13px;">pixels (Default: 65px. Increase to 70px, 80px or more if needed)</span>
                        </div>
                    </div>

                    <h3 style="margin-top: 35px; font-size: 17px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                        🎯 Brand Details & Theme Colors
                    </h3>

                    <div class="woofashion-field">
                        <label>Brand / Store Display Name (Text Fallback)</label>
                        <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[general][brand_name]" value="<?php echo esc_attr( $settings['general']['brand_name'] ); ?>">
                        <div class="desc">Displayed if no image logo is uploaded.</div>
                    </div>

                    <div class="woofashion-field">
                        <label>WhatsApp Quick Support / Order Phone</label>
                        <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[general][whatsapp_number]" value="<?php echo esc_attr( $settings['general']['whatsapp_number'] ); ?>">
                        <div class="desc">International format with country code (e.g. +8801700000000).</div>
                    </div>

                    <div class="woofashion-field">
                        <label>Primary Theme Accent Color</label>
                        <input type="color" style="width: 80px; height: 38px; padding: 2px; border-radius: 6px; border: 1px solid #cbd5e1;" name="woofashion_settings[general][primary_color]" value="<?php echo esc_attr( $settings['general']['primary_color'] ); ?>">
                    </div>
                </div>
            </div>

            <!-- HOMEPAGE BUILDER TAB -->
            <div id="tab-content-homepage" class="woofashion-tab-content <?php echo $active_tab === 'homepage' ? 'active' : ''; ?>">
                <div class="woofashion-card">
                    <h3 style="margin-top: 0; font-size: 17px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                        🏠 Homepage Section Visibility (On/Off)
                    </h3>
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Toggle which sections appear on the homepage of your storefront:</p>

                    <?php
                    $section_labels = array(
                        'hero_slider'        => array( 'Hero Banner Slider', 'Main slider displaying featured hero slides and campaigns.' ),
                        'features_bar'       => array( 'Features / Perks Bar', '4-column banner (Free return, Quality support, Secure payment, etc.)' ),
                        'flash_sale'         => array( 'Flash Sale Section', 'Countdown timer deal section for discounted products.' ),
                        'category_slider'    => array( 'Category Slider', 'Visual product category carousel with icons & badges.' ),
                        'special_products'   => array( 'Special Brand Products Section', 'Left featured poster banner with right product cards.' ),
                        'trending_products'  => array( 'Trending Products Tabs', 'Tabbed product showcase (Featured, New, Top, etc.)' ),
                        'best_selling'       => array( 'Best Selling Section', 'Curated best-selling collection cards.' ),
                        'new_arrivals'       => array( 'New Arrivals Section', 'Recently added collection items.' ),
                        'favourite_products' => array( 'Favourite Products Showcase', 'Compact favorite outfits grid.' ),
                        'brand_marquee'      => array( 'Brand Logos Marquee', 'Scrolling sponsor / brand logos slider.' ),
                        'blog_section'       => array( 'Latest Blog / News', 'Featured store news and blog articles.' ),
                        'subscription'       => array( 'Newsletter Subscription', 'Email subscription newsletter bar.' ),
                    );

                    foreach ( $section_labels as $sec_key => $sec_data ) :
                        $is_checked = ( $settings['homepage_sections'][ $sec_key ] ?? 'yes' ) === 'yes';
                    ?>
                        <div class="woofashion-switch-row">
                            <div>
                                <strong style="color: #0f172a; font-size: 14px;"><?php echo esc_html( $sec_data[0] ); ?></strong>
                                <div style="color: #64748b; font-size: 12px; margin-top: 2px;"><?php echo esc_html( $sec_data[1] ); ?></div>
                            </div>
                            <label class="woofashion-toggle">
                                <input type="checkbox" name="woofashion_settings[homepage_sections][<?php echo esc_attr( $sec_key ); ?>]" value="yes" <?php checked( $is_checked ); ?>>
                                <span class="woofashion-slider"></span>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <h3 style="margin-top: 35px; font-size: 17px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                        ✏️ Custom Section Headings
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="woofashion-field">
                            <label>Special Products Heading</label>
                            <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[section_titles][special_title]" value="<?php echo esc_attr( $settings['section_titles']['special_title'] ); ?>">
                        </div>
                        <div class="woofashion-field">
                            <label>Trending Products Heading</label>
                            <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[section_titles][trending_title]" value="<?php echo esc_attr( $settings['section_titles']['trending_title'] ); ?>">
                        </div>
                        <div class="woofashion-field">
                            <label>Best Selling Heading</label>
                            <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[section_titles][best_selling_title]" value="<?php echo esc_attr( $settings['section_titles']['best_selling_title'] ); ?>">
                        </div>
                        <div class="woofashion-field">
                            <label>New Arrivals Heading</label>
                            <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[section_titles][new_arrivals_title]" value="<?php echo esc_attr( $settings['section_titles']['new_arrivals_title'] ); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- HEADER TAB -->
            <div id="tab-content-header" class="woofashion-tab-content <?php echo $active_tab === 'header' ? 'active' : ''; ?>">
                <div class="woofashion-card">
                    <h3 style="margin-top: 0; font-size: 17px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                        🔝 Header Configuration
                    </h3>

                    <div class="woofashion-field">
                        <label>Top Notice / Announcement Bar Text</label>
                        <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[header][top_announcement]" value="<?php echo esc_attr( $settings['header']['top_announcement'] ); ?>">
                        <div class="desc">Displays in the header top bar (e.g. Free shipping notifications, seasonal promo notice).</div>
                    </div>

                    <div class="woofashion-field">
                        <label>Header Hotline / Phone</label>
                        <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[header][hotline_phone]" value="<?php echo esc_attr( $settings['header']['hotline_phone'] ); ?>">
                        <div class="desc">Helpline phone displayed on the top right header.</div>
                    </div>

                    <div class="woofashion-field">
                        <label>Support Email</label>
                        <input type="email" class="regular-text" style="width: 100%;" name="woofashion_settings[header][support_email]" value="<?php echo esc_attr( $settings['header']['support_email'] ); ?>">
                    </div>

                    <div class="woofashion-switch-row" style="margin-top: 20px;">
                        <div>
                            <strong style="color: #0f172a; font-size: 14px;">Show 'Track Order' Menu Link</strong>
                            <div style="color: #64748b; font-size: 12px;">Display Track Order in header navigation.</div>
                        </div>
                        <label class="woofashion-toggle">
                            <input type="checkbox" name="woofashion_settings[header][enable_track_order]" value="yes" <?php checked( ( $settings['header']['enable_track_order'] ?? 'yes' ) === 'yes' ); ?>>
                            <span class="woofashion-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- FOOTER TAB -->
            <div id="tab-content-footer" class="woofashion-tab-content <?php echo $active_tab === 'footer' ? 'active' : ''; ?>">
                <div class="woofashion-card">
                    <h3 style="margin-top: 0; font-size: 17px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                        🔻 Footer & Contact Details
                    </h3>

                    <div class="woofashion-field">
                        <label>Footer About / Store Description</label>
                        <textarea class="regular-text" style="width: 100%;" rows="3" name="woofashion_settings[footer][about_bio]"><?php echo esc_textarea( $settings['footer']['about_bio'] ); ?></textarea>
                    </div>

                    <div class="woofashion-field">
                        <label>Store Address</label>
                        <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][contact_address]" value="<?php echo esc_attr( $settings['footer']['contact_address'] ); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="woofashion-field">
                            <label>Footer Phone</label>
                            <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][contact_phone]" value="<?php echo esc_attr( $settings['footer']['contact_phone'] ); ?>">
                        </div>
                        <div class="woofashion-field">
                            <label>Footer Contact Email</label>
                            <input type="email" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][contact_email]" value="<?php echo esc_attr( $settings['footer']['contact_email'] ); ?>">
                        </div>
                    </div>

                    <div class="woofashion-field">
                        <label>Copyright Text</label>
                        <input type="text" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][copyright_text]" value="<?php echo esc_attr( $settings['footer']['copyright_text'] ); ?>">
                    </div>

                    <h4 style="margin-top: 30px; font-size: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Social Media Links</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="woofashion-field">
                            <label>Facebook URL</label>
                            <input type="url" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][facebook]" value="<?php echo esc_attr( $settings['footer']['facebook'] ); ?>">
                        </div>
                        <div class="woofashion-field">
                            <label>Twitter (X) URL</label>
                            <input type="url" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][twitter]" value="<?php echo esc_attr( $settings['footer']['twitter'] ); ?>">
                        </div>
                        <div class="woofashion-field">
                            <label>Instagram URL</label>
                            <input type="url" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][instagram]" value="<?php echo esc_attr( $settings['footer']['instagram'] ); ?>">
                        </div>
                        <div class="woofashion-field">
                            <label>LinkedIn URL</label>
                            <input type="url" class="regular-text" style="width: 100%;" name="woofashion_settings[footer][linkedin]" value="<?php echo esc_attr( $settings['footer']['linkedin'] ); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; align-items: center; gap: 15px;">
                <button type="submit" class="woofashion-submit-btn">
                    💾 Save Theme Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Media Uploader & Instant Tab Switching Script -->
    <script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.woofashion-tab-item').on('click', function(e) {
            e.preventDefault();
            var tabName = $(this).data('tab');

            $('.woofashion-tab-item').removeClass('active');
            $(this).addClass('active');

            $('.woofashion-tab-content').removeClass('active');
            $('#tab-content-' + tabName).addClass('active');

            $('#woofashion_active_tab_input').val(tabName);
        });

        // Media Library Uploader
        $('.woofashion-media-upload-btn').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            var targetInput = $('#' + btn.data('target'));
            var previewBox = $('#' + btn.data('preview'));

            var customUploader = wp.media({
                title: 'Select or Upload Store Logo',
                button: {
                    text: 'Use this logo'
                },
                multiple: false
            }).on('select', function() {
                var attachment = customUploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
                previewBox.html('<img src="' + attachment.url + '" alt="Logo Preview">');
                btn.siblings('.woofashion-media-remove-btn').show();
            }).open();
        });

        // Remove Media
        $('.woofashion-media-remove-btn').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            var targetInput = $('#' + btn.data('target'));
            var previewBox = $('#' + btn.data('preview'));

            targetInput.val('');
            previewBox.html('');
            btn.hide();
        });
    });
    </script>
    <?php
}
