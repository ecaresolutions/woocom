<?php
/**
 * Checkout Form
 *
 * @package WooCommerce/Templates
 * @version 10.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<div class="checkout-page-wrapper bg-white py-4 overflow-x-hidden">
    <div class="container checkout-content-container mx-auto px-4">
        
        <!-- Checkout Header -->
        <div class="text-center mb-6">
            <h1 class="text-[24px] md:text-[30px] font-bold text-primary mb-1">Checkout</h1>
            <nav class="flex justify-center items-center text-[13px] md:text-[14px] text-gray-400 font-medium">
                <a href="<?php echo esc_url(home_url()); ?>" class="hover:text-secondary">Home</a>
                <span class="mx-2 text-gray-300">›</span>
                <span class="text-secondary">Checkout</span>
            </nav>
        </div>

        <!-- Login/Register Notice -->
        <?php if ( ! is_user_logged_in() ) : ?>
        <div class="bg-[#f8fafc] rounded-xl p-4 mb-8 border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="text-[15px] text-gray-600">
                Have any account? please login or register
            </div>
            <div class="flex items-center justify-center gap-3 w-full md:w-auto">
                <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="px-6 py-1.5 border border-secondary text-secondary font-medium rounded-lg text-sm hover:bg-secondary/5 transition-colors bg-white">Login</a>
                <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="px-6 py-1.5 bg-secondary text-white font-medium rounded-lg text-sm hover:bg-secondary/90 transition-colors shadow-sm">Register</a>
            </div>
        </div>
        <?php endif; ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

            <div class="flex flex-col lg:flex-row gap-8 w-full min-w-0">
                
                <!-- Left Column: Order Review & Addresses -->
                <div class="w-full min-w-0 lg:w-[62%] space-y-6">
                    
                    <!-- Order Review (Top List) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-50">
                            <h3 class="text-[16px] font-bold text-[#253D4E] flex items-center gap-2">
                                <span class="checkout-title-marker"></span>
                                Order review
                            </h3>
                        </div>
                        <div class="p-4">
                            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
                            <div id="order_review" class="woocommerce-checkout-review-order">
                                <?php woocommerce_order_review(); ?>
                            </div>
                            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                        </div>
                    </div>

                    <!-- Cross-sell Section -->
                    <?php 
                    if ( function_exists( 'woocom_get_cart_cross_sell_html' ) ) {
                        $cross_sell_content = woocom_get_cart_cross_sell_html();
                        if ( $cross_sell_content && strpos( $cross_sell_content, 'No products found' ) === false ) :
                        ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-4 border-b border-gray-50">
                                <h3 class="text-[16px] font-bold text-[#253D4E] flex items-center gap-2">
                                    <span class="checkout-title-marker"></span>
                                    Recommended for you
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="flex flex-wrap gap-3">
                                    <?php echo $cross_sell_content; ?>
                                </div>
                            </div>
                        </div>
                        <?php 
                        endif;
                    } 
                    ?>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-50">
                            <h3 class="text-[16px] font-bold text-[#253D4E] flex items-center gap-2">
                                <span class="checkout-title-marker"></span>
                                Billing Details
                            </h3>
                        </div>
                        <div class="p-4">
                            <?php if ( $checkout->get_checkout_fields() ) : ?>
                                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                                <div class="shipping-fields-custom">
                                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                                </div>
                                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Billing Address Toggle -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 flex items-center justify-between cursor-pointer" id="billing-address-toggle">
                            <h3 class="text-[16px] font-bold text-[#253D4E] flex items-center gap-2">
                                <span class="checkout-title-marker"></span>
                                Shipping Address (If different)
                            </h3>
                            <div class="toggle-circle checkout-billing-toggle">
                                <div></div>
                            </div>
                        </div>
                        <div id="billing-address-fields" class="hidden p-4 pt-0">
                            <div class="woocommerce-shipping-fields__field-wrapper">
                                <?php
                                $shipping_fields = $checkout->get_checkout_fields( 'shipping' );
                                
                                // Explicitly output fields in order
                                $fields_to_show = array(
                                    'shipping_first_name',
                                    'shipping_phone',
                                    'shipping_country',
                                    'shipping_state',
                                    'shipping_city',
                                    'shipping_address_1'
                                );

                                foreach ( $fields_to_show as $key ) {
                                    if ( isset( $shipping_fields[$key] ) ) {
                                        woocommerce_form_field( $key, $shipping_fields[$key], $checkout->get_value( $key ) );
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Special Notes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-50">
                            <h3 class="text-[14px] font-bold text-[#253D4E] flex items-center gap-2">
                                <span class="checkout-title-marker"></span>
                                Special notes <span class="text-gray-400 font-medium ml-1">(Optional)</span>
                            </h3>
                        </div>
                        <div class="p-5">
                            <textarea name="order_comments" class="w-full h-24 border border-gray-200 rounded-lg p-3 text-sm focus:border-secondary focus:ring-0 resize-none" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Payment & Totals -->
                <div class="w-full min-w-0 lg:w-[38%] space-y-6">
                    
                    <!-- Payment Method -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-50">
                            <h3 class="text-[16px] font-bold text-[#253D4E] flex items-center gap-2">
                                <span class="checkout-title-marker"></span>
                                Payment method
                            </h3>
                        </div>
                        <div class="p-4">
                            <div id="payment-custom-area">
                                <?php woocommerce_checkout_payment(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary / Totals -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                        <div id="coupon-toggle" class="p-4 flex items-center justify-between cursor-pointer border-b border-gray-50 bg-gray-50/30">
                            <h3 class="text-[15px] font-medium text-[#253D4E]">Have any coupon or gift voucher?</h3>
                            <svg id="coupon-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary transition-transform duration-300"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                        <div id="coupon-content" class="p-5" style="display: none;">
                            <div class="relative flex items-center border border-gray-200 rounded-xl p-1.5 focus-within:border-secondary transition-colors min-w-0">
                                <input type="text" name="coupon_code" class="flex-1 min-w-0 px-3 py-2 text-sm outline-none border-none focus:ring-0 text-gray-500 bg-transparent" id="custom_coupon_code" value="" placeholder="Enter Coupon" />
                                <button type="button" id="apply-coupon-btn" class="px-3 sm:px-5 py-2.5 bg-secondary text-white font-bold rounded-lg text-[12px] sm:text-[13px] hover:bg-secondary/90 transition-all duration-200 whitespace-nowrap flex-shrink-0">Apply coupon</button>
                            </div>
                        </div>
                    </div>

                    <script>
                    jQuery(document).ready(function($) {
                        // Billing Toggle logic
                        $('#billing-address-toggle').on('click', function() {
                            $('#billing-address-fields').slideToggle(300);
                            $(this).find('.toggle-circle').toggleClass('is-active');
                        });

                        // Toggle logic for coupon
                        $('#coupon-toggle').on('click', function() {
                            $('#coupon-content').slideToggle(300);
                            $('#coupon-arrow').toggleClass('rotate-180');
                        });

                        // Apply coupon logic
                        $('#apply-coupon-btn').on('click', function(e) {
                            e.preventDefault();
                            var coupon_code = $('#custom_coupon_code').val();
                            
                            if (!coupon_code) {
                                alert('Please enter a coupon code.');
                                return;
                            }

                            $( '.woocommerce-checkout' ).addClass( 'processing' ).block({
                                message: null,
                                overlayCSS: {
                                    background: '#fff',
                                    opacity: 0.6
                                }
                            });

                            var data = {
                                security: wc_checkout_params.apply_coupon_nonce,
                                coupon_code: coupon_code
                            };

                            $.ajax({
                                type: 'POST',
                                url: wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'apply_coupon' ),
                                data: data,
                                success: function( code ) {
                                    $( '.woocommerce-error, .woocommerce-message' ).remove();
                                    $( '.woocommerce-checkout' ).removeClass( 'processing' ).unblock();

                                    if ( code ) {
                                        $( '.woocommerce-checkout' ).before( code );
                                        $( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );
                                    }
                                },
                                dataType: 'html'
                            });
                        });

                        // Trigger checkout update on shipping method change
                        $(document.body).on('change', 'input.shipping_method', function() {
                            $(document.body).trigger('update_checkout');
                        });
                    });
                    </script>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 space-y-4">
                            <!-- Dynamic Shipping Methods -->
                            <div class="space-y-2 mb-4">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Delivery Method</span>
                                <div class="shipping-card-wrapper bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 custom-shipping-ui" style="display: block !important;">
                                    <?php 
                                    if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
                                        wc_cart_totals_shipping_html();
                                    } else {
                                        echo '<p class="text-xs text-gray-400">Please enter your address to view shipping methods.</p>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div id="checkout-totals-fragment">
                                <div class="flex justify-between text-sm text-gray-500 pt-2 border-t border-gray-50">
                                    <span>Sub total</span>
                                    <span class="font-bold text-gray-800"><?php wc_cart_totals_subtotal_html(); ?></span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Delivery cost</span>
                                    <span class="font-bold text-gray-800"><?php echo WC()->cart->get_cart_shipping_total(); ?></span>
                                </div>
                                <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-800">Total</span>
                                    <span class="text-lg font-bold text-gray-800"><?php wc_cart_totals_order_total_html(); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Place Order -->
                    <div class="checkout-place-order pt-4 border-t lg:border-none">
                        <div class="checkout-terms-row flex items-start gap-2 mb-4">
                            <input type="checkbox" id="terms-custom" class="mt-0.5 rounded-none text-secondary focus:ring-secondary w-4 h-4 flex-shrink-0" checked required>
                            <label for="terms-custom" class="text-[13px] text-gray-500 leading-5">
                                I have read and agree to the <a href="#" class="text-secondary font-bold underline">Terms and Conditions</a>, <a href="#" class="text-secondary font-bold underline">Privacy Policy</a> & <a href="#" class="text-secondary font-bold underline">Refund and Return Policy</a>.
                            </label>
                        </div>
                        <button type="submit" class="w-full bg-secondary text-white font-bold py-4 rounded-lg hover:bg-secondary/90 transition-all duration-300 shadow-md text-base uppercase tracking-wider <?php echo get_option( 'checkout_button_shake', 1 ) ? 'checkout-shake' : ''; ?>" name="woocommerce_checkout_place_order" id="place_order">
                            PLACE ORDER
                        </button>
                        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<style>
/* Disable checkout AJAX loaders, overlays, and spinners completely for seamless updates */
.blockUI.blockOverlay,
.blockUI.blockMsg,
.woocommerce .blockUI.blockOverlay,
.woocommerce .blockUI.blockMsg,
.woocommerce-page .blockUI.blockOverlay,
.woocommerce-page .blockUI.blockMsg,
.woocommerce .button.loading::after,
.woocommerce-page .button.loading::after,
.loading::after,
a.ajax_add_to_cart.loading::after,
.woocommerce a.ajax_add_to_cart.loading::after,
.woocommerce-page a.ajax_add_to_cart.loading::after {
    display: none !important;
    background: none !important;
    opacity: 0 !important;
    pointer-events: none !important;
    content: none !important;
}
</style>

