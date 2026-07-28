<?php
/**
 * WooCommerce Specific Functions
 *
 * @package Woocom
 */

if ( class_exists( 'WooCommerce' ) ) {
	if ( ! function_exists( 'woocom_get_cart_item_image_url' ) ) {
		function woocom_get_cart_item_image_url( $cart_item, $size = 'thumbnail' ) {
			if ( empty( $cart_item['data'] ) || ! is_a( $cart_item['data'], 'WC_Product' ) ) {
				return wc_placeholder_img_src( $size );
			}

			$product  = $cart_item['data'];
			$image_id = $product->get_image_id();

			if ( ! $image_id && ! empty( $cart_item['product_id'] ) ) {
				$parent_product = wc_get_product( $cart_item['product_id'] );
				if ( $parent_product ) {
					$image_id = $parent_product->get_image_id();
				}
			}

			$image_url = $image_id ? wp_get_attachment_image_url( $image_id, $size ) : '';

			return $image_url ? $image_url : wc_placeholder_img_src( $size );
		}
	}

	/**
	 * Remove default WooCommerce styling if needed, or add custom ones.
	 */
	function woocom_add_woocommerce_support() {
		add_theme_support( 'woocommerce' );
	}
	add_action( 'after_setup_theme', 'woocom_add_woocommerce_support' );

	/**
	 * Custom checkout fields for Bangladesh (Districts/Thanas)
	 */
	add_filter( 'woocommerce_checkout_fields', 'woocom_custom_checkout_fields' );

	function woocom_custom_checkout_fields( $fields ) {
		// We will implement the cascading logic in a future step.
		return $fields;
	}

	/**
	 * Change number of products per row
	 */
	add_filter( 'loop_shop_columns', 'woocom_loop_columns', 999 );
	if ( ! function_exists( 'woocom_loop_columns' ) ) {
		function woocom_loop_columns() {
			return 4; // Matching ghorerbazar grid
		}
	}

	
	/**
	 * Customize Breadcrumbs
	 */
	add_filter( 'woocommerce_breadcrumb_defaults', 'woocom_change_breadcrumb_delimiter' );
	function woocom_change_breadcrumb_delimiter( $defaults ) {
		$defaults['delimiter'] = ' <span class="text-gray-400 mx-2">></span> ';
		$defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb text-sm text-gray-500 mb-6 flex items-center" itemprop="breadcrumb">';
		$defaults['wrap_after'] = '</nav>';
		return $defaults;
	}

	/**
	 * AJAX handler to get current cart data
	 */
	add_action( 'wp_ajax_woocom_get_cart_data', 'woocom_get_cart_data' );
	add_action( 'wp_ajax_nopriv_woocom_get_cart_data', 'woocom_get_cart_data' );

	function woocom_get_cart_data() {
		if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
			wp_send_json_error();
		}

		$cart = WC()->cart;
		$items = array();

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$items[] = array(
				'key'   => $cart_item_key,
				'title' => $product->get_name(),
				'image' => woocom_get_cart_item_image_url( $cart_item, 'thumbnail' ),
				'price' => (float) $product->get_price(),
				'qty'   => (int) $cart_item['quantity'],
				'permalink' => get_permalink( $product->get_id() ),
			);
		}

		wp_send_json_success( array(
			'items' => $items,
			'total' => (float) $cart->get_subtotal(),
			'cross_sell_html' => woocom_get_cart_cross_sell_html()
		) );
	}

	/**
	 * AJAX handler to remove item from cart
	 */
	add_action( 'wp_ajax_woocom_remove_cart_item', 'woocom_remove_cart_item' );
	add_action( 'wp_ajax_nopriv_woocom_remove_cart_item', 'woocom_remove_cart_item' );

	function woocom_remove_cart_item() {
		$cart_item_key = isset( $_GET['cart_item_key'] ) ? sanitize_text_field( $_GET['cart_item_key'] ) : '';
		if ( ! empty( $cart_item_key ) && class_exists( 'WooCommerce' ) ) {
			if ( WC()->cart->remove_cart_item( $cart_item_key ) ) {
				wp_send_json_success();
			}
		}
		wp_send_json_error();
	}

	/**
	 * AJAX handler to update cart quantity
	 */
	add_action( 'wp_ajax_woocom_update_cart_qty', 'woocom_update_cart_qty' );
	add_action( 'wp_ajax_nopriv_woocom_update_cart_qty', 'woocom_update_cart_qty' );

	function woocom_update_cart_qty() {
		$cart_item_key = isset( $_GET['cart_item_key'] ) ? sanitize_text_field( $_GET['cart_item_key'] ) : '';
		$new_qty = isset( $_GET['qty'] ) ? (int) $_GET['qty'] : 0;

		if ( ! empty( $cart_item_key ) && class_exists( 'WooCommerce' ) ) {
			if ( WC()->cart->set_quantity( $cart_item_key, $new_qty ) ) {
				wp_send_json_success();
			}
		}
		wp_send_json_error();
	}

	/**
	 * Get Cross-sell HTML for Cart Drawer
	 */
	function woocom_get_cart_cross_sell_html() {
		if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) return '';

		$price_decimals           = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2;
		$price_decimal_separator  = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.';
		$price_thousand_separator = function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',';
		$cart_items  = WC()->cart->get_cart();
		$product_ids = array();
		$categories  = array();

		foreach ( $cart_items as $item ) {
			$product_ids[] = $item['product_id'];
			if ( ! empty( $item['variation_id'] ) ) {
				$product_ids[] = $item['variation_id'];
			}
			$item_cats = wp_get_post_terms( $item['product_id'], 'product_cat', array( 'fields' => 'all' ) );
			if ( ! is_wp_error( $item_cats ) ) {
				foreach ( $item_cats as $term ) {
					// Check if this term is a parent of any other term in the list
					$is_parent = false;
					foreach ( $item_cats as $other_term ) {
						if ( $other_term->parent === $term->term_id ) {
							$is_parent = true;
							break;
						}
					}
					// Ignore broad/uncategorized terms if other specific categories are available
					if ( $term->slug === 'uncategorized' && count( $item_cats ) > 1 ) {
						$is_parent = true;
					}
					if ( ! $is_parent ) {
						$categories[] = $term->slug;
					}
				}
			}
		}

		$categories  = array_unique( $categories );
		sort( $product_ids );

		// Cache cross-sell per unique cart composition with new version prefix — 2-hour TTL.
		$cache_key = 'woocom_cross_sell_v3_' . md5( implode( ',', $product_ids ) );
		$html      = get_transient( $cache_key );
		if ( false !== $html ) {
			return $html;
		}

		$args = array(
			'limit'      => 10,
			'status'     => 'publish',
			'visibility' => 'visible',
			'exclude'    => $product_ids,
			'orderby'    => 'popularity', // deterministic — can be cached
		);

		if ( ! empty( $categories ) ) {
			$args['category'] = $categories;
		}

		$products = wc_get_products( $args );
		$html     = '';

		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$price     = $product->get_price();
				$image     = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );
				$image_url = $image ? $image[0] : wc_placeholder_img_src();

				$html .= '<div class="bg-white border border-gray-200 rounded-lg p-2.5 flex gap-2.5 shadow-sm flex-shrink-0 w-[calc(50%-6px)] hover:shadow-md transition-shadow">
					<div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-gray-50 rounded overflow-hidden">
						<a href="' . esc_url( $product->get_permalink() ) . '">
							<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" class="max-w-full max-h-full object-contain mix-blend-multiply">
						</a>
					</div>
					<div class="flex flex-col justify-between flex-grow overflow-hidden">
						<a href="' . esc_url( $product->get_permalink() ) . '">
							<p class="text-[11px] font-medium text-gray-800 leading-tight line-clamp-2">' . esc_html( $product->get_name() ) . '</p>
						</a>
						<div class="flex items-center justify-between mt-1.5">
							<span class="text-secondary font-bold text-[12px]">৳' . number_format( $price, $price_decimals, $price_decimal_separator, $price_thousand_separator ) . '</span>
							<a href="' . esc_url( $product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $product->get_id() ) . '" class="add_to_cart_button ajax_add_to_cart bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center hover:bg-secondary/80 transition-colors" title="Add to Cart">
								<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
							</a>
						</div>
					</div>
				</div>';
			}
		} else {
			$html = '<p class="text-[11px] text-gray-500">No products found.</p>';
		}

		set_transient( $cache_key, $html, 2 * HOUR_IN_SECONDS );

		return $html;
	}


	/**
	 * Handle Buy Now Redirect
	 */
	add_filter( 'woocommerce_add_to_cart_redirect', 'woocom_buy_now_redirect' );
	function woocom_buy_now_redirect( $url ) {
		if ( isset( $_REQUEST['buy_now_redirect'] ) && $_REQUEST['buy_now_redirect'] == '1' ) {
			return wc_get_checkout_url();
		}
		return $url;
	}

	/**
	 * Remove default variation button (we have our own)
	 */
	remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );

	/**
	 * AJAX Product Quick View Callback
	 */
	add_action( 'wp_ajax_woocom_quick_view', 'woocom_quick_view_callback' );
	add_action( 'wp_ajax_nopriv_woocom_quick_view', 'woocom_quick_view_callback' );
	function woocom_quick_view_callback() {
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		if ( ! $product_id ) {
			wp_send_json_error( 'Invalid Product ID' );
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			wp_send_json_error( 'Product not found' );
		}

		// Get images
		$image_id = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src( 'large' );
		
		// Get gallery
		$gallery_ids = $product->get_gallery_image_ids();
		
		// Title, price, description
		$title = $product->get_name();
		$price_html = $product->get_price_html();
		
		$short_desc = $product->get_short_description();
		$description = $short_desc ? $short_desc : wp_trim_words( $product->get_description(), 20 );
		if ( empty( trim( strip_tags( $description ) ) ) ) {
			$description = 'প্রিমিয়াম কোয়ালিটির এই পণ্যটি সরাসরি আসল উৎস থেকে সংগৃহীত। এটি শতভাগ বিশুদ্ধ, স্বাস্থ্যসম্মত এবং দৈনন্দিন ব্যবহারের জন্য নিরাপদ।';
		}

		$is_variable = $product->is_type( 'variable' );
		
		ob_start();
		?>
		<div class="flex flex-col md:flex-row gap-6 p-4 max-w-4xl bg-white rounded-lg relative">
			<!-- Left: Images -->
			<div class="w-full md:w-1/2 flex flex-col gap-3">
				<div class="relative w-full pt-[100%] border border-gray-100 rounded-lg overflow-hidden bg-gray-50/30">
					<div class="absolute inset-0 flex items-center justify-center p-4">
						<img id="qv-main-img" src="<?php echo esc_url( $image_url ); ?>" class="max-w-full max-h-full object-contain mx-auto" alt="<?php echo esc_attr( $title ); ?>">
					</div>
				</div>
				<?php if ( ! empty( $gallery_ids ) ) : ?>
					<div class="flex gap-2 overflow-x-auto py-1">
						<div class="qv-thumb w-12 h-12 border border-secondary rounded p-1 cursor-pointer flex-shrink-0" data-src="<?php echo esc_url( $image_url ); ?>">
							<img src="<?php echo esc_url( $image_url ); ?>" class="w-full h-full object-contain">
						</div>
						<?php foreach ( $gallery_ids as $g_id ) : 
							$g_url = wp_get_attachment_image_url( $g_id, 'large' );
							if ( ! $g_url ) continue;
						?>
							<div class="qv-thumb w-12 h-12 border border-gray-200 hover:border-secondary rounded p-1 cursor-pointer flex-shrink-0" data-src="<?php echo esc_url( $g_url ); ?>">
								<img src="<?php echo esc_url( $g_url ); ?>" class="w-full h-full object-contain">
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Right: Content -->
			<div class="w-full md:w-1/2 flex flex-col justify-between">
				<div>
					<h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2 leading-tight"><?php echo esc_html( $title ); ?></h2>
					<div class="text-secondary font-bold text-lg md:text-xl mb-3">
						<?php echo $price_html; ?>
					</div>
					<div class="text-gray-600 text-sm mb-4 leading-relaxed line-clamp-4">
						<?php echo wp_kses_post( $description ); ?>
					</div>
				</div>

				<!-- Form / Buttons -->
				<div>
					<?php if ( $product->is_in_stock() ) : ?>
						<?php if ( $is_variable ) : ?>
							<!-- Redirect variable product to single page for selection -->
							<div class="mt-4">
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="w-full h-11 bg-primary text-white font-bold rounded-lg flex items-center justify-center gap-2 hover:bg-primary/90 transition-all text-sm uppercase">
									Select Options
								</a>
							</div>
						<?php else : ?>
							<!-- Simple Product Add to Cart and Buy Now -->
							<div class="flex items-center gap-3 mb-4">
								<span class="text-gray-600 text-sm font-semibold">Qty:</span>
								<div class="flex items-center justify-between border border-gray-300 rounded-md w-[100px] h-[36px] px-1 bg-white">
									<button type="button" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 font-bold qv-qty-minus rounded-full">-</button>
									<input type="number" value="1" min="1" class="w-8 text-center border-none focus:ring-0 font-semibold text-gray-800 text-[14px] qv-qty-input bg-transparent p-0" readonly>
									<button type="button" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 font-bold qv-qty-plus rounded-full">+</button>
								</div>
							</div>

							<div class="grid grid-cols-2 gap-2">
								<button type="button" class="qv-add-to-cart-btn w-full h-11 bg-secondary text-white font-bold rounded-md flex items-center justify-center gap-2 hover:bg-secondary/90 transition-all text-xs uppercase" data-product_id="<?php echo esc_attr( $product_id ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-4 h-4"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
									Add To Cart
								</button>
								<button type="button" class="qv-buy-now-btn w-full h-11 bg-primary text-white font-bold rounded-md flex items-center justify-center gap-2 hover:bg-primary/90 transition-all text-xs uppercase" data-product_id="<?php echo esc_attr( $product_id ); ?>">
									Buy Now
								</button>
							</div>
						<?php endif; ?>
					<?php else : ?>
						<div class="bg-gray-100 text-gray-500 text-center py-2.5 rounded-lg font-bold text-sm uppercase">
							Out of Stock
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		wp_send_json_success( array( 'html' => $html ) );
	}
}

/**
 * Remove default WooCommerce error notice display at the top of the login page
 * so our custom placed wc_print_notices() inside form cards renders correctly.
 */
remove_action( 'woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10 );

/**
 * Remove the dynamic registration fields from functions.php to prevent duplicates,
 * since they are now natively styled and rendered in the form-login.php template.
 */
remove_action( 'woocommerce_register_form', 'woocom_add_registration_fields' );
