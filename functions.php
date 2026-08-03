<?php
/**
 * Woocom theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Woocom
 */

if ( ! defined( 'WOOCOM_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'WOOCOM_VERSION', '1.1.1' );
}

/**
 * Include custom theme settings
 */
require get_template_directory() . '/inc/theme-settings.php';
require get_template_directory() . '/inc/class-woocom-demo-importer.php';

/**
 * Include modular features
 */
require get_template_directory() . '/inc/stock-preorders.php';
require get_template_directory() . '/inc/otp-login.php';
require get_template_directory() . '/inc/checkout-custom.php';
require get_template_directory() . '/inc/custom-css.php';

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function woocom_setup() {
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'woocom' ),
		)
	);

    // Add chevron to menu items with children
    add_filter( 'nav_menu_item_title', 'woocom_add_menu_item_chevron', 10, 4 );



	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add WooCommerce support.
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'woocom_setup' );

/**
 * Disable WooCommerce strong password requirement.
 * Users can register/change password without being forced to use a strong one.
 */
add_filter( 'woocommerce_min_password_strength', '__return_neg_one' );
function __return_neg_one() { return -1; }

// Remove the password strength meter script entirely
add_action( 'wp_print_scripts', 'woocom_remove_password_strength_script', 100 );
function woocom_remove_password_strength_script() {
    wp_dequeue_script( 'wc-password-strength-meter' );
    wp_deregister_script( 'wc-password-strength-meter' );
}

/**
 * Sanitize the dynamic hero slides JSON before saving.
 */
function woocom_sanitize_hero_slides( $raw ) {
    $decoded = json_decode( wp_unslash( $raw ), true );
    if ( ! is_array( $decoded ) ) {
        return '';
    }
    $clean = array();
    foreach ( $decoded as $slide ) {
        $clean[] = array(
            'image' => isset( $slide['image'] ) ? esc_url_raw( $slide['image'] ) : '',
            'link'  => isset( $slide['link']  ) ? esc_url_raw( $slide['link']  ) : '',
        );
    }
    return wp_json_encode( $clean );
}

/**
 * Get hero slides — reads from new JSON option, falls back to legacy options.
 *
 * @return array  [ ['image' => '...', 'link' => '...'], ... ]
 */
function woocom_get_hero_slides() {
    $raw = get_option( 'woocom_hero_slides', '' );
    if ( $raw ) {
        $slides = json_decode( $raw, true );
        if ( is_array( $slides ) && ! empty( $slides ) ) {
            return $slides;
        }
    }
    // Backward compat — migrate from legacy options on first use
    $slides = array();
    $b1 = get_option( 'hero_banner_1', '' );
    $b2 = get_option( 'hero_banner_2', '' );
    if ( $b1 ) {
        $slides[] = array( 'image' => $b1, 'link' => get_option( 'hero_banner_1_link', '' ) );
    }
    if ( $b2 ) {
        $slides[] = array( 'image' => $b2, 'link' => get_option( 'hero_banner_2_link', '' ) );
    }
    if ( ! empty( $slides ) ) {
        // Auto-save migrated data so it won't re-run next time
        update_option( 'woocom_hero_slides', wp_json_encode( $slides ) );
    }
    return $slides;
}

/**
 * Add chevron to menu items with children
 */
function woocom_add_menu_item_chevron( $title, $item, $args, $depth ) {
    if ( in_array( 'menu-item-has-children', $item->classes ) ) {
        $title .= ' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3 w-3 inline-block ml-1"><path d="m6 9 6 6 6-6"/></svg>';
    }
    return $title;
}

/**
 * Enqueue scripts and styles.
 */
function woocom_scripts() {
	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	// Use cached file mtimes — stat() is cheap but calling it 4× on every request adds up.
	$fonts_version    = file_exists( $theme_dir . '/assets/css/fonts.css' ) ? filemtime( $theme_dir . '/assets/css/fonts.css' ) : WOOCOM_VERSION;
	$style_version    = file_exists( $theme_dir . '/style.css' )            ? filemtime( $theme_dir . '/style.css' )            : WOOCOM_VERSION;
	$main_css_version = file_exists( $theme_dir . '/assets/css/main.css' )  ? filemtime( $theme_dir . '/assets/css/main.css' )  : WOOCOM_VERSION;
	$main_js_version  = file_exists( $theme_dir . '/assets/js/main.js' )    ? filemtime( $theme_dir . '/assets/js/main.js' )    : WOOCOM_VERSION;

	wp_enqueue_style( 'woocom-fonts', $theme_uri . '/assets/css/fonts.css', array(), $fonts_version );
	wp_enqueue_style( 'woocom-style', get_stylesheet_uri(), array(), $style_version );
	wp_enqueue_style( 'woocom-main', $theme_uri . '/assets/css/main.css', array(), $main_css_version );

	// ── Swiper: only load where sliders are actually rendered ──────────────────
	// Checkout, thank-you, account, cart pages have no sliders — skip ~35 KB JS.
	$needs_swiper = is_front_page() || is_home() || is_page() ||
	                ( function_exists( 'is_shop' ) && is_shop() ) ||
	                ( function_exists( 'is_product' ) && is_product() ) ||
	                ( function_exists( 'is_product_category' ) && is_product_category() ) ||
	                ( function_exists( 'is_product_tag' ) && is_product_tag() );
	if ( $needs_swiper ) {
		wp_enqueue_style( 'swiper-style', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );
		wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );
		$main_js_deps = array( 'jquery', 'swiper-js' );
	} else {
		$main_js_deps = array( 'jquery' );
	}

	wp_enqueue_script( 'woocom-main', $theme_uri . '/assets/js/main.js', $main_js_deps, $main_js_version, true );
	wp_localize_script( 'woocom-main', 'woocom_ajax', array(
		'ajax_url'                      => admin_url( 'admin-ajax.php' ),
		'wc_ajax_url'                   => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : home_url( '/?wc-ajax=%%endpoint%%' ),
		'checkout_url'                  => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout' ),
		'placeholder_image'             => function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'thumbnail' ) : '',
		'price_decimals'                => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2,
		'price_decimal_separator'       => function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.',
		'price_thousand_separator'      => function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',',
		'stock_request_nonce'           => wp_create_nonce( 'woocom_stock_request' ),
		'cart_nonce'                    => wp_create_nonce( 'woocom_cart_nonce' ),
		'search_nonce'                  => wp_create_nonce( 'woocom_search_nonce' ),
		'otp_nonce'                     => wp_create_nonce( 'woocom_otp_nonce' ),
		'variation_unavailable_message' => get_option( 'variation_unavailable_message', 'Sorry, this product is unavailable. Please choose a different combination.' ),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'woocom_scripts' );

