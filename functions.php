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
	$needs_swiper = is_front_page() || is_home() || is_shop() || is_product() ||
	                is_product_category() || is_product_tag() || is_page();
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
		'checkout_url'                  => wc_get_checkout_url(),
		'placeholder_image'             => function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'thumbnail' ) : '',
		'price_decimals'                => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2,
		'price_decimal_separator'       => function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.',
		'price_thousand_separator'      => function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',',
		'stock_request_nonce'           => wp_create_nonce( 'woocom_stock_request' ),
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
 * Out-of-stock and pre-order phone request system.
 */
function woocom_stock_requests_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'woocom_stock_requests';
}

function woocom_create_stock_requests_table() {
	global $wpdb;

	$table_name      = woocom_stock_requests_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table_name} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		product_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
		product_title TEXT NOT NULL,
		request_type VARCHAR(30) NOT NULL DEFAULT 'out_of_stock',
		phone VARCHAR(40) NOT NULL,
		quantity INT UNSIGNED NOT NULL DEFAULT 1,
		page_url TEXT NULL,
		user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
		status VARCHAR(30) NOT NULL DEFAULT 'new',
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY product_id (product_id),
		KEY request_type (request_type),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'woocom_stock_requests_db_version', '1.2.0' );
}
add_action( 'after_switch_theme', 'woocom_create_stock_requests_table' );

function woocom_maybe_create_stock_requests_table() {
	if ( get_option( 'woocom_stock_requests_db_version' ) !== '1.2.0' ) {
		woocom_create_stock_requests_table();
	}
}
// Run only in wp-admin — no need to check DB schema on every frontend request.
add_action( 'admin_init', 'woocom_maybe_create_stock_requests_table' );

function woocom_get_product_request_type( $product ) {
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return '';
	}

	if ( 'onbackorder' === $product->get_stock_status() || $product->is_on_backorder( 1 ) ) {
		return 'pre_order';
	}

	if ( ! $product->is_in_stock() || 'outofstock' === $product->get_stock_status() ) {
		return 'out_of_stock';
	}

	return '';
}

function woocom_get_stock_request_label( $request_type ) {
	if ( 'pre_order' === $request_type ) {
		return get_option( 'woocom_text_pre_order', 'Pre Order' );
	}

	return get_option( 'woocom_text_stock_out', 'Out of stock' );
}

function woocom_render_stock_request_badge( $request_type ) {
	if ( ! $request_type ) {
		return '';
	}

	$class = 'pre_order' === $request_type ? 'woocom-stock-badge woocom-stock-badge--preorder' : 'woocom-stock-badge woocom-stock-badge--out';

	return '<span class="' . esc_attr( $class ) . '">' . esc_html( woocom_get_stock_request_label( $request_type ) ) . '</span>';
}

function woocom_render_stock_request_form( $product_id, $request_type = '', $context = 'single' ) {
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return '';
	}

	$request_type = $request_type ? $request_type : woocom_get_product_request_type( $product );
	if ( ! $request_type ) {
		return '';
	}

	$is_archive = 'archive' === $context;
	$title      = 'pre_order' === $request_type ? get_option( 'woocom_text_pre_order', 'Pre Order' ) : get_option( 'woocom_text_stock_out', 'Stock Out' );
	$button     = $title;

	// Archive context: show a button linking to the product page instead of inline form
	if ( $is_archive ) {
		ob_start();
		?>
		<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="w-full border border-primary/40 text-primary hover:bg-primary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[4px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2 mt-auto">
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
			<?php echo esc_html( $button ); ?>
		</a>
		<?php
		return ob_get_clean();
	}

	// Single product context: show phone + quantity form
	ob_start();
	?>
	<form class="woocom-stock-request-form woocom-stock-request-form--single" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-request-type="<?php echo esc_attr( $request_type ); ?>">
		<div class="woocom-stock-request-title"><?php echo esc_html( $title ); ?></div>
		<div class="woocom-stock-request-fields">
			<input type="tel" name="phone" inputmode="tel" autocomplete="tel" required placeholder="<?php esc_attr_e( 'Mobile number', 'woocom' ); ?>">
			<input type="number" name="quantity" min="1" value="1" required placeholder="<?php esc_attr_e( 'Qty', 'woocom' ); ?>" class="woocom-stock-request-qty">
			<button type="submit"><?php echo esc_html( $button ); ?></button>
		</div>
		<div class="woocom-stock-request-message" aria-live="polite"></div>
	</form>
	<?php
	return ob_get_clean();
}

