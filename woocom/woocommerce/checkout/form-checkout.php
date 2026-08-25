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

<style>
/* Early layout lock to prevent desktop layout shifting (FOUC) during load */
@media (min-width: 1024px) {
    body.woocommerce-checkout form.woocommerce-checkout {
        display: grid !important;
        grid-template-columns: 55% 45% !important;
        grid-template-rows: auto 1fr !important;
        width: 100% !important;
    }
    body.woocommerce-checkout .checkout-left-col {
        grid-column: 1 !important;
        grid-row: 1 !important;
        width: 100% !important;
        display: flex !important;
        justify-content: flex-end !important;
    }
    body.woocommerce-checkout .checkout-right-col {
        grid-column: 2 !important;
        grid-row: 1 / span 2 !important;
        width: 100% !important;
        display: flex !important;
        justify-content: flex-start !important;
    }
    body.woocommerce-checkout .checkout-payment-col {
        grid-column: 1 !important;
        grid-row: 2 !important;
        width: 100% !important;
        display: flex !important;
        justify-content: flex-end !important;
    }
}
</style>

<div class="checkout-page-wrapper bg-white py-0 overflow-x-hidden min-h-screen">
    
    <form name="checkout" method="post" class="checkout woocommerce-checkout min-h-screen flex flex-col lg:flex-row w-full" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
        
        <!-- Right Column: Order summary, Coupon, Recommended -->
        <div class="checkout-right-col w-full bg-[#fafafa] px-4 sm:px-8 lg:px-16 py-8 lg:py-12 flex justify-start">
            <div class="checkout-right-inner w-full max-w-[480px] space-y-8">
                
                <!-- Dynamic Website Logo & Sign In Link (Mobile only) -->
                <div class="flex lg:hidden items-center justify-between pb-3 border-b border-gray-100 mb-2">
                    <?php 
                    $logo_url = get_option( 'theme_logo' );
                    if ( ! $logo_url ) {
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        if ( $custom_logo_id ) {
                            $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                        }
                    }

                    if ( $logo_url ) {
                        echo '<img src="' . esc_url( $logo_url ) . '" class="h-6 lg:h-10 w-auto object-contain" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                    } else {
                        echo '<span class="text-2xl font-black text-primary">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
                    }
                    ?>
                    
                    <div class="flex items-center gap-3">
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="text-xs text-gray-500 hover:text-secondary font-bold flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            Back Shop
                        </a>
                        <?php if ( ! is_user_logged_in() ) : ?>
                            <span class="text-gray-300 text-xs">|</span>
                            <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="text-xs text-gray-500 hover:text-secondary font-bold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Sign in
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Order summary -->
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-[#253D4E] flex items-center gap-2">
                        <span class="checkout-title-marker"></span>
                        Order summary
                    </h2>
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden p-4">
                        <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php woocommerce_order_review(); ?>
                        </div>
                        <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                    </div>
                </div>

                <!-- Coupon Input -->
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <div id="coupon-toggle" class="p-4 flex items-center justify-between cursor-pointer border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-[14px] font-medium text-[#253D4E]">Have any coupon or gift voucher?</h3>
                        <svg id="coupon-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary transition-transform duration-300"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                    <div id="coupon-content" class="p-4" style="display: none;">
                        <div class="relative flex items-center border border-gray-200 rounded-xl p-1.5 focus-within:border-secondary transition-colors min-w-0">
                            <input type="text" name="coupon_code" class="flex-1 min-w-0 px-3 py-2 text-sm outline-none border-none focus:ring-0 text-gray-500 bg-transparent" id="custom_coupon_code" value="" placeholder="Enter Coupon" />
                            <button type="button" id="apply-coupon-btn" class="px-5 py-2.5 bg-secondary text-white font-bold rounded-lg text-[13px] hover:bg-secondary/90 transition-all duration-200 whitespace-nowrap flex-shrink-0">Apply</button>
                        </div>
                    </div>
                </div>

                <!-- Order Totals Price Breakdown -->
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden p-5">
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
                </div>

                <!-- Recommended for you (Cross-sell Section styled as Highlight Panel) -->
                <?php 
                if ( function_exists( 'woocom_get_cart_cross_sell_html' ) ) {
                    $cross_sell_content = woocom_get_cart_cross_sell_html( 4 );
                    if ( $cross_sell_content && strpos( $cross_sell_content, 'No products found' ) === false ) :
                    ?>
                    <div class="bg-gradient-to-br from-[#FFF9F6] to-[#FFF5F0] border-2 border-dashed rounded-2xl p-4 md:p-5 shadow-[0_4px_20px_rgba(249,115,22,0.06)] relative overflow-hidden space-y-4 mt-6" style="border-color: var(--color-secondary, #F7A501) !important;">
                        <!-- Decorative background glow -->
                        <div class="absolute -right-10 -top-10 w-24 h-24 bg-orange-100 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        
                        <div class="flex items-center justify-between relative z-10">
                            <h3 class="text-[15px] font-bold text-[#253D4E] flex items-center gap-2">
                                <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20" style="color: var(--color-secondary, #F7A501) !important;">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Recommended for you
                            </h3>
                        </div>
                        <div class="flex flex-wrap gap-3 relative z-10">
                            <?php echo $cross_sell_content; ?>
                        </div>
                    </div>
                    <?php 
                    endif;
                } 
                ?>

            </div>
        </div>
            <!-- Left Column: Logo, Address Grid, Shipping, Payment, Place Order -->
        <div class="checkout-left-col w-full bg-white px-4 sm:px-8 lg:px-16 py-8 lg:py-12 border-r border-gray-200 flex justify-end">
            <div class="checkout-left-inner w-full max-w-[580px] space-y-8">
                
                <!-- Dynamic Website Logo & Sign In Link (Desktop only) -->
                <div class="hidden lg:flex items-center justify-between pb-3 border-b border-gray-100 mb-2">
                    <?php 
                    $logo_url = get_option( 'theme_logo' );
                    if ( ! $logo_url ) {
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        if ( $custom_logo_id ) {
                            $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                        }
                    }

                    if ( $logo_url ) {
                        echo '<img src="' . esc_url( $logo_url ) . '" class="h-6 lg:h-10 w-auto object-contain" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                    } else {
                        echo '<span class="text-2xl font-black text-primary">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
                    }
                    ?>
                    
                    <div class="flex items-center gap-3">
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="text-xs text-gray-500 hover:text-secondary font-bold flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            Back Shop
                        </a>
                        <?php if ( ! is_user_logged_in() ) : ?>
                            <span class="text-gray-300 text-xs">|</span>
                            <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="text-xs text-gray-500 hover:text-secondary font-bold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Sign in
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Delivery Address Selector Panel & Input Fields -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-[#253D4E] flex items-center gap-2">
                            <span class="checkout-title-marker"></span>
                            Delivery
                        </h2>
                    </div>
                    
                    <!-- Saved Address cards list (Autofill selection grid) -->
                    <div id="address-cards-grid-container" class="space-y-4">
                        <div id="address-cards-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Dynamically generated address cards -->
                        </div>
                        <div class="text-center mt-2">
                            <button type="button" id="show-more-less-addresses" class="text-xs text-slate-500 hover:text-secondary font-semibold transition-colors hidden">
                                Show Less
                            </button>
                        </div>
                    </div>

                    <!-- Billing Address Input Fields directly on page -->
                    <div class="bg-white rounded-xl border border-gray-100 p-4 space-y-4">
                        <?php if ( $checkout->get_checkout_fields() ) : ?>
                            <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                            <div class="shipping-fields-custom">
                                <?php do_action( 'woocommerce_checkout_billing' ); ?>
                            </div>
                            <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
                        <?php endif; ?>
                    </div>
                </div>



                <!-- Shipping Method Panel -->
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-[#253D4E] flex items-center gap-2">
                        <span class="checkout-title-marker"></span>
                        Shipping method
                    </h2>
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
            </div>
        </div>
<!-- Left Column Bottom: Payment and Place Order -->
        <div class="checkout-payment-col w-full bg-white px-4 sm:px-8 lg:px-16 pt-4 lg:pt-6 pb-12 lg:pb-16 border-r border-gray-200 flex justify-end">
            <div class="checkout-payment-inner w-full max-w-[580px] space-y-8">
<!-- Payment Panel -->
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <h2 class="text-lg font-bold text-[#253D4E] flex items-center gap-2">
                        <span class="checkout-title-marker"></span>
                        Payment
                    </h2>
                    <div id="payment-custom-area" class="bg-white rounded-xl border border-gray-100 overflow-hidden p-4">
                        <?php woocommerce_checkout_payment(); ?>
                    </div>
                </div>

                <!-- Place Order -->
                <div class="checkout-place-order pt-6">
                    <div class="checkout-terms-row flex items-start gap-2 mb-4">
                        <input type="checkbox" name="terms" id="terms-custom" class="mt-0.5 rounded-none text-secondary focus:ring-secondary w-4 h-4 flex-shrink-0" checked required>
                        <label for="terms-custom" class="text-[13px] text-gray-500 leading-5">
                            I have read and agree to the <a href="#" class="text-secondary font-bold underline">Terms and Conditions</a>, <a href="#" class="text-secondary font-bold underline">Privacy Policy</a> & <a href="#" class="text-secondary font-bold underline">Refund and Return Policy</a>.
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-secondary text-white font-bold py-4 rounded-xl hover:bg-secondary/90 transition-all duration-300 shadow-md text-base uppercase tracking-wider <?php echo get_option( 'checkout_button_shake', 1 ) ? 'checkout-shake' : ''; ?>" name="woocommerce_checkout_place_order" id="place_order">
                        PLACE ORDER
                    </button>
                    <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
                </div>

            </div>
        </div>
            </div>
        </div>
</form>

        <!-- Mobile Sticky Bottom Bar for easier checkout -->
        <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 z-40 flex items-center justify-between shadow-[0_-4px_10px_rgba(0,0,0,0.08)]">
            <div class="flex flex-col min-w-0">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider truncate">Total (<?php echo WC()->cart->get_cart_contents_count(); ?> items)</span>
                <span id="mobile-sticky-total" class="text-[17px] font-black text-secondary whitespace-nowrap">
                    <?php wc_cart_totals_order_total_html(); ?>
                </span>
            </div>
            <button type="button" id="mobile-sticky-submit" class="bg-secondary hover:bg-secondary/90 text-white font-bold px-6 py-3 rounded-xl shadow-md active:scale-95 transition-all text-sm uppercase tracking-wider cursor-pointer whitespace-nowrap">
                PLACE ORDER
            </button>
        </div>

    </div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<style>
/* Force parent theme containers to span full screen width on Checkout Page */
body.woocommerce-checkout #primary,
body.woocommerce-checkout .site-main,
body.woocommerce-checkout #content,
body.woocommerce-checkout .site-content,
body.woocommerce-checkout #page,
body.woocommerce-checkout .site {
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* Hide Theme Header and Footer on Checkout Page */
#masthead,
#colophon {
    display: none !important;
}

/* Shopify Split Layout Styles (Extremely Compact) */
.checkout-page-wrapper {
    min-height: 100vh !important;
    background-color: #ffffff !important;
}
body.woocommerce-checkout form.woocommerce-checkout {
    display: flex !important;
    flex-direction: column !important;
    min-height: 100vh !important;
    width: 100% !important;
}
@media (min-width: 1024px) {
    body.woocommerce-checkout form.woocommerce-checkout {
        display: grid !important;
        grid-template-columns: 55% 45% !important;
        grid-template-rows: auto 1fr !important;
    }
    body.woocommerce-checkout .checkout-left-col {
        grid-column: 1 !important;
        grid-row: 1 !important;
        width: 100% !important;
    }
    body.woocommerce-checkout .checkout-right-col {
        grid-column: 2 !important;
        grid-row: 1 / span 2 !important;
        width: 100% !important;
    }
    body.woocommerce-checkout .checkout-payment-col {
        grid-column: 1 !important;
        grid-row: 2 !important;
        width: 100% !important;
    }
}
body.woocommerce-checkout .checkout-left-col,
body.woocommerce-checkout .checkout-payment-col {
    background-color: #ffffff !important;
    border-right: 1px solid #e5e7eb !important;
    display: flex !important;
    justify-content: flex-end !important;
    padding: 20px 40px 20px 20px !important;
}
@media (min-width: 1024px) {
    body.woocommerce-checkout .checkout-left-col {
        padding-top: 48px !important;
        padding-bottom: 32px !important;
        padding-right: 48px !important;
        padding-left: 24px !important;
        width: 100% !important;
    }
    body.woocommerce-checkout .checkout-payment-col {
        padding-top: 0 !important;
        padding-bottom: 64px !important;
        padding-right: 48px !important;
        padding-left: 24px !important;
        width: 100% !important;
    }
}
body.woocommerce-checkout .checkout-payment-col {
    padding-top: 0 !important;
}
body.woocommerce-checkout .checkout-right-col {
    background-color: #fafafa !important;
    display: flex !important;
    justify-content: flex-start !important;
    padding: 20px 20px 20px 40px !important;
}
@media (min-width: 1024px) {
    body.woocommerce-checkout .checkout-right-col {
        padding-top: 48px !important;
        padding-bottom: 64px !important;
        padding-left: 48px !important;
        padding-right: 24px !important;
        width: 100% !important;
    }
}
.checkout-left-inner,
.checkout-payment-inner {
    width: 100% !important;
    max-width: 540px !important;
}
@media (min-width: 1024px) {
    .checkout-left-inner,
    .checkout-payment-inner {
        margin-left: auto !important;
        margin-right: 0 !important;
    }
}
.checkout-right-inner {
    width: 100% !important;
    max-width: 440px !important;
}
@media (min-width: 1024px) {
    .checkout-right-inner {
        margin-right: auto !important;
        margin-left: 0 !important;
    }
}

@media (max-width: 1023px) {
    body {
        padding-bottom: 64px !important;
    }
    body.woocommerce-checkout form.woocommerce-checkout {
        flex-direction: column !important;
    }
    body.woocommerce-checkout .checkout-left-col,
    body.woocommerce-checkout .checkout-payment-col,
    body.woocommerce-checkout .checkout-right-col {
        width: 100% !important;
        padding: 12px 16px !important;
        display: block !important;
        border-right: none !important;
    }
    body.woocommerce-checkout .checkout-payment-col {
        padding-top: 4px !important;
    }
    body.woocommerce-checkout .checkout-left-inner,
    body.woocommerce-checkout .checkout-payment-inner,
    body.woocommerce-checkout .checkout-right-inner {
        max-width: 100% !important;
    }
    body.woocommerce-checkout .checkout-right-col {
        background-color: #fafafa !important;
        border-top: 1px solid #e5e7eb !important;
    }
    #billing_state_field,
    #shipping_state_field,
    #billing_city_field,
    #shipping_city_field {
        width: 100% !important;
        float: none !important;
        clear: both !important;
    }
}
/* Custom Saved Address Cards Grid */
#address-cards-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 8px !important;
    width: 100% !important;
}
@media (max-width: 640px) {
    #address-cards-grid {
        grid-template-columns: 1fr !important;
    }
}
.address-card.border-secondary {
    border-color: var(--color-primary, #2563EB) !important;
}
.address-card.bg-secondary-light\/5 {
    background-color: color-mix(in srgb, var(--color-primary, #2563EB) 5%, #ffffff) !important;
}
.address-card.ring-secondary {
    box-shadow: 0 0 0 1px var(--color-primary, #2563EB) !important;
}

/* Prevent WooCommerce default floated layout styles from causing overlapping */
.shipping-card-wrapper,
.custom-shipping-ui,
#payment-custom-area,
.woocommerce-checkout-payment,
#payment,
.checkout-place-order {
    display: flow-root !important;
    float: none !important;
    clear: both !important;
    width: 100% !important;
}
.woocommerce-shipping-methods,
.woocommerce-shipping-methods li,
.payment_methods,
.payment_methods li {
    clear: both !important;
    float: none !important;
    display: block !important;
}

/* Compact Payment methods layout */
#payment-custom-area {
    padding: 0 !important;
    border: none !important;
    background: transparent !important;
    overflow: visible !important;
}
.woocommerce-checkout-payment ul.payment_methods {
    padding: 2px !important;
    margin: 0 !important;
}
/* If there is only one payment method, let it span full width */
.woocommerce-checkout-payment ul.payment_methods > li:only-child {
    grid-column: span 2 / span 2 !important;
}

/* Global Compact/Congested Overrides for Checkout Page */
.checkout-page-wrapper .p-4,
.checkout-page-wrapper .p-5,
.checkout-page-wrapper .p-6 {
    padding: 8px 12px !important;
}
.checkout-page-wrapper .space-y-8 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 12px !important;
}
.checkout-page-wrapper .space-y-6 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 8px !important;
}
.checkout-page-wrapper .space-y-4 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 6px !important;
}
.checkout-page-wrapper .space-y-3 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 4px !important;
}

