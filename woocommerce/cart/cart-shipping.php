<?php
/**
 * Shipping Methods Display
 *
 * Custom override: outputs divs instead of <tr>/<td> to avoid orphaned table
 * row context causing horizontal overflow when rendered inside a <div>.
 *
 * @package WooCommerce\Templates
 * @version 8.8.0
 */

defined( 'ABSPATH' ) || exit;

$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );
?>
<div class="woocommerce-shipping-totals shipping">
	<?php if ( ! empty( $available_methods ) && is_array( $available_methods ) ) : ?>
		<ul id="shipping_method" class="woocommerce-shipping-methods">
			<?php foreach ( $available_methods as $method ) : ?>
				<li>
					<?php
					if ( 1 < count( $available_methods ) ) {
						printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					printf( '<label for="shipping_method_%1$s_%2$s">%3$s</label>', $index, esc_attr( sanitize_title( $method->id ) ), wc_cart_totals_shipping_method_label( $method ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					do_action( 'woocommerce_after_shipping_rate', $method, $index );
					?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	elseif ( ! $has_calculated_shipping || ! $formatted_destination ) :
		echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'woocommerce' ) ) );
	elseif ( ! is_cart() ) :
		echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
	else :
		echo wp_kses_post(
			apply_filters(
				'woocommerce_cart_no_shipping_available_html',
				sprintf( esc_html__( 'No shipping options were found for %s.', 'woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' ),
				$formatted_destination
			)
		);
	endif;
	?>
</div>