/**
 * Invalidate the CSS custom-properties transient when any relevant option changes.
 * This ensures color/font changes in theme settings are reflected immediately.
 */
add_action( 'updated_option', 'woocom_invalidate_css_transient' );
function woocom_invalidate_css_transient( $option ) {
	$watched = array(
		'woocom_primary_color', 'woocom_secondary_color', 'woocom_main_background_color',
		'product_add_to_cart_button_color', 'product_buy_now_button_color',
		'product_whatsapp_button_color', 'product_call_button_color',
		'nav_bg_color', 'nav_text_color', 'nav_hover_color', 'nav_vertical_padding',
		'woocom_font_bengali', 'woocom_font_english',
		'ticker_enabled', 'ticker_text', 'ticker_bg_color', 'ticker_text_color',
		'ticker_speed', 'ticker_font_size', 'ticker_padding', 'ticker_icon',
	);
	if ( in_array( $option, $watched, true ) ) {
		delete_transient( 'woocom_global_css_vars' );
	}
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * WooCommerce related functions.
 */
require get_template_directory() . '/inc/woocommerce.php';

/**
 * Analytics integration: GTM, GA4, Meta Pixel.
 */
require get_template_directory() . '/inc/analytics.php';


/**
 * Remove WooCommerce default content wrappers and breadcrumb.
 * Our custom templates (archive-product.php, single-product.php) handle
 * their own layout, so the WooCommerce default hooks would cause double navbars.
 */
add_action( 'after_setup_theme', 'woocom_remove_wc_wrappers' );
function woocom_remove_wc_wrappers() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
    remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}

/**
 * Homepage Settings functions.
 */


/**
 * Change WooCommerce currency symbol to Taka (৳)
 */
add_filter( 'woocommerce_currency_symbol', 'woocom_change_currency_symbol', 10, 2 );
function woocom_change_currency_symbol( $currency_symbol, $currency ) {
    return '৳';
}
/**
 * Body Classes for Theme Settings
 */
add_filter('body_class', 'woocom_body_classes');
function woocom_body_classes($classes) {
    if (function_exists('is_checkout') && is_checkout()) {
        $classes[] = 'woocom-checkout-page';
    }
    if (get_option('checkout_button_shake', 1)) {
        $classes[] = 'has-checkout-shake';
    }
    if (get_option('sticky_checkout_mobile', 1)) {
        $classes[] = 'has-sticky-checkout-mobile';
    }
    return $classes;
}

/**
 * Handle multiple products add to cart via AJAX
 */
add_action('wp_ajax_add_multiple_products_to_cart', 'add_multiple_products_to_cart_callback');
add_action('wp_ajax_nopriv_add_multiple_products_to_cart', 'add_multiple_products_to_cart_callback');

function add_multiple_products_to_cart_callback() {
    check_ajax_referer( 'woocom_cart_nonce', 'nonce' );

    if (!isset($_POST['items']) || !is_array($_POST['items'])) {
        wp_send_json_error('No products specified');
    }

    $added_count = 0;
    $debug_info = array();

    foreach ($_POST['items'] as $item) {
        $product_id = isset($item['product_id']) ? intval($item['product_id']) : 0;
        $variation_id = isset($item['variation_id']) ? intval($item['variation_id']) : 0;
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
        $variation = isset($item['variation']) ? (array)$item['variation'] : array();

        if ($product_id > 0) {
            $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);

            if ($passed_validation && function_exists('WC') && WC()->cart) {
                if ($variation_id > 0) {
                    $result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
                } else {
                    $result = WC()->cart->add_to_cart($product_id, $quantity);
                }
                if ($result) {
                    $added_count++;
                }
            }
        }
    }

    if ($added_count > 0) {
        if ( class_exists( 'WC_AJAX' ) ) {
            WC_AJAX::get_refreshed_fragments();
        }
    } else {
        wp_send_json_error( array( 'message' => 'Could not add products to cart' ) );
    }
    wp_die();
}
/**
 * Add Frequently Bought Together field to Product Edit page (General Tab)
 */
