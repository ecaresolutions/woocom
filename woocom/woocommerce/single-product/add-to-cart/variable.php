<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$active_price = $product->get_price();
$reg_price = $product->get_regular_price();
if ( $product->is_type( 'variable' ) ) {
    $active_price = $product->get_variation_price( 'min', true );
    $reg_price = $product->get_variation_regular_price( 'min', true );
}
$save_diff = $reg_price > $active_price ? ($reg_price - $active_price) : 0;

$text_add_to_cart = get_option( 'woocom_text_add_to_cart', 'Add To Cart' ) ?: 'Add To Cart';
$text_buy_now = get_option( 'woocom_text_buy_now', 'Buy Now' ) ?: 'Buy Now';

wp_enqueue_script( 'wc-add-to-cart-variation' );
wp_add_inline_script(
	'wc-add-to-cart-variation',
	'if (window.wc_add_to_cart_variation_params) { window.wc_add_to_cart_variation_params.i18n_unavailable_text = ' . wp_json_encode( get_option( 'variation_unavailable_message', 'Sorry, this product is unavailable. Please choose a different combination.' ) ) . '; }',
	'after'
);

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); 

$product_request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
if ( $product_request_type && function_exists( 'woocom_render_stock_request_form' ) ) {
	echo woocom_render_stock_request_form( $product->get_id(), $product_request_type, 'single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	do_action( 'woocommerce_after_add_to_cart_form' );
	return;
}

if ( ! function_exists('custom_get_variation_for_option') ) {
	function custom_get_variation_for_option( $attribute_name, $option, $available_variations ) {
		foreach ( $available_variations as $variation ) {
			// Check for 'attribute_' + slug (standard WC)
			$key = 'attribute_' . sanitize_title( $attribute_name );
			
			if ( isset( $variation['attributes'][ $key ] ) ) {
				$val = $variation['attributes'][ $key ];
				// Match exact value or 'any' (empty string)
				if ( $val === $option || $val === '' ) {
					return $variation;
				}
			}
		}
		
		// Fallback: search for ANY variation if the exact match fails (to at least show a price)
		if ( ! empty( $available_variations ) ) {
			return reset( $available_variations );
		}
		
		return false;
	}
}

$currency = get_woocommerce_currency_symbol();
$decimals = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2;

?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<div class="custom-variations-wrapper mb-8 mt-4">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<div class="custom-attribute-row mb-6">
					<h3 class="font-bold text-gray-600 mb-3 text-[14px]">
						Select <?php echo wc_attribute_label( $attribute_name ); ?>
					</h3>
					<div class="grid grid-cols-3 lg:grid-cols-4 gap-2 md:gap-4">
						<?php 
						// Sort options according to the drag-and-drop order of variations in the backend
						$ordered_options = array();
						$attr_key = 'attribute_' . sanitize_title( $attribute_name );
						
						if ( !empty( $available_variations ) ) {
							foreach ( $available_variations as $var_data ) {
								if ( isset( $var_data['attributes'] ) ) {
									$val = '';
									if ( isset( $var_data['attributes'][$attr_key] ) ) {
										$val = $var_data['attributes'][$attr_key];
									} elseif ( isset( $var_data['attributes'][ 'attribute_' . $attribute_name ] ) ) {
										$val = $var_data['attributes'][ 'attribute_' . $attribute_name ];
									}
									
									if ( !empty( $val ) && !in_array( $val, $ordered_options ) ) {
										$ordered_options[] = $val;
									}
								}
							}
						}

						if ( !empty( $ordered_options ) ) {
							usort( $options, function( $a, $b ) use ( $ordered_options ) {
								$pos_a = -1;
								$pos_b = -1;
								
								foreach ( $ordered_options as $index => $ordered_val ) {
									if ( sanitize_title( $a ) === sanitize_title( $ordered_val ) || $a === $ordered_val ) {
										$pos_a = $index;
									}
									if ( sanitize_title( $b ) === sanitize_title( $ordered_val ) || $b === $ordered_val ) {
										$pos_b = $index;
									}
								}
								
								if ( $pos_a === -1 ) return 1;
								if ( $pos_b === -1 ) return -1;
								return $pos_a - $pos_b;
							});
						} elseif ( taxonomy_exists( $attribute_name ) ) {
							$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );
							$ordered_slugs = wp_list_pluck( $terms, 'slug' );
							
							usort( $options, function( $a, $b ) use ( $ordered_slugs ) {
								$pos_a = array_search( $a, $ordered_slugs );
								$pos_b = array_search( $b, $ordered_slugs );
								if ( $pos_a === false ) return 1;
								if ( $pos_b === false ) return -1;
								return $pos_a - $pos_b;
							});
						}

						foreach ( $options as $option ) : 
						?>
							<?php
							$variation = custom_get_variation_for_option( $attribute_name, $option, $available_variations );
							if ( $variation ) {
								$reg_price = isset($variation['display_regular_price']) ? $variation['display_regular_price'] : 0;
								$sale_price = isset($variation['display_price']) ? $variation['display_price'] : 0;
								$savings = $reg_price - $sale_price;
								
								// Extract numeric value from option (e.g., "50 ml" -> 50)
								preg_match('/([0-9\.]+)/', $option, $matches);
								$amount = isset($matches[1]) ? (float) $matches[1] : 0;
								
								$attribute_label = wc_attribute_label( $attribute_name );

								// Try option text first (e.g., "50 ml"), then fall back to attribute label (e.g., "KG").
								preg_match('/[a-zA-Z]+/', str_replace(' ', '', $option), $unit_matches);
								$unit = isset($unit_matches[0]) ? strtolower($unit_matches[0]) : '';

								if ( '' === $unit ) {
									preg_match('/[a-zA-Z]+/', str_replace(' ', '', $attribute_label), $attribute_unit_matches);
									$unit = isset($attribute_unit_matches[0]) ? strtolower($attribute_unit_matches[0]) : '';
								}
								
								$price_per_unit = '';
								if ( $amount > 0 && $sale_price > 0 ) {
									$ppu = $sale_price / $amount;
									$price_per_unit = $currency . number_format( $ppu, 2 );
									if ( '' !== $unit ) {
										$price_per_unit .= ' / ' . $unit;
									}
								}
								?>
								<div
									class="variant-box relative border border-[#cccccc] rounded-[10px] cursor-pointer transition-all bg-white flex flex-col items-stretch"
									data-attribute="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"
									data-value="<?php echo esc_attr( $option ); ?>"
									data-variation-id="<?php echo esc_attr( isset( $variation['variation_id'] ) ? $variation['variation_id'] : 0 ); ?>"
									data-display-price="<?php echo esc_attr( $sale_price ); ?>"
									data-regular-price="<?php echo esc_attr( $reg_price ); ?>"
									data-savings="<?php echo esc_attr( max( 0, $savings ) ); ?>"
								>
									<?php if ( $savings > 0 ) : ?>
										<span class="text-white text-[10px] font-normal px-1.5 py-0.5 rounded-[3px] absolute -top-2.5 left-3 z-10" style="background-color: var(--color-secondary);">Save <?php echo esc_html( round( $savings ) ); ?>/-</span>
									<?php endif; ?>
									
									<div class="variant-top bg-white text-gray-500 text-center py-2 rounded-t-[9px] text-[17px] font-normal border-b border-[#cccccc] transition-colors">
										<?php 
											// Show term name if taxonomy, otherwise show option value
											if ( taxonomy_exists( $attribute_name ) ) {
												$term = get_term_by( 'slug', $option, $attribute_name );
												echo esc_html( $term ? $term->name : $option );
											} else {
												echo esc_html( $option );
											}
										?>
									</div>
									
									<div class="variant-bottom p-2 text-center flex-1 flex flex-col justify-center bg-white rounded-b-[9px]">
										<div class="price-row flex items-center justify-center gap-1.5 text-base leading-none mb-1">
											<?php if ( $savings > 0 ) : ?>
												<span class="text-[#888888] line-through text-[15px]"><?php echo $currency . esc_html( round( $reg_price ) ); ?></span>
											<?php endif; ?>
											<span class="text-black text-[17px] font-normal"><?php echo $currency . esc_html( round( $sale_price ) ); ?></span>
										</div>
										<?php if ( $price_per_unit ) : ?>
											<span class="font-bold text-[13px] mt-0.5 price-per-unit" style="color: var(--color-primary);"><?php echo esc_html( $price_per_unit ); ?></span>
										<?php endif; ?>
									</div>
								</div>
							<?php } ?>
						<?php endforeach; ?>
					</div>

					<!-- Hidden native select for WooCommerce compatibility -->
					<div class="hidden-select-wrapper" style="display:none;">
						<?php
							wc_dropdown_variation_attribute_options(
								array(
									'options'   => $options,
									'attribute' => $attribute_name,
									'product'   => $product,
									'class'     => 'hidden-variation-select',
								)
							);
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 */
				do_action( 'woocommerce_single_variation' );
			?>
			
			<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
			<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
			<input type="hidden" name="variation_id" class="variation_id" value="0" />

			<?php
				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>

		<!-- Premium Single Product Actions Styles -->
		<style>
		.premium-product-actions {
			margin-top: 15px;
			margin-bottom: 20px;
			font-family: 'Outfit', 'Inter', sans-serif;
		}
		.premium-qty-subtotal-card {
			display: flex;
			justify-content: space-between;
			align-items: center;
			background-color: #FAFAFA;
			border: 1px solid #EBEBEB;
			border-radius: 16px;
			padding: 14px 20px;
			margin-bottom: 16px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.01);
		}
		.premium-card-label {
			font-size: 11px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			color: #8C94A0;
			margin-bottom: 4px;
			display: block;
		}
		.premium-subtotal-price {
			font-size: 20px;
			font-weight: 900;
			color: var(--color-primary, #1E5D02);
			line-height: 1.1;
		}
		.premium-save-badge {
			font-size: 11px;
			font-weight: 700;
			color: var(--color-primary, #2563EB);
			background-color: color-mix(in srgb, var(--color-primary, #2563EB) 8%, #ffffff);
			border: 1px solid color-mix(in srgb, var(--color-primary, #2563EB) 15%, #ffffff);
			padding: 2px 8px;
			border-radius: 9999px;
			display: inline-block;
			vertical-align: middle;
			margin-left: 6px;
		}
		.premium-qty-selector {
			display: flex;
			align-items: center;
			justify-content: space-between;
			background-color: #ffffff;
			border: 1px solid #E2E8F0;
			border-radius: 9999px;
			width: 110px;
			height: 40px;
			padding: 2px;
			box-shadow: 0 1px 2px rgba(0,0,0,0.02);
		}
		.premium-qty-btn {
			width: 32px;
			height: 32px;
			border-radius: 50%;
			background-color: #F8FAFC;
			border: none;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 15px;
			font-weight: 700;
			color: #64748B;
			cursor: pointer;
			transition: all 0.2s ease;
			user-select: none;
		}
		.premium-qty-btn:hover {
			background-color: #E2E8F0;
			color: #0F172A;
		}
		.premium-qty-input {
			width: 28px;
			text-align: center;
			border: none !important;
			background: transparent !important;
			padding: 0 !important;
			margin: 0 !important;
			font-size: 14px;
			font-weight: 700;
			color: var(--color-primary, #1E5D02);
			pointer-events: none;
		}
		.premium-btn-row-main {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 12px;
			margin-bottom: 12px;
		}
		.premium-btn-main {
			height: 50px;
			border-radius: 9999px !important;
			border: none;
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
			gap: 8px !important;
			position: relative !important;
			padding: 0 15px !important;
			cursor: pointer;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
			text-decoration: none !important;
			width: 100%;
		}
		.premium-btn-main svg {
			position: static !important;
			transform: none !important;
			margin: 0 !important;
			display: inline-block !important;
			vertical-align: middle !important;
			width: 18px !important;
			height: 18px !important;
			flex-shrink: 0 !important;
		}
		.premium-btn-main span {
			position: static !important;
			margin: 0 !important;
			padding: 0 !important;
			display: inline-block !important;
			vertical-align: middle !important;
			color: inherit !important;
			font-size: 14px !important;
			font-weight: 700 !important;
		}
		.premium-btn-main.add-to-cart {
			background: #FBBF24 !important;
			color: #1E293B !important;
		}
		.premium-btn-main.add-to-cart:hover {
			background: #F59E0B !important;
			transform: translateY(-1.5px);
			box-shadow: 0 6px 12px rgba(245, 158, 11, 0.15);
		}
		.premium-btn-main.add-to-cart svg {
			stroke: #1E293B !important;
		}
		.premium-btn-main.buy-now {
			background: #10B981 !important;
			color: #ffffff !important;
		}
		.premium-btn-main.buy-now:hover {
			background: #059669 !important;
			transform: translateY(-1.5px);
			box-shadow: 0 6px 12px rgba(16, 185, 129, 0.15);
		}
		.premium-btn-row-sec {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 12px;
		}
		.premium-btn-sec {
			height: 52px;
			border-radius: 9999px;
			background-color: #ffffff;
			border: 1px solid #E2E8F0;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			cursor: pointer;
			transition: all 0.3s ease;
			text-decoration: none !important;
			padding: 0 16px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.01);
		}
		.premium-btn-sec:hover {
			border-color: #CBD5E1;
			background-color: #F8FAFC;
			transform: translateY(-1px);
			box-shadow: 0 4px 8px rgba(0,0,0,0.03);
		}
		.premium-btn-sec-icon {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
		}
		.premium-btn-sec-content {
			display: flex;
			flex-direction: column;
			text-align: left;
			line-height: 1.25;
		}
		.premium-btn-sec-title {
			font-size: 12px;
			font-weight: 700;
			color: #1E293B;
		}
		.premium-btn-sec-subtitle {
			font-size: 9px;
			color: #64748B;
			font-weight: 500;
		}
		@media (max-w: 640px) {
			.premium-qty-subtotal-card {
				padding: 10px 14px;
			}
			.premium-subtotal-price {
				font-size: 18px;
			}
			.premium-btn-main {
				font-size: 13px;
				height: 46px;
			}
			.premium-btn-sec {
				height: 48px;
				padding: 0 10px;
				gap: 6px;
			}
			.premium-btn-sec-title {
				font-size: 11px;
			}
			.premium-btn-sec-subtitle {
				font-size: 8px;
			}
		}
		</style>

		<div class="premium-product-actions">
			<!-- Quantity & Subtotal Card -->
			<div class="premium-qty-subtotal-card">
				<!-- Subtotal Price -->
				<div class="flex flex-col">
					<span class="premium-card-label">Subtotal Price</span>
					<div class="flex items-center">
						<span id="premium-dynamic-subtotal" class="premium-subtotal-price" data-base-price="<?php echo esc_attr($active_price); ?>">
							৳<?php echo number_format($active_price); ?>
						</span>
						<span id="premium-dynamic-save" class="premium-save-badge" data-base-save="<?php echo esc_attr($save_diff); ?>" style="<?php echo $save_diff > 0 ? '' : 'display: none;'; ?>">
							Save ৳<?php echo number_format($save_diff); ?>
						</span>
					</div>
				</div>

				<!-- Quantity Selector -->
				<div class="flex flex-col items-end">
					<span class="premium-card-label">Quantity</span>
					<div class="premium-qty-selector">
						<button type="button" class="premium-qty-btn quantity-minus">-</button>
						<input type="number" name="quantity" value="1" min="1" class="premium-qty-input qty-input" readonly>
						<button type="button" class="premium-qty-btn quantity-plus">+</button>
					</div>
				</div>
			</div>

			<!-- Main Action Buttons Row -->
			<div class="premium-btn-row-main">
				<!-- Add to Cart -->
				<button type="button" id="single-add-to-cart-btn" class="premium-btn-main add-to-cart single_add_to_cart_button button alt">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
					<span><?php echo esc_html( $text_add_to_cart ); ?></span>
				</button>

				<!-- Buy Now -->
				<button type="button" id="buy-now-btn" class="premium-btn-main buy-now buy_now_button checkout-shake">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
					<span><?php echo esc_html( $text_buy_now ); ?></span>
				</button>
			</div>

			<!-- Secondary Action Buttons Row -->
			<div class="premium-btn-row-sec">
				<!-- Call For Order -->
				<?php 
				$call_number_raw = get_option( 'contact_phone', '' );
				$call_href = 'tel:' . preg_replace( '/[^0-9+]/', '', $call_number_raw ); 
				?>
				<a href="<?php echo esc_url( $call_href ); ?>" class="premium-btn-sec">
					<div class="premium-btn-sec-icon" style="color: #10B981;">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
					</div>
					<div class="premium-btn-sec-content">
						<span class="premium-btn-sec-title"><?php echo esc_html( $call_number_raw ?: '+8809613821489' ); ?></span>
						<span class="premium-btn-sec-subtitle">Call for Order</span>
					</div>
				</a>

				<!-- Order Via WhatsApp -->
				<?php
				$whatsapp_raw = preg_replace( '/[^0-9]/', '', get_option( 'woocom_whatsapp_number', '' ) );
				if ( $whatsapp_raw ) :
					$wa_message = 'I would like to order: ' . get_the_title() . ' - ' . get_permalink();
					$wa_url     = 'https://wa.me/' . $whatsapp_raw . '?text=' . rawurlencode( $wa_message );
				?>
				<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="premium-btn-sec">
					<div class="premium-btn-sec-icon" style="color: #25D366;">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.393 0 12.03c0 2.123.554 4.197 1.606 6.044L0 24l6.102-1.6c1.791.977 3.806 1.492 5.864 1.492h.005c6.634 0 12.032-5.391 12.036-12.028.002-3.218-1.253-6.242-3.534-8.524z"/></svg>
					</div>
					<div class="premium-btn-sec-content">
						<span class="premium-btn-sec-title">Order Via WhatsApp</span>
						<span class="premium-btn-sec-subtitle">Chat with us</span>
					</div>
				</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
?>

<style>
/* Dynamic Colors for Variant Switcher based on Theme Settings */
.variant-box {
    border: 1px solid #cccccc;
}
.variant-box:hover {
    border-color: var(--color-primary) !important;
}
.variant-box.selected-variant {
    border-color: var(--color-primary) !important;
}
.variant-box.selected-variant .variant-top {
    background-color: color-mix(in srgb, var(--color-primary) 15%, white) !important;
    color: var(--color-primary) !important;
    border-color: color-mix(in srgb, var(--color-primary) 15%, white) !important;
}

/* Style for Variation Price */
.woocommerce-variation-price {
    margin-bottom: 10px;
}
.woocommerce-variation-price .price {
    font-size: 24px;
    font-weight: bold;
    color: var(--color-secondary);
}
.woocommerce-variation-availability {
    font-size: 14px;
    margin-bottom: 10px;
}

/* Ensure single_variation_wrap is unstyled and clean */
.single_variation_wrap {
    display: block;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
    min-height: 0 !important;
}
.woocommerce-variation.single_variation {
    margin-bottom: 15px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.variations_form');
    if (!form) return;

    // Premium Subtotal elements
    const qtyInput = form.querySelector('.qty-input');
    const subtotalDisplay = document.getElementById('premium-dynamic-subtotal');
    const saveDisplay = document.getElementById('premium-dynamic-save');
    const formQtyInputs = document.querySelectorAll('.hidden-qty-input');

    const updateTotals = (qty) => {
        const basePrice = parseFloat(subtotalDisplay ? subtotalDisplay.dataset.basePrice : 0);
        const baseSave = parseFloat(saveDisplay ? saveDisplay.dataset.baseSave : 0);

        // Update subtotal
        if (subtotalDisplay) {
            const newSubtotal = basePrice * qty;
            subtotalDisplay.innerText = '৳' + newSubtotal.toLocaleString('en-US');
        }
        // Update save badge
        if (saveDisplay) {
            const newSave = baseSave * qty;
            saveDisplay.innerText = 'Save ৳' + newSave.toLocaleString('en-US');
        }
        // Update hidden inputs for WooCommerce forms
        formQtyInputs.forEach(input => {
            input.value = qty;
        });
    };

    if (qtyInput) {
        qtyInput.addEventListener('change', function() {
            const val = parseInt(this.value) || 1;
            updateTotals(val);
        });
    }

    const variantBoxes = document.querySelectorAll('.variant-box');
    const variationIdInput = form.querySelector('input[name="variation_id"]');
    const priceDisplay = document.getElementById('product-price');
    const originalPriceHtml = priceDisplay ? priceDisplay.innerHTML : '';
    const imageSaleBadge = document.getElementById('product-image-sale-badge');
    const imageSaleBadgeText = imageSaleBadge ? imageSaleBadge.querySelector('span') : null;
    const originalImageSaleBadgeLabel = imageSaleBadge ? imageSaleBadge.dataset.originalLabel : '';
    const buyNowButton = document.getElementById('buy-now-btn');
    const variationNotice = form.querySelector('.woocommerce-variation.single_variation');
    const unavailableMessage = (window.woocom_ajax && woocom_ajax.variation_unavailable_message)
        ? woocom_ajax.variation_unavailable_message
        : 'Sorry, this product is unavailable. Please choose a different combination.';
    let variations = [];
    try {
        variations = JSON.parse(form.getAttribute('data-product_variations') || '[]');
    } catch (error) {
        variations = [];
    }
    const currencySymbol = <?php echo wp_json_encode( $currency ); ?>;
    const priceDecimals = <?php echo wp_json_encode( $decimals ); ?>;

    const formatVariationPrice = (price) => {
        const amount = Number(price);
        if (Number.isNaN(amount)) return '';

        return amount.toFixed(priceDecimals) + currencySymbol;
    };

    const updateImageSaleBadge = (variation) => {
        if (!imageSaleBadge || !imageSaleBadgeText) return;

        if (variation) {
            const regularPrice = Number(variation.display_regular_price);
            const salePrice = Number(variation.display_price);
            const savings = regularPrice - salePrice;

            if (regularPrice > 0 && salePrice > 0 && savings > 0) {
                imageSaleBadgeText.textContent = 'Save ৳' + Math.round(savings);
                imageSaleBadge.classList.remove('hidden');
                return;
            }
        }

        if (originalImageSaleBadgeLabel) {
            imageSaleBadgeText.textContent = originalImageSaleBadgeLabel;
            imageSaleBadge.classList.remove('hidden');
        } else {
            imageSaleBadge.classList.add('hidden');
        }
    };

    const updateVariationImage = (variation) => {
        const slider = document.getElementById('main-image-slider');
        if (!slider) return;

        const slides = Array.from(slider.children);
        if (slides.length === 0) return;

        if (variation && variation.image && variation.image.src) {
            // Find a slide that matches the variation's image URL, ID, or filename prefix
            let slideIndex = -1;
            for (let i = 0; i < slides.length; i++) {
                const img = slides[i].querySelector('img');
                if (img) {
                    const imgUrl = img.src;
                    const varUrl = variation.image.src;
                    
                    // Extract filename prefix to handle size/suffix differences
                    const imgFilename = imgUrl.substring(imgUrl.lastIndexOf('/') + 1).split('?')[0].split('-')[0].split('_')[0].split('_')[0];
                    const varFilename = varUrl.substring(varUrl.lastIndexOf('/') + 1).split('?')[0].split('-')[0].split('_')[0].split('_')[0];

                    if (imgUrl === varUrl || imgUrl.includes(varUrl) || varUrl.includes(imgUrl) || 
                        (variation.image_id && slides[i].dataset.imageId == variation.image_id) ||
                        (imgFilename && varFilename && imgFilename === varFilename)) {
                        slideIndex = i;
                        break;
                    }
                }
            }

            if (slideIndex !== -1) {
                // Restore first slide image to original if it was temporarily changed
                const firstImg = slides[0].querySelector('img');
                if (firstImg && window.originalMainImageSrc && firstImg.src !== window.originalMainImageSrc) {
                    firstImg.src = window.originalMainImageSrc;
                    if (window.originalMainImageSrcset) {
                        firstImg.srcset = window.originalMainImageSrcset;
                    } else {
                        firstImg.removeAttribute('srcset');
                    }
                }

                // Slide to the matching gallery image
                const thumbs = document.querySelectorAll('.thumbnail-item');
                if (thumbs[slideIndex]) {
                    changeMainImage(thumbs[slideIndex], slideIndex);
                }
            } else {
                // If it is a custom variation image NOT in the gallery (e.g. 12 KG image), 
                // temporarily swap the first slide's image ONLY (do not touch thumbnails)
                const firstImg = slides[0].querySelector('img');
                if (firstImg) {
                    firstImg.src = variation.image.src;
                    if (variation.image.srcset) {
                        firstImg.srcset = variation.image.srcset;
                    } else {
                        firstImg.removeAttribute('srcset');
                    }

                    // Slide to index 0
                    const thumbs = document.querySelectorAll('.thumbnail-item');
                    if (thumbs[0]) {
                        changeMainImage(thumbs[0], 0);
                    }
                }
            }
        } else {
            // If variation is cleared, reset the first slide back to the original product image
            const firstImg = slides[0].querySelector('img');
            if (firstImg && window.originalMainImageSrc) {
                firstImg.src = window.originalMainImageSrc;
                if (window.originalMainImageSrcset) {
                    firstImg.srcset = window.originalMainImageSrcset;
                } else {
                    firstImg.removeAttribute('srcset');
                }
            }

            // Slide to index 0
            const thumbs = document.querySelectorAll('.thumbnail-item');
            if (thumbs[0]) {
                changeMainImage(thumbs[0], 0);
            }
        }
    };

    const variationFromBox = (box) => {
        if (!box) return false;

        const variationId = Number(box.dataset.variationId || 0);
        if (!variationId) return false;

        return {
            variation_id: variationId,
            display_price: Number(box.dataset.displayPrice || 0),
            display_regular_price: Number(box.dataset.regularPrice || 0)
        };
    };

    const enableSelectedVariationButton = () => {
        const addButton = form.querySelector('.single_add_to_cart_button');
        form.classList.remove('has-unavailable-variation');

        if (addButton) {
            addButton.disabled = false;
            addButton.classList.remove('disabled', 'wc-variation-selection-needed', 'wc-variation-is-unavailable');
        }

        if (buyNowButton) {
            buyNowButton.disabled = false;
            buyNowButton.classList.remove('disabled', 'wc-variation-is-unavailable');
            buyNowButton.removeAttribute('aria-disabled');
        }
    };

    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    const showVariationNotice = (message) => {
        if (!variationNotice) return;

        variationNotice.innerHTML = '<p role="alert">' + escapeHtml(message) + '</p>';
    };

    const clearVariationNotice = () => {
        if (variationNotice) {
            variationNotice.innerHTML = '';
        }
    };

    const disableSelectedVariationButtons = (message = unavailableMessage) => {
        const addButton = form.querySelector('.single_add_to_cart_button');
        form.classList.add('has-unavailable-variation');

        if (variationIdInput) {
            variationIdInput.value = '0';
        }

        if (addButton) {
            addButton.disabled = true;
            addButton.classList.add('disabled', 'wc-variation-is-unavailable');
        }

        if (buyNowButton) {
            buyNowButton.disabled = true;
            buyNowButton.classList.add('disabled', 'wc-variation-is-unavailable');
            buyNowButton.setAttribute('aria-disabled', 'true');
        }

        showVariationNotice(message);
    };

    const isVariationUnavailable = (variation) => {
        if (!variation) return true;

        return variation.variation_is_active === false
            || variation.is_purchasable === false
            || variation.is_in_stock === false
            || variation.is_available === false;
    };

    const getSelectedAttributes = () => {
        const selected = {};
        form.querySelectorAll('select[name^="attribute_"]').forEach(select => {
            selected[select.name] = select.value;
        });

        return selected;
    };

    const findMatchingVariation = () => {
        const selected = getSelectedAttributes();
        const selectedKeys = Object.keys(selected);

        return variations.find(variation => {
            return selectedKeys.every(key => {
                const expected = variation.attributes ? variation.attributes[key] : '';
                return selected[key] && (expected === selected[key] || expected === '');
            });
        });
    };

    const allAttributesSelected = () => {
        const selects = Array.from(form.querySelectorAll('select[name^="attribute_"]'));
        return selects.length > 0 && selects.every(select => select.value);
    };

    const updateSelectedVariation = (selectedBox = null) => {
        const variation = findMatchingVariation() || variationFromBox(selectedBox);

        if (variation && variation.variation_id) {
            if (priceDisplay) {
                priceDisplay.innerHTML = formatVariationPrice(variation.display_price);
            }
            updateImageSaleBadge(variation);
            updateVariationImage(variation);

            window.currentSelectedVariation = variation;
            if (window.updateFbtMainProduct) {
                window.updateFbtMainProduct(variation);
            }

            if (isVariationUnavailable(variation)) {
                disableSelectedVariationButtons();
                if (typeof jQuery !== 'undefined') {
                    jQuery(form).trigger('hide_variation');
                }
                return false;
            }

            if (variationIdInput) {
                variationIdInput.value = variation.variation_id;
            }
            clearVariationNotice();
            enableSelectedVariationButton();
            if (typeof jQuery !== 'undefined') {
                jQuery(form).trigger('found_variation', [variation]);
            }
            return variation;
        }

        window.currentSelectedVariation = null;
        if (window.updateFbtMainProduct) {
            window.updateFbtMainProduct(null);
        }

        if (variationIdInput) {
            variationIdInput.value = '0';
        }
        if (priceDisplay) {
            priceDisplay.innerHTML = originalPriceHtml;
        }
        updateImageSaleBadge(false);
        updateVariationImage(false);

        if (allAttributesSelected()) {
            disableSelectedVariationButtons();
        } else {
            clearVariationNotice();
            form.classList.remove('has-unavailable-variation');
        }

        return false;
    };
    
    // Function to update the hidden selects based on box selection
    const updateSelect = (attr, val) => {
        const select = form.querySelector('select[name="attribute_' + attr + '"]');
        if (select) {
            select.value = val;
            if (typeof jQuery !== 'undefined') {
                jQuery(select).trigger('change');
            } else {
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    };

    variantBoxes.forEach(box => {
        box.addEventListener('click', function() {
            if(this.classList.contains('disabled')) return;

            const attr = this.dataset.attribute;
            const val = this.dataset.value;
            
            const row = this.closest('.custom-attribute-row');
            
            // UI Update
            row.querySelectorAll('.variant-box').forEach(b => b.classList.remove('selected-variant'));
            this.classList.add('selected-variant');
            
            // Sync with WC Select
            updateSelect(attr, val);
            updateSelectedVariation(this);
        });
    });

    // Handle WooCommerce reset/sync
    if (typeof jQuery !== 'undefined') {
        jQuery(form).on('reset_data', function() {
            variantBoxes.forEach(b => b.classList.remove('selected-variant'));
            if (priceDisplay) {
                priceDisplay.innerHTML = originalPriceHtml;
            }
            updateImageSaleBadge(false);
            if (variationIdInput) {
                variationIdInput.value = '0';
            }
            clearVariationNotice();
            form.classList.remove('has-unavailable-variation');

            // Reset subtotal to original minimum variation price
            const originalMinPrice = <?php echo (float) $active_price; ?>;
            const originalMinSave = <?php echo (float) $save_diff; ?>;
            if (subtotalDisplay) {
                subtotalDisplay.dataset.basePrice = originalMinPrice;
            }
            if (saveDisplay) {
                saveDisplay.dataset.baseSave = originalMinSave;
                if (originalMinSave > 0) {
                    saveDisplay.style.display = 'inline-block';
                } else {
                    saveDisplay.style.display = 'none';
                }
            }
            const currentQty = parseInt(qtyInput.value) || 1;
            updateTotals(currentQty);

            autoSelectSingleOptions();
        });
        
        // When WC updates a variation (match found)
        jQuery(form).on('found_variation', function(event, variation) {
            console.log('Variation found:', variation);
            if (variation) {
                const basePrice = parseFloat(variation.display_price);
                const regularPrice = parseFloat(variation.display_regular_price || variation.display_price);
                const saveAmt = regularPrice > basePrice ? (regularPrice - basePrice) : 0;
                
                if (subtotalDisplay) {
                    subtotalDisplay.dataset.basePrice = basePrice;
                }
                if (saveDisplay) {
                    saveDisplay.dataset.baseSave = saveAmt;
                    if (saveAmt > 0) {
                        saveDisplay.style.display = 'inline-block';
                    } else {
                        saveDisplay.style.display = 'none';
                    }
                }
                
                const currentQty = parseInt(qtyInput.value) || 1;
                updateTotals(currentQty);
            }
        });

        function autoSelectSingleOptions() {
            const rows = form.querySelectorAll('.custom-attribute-row');
            rows.forEach(row => {
                const boxes = row.querySelectorAll('.variant-box');
                const select = row.querySelector('select');
                
                if (boxes.length === 1 && !boxes[0].classList.contains('selected-variant')) {
                    boxes[0].click();
                } else if (select && select.value) {
                    const box = row.querySelector(`.variant-box[data-value="${select.value}"]`);
                    if (box && !box.classList.contains('selected-variant')) {
                        box.classList.add('selected-variant');
                    }
                }
            });
            updateSelectedVariation();
        }

        // Initial check
        setTimeout(autoSelectSingleOptions, 300);
    }
});
</script>
