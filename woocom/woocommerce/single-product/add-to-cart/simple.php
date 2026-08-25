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

	<!-- Premium Single Product Actions Styles -->
	<style>
	.premium-product-actions {
		margin-top: 20px;
		margin-bottom: 25px;
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

	<?php
	$active_price = (float) $product->get_price();
	$reg_price = (float) $product->get_regular_price();
	$save_diff = $reg_price > $active_price ? ($reg_price - $active_price) : 0;
	?>

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
					<?php if ($save_diff > 0) : ?>
						<span id="premium-dynamic-save" class="premium-save-badge" data-base-save="<?php echo esc_attr($save_diff); ?>">
							Save ৳<?php echo number_format($save_diff); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>

			<!-- Quantity Selector -->
			<div class="flex flex-col items-end">
				<span class="premium-card-label">Quantity</span>
				<div class="premium-qty-selector">
					<button type="button" class="premium-qty-btn quantity-minus">-</button>
					<input type="number" id="quantity_<?php echo esc_attr( $product->get_id() ); ?>" name="quantity" value="1" min="1" class="premium-qty-input qty-input" readonly>
					<button type="button" class="premium-qty-btn quantity-plus">+</button>
				</div>
			</div>
		</div>

		<!-- Main Action Buttons Row -->
		<div class="premium-btn-row-main">
			<!-- Add to Cart Form -->
			<form class="cart m-0" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
				<input type="hidden" name="quantity" value="1" class="hidden-qty-input" />
				<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
				<button type="submit" id="single-add-to-cart-btn" class="premium-btn-main add-to-cart">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
					<span><?php echo esc_html( $text_add_to_cart ); ?></span>
				</button>
				<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
			</form>

			<!-- Buy Now Form -->
			<form class="cart m-0" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" method="get">
				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
				<input type="hidden" name="quantity" value="1" class="hidden-qty-input" />
				<button type="submit" class="premium-btn-main buy-now checkout-shake">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
					<span><?php echo esc_html( $text_buy_now ); ?></span>
				</button>
			</form>
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

	<!-- Dynamic Subtotal & Quantity JS Sync -->
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const card = document.querySelector('.premium-qty-subtotal-card');
		if (!card) return;

		const minusBtn = card.querySelector('.quantity-minus');
		const plusBtn = card.querySelector('.quantity-plus');
		const qtyInput = card.querySelector('.qty-input');
		const subtotalDisplay = document.getElementById('premium-dynamic-subtotal');
		const saveDisplay = document.getElementById('premium-dynamic-save');
		const formQtyInputs = document.querySelectorAll('.hidden-qty-input');

		if (!qtyInput) return;

		const basePrice = parseFloat(subtotalDisplay ? subtotalDisplay.dataset.basePrice : 0);
		const baseSave = parseFloat(saveDisplay ? saveDisplay.dataset.baseSave : 0);

		const updateTotals = (qty) => {
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
	});
	</script>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