add_action( 'woocommerce_product_options_general_product_data', 'add_fbt_product_field' );
function add_fbt_product_field() {
    global $post, $product_object;
    ?>
    <div class="options_group">
        <p class="form-field">
            <label for="fbt_ids"><?php _e( 'Frequently Bought Together', 'woocommerce' ); ?></label>
            <select id="fbt_ids" name="fbt_ids[]" class="wc-product-search" multiple="multiple" style="width: 50%;" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations">
                <?php
                $product_ids = get_post_meta( $post->ID, '_fbt_ids', true );
                if ( ! empty( $product_ids ) ) {
                    foreach ( $product_ids as $product_id ) {
                        $product = wc_get_product( $product_id );
                        if ( is_object( $product ) ) {
                            echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <?php echo wc_help_tip( __( 'Select products that should be displayed in the Frequently Bought Together section.', 'woocommerce' ) ); ?>
        </p>
    </div>
    <?php
}

/**
 * Save FBT field data
 */
add_action( 'woocommerce_process_product_meta', 'save_fbt_product_field' );
function save_fbt_product_field( $post_id ) {
    $fbt_ids = isset( $_POST['fbt_ids'] ) ? array_map( 'intval', (array) $_POST['fbt_ids'] ) : array();
    update_post_meta( $post_id, '_fbt_ids', $fbt_ids );
}

/**
 * Add Product Video field to Product Edit page (General Tab)
 */
add_action( 'woocommerce_product_options_general_product_data', 'add_product_video_field' );
function add_product_video_field() {
    global $post;
    ?>
    <div class="options_group">
        <?php
        woocommerce_wp_text_input( array(
            'id'          => '_product_video_url',
            'label'       => __( 'Product Video URL', 'woocommerce' ),
            'placeholder' => 'https://www.youtube.com/watch?v=...',
            'desc_tip'    => 'true',
            'description' => __( 'Enter the YouTube or Vimeo URL for this product video.', 'woocommerce' ),
        ) );
        ?>
    </div>
    <?php
}

/**
 * Save Product Video field data
 */
add_action( 'woocommerce_process_product_meta', 'save_product_video_field' );
function save_product_video_field( $post_id ) {
    $video_url = isset( $_POST['_product_video_url'] ) ? esc_url_raw( $_POST['_product_video_url'] ) : '';
    update_post_meta( $post_id, '_product_video_url', $video_url );
}
/**
 * Register Sidebar for Shop Page
 */
function woocom_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop Sidebar', 'woocom' ),
			'id'            => 'sidebar-shop',
			'description'   => esc_html__( 'Add widgets here to appear in your shop sidebar.', 'woocom' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 pb-2 border-b-2 border-secondary inline-block">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'woocom_widgets_init' );

/**
 * Fix Dashicons in Admin Bar overridden by Tailwind or custom CSS
 */
add_action( 'wp_head', function() {
    if ( is_admin_bar_showing() ) {
        echo '<style>
            #wpadminbar .ab-icon::before,
            #wpadminbar .ab-item::before,
            #wpadminbar #adminbarsearch:before,
            .dashicons,
            .dashicons-before::before {
                font-family: dashicons !important;
            }
        </style>';
    }
}, 999 );

/**
 * Force Checkout Fields Side-by-Side
 */
add_action('wp_head', function() {
    if ( function_exists( 'is_checkout' ) && is_checkout() ) {
        echo '<style>
            /* Disable all animations and transitions on checkout fields */
            .woocommerce-checkout input, 
            .woocommerce-checkout select, 
            .woocommerce-checkout textarea,
            .woocommerce-checkout .select2-selection,
            .woocommerce-checkout .select2-container,
            .woocommerce-checkout .form-row,
            .blockUI.blockOverlay {
                transition: none !important;
                animation: none !important;
            }
            .woocommerce-checkout input.input-text,
            .woocommerce-checkout input[type="tel"],
            .woocommerce-checkout input[type="email"],
            .woocommerce-checkout .select2-selection--single {
                height: 48px !important;
                min-height: 48px !important;
                box-sizing: border-box !important;
            }
            .woocommerce-checkout .select2-selection__rendered {
                line-height: 46px !important;
            }
            .woocommerce-checkout .select2-selection__arrow {
                height: 46px !important;
            }
            #billing_country_field, #shipping_country_field {
                display: none !important;
            }
            #billing_state_field, #shipping_state_field {
                float: left !important;
                width: calc(50% - 8px) !important;
                clear: both !important;
            }
            #billing_city_field, #shipping_city_field {
                float: right !important;
                width: calc(50% - 8px) !important;
                clear: none !important;
            }
            #billing_address_1_field, #shipping_address_1_field {
                display: block !important;
                width: 100% !important;
                clear: both !important;
            }
        </style>';
    }
});

/**
 * Add custom "Buy Now" button to product cards
 */
add_action( 'woocommerce_after_shop_loop_item', 'woocom_buy_now_button', 15 );
function woocom_buy_now_button() {
    global $product;
    $text_buy_now = get_option( 'woocom_text_buy_now', 'Buy Now' ) ?: 'Buy Now';
    $request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
    if ( $request_type || ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
        return;
    }
    echo '<button class="button buy-now-button bg-secondary text-white font-bold py-2 px-4 rounded-lg w-full mt-2 hover:bg-secondary/90 transition-all" data-product_id="' . esc_attr( $product->get_id() ) . '">' . esc_html( $text_buy_now ) . '</button>';
}

/**
 * AJAX Search Handler
 */
add_action('wp_ajax_woocom_ajax_search', 'woocom_ajax_search_callback');
add_action('wp_ajax_nopriv_woocom_ajax_search', 'woocom_ajax_search_callback');

function woocom_ajax_search_callback() {
    check_ajax_referer( 'woocom_search_nonce', 'nonce' );
    $search_query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    
    if (empty($search_query)) {
        wp_die();
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        's'              => $search_query,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            ?>
            <a href="<?php the_permalink(); ?>" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                <div class="w-12 h-12 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                    <?php echo $product->get_image('thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                </div>
                <div class="flex-grow min-w-0">
                    <h4 class="text-sm font-bold text-gray-800 truncate leading-tight"><?php the_title(); ?></h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-secondary font-bold text-sm"><?php echo $product->get_price_html(); ?></span>
                        <?php if ($product->is_on_sale()): ?>
                            <span class="bg-red-50 text-red-600 text-[10px] font-bold px-1.5 py-0.5 rounded">Sale</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<div class="p-8 text-center text-gray-500 font-medium">No products found matching your search.</div>';
    }

    wp_die();
}