function woocom_handle_stock_request() {
	check_ajax_referer( 'woocom_stock_request', 'nonce' );

	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$request_type = isset( $_POST['request_type'] ) ? sanitize_key( wp_unslash( $_POST['request_type'] ) ) : '';
	$phone        = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$quantity     = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	$page_url     = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';
	$product      = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || ! in_array( $request_type, array( 'out_of_stock', 'pre_order' ), true ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product request.', 'woocom' ) ) );
	}

	if ( strlen( preg_replace( '/\D+/', '', $phone ) ) < 8 ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid mobile number.', 'woocom' ) ) );
	}

	global $wpdb;
	$table_name = woocom_stock_requests_table_name();
	$inserted   = $wpdb->insert(
		$table_name,
		array(
			'product_id'    => $product_id,
			'product_title' => $product->get_name(),
			'request_type'  => $request_type,
			'phone'         => $phone,
			'quantity'      => $quantity,
			'page_url'      => $page_url,
			'user_id'       => get_current_user_id(),
			'status'        => 'new',
			'created_at'    => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s' )
	);

	if ( ! $inserted ) {
		wp_send_json_error( array( 'message' => __( 'Could not save your request. Please try again.', 'woocom' ) ) );
	}

	wp_send_json_success( array( 'message' => __( 'Thanks! Your pre-order request has been submitted.', 'woocom' ) ) );
}
add_action( 'wp_ajax_woocom_stock_request', 'woocom_handle_stock_request' );
add_action( 'wp_ajax_nopriv_woocom_stock_request', 'woocom_handle_stock_request' );

function woocom_stock_requests_admin_menu() {
	// Pre-order Requests — separate top-level menu with cart icon
	add_menu_page(
		__( 'Pre-order Requests', 'woocom' ),
		__( 'Pre-orders', 'woocom' ),
		'manage_options',
		'woocom-preorder-requests',
		'woocom_preorder_requests_admin_page',
		'dashicons-cart',
		56
	);

	// Stock Requests — submenu under theme settings
	add_submenu_page(
		'woocom-settings',
		__( 'Stock Out Requests', 'woocom' ),
		__( 'Stock Out Requests', 'woocom' ),
		'manage_options',
		'woocom-stock-requests',
		'woocom_stock_requests_admin_page'
	);
}
add_action( 'admin_menu', 'woocom_stock_requests_admin_menu', 20 );

/**
 * Pre-order Requests admin page.
 */
function woocom_preorder_requests_admin_page() {
	global $wpdb;
	$table_name = woocom_stock_requests_table_name();
	$requests   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE request_type = %s ORDER BY created_at DESC LIMIT 200", 'pre_order' ) );

	// Handle status update
	if ( isset( $_GET['action'], $_GET['request_id'], $_GET['_wpnonce'] ) && 'update_status' === $_GET['action'] ) {
		if ( wp_verify_nonce( $_GET['_wpnonce'], 'woocom_update_request_status' ) ) {
			$new_status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
			$request_id = absint( $_GET['request_id'] );
			if ( $request_id && in_array( $new_status, array( 'new', 'contacted', 'completed', 'cancelled' ), true ) ) {
				$wpdb->update( $table_name, array( 'status' => $new_status ), array( 'id' => $request_id ), array( '%s' ), array( '%d' ) );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Status updated.', 'woocom' ) . '</p></div>';
				// Re-fetch
				$requests = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE request_type = %s ORDER BY created_at DESC LIMIT 200", 'pre_order' ) );
			}
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Pre-order Requests', 'woocom' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Customers who submitted pre-order requests for out-of-stock products.', 'woocom' ); ?></p>
		<table class="widefat striped" style="margin-top:15px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Product', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Mobile', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Quantity', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Status', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'woocom' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $requests ) : ?>
					<?php foreach ( $requests as $request ) : ?>
						<tr>
							<td><?php echo esc_html( date_i18n( 'd M Y, h:i A', strtotime( $request->created_at ) ) ); ?></td>
							<td>
								<strong><?php echo esc_html( $request->product_title ); ?></strong>
								<div class="row-actions">
									<a href="<?php echo esc_url( get_edit_post_link( $request->product_id ) ); ?>"><?php esc_html_e( 'Edit product', 'woocom' ); ?></a>
									<?php if ( $request->page_url ) : ?>
										| <a href="<?php echo esc_url( $request->page_url ); ?>" target="_blank"><?php esc_html_e( 'View page', 'woocom' ); ?></a>
									<?php endif; ?>
								</div>
							</td>
							<td><strong><?php echo esc_html( $request->phone ); ?></strong></td>
							<td><?php echo esc_html( isset( $request->quantity ) ? $request->quantity : 1 ); ?></td>
							<td>
								<?php
								$status_colors = array( 'new' => '#2271b1', 'contacted' => '#dba617', 'completed' => '#00a32a', 'cancelled' => '#d63638' );
								$color = isset( $status_colors[ $request->status ] ) ? $status_colors[ $request->status ] : '#666';
								?>
								<span style="background:<?php echo esc_attr( $color ); ?>;color:#fff;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">
									<?php echo esc_html( ucfirst( $request->status ) ); ?>
								</span>
							</td>
							<td>
								<?php
								$statuses = array( 'contacted', 'completed', 'cancelled' );
								foreach ( $statuses as $s ) {
									if ( $s === $request->status ) continue;
									$url = wp_nonce_url( admin_url( 'admin.php?page=woocom-preorder-requests&action=update_status&request_id=' . $request->id . '&status=' . $s ), 'woocom_update_request_status' );
									echo '<a href="' . esc_url( $url ) . '" style="margin-right:8px;text-decoration:none;">' . esc_html( ucfirst( $s ) ) . '</a>';
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No pre-order requests yet.', 'woocom' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Stock Out Requests admin page (existing, under theme settings).
 */
function woocom_stock_requests_admin_page() {
	global $wpdb;
	$table_name = woocom_stock_requests_table_name();
	$requests   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE request_type = %s ORDER BY created_at DESC LIMIT 200", 'out_of_stock' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Stock Out Requests', 'woocom' ); ?></h1>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Product', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Mobile', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Quantity', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Status', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Link', 'woocom' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $requests ) : ?>
					<?php foreach ( $requests as $request ) : ?>
						<tr>
							<td><?php echo esc_html( date_i18n( 'd M Y, h:i A', strtotime( $request->created_at ) ) ); ?></td>
							<td>
								<?php echo esc_html( $request->product_title ); ?>
								<div class="row-actions">#<?php echo esc_html( $request->product_id ); ?></div>
							</td>
							<td><strong><?php echo esc_html( $request->phone ); ?></strong></td>
							<td><?php echo esc_html( isset( $request->quantity ) ? $request->quantity : 1 ); ?></td>
							<td><?php echo esc_html( ucfirst( $request->status ) ); ?></td>
							<td>
								<?php if ( $request->page_url ) : ?>
									<a href="<?php echo esc_url( $request->page_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View', 'woocom' ); ?></a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No stock out requests yet.', 'woocom' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

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
 * Global Custom CSS — Colors + Navigation
 */
add_action('wp_head', 'woocom_global_custom_css', 10);
function woocom_global_custom_css() {
    // ── Transient cache: rebuild only after a settings change or 24 h ──────────
    delete_transient( 'woocom_global_css_vars' );
    $css = false;
    if ( false === $css ) {
        $primary   = get_option('woocom_primary_color') ?: '#1E5D02';
        $secondary = get_option('woocom_secondary_color') ?: '#F7A501';
        $main_background = get_option('woocom_main_background_color', '#FBF9F5') ?: '#FBF9F5';
        $product_add_to_cart_color = get_option('product_add_to_cart_button_color', $secondary) ?: $secondary;
        $product_buy_now_color     = get_option('product_buy_now_button_color', $primary) ?: $primary;
        $product_whatsapp_color    = get_option('product_whatsapp_button_color', '#25D366') ?: '#25D366';
        $product_call_color        = get_option('product_call_button_color', '#1e3a8a') ?: '#1e3a8a';
        $nav_bg    = get_option('nav_bg_color', '#000000');
        $nav_text  = get_option('nav_text_color', '#ffffff');
        $nav_hover = get_option('nav_hover_color', '#F7A501');
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
 * Customize Checkout Fields
 */
add_filter( 'woocommerce_checkout_fields', 'woocom_customize_checkout_fields' );
function woocom_customize_checkout_fields( $fields ) {
        // 1. Name & Phone (Stack on mobile)
    $fields['billing']['billing_first_name']['priority'] = 10;
    $fields['billing']['billing_first_name']['placeholder'] = 'Your Full Name *';
    $fields['billing']['billing_first_name']['label'] = '';
    $fields['billing']['billing_first_name']['class'] = array('form-row-wide', 'mb-4');
    
    $fields['billing']['billing_phone']['priority'] = 20;
    $fields['billing']['billing_phone']['placeholder'] = '017********';
    $fields['billing']['billing_phone']['label'] = '';
    $fields['billing']['billing_phone']['class'] = array('form-row-wide', 'mb-4', 'phone-prefix-88');

    // 2. District (Dropdown) & Thana (Select)
    $fields['billing']['billing_state']['priority'] = 30;
    $fields['billing']['billing_state']['label'] = '';
    $fields['billing']['billing_state']['placeholder'] = 'Select District';
    $fields['billing']['billing_state']['class'] = array('form-row-first', '!w-[48%]', '!float-left', 'mb-4');

    $fields['billing']['billing_city']['type'] = 'select'; // Changed to select
    $fields['billing']['billing_city']['priority'] = 40;
    $fields['billing']['billing_city']['label'] = '';
    $fields['billing']['billing_city']['options'] = array('' => 'Select Thana / Area');
    $fields['billing']['billing_city']['class'] = array('form-row-last', '!w-[48%]', '!float-right', 'mb-4', 'thana-dropdown-field');

    // 3. Address (Full Width)
    $fields['billing']['billing_address_1']['priority'] = 50;
    $fields['billing']['billing_address_1']['placeholder'] = 'ex: House no. / building / street / area';
    $fields['billing']['billing_address_1']['label'] = '';
    $fields['billing']['billing_address_1']['class'] = array('form-row-wide', 'w-full', 'clear-both', 'mb-4');

    // Hide Country but force it to Bangladesh
    $fields['billing']['billing_country']['type'] = 'hidden';
    $fields['billing']['billing_country']['default'] = 'BD';
    $fields['billing']['billing_country']['label'] = '';

    // Apply same to shipping (Displayed in Billing Toggle)
    if (isset($fields['shipping'])) {
        $fields['shipping']['shipping_first_name']['priority'] = 10;
        $fields['shipping']['shipping_first_name']['placeholder'] = 'Your Full Name *';
        $fields['shipping']['shipping_first_name']['label'] = '';
        $fields['shipping']['shipping_first_name']['class'] = array('form-row-first', 'mb-4');
        
        $fields['shipping']['shipping_phone']['type'] = 'tel';
        $fields['shipping']['shipping_phone']['priority'] = 20;
        $fields['shipping']['shipping_phone']['placeholder'] = '017*********';
        $fields['shipping']['shipping_phone']['label'] = '';
        $fields['shipping']['shipping_phone']['class'] = array('form-row-last', 'mb-4', 'phone-prefix-88');
        $fields['shipping']['shipping_phone']['required'] = false;

        $fields['shipping']['shipping_country']['type'] = 'hidden';
        $fields['shipping']['shipping_country']['default'] = 'BD';
        $fields['shipping']['shipping_country']['label'] = '';

        $fields['shipping']['shipping_state']['priority'] = 30;
        $fields['shipping']['shipping_state']['label'] = '';
        $fields['shipping']['shipping_state']['placeholder'] = 'Select District';
        $fields['shipping']['shipping_state']['class'] = array('form-row-first', 'mb-4');

        $fields['shipping']['shipping_city']['type'] = 'select';
        $fields['shipping']['shipping_city']['options'] = array('' => 'Select Thana (Optional)');
        $fields['shipping']['shipping_city']['label'] = '';
        $fields['shipping']['shipping_city']['priority'] = 40;
        $fields['shipping']['shipping_city']['class'] = array('form-row-last', 'mb-4', 'thana-dropdown-field');

        $fields['shipping']['shipping_address_1']['priority'] = 50;
        $fields['shipping']['shipping_address_1']['label'] = '';
        $fields['shipping']['shipping_address_1']['placeholder'] = 'ex: House no. / building / street / area';
        $fields['shipping']['shipping_address_1']['class'] = array('form-row-wide', 'w-full', 'clear-both', 'mb-4');
    }

    // Remove unwanted fields
    unset($fields['billing']['billing_postcode'], $fields['shipping']['shipping_postcode']);
    unset($fields['billing']['billing_last_name'], $fields['shipping']['shipping_last_name']);
    unset($fields['billing']['billing_company'], $fields['shipping']['shipping_company']);
    unset($fields['billing']['billing_address_2'], $fields['shipping']['shipping_address_2']);
    unset($fields['billing']['billing_email']);

    return $fields;
}

/**
 * Add Tailwind classes and Thana Sync Script
 */
add_action('wp_footer', 'woocom_checkout_thana_sync_script');
function woocom_checkout_thana_sync_script() {
    if (!is_checkout()) return;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        const thanaData = {
            'BD-01': ['Bandarban Sadar', 'Alikadam', 'Lama', 'Naikhongchhari', 'Rowangchhari', 'Ruma', 'Thanchi'],
            'BD-02': ['Barguna Sadar', 'Amtali', 'Bamna', 'Betagi', 'Patharghata', 'Taltali'],
            'BD-03': ['Bogura Sadar', 'Adamdighi', 'Dhunat', 'Dhupchanchia', 'Gabtali', 'Kahaloo', 'Nandigram', 'Sariakandi', 'Shajahanpur', 'Sherpur', 'Shibganj', 'Sonatala'],
            'BD-04': ['Brahmanbaria Sadar', 'Akhaura', 'Bancharampur', 'Bijoynagar', 'Kasba', 'Nabinagar', 'Nasirnagar', 'Sarail', 'Ashuganj'],
            'BD-05': ['Bagerhat Sadar', 'Chitalmari', 'Fakirhat', 'Kachua', 'Mollahat', 'Mongla', 'Morrelganj', 'Rampal', 'Sarankhola'],
            'BD-06': ['Barishal Sadar', 'Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gaurnadi', 'Hizla', 'Mehendiganj', 'Muladi', 'Wazirpur'],
            'BD-07': ['Bhola Sadar', 'Burhanuddin', 'Char Fasson', 'Daulatkhan', 'Lalmohan', 'Manpura', 'Tazumuddin'],
            'BD-08': ['Cumilla Sadar', 'Barura', 'Brahmanpara', 'Burichong', 'Chandina', 'Chauddagram', 'Daudkandi', 'Debidwar', 'Homna', 'Laksam', 'Muradnagar', 'Nangalkot', 'Titas', 'Monohargonj', 'Meghna'],
            'BD-09': ['Chandpur Sadar', 'Faridganj', 'Haimchar', 'Haziganj', 'Kachua', 'Matlab North', 'Matlab South', 'Shahrasti'],
            'BD-10': ['Chattogram City', 'Anwara', 'Banshkhali', 'Boalkhali', 'Chandanaish', 'Fatikchhari', 'Hathazari', 'Lohagara', 'Mirsharai', 'Patiya', 'Rangunia', 'Raozan', 'Sandwip', 'Satkania', 'Sitakunda'],
            'BD-11': ['Cox\'s Bazar Sadar', 'Chakaria', 'Maheshkhali', 'Ramu', 'Teknaf', 'Ukhia', 'Pekua', 'Kutubdia'],
            'BD-12': ['Chuadanga Sadar', 'Alamdanga', 'Damurhuda', 'Jiban Nagar'],
            'BD-13': ['Dhaka City', 'Savar', 'Dhamrai', 'Keraniganj', 'Nawabganj', 'Dohar'],
            'BD-14': ['Dinajpur Sadar', 'Birampur', 'Birganj', 'Birol', 'Bochaganj', 'Chirirbandar', 'Phulbari', 'Ghoraghat', 'Hakimpur', 'Kaharole', 'Khansama', 'Nawabganj', 'Parbatipur'],
            'BD-15': ['Faridpur Sadar', 'Alfadanga', 'Bhanga', 'Boalmari', 'Charbhadrasan', 'Madhukhali', 'Nagarkanda', 'Sadarpur', 'Saltha'],
            'BD-16': ['Feni Sadar', 'Chhagalnaiya', 'Daganbhuiyan', 'Parshuram', 'Sonagazi', 'Fulgazi'],
            'BD-17': ['Gopalganj Sadar', 'Kashiani', 'Kotalipara', 'Muksudpur', 'Tungipara'],
            'BD-18': ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur'],
            'BD-19': ['Gaibandha Sadar', 'Fulchhari', 'Gobindaganj', 'Palashbari', 'Sadullapur', 'Sughatta', 'Sundarganj'],
            'BD-20': ['Habiganj Sadar', 'Ajmiriganj', 'Bahubal', 'Baniyachong', 'Chunarughat', 'Lakhai', 'Madhabpur', 'Nabiganj', 'Sayestaganj'],
            'BD-21': ['Jamalpur Sadar', 'Bakshiganj', 'Dewanganj', 'Islampur', 'Madarganj', 'Melenandaha', 'Sarishabari'],
            'BD-22': ['Jashore Sadar', 'Abhaynagar', 'Bagherpara', 'Chaugachha', 'Jhikargachha', 'Keshabpur', 'Manirampur', 'Sharsha'],
            'BD-23': ['Jhenaidah Sadar', 'Harinakunda', 'Kaliganj', 'Kotchandpur', 'Maheshpur', 'Shailkupa'],
            'BD-24': ['Joypurhat Sadar', 'Akkelpur', 'Kalai', 'Khetlal', 'Panchbibi'],
            'BD-25': ['Jhalokathi Sadar', 'Kathalia', 'Nalchity', 'Rajapur'],
            'BD-26': ['Kishoreganj Sadar', 'Itna', 'Katiadi', 'Bhairab', 'Tarail', 'Hossainpur', 'Pakundia', 'Kuliarchar', 'Karimganj', 'Bajitpur', 'Austagram', 'Mithamain', 'Nikli'],
            'BD-27': ['Khulna Sadar', 'Batiaghata', 'Dacope', 'Dumuria', 'Dighalia', 'Koyra', 'Paikgachha', 'Phultala', 'Rupsha'],
            'BD-28': ['Kurigram Sadar', 'Bhurungamari', 'Char Rajibpur', 'Chilmari', 'Phulbari', 'Nageshwari', 'Rajarhat', 'Roumari', 'Ulipur'],
            'BD-29': ['Khagrachhari Sadar', 'Dighinala', 'Lakshmichhari', 'Mahalchhari', 'Manikchhari', 'Matiranga', 'Panchhari', 'Ramgarh'],
            'BD-30': ['Kushtia Sadar', 'Bheramara', 'Daulatpur', 'Khoksa', 'Kumarkhali', 'Mirpur'],
            'BD-31': ['Lakshmipur Sadar', 'Raipur', 'Ramganj', 'Ramgati', 'Kamalnagar'],
            'BD-32': ['Lalmonirhat Sadar', 'Aditmari', 'Hatibandha', 'Kaliganj', 'Patgram'],
            'BD-33': ['Manikganj Sadar', 'Singair', 'Shibalaya', 'Saturia', 'Harirampur', 'Gheor', 'Daulatpur'],
            'BD-34': ['Mymensingh Sadar', 'Bhaluka', 'Trishal', 'Haluaghat', 'Muktagachha', 'Dhobaura', 'Fulbaria', 'Gaffargaon', 'Gauripur', 'Ishwarganj', 'Nandail', 'Phulpur', 'Tara Khanda'],
            'BD-35': ['Munshiganj Sadar', 'Sreenagar', 'Sirajdikhan', 'Lauhajang', 'Gajaria', 'Tongibari'],
            'BD-36': ['Madaripur Sadar', 'Kalkini', 'Rajoir', 'Shibchar'],
            'BD-37': ['Magura Sadar', 'Mohammadpur', 'Shalkha', 'Sreepur'],
            'BD-38': ['Moulvibazar Sadar', 'Barlekha', 'Juri', 'Kamalganj', 'Kulaura', 'Rajnagar', 'Sreemangal'],
            'BD-39': ['Meherpur Sadar', 'Gangni', 'Mujibnagar'],
            'BD-40': ['Narayanganj Sadar', 'Araihazar', 'Bandar', 'Rupganj', 'Sonargaon'],
            'BD-41': ['Netrokona Sadar', 'Atpara', 'Barhatta', 'Durgapur', 'Khaliajuri', 'Kalmakanda', 'Kendua', 'Madan', 'Mohanganj', 'Purbadhala'],
            'BD-42': ['Narsingdi Sadar', 'Belabo', 'Monohardi', 'Palash', 'Raipura', 'Shibpur'],
            'BD-43': ['Narail Sadar', 'Kalia', 'Lohagara'],
            'BD-44': ['Natore Sadar', 'Bagatipara', 'Baraigram', 'Gurudaspur', 'Lalpur', 'Singra', 'Naldanga'],
            'BD-45': ['Chapai Nawabganj Sadar', 'Bholahat', 'Gomastapur', 'Nachole', 'Shibganj'],
            'BD-46': ['Nilphamari Sadar', 'Dimla', 'Domar', 'Jaldhaka', 'Kishoreganj', 'Saidpur'],
            'BD-47': ['Noakhali Sadar', 'Begumganj', 'Chatkhil', 'Companiganj', 'Hatiya', 'Senbagh', 'Sonaimuri', 'Subarnachar', 'Kabirhat'],
            'BD-48': ['Naogaon Sadar', 'Atrai', 'Badalgachhi', 'Dhamoirhat', 'Manda', 'Mahadevpur', 'Niamatpur', 'Patnitala', 'Porsha', 'Raninagar', 'Sapahar'],
            'BD-49': ['Pabna Sadar', 'Atgharia', 'Bera', 'Bhangura', 'Chatmohar', 'Faridpur', 'Ishwardi', 'Santhia', 'Sujanagar'],
            'BD-50': ['Pirojpur Sadar', 'Bhandaria', 'Kawkhali', 'Mathbaria', 'Nazirpur', 'Nesarabad', 'Indurkani'],
            'BD-51': ['Patuakhali Sadar', 'Bauphal', 'Dashmina', 'Galachipa', 'Kalapara', 'Mirzaganj', 'Dumki', 'Rangabali'],
            'BD-52': ['Panchagarh Sadar', 'Atwari', 'Boda', 'Debiganj', 'Tetulia'],
            'BD-53': ['Rajbari Sadar', 'Baliakandi', 'Goalandaghat', 'Pangsha', 'Kalukhali'],
            'BD-54': ['Rajshahi Sadar', 'Bagha', 'Bagmara', 'Charghat', 'Durgapur', 'Godagari', 'Mohanpur', 'Paba', 'Puthia', 'Tanore'],
            'BD-55': ['Rangpur Sadar', 'Badarganj', 'Gangachhara', 'Kaunia', 'Mithapukur', 'Pirgachha', 'Pirganj', 'Taraganj'],
            'BD-56': ['Rangamati Sadar', 'Baghaichhari', 'Barkal', 'Kawkhali', 'Belaichhari', 'Kaptai', 'Jurachhari', 'Langadu', 'Nanearchar', 'Rajasthali'],
            'BD-57': ['Sherpur Sadar', 'Jhenaigati', 'Nakla', 'Nalitabari', 'Sreebardi'],
            'BD-58': ['Satkhira Sadar', 'Assasuni', 'Debhata', 'Kalaroa', 'Kaliganj', 'Shyamnagar', 'Tala'],
            'BD-59': ['Sirajganj Sadar', 'Belkuchi', 'Chauhali', 'Kamarkhanda', 'Kazipur', 'Raiganj', 'Shahjadpur', 'Tarash', 'Ullahpara'],
            'BD-60': ['Sylhet Sadar', 'Dakshin Surma', 'Bishwanath', 'Balaganj', 'Fenchuganj', 'Golapganj', 'Beanibazar', 'Zakiganj', 'Kanaighat', 'Jaintiapur', 'Gowainghat', 'Companiganj', 'Osmani Nagar'],
            'BD-61': ['Sunamganj Sadar', 'Bishwamvapur', 'Chhatak', 'Derai', 'Dharamapasha', 'Dowarabazar', 'Jagannathpur', 'Jamalganj', 'Sullah', 'Tahirpur', 'South Sunamganj'],
            'BD-62': ['Shariatpur Sadar', 'Damudya', 'Gosairhat', 'Naria', 'Zajira', 'Bhedarganj'],
            'BD-63': ['Tangail Sadar', 'Basail', 'Bhuapur', 'Delduar', 'Ghatail', 'Gopalpur', 'Kalihati', 'Madhupur', 'Mirzapur', 'Nagarpur', 'Sakhipur', 'Dhanbari'],
            'BD-64': ['Thakurgaon Sadar', 'Baliadangi', 'Haripur', 'Pirganj', 'Ranisankail'],
        };

        function updateThanas(countryField, stateField, cityField) {
            const district = $(stateField).val();
            const $city = $(cityField);
            
            // Save current value if any
            const currentVal = $city.val();
            
            // Clear existing
            $city.empty().append('<option value="">Select Thana / Area</option>');
            
            if (district && thanaData[district]) {
                thanaData[district].forEach(function(thana) {
                    $city.append('<option value="' + thana + '">' + thana + '</option>');
                });
            } else if (district) {
                $city.append('<option value="Other">Other / Not Listed</option>');
            }
            
            // Restore value if it exists in new options
            if (currentVal && $city.find('option[value="' + currentVal + '"]').length > 0) {
                $city.val(currentVal);
            }

            // Trigger change for Select2 and others
            $city.trigger('change');
            if ($city.data('select2')) {
                $city.trigger('change.select2');
            }

            // Trigger WooCommerce checkout update
            $(document.body).trigger('update_checkout');
        }

        // Billing
        $(document.body).on('change', '#billing_state', function() {
            updateThanas('#billing_country', '#billing_state', '#billing_city');
        });

        // Shipping
        $(document.body).on('change', '#shipping_state', function() {
            updateThanas('#shipping_country', '#shipping_state', '#shipping_city');
        });

        // Trigger update when City changes
        $(document.body).on('change', '#billing_city, #shipping_city', function() {
            $(document.body).trigger('update_checkout');
        });

        // Force Select2 for District and Thana
        function initCheckoutSelect2() {
            $('#billing_state, #shipping_state, .thana-dropdown-field select').select2({
                minimumResultsForSearch: 10,
                width: '100%'
            });
        }

        $(document.body).on('updated_checkout', function() {
            initCheckoutSelect2();
        });

        // Trigger on load
        setTimeout(function() {
            initCheckoutSelect2();
            if ($('#billing_state').val()) $('#billing_state').trigger('change');
            if ($('#shipping_state').val()) $('#shipping_state').trigger('change');
        }, 1000);
    });
    </script>
    <?php
}

/**
 * Add Tailwind classes to checkout inputs
 */
add_filter('woocommerce_form_field_args', 'woocom_form_field_args', 10, 3);
function woocom_form_field_args($args, $key, $value) {
    $args['input_class'][] = 'w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:border-secondary focus:ring-0 focus:outline-none';
    return $args;
}

/**
 * Helper to calculate discount percentage
 */
function woocom_get_discount_percentage( $product ) {
    if ( ! $product->is_on_sale() ) return 0;
    
    $regular_price = (float) $product->get_regular_price();
    $sale_price    = (float) $product->get_price();
    
    if ( $regular_price > 0 && $sale_price > 0 ) {
        $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
        return $percentage;
    }
    return 0;
}

/**
 * AJAX Update Cart Item Qty for Checkout
 */
add_action('wp_ajax_checkout_update_qty', 'woocom_checkout_update_qty');
add_action('wp_ajax_nopriv_checkout_update_qty', 'woocom_checkout_update_qty');
function woocom_checkout_update_qty() {
    $cart_item_key = $_POST['cart_item_key'];
    $quantity = intval($_POST['quantity']);

    if (function_exists('WC') && WC()->cart && WC()->cart->set_quantity($cart_item_key, $quantity)) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
    wp_die();
}

/**
 * AJAX Remove Cart Item for Checkout
 */
add_action('wp_ajax_checkout_remove_item', 'woocom_checkout_remove_item');
add_action('wp_ajax_nopriv_checkout_remove_item', 'woocom_checkout_remove_item');
function woocom_checkout_remove_item() {
    $cart_item_key = $_POST['cart_item_key'];

    if (function_exists('WC') && WC()->cart && WC()->cart->remove_cart_item($cart_item_key)) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
    wp_die();
}

/**
 * Disable "Added to Cart" messages globally
 */
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

/**
 * Force Default Checkout Country to Bangladesh
 */
add_filter( 'default_checkout_billing_country', 'woocom_default_checkout_country' );
function woocom_default_checkout_country() {
    return 'BD';
}
/**
 * Update custom shipping fragments via AJAX
 */
add_filter( 'woocommerce_update_order_review_fragments', 'woocom_checkout_shipping_fragments' );
function woocom_checkout_shipping_fragments( $fragments ) {
    ob_start();
    ?>
    <div class="shipping-card-wrapper bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 custom-shipping-ui" style="display: block !important;">
        <?php 
        if ( function_exists('WC') && WC()->cart && WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
            wc_cart_totals_shipping_html();
        } else {
            echo '<p class="text-xs text-gray-400">Please enter your address to view shipping methods.</p>';
        }
        ?>
    </div>
    <?php
    $fragments['.shipping-card-wrapper'] = ob_get_clean();

    ob_start();
    ?>
    <div id="checkout-totals-fragment">
        <div class="flex justify-between text-sm text-gray-500 pt-2 border-t border-gray-50">
            <span>Sub total</span>
            <span class="font-bold text-gray-800"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>
        <div class="flex justify-between text-sm text-gray-500">
            <span>Delivery cost</span>
            <span class="font-bold text-gray-800"><?php echo (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_shipping_total() : ''; ?></span>
        </div>
        <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
            <span class="text-base font-bold text-gray-800">Total</span>
            <span class="text-lg font-bold text-gray-800"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
    </div>
    <?php
    $fragments['#checkout-totals-fragment'] = ob_get_clean();

    return $fragments;
}

/**
 * Hide 'Shipment' text via translation filter
 */
add_filter( 'gettext', 'woocom_hide_shipment_text', 20, 3 );
function woocom_hide_shipment_text( $translated_text, $text, $domain ) {
    if ( is_checkout() && $text === 'Shipment' ) {
        return '';
    }
    return $translated_text;
}

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
    if ( is_checkout() ) {
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

    wp_safe_redirect( wc_get_checkout_url() );
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
 * Add custom fields to WooCommerce registration form.
 */
// add_action( 'woocommerce_register_form', 'woocom_add_registration_fields' );
function woocom_add_registration_fields() {
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_first_name"><?php esc_html_e( 'Full Name', 'woocom' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text woocommerce-Input" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) echo esc_attr( wp_unslash( $_POST['billing_first_name'] ) ); ?>" required />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_phone"><?php esc_html_e( 'Phone Number', 'woocom' ); ?> <span class="required">*</span></label>
        <input type="tel" class="input-text woocommerce-Input" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) echo esc_attr( wp_unslash( $_POST['billing_phone'] ) ); ?>" required />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_address_1"><?php esc_html_e( 'Full Address', 'woocom' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text woocommerce-Input" name="billing_address_1" id="reg_billing_address_1" value="<?php if ( ! empty( $_POST['billing_address_1'] ) ) echo esc_attr( wp_unslash( $_POST['billing_address_1'] ) ); ?>" placeholder="<?php esc_attr_e( 'House/Road, Area, City', 'woocom' ); ?>" required />
    </p>
    <?php
}

/**
 * Validate custom fields on WooCommerce registration.
 */
add_filter( 'woocommerce_registration_errors', 'woocom_validate_registration_fields', 10, 3 );
function woocom_validate_registration_fields( $errors, $username, $email ) {
    if ( empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( '<strong>Error:</strong> Full Name is required.', 'woocom' ) );
    }
    if ( empty( $_POST['billing_phone'] ) ) {
        $errors->add( 'billing_phone_error', __( '<strong>Error:</strong> Phone Number is required.', 'woocom' ) );
    } elseif ( strlen( preg_replace( '/\D+/', '', $_POST['billing_phone'] ) ) < 8 ) {
        $errors->add( 'billing_phone_error', __( '<strong>Error:</strong> Please enter a valid phone number.', 'woocom' ) );
    }
    if ( empty( $_POST['billing_address_1'] ) ) {
        $errors->add( 'billing_address_1_error', __( '<strong>Error:</strong> Full Address is required.', 'woocom' ) );
    }
    return $errors;
}

/**
 * Save custom fields to user metadata upon successful registration.
 */
add_action( 'woocommerce_created_customer', 'woocom_save_registration_fields' );
function woocom_save_registration_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) );
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) );
    }
    if ( isset( $_POST['billing_phone'] ) ) {
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) );
    }
    if ( isset( $_POST['billing_address_1'] ) ) {
        update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) );
    }
}