/* Heading styles */
.checkout-page-wrapper h2.text-lg {
    font-size: 14px !important;
    font-weight: 800 !important;
    padding-bottom: 2px !important;
    margin: 0 !important;
}

/* Input Fields Compacting */
.woocommerce-checkout input[type="text"],
.woocommerce-checkout input[type="tel"],
.woocommerce-checkout input[type="email"],
.woocommerce-checkout input[type="number"],
.woocommerce-checkout select,
.woocommerce-checkout select.state_select,
.woocommerce-checkout select.country_to_state,
.woocommerce-checkout .select2-container--default .select2-selection--single {
    padding: 6px 10px !important;
    height: 34px !important;
    font-size: 12.5px !important;
    border-radius: 6px !important;
}
.woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px !important;
    padding-left: 10px !important;
    font-size: 12.5px !important;
    height: 34px !important;
}
.woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 34px !important;
    top: 0 !important;
}
.woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow b,
.woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow::before,
.woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow::after {
    top: 70% !important;
    transform: translateY(-50%) !important;
    margin-top: 0 !important;
}
.woocommerce-checkout .form-row {
    margin-bottom: 14px !important;
}
#billing_address_1_field,
#shipping_address_1_field {
    clear: both !important;
    margin-top: 14px !important;
}

