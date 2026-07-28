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

		<div class="single_variation_wrap bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100">
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
			
			<div class="variation-actions-wrapper mt-4">
				<!-- Quantity Selector -->
				<div class="flex items-center gap-4 mb-4 mt-2">
					<span class="text-[#253D4E] font-medium text-[15px]">Quantity:</span>
					<div class="flex items-center justify-between border border-[#e5e7eb] rounded-[6px] bg-white w-[120px] h-[44px] px-1 shadow-sm">
						<button type="button" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors quantity-minus text-lg font-medium leading-none">-</button>
						<input type="number" name="quantity" value="1" min="1" class="w-12 text-center border-none focus:ring-0 font-semibold text-gray-800 text-[16px] qty-input bg-transparent p-0 m-0" readonly>
						<button type="button" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors quantity-plus text-lg font-medium leading-none">+</button>
					</div>
				</div>

				<!-- Action Buttons Grid -->
				<div class="product-action-grid grid grid-cols-2 gap-2 mb-2">
					<!-- Add to Cart -->
					<button type="button" id="single-add-to-cart-btn" class="product-action-btn product-action-btn--cart single_add_to_cart_button button alt w-full h-12 bg-secondary hover:bg-secondary/90 text-white font-bold px-3 rounded-[4px] flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md text-[12px] sm:text-[14px] leading-none whitespace-nowrap overflow-hidden">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
						<span><?php echo esc_html( $text_add_to_cart ); ?></span>
					</button>

					<!-- Buy Now -->
					<button type="button" id="buy-now-btn" class="product-action-btn product-action-btn--buy buy_now_button checkout-shake w-full h-12 bg-primary hover:bg-primary/90 text-white font-bold px-3 rounded-[4px] flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md text-[12px] sm:text-[14px] leading-none whitespace-nowrap overflow-hidden">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
						<span><?php echo esc_html( $text_buy_now ); ?></span>
					</button>

					<!-- Order On WhatsApp -->
					<?php
					$whatsapp_raw = preg_replace( '/[^0-9]/', '', get_option( 'woocom_whatsapp_number', '' ) );
					if ( $whatsapp_raw ) :
						$wa_message  = 'I would like to order: ' . get_the_title() . ' - ' . get_permalink();
						$wa_url      = 'https://wa.me/' . $whatsapp_raw . '?text=' . rawurlencode( $wa_message );
					?>
					<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="product-action-btn product-action-btn--compact product-action-btn--whatsapp h-11 bg-[#25D366] hover:bg-[#128C7E] text-white font-bold px-3 rounded-[4px] flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md text-[12px] sm:text-[14px] leading-none whitespace-nowrap overflow-hidden">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" class="flex-shrink-0 w-4 h-4 sm:w-[18px] sm:h-[18px]"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.393 0 12.03c0 2.123.554 4.197 1.606 6.044L0 24l6.102-1.6c1.791.977 3.806 1.492 5.864 1.492h.005c6.634 0 12.032-5.391 12.036-12.028.002-3.218-1.253-6.242-3.534-8.524z"/></svg>
						<span>Order On WhatsApp</span>
					</a>
					<?php endif; ?>

					<!-- Call For Order -->
					<?php $call_number = preg_replace( '/[^0-9+]/', '', get_option( 'contact_phone', '' ) ); ?>
					<a href="tel:<?php echo esc_attr( $call_number ); ?>" class="product-action-btn product-action-btn--compact product-action-btn--call h-11 bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold px-3 rounded-[4px] flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md text-[12px] sm:text-[14px] leading-none whitespace-nowrap overflow-hidden">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 w-4 h-4 sm:w-[18px] sm:h-[18px]"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
						<span>Call For Order</span>
					</a>
				</div>
			</div>

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

/* Ensure single_variation_wrap is visible and styled */
.single_variation_wrap {
    display: block !important;
    min-height: 20px;
}
.woocommerce-variation.single_variation {
    margin-bottom: 15px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.variations_form');
    if (!form) return;

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
            autoSelectSingleOptions();
        });
        
        // When WC updates a variation (match found)
        jQuery(form).on('found_variation', function(event, variation) {
            console.log('Variation found:', variation);
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