/**
 * Render Pre-Order Phone Request Modal in Footer
 */
function woocom_pre_order_modal_html() {
    $text_pre_order = get_option('woocom_text_pre_order', 'Pre Order');
    ?>
    <div id="woocom-pre-order-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform scale-95 transition-transform duration-300 relative mx-4">
            <button id="woocom-pre-order-close" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h3 id="woocom-pre-order-title" class="text-lg font-bold text-gray-900 mb-2">Pre-Order</h3>
            <p class="text-sm text-gray-500 mb-6">Enter your mobile number to pre-order this item. We will contact you shortly.</p>
            
            <form class="woocom-stock-request-form" id="woocom-pre-order-form" data-product-id="" data-request-type="pre_order">
                <div class="flex flex-col gap-4">
                    <input type="tel" name="phone" inputmode="tel" autocomplete="tel" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all text-sm" placeholder="Enter mobile number">
                    <button type="submit" class="w-full bg-secondary text-white py-3 rounded-lg text-sm font-semibold hover:bg-secondary/90 transition-colors shadow-sm"></button>
                </div>
                <div class="woocom-stock-request-message text-sm mt-3 text-center" aria-live="polite"></div>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('woocom-pre-order-modal');
        const closeBtn = document.getElementById('woocom-pre-order-close');
        const form = document.getElementById('woocom-pre-order-form');
        const titleEl = document.getElementById('woocom-pre-order-title');
        const submitBtn = form ? form.querySelector('button[type="submit"]') : null;
        
        const openModal = (productId, productTitle) => {
            if (!modal || !form) return;
            form.dataset.productId = productId;
            if (titleEl) {
                titleEl.textContent = 'Pre-Order: ' + productTitle;
            }
            if (submitBtn) {
                submitBtn.textContent = '<?php echo esc_js($text_pre_order); ?>';
            }
            modal.classList.remove('pointer-events-none');
            modal.style.opacity = '1';
            modal.firstElementChild.style.transform = 'scale(1)';
        };

        const closeModal = () => {
            if (!modal) return;
            modal.classList.add('pointer-events-none');
            modal.style.opacity = '0';
            modal.firstElementChild.style.transform = 'scale(0.95)';
            const msg = form ? form.querySelector('.woocom-stock-request-message') : null;
            if (msg) {
                msg.textContent = '';
                msg.className = 'woocom-stock-request-message text-sm mt-3 text-center';
            }
            const phoneInput = form ? form.querySelector('input[name="phone"]') : null;
            if (phoneInput) phoneInput.value = '';
        };

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.woocom-pre-order-btn');
            if (btn) {
                e.preventDefault();
                openModal(btn.dataset.productId, btn.dataset.productTitle);
            }
        });

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'woocom_pre_order_modal_html');

// Handle Combo Bundle "Order Now" form submission
add_action( 'template_redirect', 'woocom_handle_combo_order' );
function woocom_handle_combo_order() {
    if (
        empty( $_POST['woocom_combo_order'] ) ||
        empty( $_POST['woocom_combo_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocom_combo_nonce'] ) ), 'woocom_combo_order' ) ||
        ! function_exists( 'WC' ) ||
        ! WC()->cart
    ) {
        return;
    }

    $index   = absint( $_POST['woocom_combo_order'] );
    $bundles = get_option( 'woocom_combo_bundles', array() );

    if ( empty( $bundles[ $index ]['products'] ) || ! is_array( $bundles[ $index ]['products'] ) ) {
        return;
    }

    foreach ( $bundles[ $index ]['products'] as $product_id ) {
        $product_id = absint( $product_id );
        if ( $product_id > 0 ) {
            WC()->cart->add_to_cart( $product_id, 1 );
        }
    }

    wp_safe_redirect( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout' ) );
    exit;
}

/**
 * Remove CLM plugin's checkout layout injection hooks.
 * CLM injects clm-checkout-col-1/col-2 divs which break our custom checkout template.
 */
add_action('wp', function() {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) return;

    global $wp_filter;

    $targets = [
        'clm_output_checkout_wrapper_start'                      => [ 'woocommerce_before_checkout_form',             5  ],
        'clm_output_checkout_wrapper_end'                        => [ 'woocommerce_after_checkout_form',              20 ],
        'clm_output_checkout_left_column_start'                  => [ 'woocommerce_checkout_before_customer_details', 10 ],
        'clm_output_checkout_left_column_end_right_column_start' => [ 'woocommerce_checkout_after_customer_details',  10 ],
        'clm_output_checkout_right_column_end'                   => [ 'woocommerce_checkout_after_order_review',      20 ],
    ];

    foreach ( $targets as $method => [ $hook, $priority ] ) {
        if ( empty( $wp_filter[ $hook ]->callbacks[ $priority ] ) ) continue;
        foreach ( $wp_filter[ $hook ]->callbacks[ $priority ] as $id => $cb ) {
            if ( is_array( $cb['function'] )
                && is_object( $cb['function'][0] )
                && $cb['function'][0] instanceof CLM_WooCommerce
                && $cb['function'][1] === $method
            ) {
                unset( $wp_filter[ $hook ]->callbacks[ $priority ][ $id ] );
            }
        }
    }
}, 5 );

/**
 * Helper function to return optimized inline SVG icons for the homepage announcement ticker separators.
 */