@media (max-width: 767px) {
    /* Extreme Compact Override for Small Mobile Screens */
    .checkout-page-wrapper h2.text-lg {
        font-size: 13px !important;
    }
    .woocommerce-checkout input[type="text"],
    .woocommerce-checkout input[type="tel"],
    .woocommerce-checkout input[type="email"],
    .woocommerce-checkout input[type="number"],
    .woocommerce-checkout select,
    .woocommerce-checkout select.state_select,
    .woocommerce-checkout select.country_to_state,
    .woocommerce-checkout .select2-container--default .select2-selection--single {
        padding: 5px 8px !important;
        height: 30px !important;
        font-size: 12px !important;
    }
    .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        height: 30px !important;
        font-size: 12px !important;
    }
    .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
    }
    .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow b,
    .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow::before,
    .woocommerce-checkout .select2-container--default .select2-selection--single .select2-selection__arrow::after {
        top: 70% !important;
    }
    .woocommerce-checkout .form-row {
        margin-bottom: 10px !important;
    }
    #billing_address_1_field,
    #shipping_address_1_field {
        margin-top: 10px !important;
    }
    .checkout-page-wrapper .p-4,
    .checkout-page-wrapper .p-5,
    .checkout-page-wrapper .p-6 {
        padding: 6px 10px !important;
    }
    .checkout-page-wrapper .space-y-8 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 8px !important;
    }
    .checkout-page-wrapper .space-y-6 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 6px !important;
    }
    .checkout-page-wrapper .space-y-4 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 4px !important;
    }
}