/**
 * ═══════════════════════════════════════════════════════════
 * OTP Login — Send OTP
 * ═══════════════════════════════════════════════════════════
 */
add_action( 'wp_ajax_nopriv_woocom_send_otp', 'woocom_ajax_send_otp' );
add_action( 'wp_ajax_woocom_send_otp',        'woocom_ajax_send_otp' );
function woocom_ajax_send_otp() {
    $phone = isset( $_POST['phone'] ) ? preg_replace( '/\D/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';

    if ( strlen( $phone ) < 10 || strlen( $phone ) > 15 ) {
        wp_send_json_error( array( 'message' => __( 'Please enter a valid phone number.', 'woocom' ) ) );
    }

    $otp = str_pad( (string) wp_rand( 0, 999999 ), 6, '0', STR_PAD_LEFT );
    set_transient( 'woocom_otp_' . md5( $phone ), $otp, 5 * MINUTE_IN_SECONDS );

    /**
     * Hook: woocom_send_otp_sms
     * Connect your SMS gateway here.
     *
     * add_action( 'woocom_send_otp_sms', function( $phone, $otp ) {
     *     // Call SMS API
     * }, 10, 2 );
     */
    do_action( 'woocom_send_otp_sms', $phone, $otp );

    $response = array( 'message' => __( 'OTP sent successfully!', 'woocom' ) );
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        $response['debug_otp'] = $otp; // Remove before going live!
    }
    wp_send_json_success( $response );
}