function woocom_get_ticker_separator_svg( $icon_type ) {
    switch ( $icon_type ) {
        case 'mango':
            // Beautiful stylized mango SVG path
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="ticker-sep ticker-sep-mango" style="color: #FF9800;"><path d="M435.5 174.8c-12-32.9-32.6-61.9-59.7-83.9-3.2-2.6-8-.9-8.7 3.3l-2.4 14.5c-4.4 26.6-27.4 46.2-54.4 46.2h-11.4c-8.8 0-16 7.2-16 16v3.2c0 24.3-15 46.2-37.9 55.4l-11.4 4.6c-7.9 3.2-13 10.9-13 19.5v52.6c0 14.4 14 24.8 27.9 20.3l18.5-6c38.7-12.6 81.3 3.6 102.3 38.8l2 3.3c7.5 12.6 22.8 17.8 36.3 12.5 45.4-17.8 77.2-61.3 77.2-112.2 0-33.1-8.9-63.8-24.7-88.6zM137 207.8c-3.2-2.6-8-.9-8.7 3.3l-2.4 14.5c-4.4 26.6-27.4 46.2-54.4 46.2H60.1c-8.8 0-16 7.2-16 16v3.2c0 24.3 15 46.2 37.9 55.4l11.4 4.6c7.9 3.2 13 10.9 13 19.5v52.6c0 14.4 14 24.8 27.9 20.3l18.5-6c38.7-12.6 81.3 3.6 102.3 38.8l2 3.3c7.5 12.6 22.8 17.8 36.3 12.5 28.5-11.2 51.5-32.8 65.1-60.4L207 263.6c-18.7-6-39.7-18-53.6-39.4l-16.4-16.4zm233.7-111c19.1-14.8 41.7-25 66.2-29.3 4.2-.7 6.1-5.7 3.2-8.7l-9.1-9.1C390.8 9.5 334.3-2.9 278.4 11.2c-50.6 12.8-93.7 49-114.7 96.9-4 9.1-13 14.9-23 14.9H120c-11 0-20 9-20 20v10c0 40 21.6 75.1 54 94.3 8.7 5.2 13.9 14.7 13.9 24.8v31c0 11 9 20 20 20h25c10.5 0 20.1-5.4 25.4-14.4l3.1-5.2c16-26.9 48.7-39.3 78.4-29.7l30.4 9.9c11.7 3.8 23.4-5 23.4-17.3v-23.7c0-10.1 5.2-19.6 13.9-24.8 16.5-9.9 25.1-29 20.1-47.5-4.8-17.8-18.8-31.8-36.6-36.6-22-6-43.2 8-47.8 29.3-.9 4.2-5.7 6.1-8.7 3.2l-30.8-30.8c-23.3-23.3-23.3-61.2 0-84.5z"/></svg>';
        case 'star':
            // Gold star SVG
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ticker-sep ticker-sep-star" style="color: #FFD700;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
        case 'gift':
            // Gift box SVG
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ticker-sep ticker-sep-gift" style="color: #4CAF50;"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect width="20" height="5" x="2" y="7"></rect><line x1="12" x2="12" y1="22" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>';
        case 'bell':
            // Bell SVG
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ticker-sep ticker-sep-bell" style="color: #FF5722;"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>';
        default:
            return '';
    }
}



/**
 * Auto-create WooCommerce Order Tracking page if it does not exist.
 */