/* Select2 Dropdown Compacting */
.select2-container--default .select2-results__option {
    padding: 4px 8px !important;
    font-size: 12.5px !important;
    line-height: 1.4 !important;
}
.select2-container--default .select2-search--dropdown {
    padding: 4px 6px !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    padding: 4px 8px !important;
    height: 28px !important;
    font-size: 12.5px !important;
    border-radius: 4px !important;
}
.select2-container--default .select2-results__option--highlighted[aria-selected],
.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: var(--color-primary, #2563EB) !important;
    color: #fff !important;
}

/* Shipping / Delivery method wrapper compacting */
.checkout-page-wrapper .custom-shipping-ui.shipping-card-wrapper {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    box-shadow: none !important;
    margin-top: 0 !important;
}
.checkout-page-wrapper .custom-shipping-ui ul#shipping_method li {
    padding: 6px 12px !important;
    border-radius: 8px !important;
    margin-bottom: 0 !important;
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
}
.checkout-page-wrapper .custom-shipping-ui ul#shipping_method li input[type="radio"] {
    width: 14px !important;
    height: 14px !important;
    margin-right: 8px !important;
    accent-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper .custom-shipping-ui ul#shipping_method li label {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #334155 !important;
    width: 100% !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
}
.checkout-page-wrapper .custom-shipping-ui ul#shipping_method li label .woocommerce-Price-amount {
    font-size: 12.5px !important;
    font-weight: 800 !important;
    color: var(--color-primary, #2563EB) !important;
    margin-left: 8px !important;
}
.checkout-page-wrapper .custom-shipping-ui p {
    margin: 0 !important;
}

