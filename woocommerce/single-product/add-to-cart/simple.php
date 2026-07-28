<?php
/**
 * Simple product add to cart
 *
 * @package WooCommerce\Templates
 * @version 10.2.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
$text_add_to_cart = get_option( 'woocom_text_add_to_cart', 'Add To Cart' ) ?: 'Add To Cart';
$text_buy_now = get_option( 'woocom_text_buy_now', 'Buy Now' ) ?: 'Buy Now';

if ( ! $product->is_purchasable() ) {
	if ( $request_type && function_exists( 'woocom_render_stock_request_form' ) ) {
		echo woocom_render_stock_request_form( $product->get_id(), $request_type, 'single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $request_type && function_exists( 'woocom_render_stock_request_form' ) ) :
	echo woocom_render_stock_request_form( $product->get_id(), $request_type, 'single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
endif;

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<div class="flex flex-col gap-4 mb-4">
		<!-- Quantity Selector -->
		<div class="flex items-center gap-4 mt-2">
			<span class="text-[#253D4E] font-medium text-[15px]">Quantity:</span>
			<div class="flex items-center justify-between border border-[#e5e7eb] rounded-[6px] bg-white w-[120px] h-[44px] px-1 shadow-sm">
				<button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors quantity-minus text-lg font-medium leading-none">-</button>
				<input type="number" id="quantity_<?php echo esc_attr( $product->get_id() ); ?>" name="quantity" class="w-12 text-center border-none focus:ring-0 font-semibold text-gray-800 text-[16px] qty-input bg-transparent p-0 m-0" value="1" min="1" readonly>
				<button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors quantity-plus text-lg font-medium leading-none">+</button>
			</div>
		</div>

		<!-- Action Buttons Grid -->
		<div class="product-action-grid grid grid-cols-2 gap-2 mb-2">
			<!-- Add to Cart -->
			<form class="cart m-0" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
				<input type="hidden" name="quantity" value="1" class="hidden-qty-input" />
				<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
				<button type="submit" id="single-add-to-cart-btn" class="product-action-btn product-action-btn--cart w-full h-12 bg-secondary hover:bg-secondary/90 text-white font-bold px-3 rounded-[4px] flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md text-[12px] sm:text-[14px] leading-none whitespace-nowrap overflow-hidden">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
					<span><?php echo esc_html( $text_add_to_cart ); ?></span>
				</button>
				<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
			</form>

			<!-- Buy Now -->
			<form class="cart m-0" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" method="get">
				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
				<input type="hidden" name="quantity" value="1" class="hidden-qty-input" />
				<button type="submit" class="product-action-btn product-action-btn--buy buy_now_button checkout-shake w-full h-12 bg-primary hover:bg-primary/90 text-white font-bold px-3 rounded-[4px] flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md text-[12px] sm:text-[14px] leading-none whitespace-nowrap overflow-hidden">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
					<span><?php echo esc_html( $text_buy_now ); ?></span>
				</button>
			</form>

			<!-- Order On WhatsApp -->
			<?php
			$whatsapp_raw = preg_replace( '/[^0-9]/', '', get_option( 'woocom_whatsapp_number', '' ) );
			if ( $whatsapp_raw ) :
				$wa_message = 'I would like to order: ' . get_the_title() . ' - ' . get_permalink();
				$wa_url     = 'https://wa.me/' . $whatsapp_raw . '?text=' . rawurlencode( $wa_message );
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

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