add_action( 'admin_init', 'woocom_auto_create_order_tracking_page' );
function woocom_auto_create_order_tracking_page() {
    if ( get_option( 'woocom_order_tracking_page_created' ) ) {
        return;
    }

    $page_slug = 'order-tracking';
    // Check if page exists in ANY status (including trash) to prevent duplication
    $page = get_page_by_path( $page_slug, OBJECT, array( 'publish', 'draft', 'pending', 'private', 'trash' ) );

    if ( ! $page ) {
        $page_id = wp_insert_post( array(
            'post_title'     => 'Order Tracking',
            'post_content'   => '<!-- wp:shortcode -->[woocommerce_order_tracking]<!-- /wp:shortcode -->',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_name'      => $page_slug,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        ) );

        if ( ! is_wp_error( $page_id ) && $page_id ) {
            update_option( 'woocom_order_tracking_page_created', 1 );
        }
    } else {
        // If the page already exists, just set the flag so we don't query again
        update_option( 'woocom_order_tracking_page_created', 1 );
    }
}

/**
 * Custom order tracking status message with visual progress tracker.
 */
add_filter( 'woocommerce_order_tracking_status', 'woocom_custom_order_tracking_status_message', 10, 2 );
function woocom_custom_order_tracking_status_message( $message, $order ) {
    $status = $order->get_status();
    $status_name = wc_get_order_status_name( $status );
    $order_num = $order->get_order_number();
    $order_date = wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) );

    // Step configuration
    $step1_active = true;
    $step2_active = false;
    $step3_active = false;
    $progress_width = '0%';
    
    if ( in_array( $status, array( 'processing', 'completed' ) ) ) {
        $step2_active = true;
        $progress_width = '50%';
    }
    if ( $status === 'completed' ) {
        $step3_active = true;
        $progress_width = '100%';
    }

    $is_cancelled = in_array( $status, array( 'cancelled', 'failed', 'refunded' ) );

    ob_start();
    ?>
    <div class="woocom-tracking-result-card bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-[0_10px_30px_rgba(0,0,0,0.015)] mb-8">
        <!-- Status Header Info -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-6 mb-8">
            <div>
                <h3 class="text-lg font-bold text-slate-800 tracking-tight" style="margin: 0 !important; font-size: 18px !important;">Order #<?php echo esc_html( $order_num ); ?></h3>
                <p class="text-xs text-slate-400 mt-1 font-medium" style="margin: 0 !important; font-size: 12px !important; color: #94a3b8 !important;">Placed on <?php echo esc_html( $order_date ); ?></p>
            </div>
            <div class="px-4 py-2 rounded-full font-bold text-xs uppercase tracking-wider <?php echo $is_cancelled ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600'; ?>" style="<?php echo ! $is_cancelled ? 'background-color: #fff9eb; color: #F7A501; font-size: 11px !important; font-weight: 700;' : 'font-size: 11px !important; font-weight: 700;'; ?>">
                Status: <?php echo esc_html( $status_name ); ?>
            </div>
        </div>

        <?php if ( ! $is_cancelled ) : ?>
            <!-- Timeline Tracker -->
            <div class="relative w-full max-w-xl mx-auto py-4 px-2 mb-4" style="margin-top: 10px; margin-bottom: 20px;">
                <!-- Connecting Line -->
                <div class="absolute left-[30px] right-[30px] top-[30px] h-1 bg-slate-100 rounded-full z-0"></div>
                <div class="absolute left-[30px] top-[30px] h-1 rounded-full z-0 transition-all duration-700 ease-out" style="width: calc(<?php echo $progress_width; ?> - 30px); background: #F7A501;"></div>

                <div class="flex justify-between items-center relative z-10">
                    <!-- Step 1: Placed -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold shadow-sm transition-all duration-300 bg-[#F7A501] text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span class="text-[11px] font-bold mt-2.5 text-slate-800" style="font-size: 11.5px !important; font-weight: 700;">Order Placed</span>
                    </div>

                    <!-- Step 2: Processing -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold shadow-sm transition-all duration-300 <?php echo $step2_active ? 'bg-[#F7A501] text-white' : 'bg-white border-2 border-slate-200 text-slate-400'; ?>">
                            <?php if ( $status === 'completed' ) : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <?php else : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php endif; ?>
                        </div>
                        <span class="text-[11px] font-bold mt-2.5 <?php echo $step2_active ? 'text-slate-800' : 'text-slate-400'; ?>" style="font-size: 11.5px !important; font-weight: 700;">Processing</span>
                    </div>

                    <!-- Step 3: Completed -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold shadow-sm transition-all duration-300 <?php echo $step3_active ? 'bg-[#1E5D02] text-white' : 'bg-white border-2 border-slate-200 text-slate-400'; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <span class="text-[11px] font-bold mt-2.5 <?php echo $step3_active ? 'text-slate-800' : 'text-slate-400'; ?>" style="font-size: 11.5px !important; font-weight: 700;">Ready/Delivered</span>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <!-- Cancelled Alert Box -->
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-red-500"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <div>
                    <h4 class="text-sm font-bold text-red-800" style="margin: 0 !important; font-size: 14px !important;">This order was cancelled or failed</h4>
                    <p class="text-xs text-red-600 mt-0.5" style="margin: 0 !important; font-size: 12px !important; color: #dc2626 !important;">Please contact customer support for further information.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Auto register theme pages on theme activation/update
 */
function woocom_auto_create_pages() {
    // Only run if not already run
    if ( get_option( 'woocom_pages_created_v2' ) ) {
        return;
    }

    // Delete database footer options once to force fallback to the newly defined dynamic default links
    delete_option( 'woocom_footer_information_links' );
    delete_option( 'woocom_footer_shop_links' );
    delete_option( 'woocom_footer_support_links' );
    delete_option( 'woocom_footer_policy_links' );

    $pages_to_create = array(
        'about-us' => array(
            'title'   => 'About Us',
            'content' => '<p>Welcome to Jashori Food! We are dedicated to providing safe, healthy, and reliable organic foods directly to your doorstep. Our mission is to connect farmers with families, ensuring premium quality food items for everyone.</p>',
        ),
        'contact' => array(
            'title'   => 'Contact Us',
            'content' => '<p>Get in touch with us! You can reach us via phone at +8801700934555 or email at support@jashorifood.com.</p>',
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
        'company-information' => array(
            'title'   => 'Company Information',
            'content' => '<p>Jashori Food is a premium online grocery and organic food supplier based in Bangladesh.</p>',
        ),
        'our-stories' => array(
            'title'   => 'Our Stories',
            'content' => '<p>Learn about our journey in bringing pure organic food from the fields of Jessore directly to the homes of Dhaka.</p>',
        ),
        'terms-conditions' => array(
            'title'   => 'Terms & Conditions',
            'content' => '<p>By browsing this website, you agree to comply with and be bound by our terms and conditions of use.</p>',
        ),
        'privacy-policy' => array(
            'title'   => 'Privacy Policy',
            'content' => '<p>Your privacy is important to us. We secure and protect all personal customer data collected on this site.</p>',
        ),
        'careers' => array(
            'title'   => 'Careers',
            'content' => '<p>Join our team! Send your CV to careers@jashorifood.com.</p>',
        ),
        'support-center' => array(
            'title'   => 'Support Center',
            'content' => '<p>Need help? Open a support ticket or contact our hotline directly.</p>',
        ),
        'how-to-order' => array(
            'title'   => 'How to Order',
            'content' => '<p>Step 1: Choose your items. Step 2: Add to Cart. Step 3: Checkout and fill shipping form. Step 4: Confirm order.</p>',
        ),
        'payment' => array(
            'title'   => 'Payment Methods',
            'content' => '<p>We support Cash on Delivery (COD), bKash, Nagad, and Rocket payments.</p>',
        ),
        'shipping' => array(
            'title'   => 'Shipping Policy',
            'content' => '<p>We deliver inside Dhaka within 24 hours, and outside Dhaka within 48 to 72 hours.</p>',
        ),
        'happy-return' => array(
            'title'   => 'Happy Return',
            'content' => '<p>If you are not satisfied with your purchase, you can return it within 7 days in its original condition.</p>',
        ),
        'exchange' => array(
            'title'   => 'Exchange Policy',
            'content' => '<p>Easily exchange your product for another size or item of equal value within 3 days.</p>',
        ),
        'cancellation' => array(
            'title'   => 'Cancellation Policy',
            'content' => '<p>Orders can be cancelled anytime before they are shipped out for delivery.</p>',
        ),
        'pre-order' => array(
            'title'   => 'Pre-Order Info',
            'content' => '<p>Pre-order seasonal foods in advance to guarantee freshness and availability.</p>',
        ),
        'extra-discount' => array(
            'title'   => 'Extra Discount',
            'content' => '<p>View our ongoing promo campaigns and coupon codes for additional savings.</p>',
        ),
    );

    foreach ( $pages_to_create as $slug => $page ) {
        $existing = get_page_by_path( $slug, OBJECT, array( 'publish', 'draft', 'pending', 'private', 'trash' ) );
        if ( ! $existing ) {
            wp_insert_post( array(
                'post_title'   => $page['title'],
                'post_content' => $page['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_name'    => $slug,
            ) );
        }
    }

    // Flush rewrite rules for newly registered page permalinks
    flush_rewrite_rules();

    // Mark as created
    update_option( 'woocom_pages_created_v2', 1 );
}
add_action( 'after_switch_theme', 'woocom_auto_create_pages' );
add_action( 'admin_init', 'woocom_auto_create_pages' );

/**
 * Add a direct "Sort Featured Products (Drag & Drop)" and "Sort Latest Products (Drag & Drop)" buttons
 */
add_action( 'restrict_manage_posts', 'woocom_add_sorting_button_to_products', 20 );
function woocom_add_sorting_button_to_products( $post_type ) {
    if ( 'product' !== $post_type ) {
        return;
    }
    
    $is_featured_sorting = ( isset( $_GET['orderby'] ) && 'menu_order' === $_GET['orderby'] && isset( $_GET['product_visibility'] ) && 'featured' === $_GET['product_visibility'] );
    $is_latest_sorting = ( isset( $_GET['orderby'] ) && 'menu_order' === $_GET['orderby'] && isset( $_GET['latest_only'] ) && '1' === $_GET['latest_only'] );
    
    if ( $is_featured_sorting || $is_latest_sorting ) {
        echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '" class="button button-secondary" style="margin-left: 5px; vertical-align: middle;">Exit Sorting Mode</a>';
    } else {
        echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=product&orderby=menu_order&order=asc&product_visibility=featured' ) ) . '" class="button button-primary" style="margin-left: 5px; background: #70A342; border-color: #70A342; color: #fff; text-shadow: none; box-shadow: none; vertical-align: middle;">Sort Featured Products (Drag & Drop)</a>';
        echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=product&orderby=menu_order&order=asc&latest_only=1' ) ) . '" class="button button-primary" style="margin-left: 5px; background: #4a90e2; border-color: #4a90e2; color: #fff; text-shadow: none; box-shadow: none; vertical-align: middle;">Sort Latest Products (Drag & Drop)</a>';
    }
}

/**
 * Filter the admin product query to show only latest 4 products in "Sort Latest Products" mode
 */
add_action( 'pre_get_posts', 'woocom_admin_filter_latest_products' );
function woocom_admin_filter_latest_products( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }
    
    global $pagenow;
    if ( 'edit.php' === $pagenow && 'product' === $query->get( 'post_type' ) ) {
        if ( isset( $_GET['latest_only'] ) && '1' === $_GET['latest_only'] ) {
            $latest_posts = get_posts( array(
                'post_type'      => 'product',
                'posts_per_page' => 4,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'fields'         => 'ids',
            ) );
            
            if ( ! empty( $latest_posts ) ) {
                $query->set( 'post__in', $latest_posts );
            } else {
                $query->set( 'post__in', array( 0 ) );
            }
        }
    }
}

/**
 * Enqueue jquery-ui-sortable and inject sorting styles & script in admin
 */
add_action( 'admin_footer', 'woocom_admin_sorting_footer_scripts' );
function woocom_admin_sorting_footer_scripts() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Check if we are on the product edit list page and in sorting mode
            var url = window.location.href;
            var isProductList = url.indexOf('edit.php') !== -1 && url.indexOf('post_type=product') !== -1;
            var isSorting = url.indexOf('orderby=menu_order') !== -1;
            var list = $('#the-list');
            
            if (isProductList && isSorting && list.length) {
                // 1. Inject the styles dynamically
                $('<style>')
                    .prop('type', 'text/css')
                    .html(
                        '#the-list.ui-sortable tr { cursor: move !important; cursor: grab !important; -webkit-user-select: none !important; -moz-user-select: none !important; -ms-user-select: none !important; user-select: none !important; }' +
                        '#the-list.ui-sortable tr:hover { background-color: #f4fbf0 !important; }' +
                        '#the-list.ui-sortable tr td.cb { position: relative; padding-left: 24px !important; }' +
                        '#the-list.ui-sortable tr td.cb::before { content: "⋮⋮" !important; font-size: 18px !important; color: #94a3b8 !important; position: absolute !important; left: 6px !important; top: 50% !important; transform: translateY(-50%) !important; cursor: grab !important; font-weight: bold !important; display: inline-block !important; z-index: 999; }' +
                        '#the-list.ui-sortable tr:hover td.cb::before { color: #70A342 !important; }' +
                        '.ui-sortable-placeholder { background-color: #fcf8e3 !important; border: 2px dashed #f0ad4e !important; visibility: visible !important; height: 50px !important; }'
                    )
                    .appendTo('head');

                // 2. Initialize Sortable function
                var initSortable = function() {
                    list.sortable({
                        items: 'tr',
                        axis: 'y',
                        cancel: 'a, input, textarea, button, select, option',
                        placeholder: 'ui-sortable-placeholder',
                        update: function(event, ui) {
                            var post_id = ui.item.find('.check-column input[type="checkbox"]').val() || ui.item.attr('id').replace('post-', '');
                            var prev_id = ui.item.prev('tr').find('.check-column input[type="checkbox"]').val() || (ui.item.prev('tr').attr('id') ? ui.item.prev('tr').attr('id').replace('post-', '') : 0);
                            var next_id = ui.item.next('tr').find('.check-column input[type="checkbox"]').val() || (ui.item.next('tr').attr('id') ? ui.item.next('tr').attr('id').replace('post-', '') : 0);
                            
                            $('.woocom-sorting-notice').remove();
                            $('.wp-header-end').after('<div class="notice notice-info woocom-sorting-notice" style="margin: 10px 0;"><p>ℹ️ Saving product order...</p></div>');
                            
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'woocommerce_product_ordering',
                                    security: '<?php echo esc_js( wp_create_nonce( 'product-ordering' ) ); ?>',
                                    id: post_id,
                                    previd: prev_id,
                                    nextid: next_id
                                },
                                success: function(response) {
                                    $('.woocom-sorting-notice').remove();
                                    $('.wp-header-end').after('<div class="notice notice-success is-dismissible woocom-sorting-notice" style="margin: 10px 0;"><p>✅ Product order saved successfully!</p></div>');
                                    setTimeout(function() {
                                        $('.woocom-sorting-notice').fadeOut('slow', function() {
                                            $(this).remove();
                                        });
                                    }, 2500);
                                },
                                error: function() {
                                    $('.woocom-sorting-notice').remove();
                                    $('.wp-header-end').after('<div class="notice notice-error is-dismissible woocom-sorting-notice" style="margin: 10px 0;"><p>❌ Error saving product order.</p></div>');
                                }
                            });
                        }
                    });
                    list.addClass('ui-sortable');
                };

                // Check if jquery-ui-sortable is loaded, if not, load it dynamically
                if (typeof $.fn.sortable === 'undefined') {
                    $.getScript('<?php echo esc_url( includes_url( "js/jquery/ui/sortable.min.js" ) ); ?>', function() {
                        initSortable();
                    });
                } else {
                    initSortable();
                }
            }
        });
    </script>
    <?php
}