/**
 * ═══════════════════════════════════════════════════════════
 * OTP Login — Verify OTP & Log In
 * ═══════════════════════════════════════════════════════════
 */
add_action( 'wp_ajax_nopriv_woocom_verify_otp', 'woocom_ajax_verify_otp' );
add_action( 'wp_ajax_woocom_verify_otp',        'woocom_ajax_verify_otp' );
function woocom_ajax_verify_otp() {
    $phone = isset( $_POST['phone'] ) ? preg_replace( '/\D/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
    $otp   = isset( $_POST['otp'] )   ? sanitize_text_field( wp_unslash( $_POST['otp'] ) )   : '';

    if ( ! $phone || ! $otp ) {
        wp_send_json_error( array( 'message' => __( 'Invalid request.', 'woocom' ) ) );
    }

    $stored_otp = get_transient( 'woocom_otp_' . md5( $phone ) );
    if ( false === $stored_otp || ! hash_equals( $stored_otp, $otp ) ) {
        wp_send_json_error( array( 'message' => __( 'Invalid or expired OTP. Please try again.', 'woocom' ) ) );
    }

    delete_transient( 'woocom_otp_' . md5( $phone ) );

    // Find user by billing phone
    $users = get_users( array(
        'meta_key'   => 'billing_phone',
        'meta_value' => $phone,
        'number'     => 1,
        'fields'     => 'ids',
    ) );

    if ( ! empty( $users ) ) {
        $user_id = (int) $users[0];
    } else {
        // Auto-create account for new OTP users
        $username = 'user_' . $phone;
        $password = wp_generate_password( 16 );
        $email    = $phone . '@otp.woocom.local';
        $user_id  = wp_create_user( $username, $password, $email );
        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Could not create account. Please register first.', 'woocom' ) ) );
        }
        update_user_meta( $user_id, 'billing_phone', $phone );
        $u = new WP_User( $user_id );
        $u->set_role( 'customer' );
    }

    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );
    $user_data = get_userdata( $user_id );
    do_action( 'wp_login', $user_data->user_login, $user_data );

    wp_send_json_success( array(
        'message'  => __( 'Login successful!', 'woocom' ),
        'redirect' => wc_get_account_endpoint_url( 'dashboard' ),
    ) );
}

/**
 * Auto-create WooCommerce Order Tracking page if it does not exist.
 */
add_action( 'init', 'woocom_auto_create_order_tracking_page' );
function woocom_auto_create_order_tracking_page() {
    $page_slug = 'order-tracking';
    if ( ! get_page_by_path( $page_slug ) ) {
        wp_insert_post( array(
            'post_title'     => 'Order Tracking',
            'post_content'   => '<!-- wp:shortcode -->[woocommerce_order_tracking]<!-- /wp:shortcode -->',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_name'      => $page_slug,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        ) );
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
        $existing = get_page_by_path( $slug );
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