/* Totals fragment compacting */
#checkout-totals-fragment > div {
    padding-top: 4px !important;
    padding-bottom: 4px !important;
    font-size: 12.5px !important;
}
#checkout-totals-fragment .border-t {
    padding-top: 4px !important;
    margin-top: 4px !important;
}

/* Order review items compacting */
.checkout-page-wrapper .woocommerce-checkout-review-order-table.space-y-4 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 4px !important;
}
.checkout-page-wrapper .checkout-cart-item {
    padding: 4px 6px !important;
    border-radius: 6px !important;
}
.checkout-page-wrapper .checkout-cart-item .w-16.h-16 {
    width: 38px !important;
    height: 38px !important;
}
.checkout-page-wrapper .checkout-cart-item h4 {
    font-size: 12px !important;
}
.checkout-page-wrapper .checkout-cart-item .gap-4 {
    gap: 6px !important;
}
.checkout-page-wrapper .checkout-qty-minus,
.checkout-page-wrapper .checkout-qty-plus {
    padding: 0px 4px !important;
    font-size: 11px !important;
}
/* Hide number input spinners and force text color in checkout quantity box */
.checkout-cart-item input[type="number"] {
    -moz-appearance: textfield !important;
    color: #1e293b !important;
    background: transparent !important;
}
.checkout-cart-item input[type="number"]::-webkit-outer-spin-button,
.checkout-cart-item input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}

/* Page columns gap and margins */
.checkout-page-wrapper .gap-8 {
    gap: 12px !important;
}
.checkout-page-wrapper .mb-6 {
    margin-bottom: 8px !important;
}
.checkout-page-wrapper .py-4 {
    padding-top: 4px !important;
    padding-bottom: 4px !important;
}

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

/* Hide WooCommerce account creation fields (password) by default to prevent flashing on reload */
.woocommerce-checkout div.create-account {
    display: none;
}

.woocommerce-checkout .woocommerce-NoticeGroup-checkout,
.woocommerce-checkout .woocommerce-NoticeGroup,
.woocommerce-checkout .woocommerce-invalid-required-for-ship,
.woocommerce-checkout .woocommerce-invalid-required,
.woocommerce-checkout .woocommerce-invalid-email,
.woocommerce-checkout .woocommerce-invalid-phone,
.woocommerce-checkout .error,
.woocommerce-checkout label.error,
.woocommerce-checkout span.error,
.woocommerce-checkout .woocommerce-error-message,
.woocommerce-checkout .wc-block-components-validation-error,
.woocommerce-checkout .woocommerce-invalid .woocommerce-error,
.woocommerce-checkout .woocommerce-invalid label[for] ~ .woocommerce-error,
.woocommerce-checkout .woocommerce-invalid .error-message,
.woocommerce-checkout .form-row .woocommerce-error,
.woocommerce-checkout .form-row .woocommerce-error-message,
.woocommerce-checkout .form-row [class*="error"],
.woocommerce-checkout .form-row [class*="validation"] {
    display: none !important;
}
/* Ensure the input borders still turn red for visual feedback */
.woocommerce-checkout .woocommerce-invalid input,
.woocommerce-checkout .woocommerce-invalid select,
.woocommerce-checkout .woocommerce-invalid .select2-selection {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}
/* Disable default WooCommerce validation checkmark and cross/exclamation background icons */
.woocommerce-checkout .woocommerce-validated input,
.woocommerce-checkout .woocommerce-validated select,
.woocommerce-checkout .woocommerce-validated textarea,
.woocommerce-checkout .woocommerce-validated .select2-selection,
.woocommerce-checkout .woocommerce-invalid input,
.woocommerce-checkout .woocommerce-invalid select,
.woocommerce-checkout .woocommerce-invalid textarea,
.woocommerce-checkout .woocommerce-invalid .select2-selection {
    background-image: none !important;
    padding-right: 12px !important;
}