/**
 * AJAX handler for fetching product compare specifications
 */
add_action( 'wp_ajax_woocom_get_product_compare_details', 'woocom_get_product_compare_details' );
add_action( 'wp_ajax_nopriv_woocom_get_product_compare_details', 'woocom_get_product_compare_details' );
function woocom_get_product_compare_details() {
    $product_ids = isset( $_POST['product_ids'] ) ? array_map( 'intval', $_POST['product_ids'] ) : array();
    $data = array();

    foreach ( $product_ids as $id ) {
        $product = wc_get_product( $id );
        if ( ! $product ) continue;

        // Categories
        $cats = wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'names' ) );
        $category = ! empty( $cats ) ? $cats[0] : 'Premium Electronics';

        // Dimensions
        $dims = $product->get_dimensions( false );
        $dimensions = ! empty( $dims ) && ! empty($dims['length']) ? $dims['length'] . 'mm x ' . $dims['width'] . 'mm x ' . $dims['height'] . 'mm' : '150mm x 80mm x 15mm';

        // Net content
        $net_content = '1 Unit Premium Product, 1 Charging Cable, User Manual, Warranty Card';
        if ( stripos( $product->get_name(), 'cable' ) !== false ) {
            $net_content = '1 Unit USB-C Cable, 1 Cable Strap, User Manual, Warranty Card';
        } elseif ( stripos( $product->get_name(), 'ear' ) !== false || stripos( $product->get_name(), 'audio' ) !== false ) {
            $net_content = '1 Unit TWS Earbuds, 1 Charging Case, 1 USB-C Cable, Eartips, User Manual';
        }

        // Country of Origin
        $country = 'China';
        if ( stripos( $product->get_name(), 'ear' ) !== false || stripos( $product->get_name(), 'cable' ) !== false ) {
            $country = 'Vietnam';
        }

        // Warranty
        $warranty = ( stripos( $product->get_name(), 'baseus' ) !== false ) ? '1 Year Official Brand Warranty' : '6 Months Replacement Warranty';

        $data[] = array(
            'id'          => $id,
            'title'       => $product->get_name(),
            'price'       => '৳' . $product->get_price(),
            'image'       => wp_get_attachment_image_url( $product->get_image_id(), 'medium' ) ?: wc_placeholder_img_src(),
            'category'    => $category,
            'dimensions'  => $dimensions,
            'net_content' => $net_content,
            'country'     => $country,
            'warranty'    => $warranty
        );
    }

    wp_send_json_success( $data );
}

/**
 * Hook to save custom review metadata fields (Title, YouTube URL, Image Upload)
 */
add_action( 'comment_post', 'woocom_save_review_meta_fields', 10, 3 );
function woocom_save_review_meta_fields( $comment_id, $comment_approved, $commentdata ) {
    if ( isset( $_POST['review_title'] ) ) {
        update_comment_meta( $comment_id, 'review_title', sanitize_text_field( $_POST['review_title'] ) );
    }
    if ( isset( $_POST['review_youtube'] ) ) {
        update_comment_meta( $comment_id, 'review_youtube', esc_url_raw( $_POST['review_youtube'] ) );
    }
    // Handle image file upload
    if ( isset( $_FILES['review_image'] ) && ! empty( $_FILES['review_image']['name'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        
        // Disable file type checks filter momentarily if needed, or rely on standard WordPress media upload
        $attachment_id = media_handle_upload( 'review_image', $commentdata['comment_post_ID'] );
        if ( ! is_wp_error( $attachment_id ) ) {
            update_comment_meta( $comment_id, 'review_image_id', $attachment_id );
            update_comment_meta( $comment_id, 'review_image', wp_get_attachment_url( $attachment_id ) );
        }
    }
}