/* Fix phone prefix positioning using relative input wrapper and z-index mapping */
.phone-prefix-88::before {
    content: none !important;
    display: none !important;
}
.phone-prefix-88 .woocommerce-input-wrapper {
    position: relative !important;
    display: block !important;
}
.phone-prefix-88 .woocommerce-input-wrapper::before {
    content: "88" !important;
    display: flex !important;
    position: absolute !important;
    left: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    background: #f1f5f9 !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    z-index: 99 !important; /* Stand on top of the input field */
    pointer-events: none !important;
    line-height: 1 !important;
    align-items: center !important;
    justify-content: center !important;
    height: 28px !important; /* Clean fit inside 34px-48px inputs */
    box-sizing: border-box !important;
}
.phone-prefix-88 .woocommerce-input-wrapper input {
    position: relative !important;
    z-index: 1 !important;
    padding-left: 54px !important;
}
.woocommerce-checkout .phone-prefix-88 input.input-text {
    padding-left: 54px !important;
}

/* Color overrides for Checkout page to match primary theme color */
.checkout-page-wrapper .text-secondary,
.woocommerce-checkout .text-secondary {
    color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper .bg-secondary,
.woocommerce-checkout .bg-secondary {
    background-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper .border-secondary,
.woocommerce-checkout .border-secondary {
    border-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper .hover\:bg-secondary\/90:hover,
.woocommerce-checkout .hover\:bg-secondary\/90:hover {
    background-color: var(--color-primary, #2563EB) !important;
    opacity: 0.9 !important;
}
.checkout-page-wrapper .hover\:bg-secondary:hover,
.woocommerce-checkout .hover\:bg-secondary:hover {
    background-color: var(--color-primary, #2563EB) !important;
    opacity: 0.9 !important;
}
.checkout-page-wrapper .focus-within\:border-secondary:focus-within,
.woocommerce-checkout .focus-within\:border-secondary:focus-within {
    border-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper .focus\:border-secondary:focus,
.woocommerce-checkout .focus\:border-secondary:focus {
    border-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper .text-secondary.underline,
.woocommerce-checkout .text-secondary.underline {
    color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper input[type="checkbox"].text-secondary,
.woocommerce-checkout input[type="checkbox"].text-secondary {
    accent-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper #apply-coupon-btn,
.woocommerce-checkout #apply-coupon-btn {
    background-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper #apply-coupon-btn:hover,
.woocommerce-checkout #apply-coupon-btn:hover {
    opacity: 0.9 !important;
}
.checkout-page-wrapper #place_order,
.woocommerce-checkout #place_order {
    background-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper #place_order:hover,
.woocommerce-checkout #place_order:hover {
    opacity: 0.9 !important;
}

/* Additional payment method overrides */
.checkout-page-wrapper .bg-\[\#eff6ff\],
.woocommerce-checkout .bg-\[\#eff6ff\] {
    background-color: color-mix(in srgb, var(--color-primary, #2563EB) 8%, #ffffff) !important;
}
.checkout-page-wrapper .hover\:border-secondary\/20:hover,
.woocommerce-checkout .hover\:border-secondary\/20:hover {
    border-color: color-mix(in srgb, var(--color-primary, #2563EB) 20%, transparent) !important;
}
.checkout-page-wrapper .focus\:ring-secondary:focus,
.woocommerce-checkout .focus\:ring-secondary:focus {
    --tw-ring-color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper .hover\:text-secondary:hover,
.woocommerce-checkout .hover\:text-secondary:hover {
    color: var(--color-primary, #2563EB) !important;
}
.checkout-page-wrapper svg.text-secondary,
.woocommerce-checkout svg.text-secondary {
    color: var(--color-primary, #2563EB) !important;
}
.woocommerce-checkout .form-row label {
    display: none !important;
}

/* Custom Premium Toast Notification styling */
#toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 350px;
    width: calc(100% - 40px);
}
@media (max-width: 767px) {
    #toast-container {
        top: 15px;
        right: 20px;
        left: 20px;
        max-width: none;
        width: auto;
    }
}

.custom-toast {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid #FCA5A5; /* Soft red border for error toast */
    border-left: 4px solid #EF4444; /* Bold red accent on the left */
    padding: 12px 16px;
    border-radius: 12px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    transform: translateX(120%);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}
.custom-toast.show {
    transform: translateX(0);
    opacity: 1;
}
.custom-toast.hide {
    transform: translateX(120%);
    opacity: 0;
}
@media (max-width: 767px) {
    .custom-toast {
        transform: translateY(-50px);
    }
    .custom-toast.show {
        transform: translateY(0);
    }
    .custom-toast.hide {
        transform: translateY(-50px);
        opacity: 0;
    }
}

.custom-toast-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #EF4444;
    flex-shrink: 0;
}
.custom-toast-message {
    font-size: 13px;
    font-weight: 600;
    color: #1F2937;
    line-height: 1.4;
}
.custom-toast-close {
    margin-left: auto;
    color: #9CA3AF;
    cursor: pointer;
    transition: color 0.2s;
    flex-shrink: 0;
}
.custom-toast-close:hover {
    color: #4B5563;
}
.custom-toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: #EF4444;
    width: 100%;
    transform-origin: left;
}
</style>

<script type="text/javascript">
(function() {
    // Clean up add-to-cart query parameters from the URL immediately on checkout page load
    if (window.history && window.history.replaceState) {
        var url = new URL(window.location.href);
        if (url.searchParams.has('add-to-cart')) {
            url.searchParams.delete('add-to-cart');
            url.searchParams.delete('quantity');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    }
})();

jQuery(document).ready(function($) {
    var savedAddresses = [];
    try {
        savedAddresses = JSON.parse(localStorage.getItem('woocom_checkout_addresses')) || [];
    } catch(e) {
        savedAddresses = [];
    }

    var selectedAddressId = localStorage.getItem('woocom_selected_address_id') || null;

    // Render Saved Addresses
    function renderAddressCards() {
        var $container = $('#address-cards-grid-container');
        var $grid = $('#address-cards-grid');
        $grid.empty();
        
        if (savedAddresses.length === 0) {
            $container.hide();
            return;
        } else {
            $container.show();
        }

        savedAddresses.forEach(function(addr) {
            var isSelected = (addr.id == selectedAddressId);
            var cardHtml = `
                <div class="address-card p-2.5 sm:p-3 border rounded-xl relative cursor-pointer transition-all ${isSelected ? 'border-secondary bg-secondary-light/5 ring-1 ring-secondary' : 'border-gray-200 hover:border-slate-300 bg-gray-50/10'}" data-id="${addr.id}">
                    <div class="flex items-center justify-between mb-1.5 pb-1.5 border-b border-dashed border-gray-100">
                        <div class="font-bold text-[#253D4E] text-[13px] flex items-center gap-1.5 flex-wrap">
                            <span>${addr.name}</span>
                            ${isSelected ? '<span class="text-[9px] bg-secondary text-white font-bold px-1.5 py-0.5 rounded-full uppercase scale-90 origin-left">Selected</span>' : ''}
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button type="button" class="delete-addr-btn text-slate-400 hover:text-red-500 p-1 cursor-pointer" data-id="${addr.id}" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500 space-y-0.5 leading-tight">
                        <div><strong class="text-gray-700">Phone:</strong> ${addr.phone}</div>
                        <div><strong class="text-gray-700">Address:</strong> ${addr.address}, ${addr.city_text}, ${addr.state_text}</div>
                    </div>
                </div>
            `;
            $grid.append(cardHtml);
        });
    }

    // Populate checkout fields from active address
    function applySelectedAddress() {
        var addr = savedAddresses.find(function(a) { return a.id == selectedAddressId; });
        if (!addr) return;

        $('#billing_first_name').val(addr.name);
        $('#billing_phone').val(addr.phone);
        $('#billing_state').val(addr.state_val).trigger('change');
        
        // Use timeout to let WooCommerce update Thana dropdown list before setting its value
        setTimeout(function() {
            if (addr.city_val) {
                $('#billing_city').val(addr.city_val).trigger('change');
            }
            $('body').trigger('update_checkout');
        }, 600);

        $('#billing_address_1').val(addr.address);
    }

    // Select address card
    $(document).on('click', '.address-card', function(e) {
        if ($(e.target).closest('.delete-addr-btn').length > 0) return;
        selectedAddressId = $(this).data('id');
        localStorage.setItem('woocom_selected_address_id', selectedAddressId);
        applySelectedAddress();
        renderAddressCards();
    });

    // Delete address
    $(document).on('click', '.delete-addr-btn', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this address?')) {
            savedAddresses = savedAddresses.filter(function(a) { return a.id != id; });
            localStorage.setItem('woocom_checkout_addresses', JSON.stringify(savedAddresses));
            if (selectedAddressId == id) {
                selectedAddressId = savedAddresses.length > 0 ? savedAddresses[0].id : null;
                localStorage.setItem('woocom_selected_address_id', selectedAddressId);
                if (selectedAddressId) {
                    applySelectedAddress();
                } else {
                    // Reset fields
                    $('#billing_first_name').val('');
                    $('#billing_phone').val('');
                    $('#billing_state').val('').trigger('change');
                    $('#billing_city').val('').trigger('change');
                    $('#billing_address_1').val('');
                    $('body').trigger('update_checkout');
                }
            }
            renderAddressCards();
        }
    });

    // Init
    renderAddressCards();
    if (selectedAddressId) {
        applySelectedAddress();
    } else if (savedAddresses.length > 0) {
        selectedAddressId = savedAddresses[0].id;
        localStorage.setItem('woocom_selected_address_id', selectedAddressId);
        applySelectedAddress();
        renderAddressCards();
    }

    // Shipping Address Toggle
    $(document).on('click', '#billing-address-toggle', function() {
        var $fields = $('#billing-address-fields');
        var $checkbox = $('#ship-to-different-address-checkbox');
        var $circle = $('.checkout-billing-toggle');

        if ($fields.is(':visible')) {
            $fields.slideUp();
            $checkbox.prop('checked', false).trigger('change');
            $circle.removeClass('active');
        } else {
            $fields.slideDown();
            $checkbox.prop('checked', true).trigger('change');
            $circle.addClass('active');
        }
    });

    // Coupon Toggle
    $(document).on('click', '#coupon-toggle', function() {
        var $content = $('#coupon-content');
        var $arrow = $('#coupon-arrow');
        $content.slideToggle();
        $arrow.toggleClass('rotate-180');
    });

    // Custom Coupon Apply logic
    $(document).on('click', '#apply-coupon-btn', function() {
        var code = $('#custom_coupon_code').val();
        if (!code) {
            alert('Please enter a coupon code.');
            return;
        }
        var $couponField = $('#coupon_code');
        if ($couponField.length) {
            $couponField.val(code);
            $('form.checkout_coupon, form.woocommerce-coupon-form').submit();
        } else {
            $('.showcoupon').trigger('click');
            setTimeout(function() {
                var $realInput = $('.checkout_coupon input[name="coupon_code"], form.woocommerce-coupon-form input[name="coupon_code"]');
                if ($realInput.length) {
                    $realInput.val(code);
                    $('.checkout_coupon button[name="apply_coupon"], form.woocommerce-coupon-form button[name="apply_coupon"]').trigger('click');
                }
            }, 300);
        }
    });

    // Prevent mouse wheel from changing input value on number fields
    $(document).on('wheel', 'input[type=number]', function (e) {
        $(this).blur();
    });

    // Checkout Quantity Update Handler (+ / -)
    $(document).on('click', '.checkout-qty-minus, .checkout-qty-plus', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $input = $button.siblings('input[type="number"]');
        var cart_item_key = $input.data('cart_item_key');
        var currentQty = parseInt($input.val()) || 1;
        var action = $button.hasClass('checkout-qty-plus') ? 'increase' : 'decrease';

        if (action === 'decrease' && currentQty <= 1) {
            return;
        }

        $button.prop('disabled', true);
        var ajax_url = (typeof wc_checkout_params !== 'undefined') ? wc_checkout_params.ajax_url : '/wp-admin/admin-ajax.php';

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'woocom_update_mini_cart_qty',
                cart_item_key: cart_item_key,
                qty_action: action
            },
            success: function() {
                $('body').trigger('update_checkout');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });

    // Checkout Item Removal Handler
    $(document).on('click', '.remove-item', function(e) {
        e.preventDefault();
        var $button = $(this);
        var cart_item_key = $button.data('cart_item_key');

        if (!cart_item_key) return;

        $button.css('pointer-events', 'none').css('opacity', '0.5');
        var ajax_url = (typeof wc_checkout_params !== 'undefined') ? wc_checkout_params.ajax_url : '/wp-admin/admin-ajax.php';

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'woocom_remove_mini_cart_item',
                cart_item_key: cart_item_key
            },
            success: function() {
                $('body').trigger('update_checkout');
            }
        });
    });

    // Update mobile sticky bar total price when checkout updates
    $(document.body).on('updated_checkout', function() {
        var totalHtml = $('#checkout-totals-fragment span.text-lg').html() || $('.order-total .amount').html();
        if (totalHtml) {
            $('#mobile-sticky-total').html(totalHtml);
        }
    });

    // Mobile sticky bar submit action
    $(document).on('click', '#mobile-sticky-submit', function(e) {
        e.preventDefault();
        var $actualSubmit = $('#place_order');
        if ($actualSubmit.length) {
            $actualSubmit.trigger('click');
        }
    });

    // Custom Toast Notification System
    function showToast(message, type = 'error') {
        var $container = $('#toast-container');
        if ($container.length === 0) {
            $container = $('<div id="toast-container"></div>').appendTo('body');
        }
        
        var toastId = 'toast-' + Date.now() + Math.random().toString(36).substr(2, 5);
        var toastHtml = `
            <div id="${toastId}" class="custom-toast">
                <div class="custom-toast-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="custom-toast-message">${message}</div>
                <div class="custom-toast-close" onclick="closeToast('${toastId}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </div>
                <div class="custom-toast-progress"></div>
            </div>
        `;
        
        var $toast = $(toastHtml).appendTo($container);
        
        setTimeout(function() {
            $toast.addClass('show');
        }, 50);
        
        var duration = 4000;
        var $progress = $toast.find('.custom-toast-progress');
        $progress.css({
            transition: 'transform ' + duration + 'ms linear',
            transform: 'scaleX(0)'
        });
        
        var autoCloseTimeout = setTimeout(function() {
            closeToast(toastId);
        }, duration);
        
        $toast.data('timeout', autoCloseTimeout);
    }

    function closeToast(toastId) {
        var $toast = $('#' + toastId);
        if ($toast.length) {
            clearTimeout($toast.data('timeout'));
            $toast.removeClass('show').addClass('hide');
            setTimeout(function() {
                $toast.remove();
            }, 400);
        }
    }
    window.closeToast = closeToast;

    // Handle WooCommerce checkout validation errors using Toasts
    $(document.body).on('checkout_error', function(event, errorMessage) {
        // Prevent annoying default page scroll-to-top
        $('html, body').stop();
        
        // Parse and show toast notifications for each error message
        var $errors = $('<div>' + errorMessage + '</div>').find('li');
        if ($errors.length > 0) {
            $errors.each(function() {
                var text = $(this).text();
                showToast(text, 'error');
            });
        } else {
            var cleanText = $('<div>' + errorMessage + '</div>').text().trim();
            if (cleanText) {
                showToast(cleanText, 'error');
            }
        }
    });
});
</script>

