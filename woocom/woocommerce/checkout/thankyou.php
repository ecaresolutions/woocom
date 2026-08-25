<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="thank-you-page-wrapper bg-gray-50 py-16 min-h-[70vh]">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <?php if ( $order ) : ?>

            <?php if ( $order->has_status( 'failed' ) ) : ?>
                <div class="bg-white rounded-xl p-8 text-center shadow-sm border border-gray-100 mb-8">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php esc_html_e( 'Order Failed', 'woocommerce' ); ?></h1>
                    <p class="text-gray-500 mb-8"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
                    <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="px-10 py-4 bg-secondary text-white font-bold rounded-xl hover:bg-secondary/90 transition-all inline-block shadow-lg shadow-secondary/20">
                        <?php esc_html_e( 'Pay Now', 'woocommerce' ); ?>
                    </a>
                </div>
            <?php else : ?>
                
                <div class="bg-white rounded-xl p-8 text-center shadow-sm border border-gray-100 mb-8 overflow-hidden relative">
                    <!-- Decorative background element -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-full -mr-16 -mt-16"></div>
                    
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    
                    <h1 class="text-3xl font-extrabold text-primary mb-2"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Order Placed Successfully!', 'woocommerce' ), $order ); ?></h1>
                    <p class="text-gray-500 mb-10">Thank you for your purchase. We've received your order and it is now being processed.</p>

                    <!-- Order Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 bg-gray-50 rounded-xl text-left border border-gray-100">
                        <div class="space-y-1">
                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold">Order Number</p>
                            <p class="text-[15px] font-bold text-gray-800">#<?php echo $order->get_order_number(); ?></p>
                        </div>
                        <div class="space-y-1 border-l border-gray-200 pl-4">
                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold">Date</p>
                            <p class="text-[15px] font-bold text-gray-800"><?php echo wc_format_datetime( $order->get_date_created() ); ?></p>
                        </div>
                        <div class="space-y-1 border-l border-gray-200 pl-4">
                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold">Total Amount</p>
                            <p class="text-[15px] font-bold text-secondary"><?php echo $order->get_formatted_order_total(); ?></p>
                        </div>
                        <div class="space-y-1 border-l border-gray-200 pl-4">
                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold">Payment Method</p>
                            <p class="text-[15px] font-bold text-gray-800"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-secondary rounded-full"></span>
                        Order Details
                    </h3>
                    
                    <div class="space-y-4">
                        <?php
                        foreach ( $order->get_items() as $item_id => $item ) {
                            $product = $item->get_product();
                            ?>
                            <div class="flex items-center justify-between py-4 border-b border-gray-50 last:border-0">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 p-2 flex-shrink-0">
                                        <?php echo $product ? $product->get_image() : ''; ?>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800"><?php echo esc_html( $item->get_name() ); ?></h4>
                                        <p class="text-xs text-gray-400 mt-1">Quantity: <?php echo esc_html( $item->get_quantity() ); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-800"><?php echo $order->get_formatted_line_subtotal( $item ); ?></p>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <div class="mt-8 p-5 bg-gray-50 rounded-xl space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 font-medium">Subtotal</span>
                            <span class="text-gray-800 font-bold"><?php echo $order->get_subtotal_to_display(); ?></span>
                        </div>
                        
                        <?php 
                        foreach ( $order->get_order_item_totals() as $key => $total ) {
                            if ( in_array( $key, array( 'cart_subtotal', 'order_total' ) ) ) continue;
                            ?>
                            <div class="flex justify-between text-sm pt-3 border-t border-gray-200/50">
                                <span class="text-gray-500 font-medium"><?php echo esc_html( wp_strip_all_tags( $total['label'] ) ); ?></span>
                                <span class="text-gray-800 font-bold"><?php echo wp_kses_post( $total['value'] ); ?></span>
                            </div>
                            <?php
                        }
                        ?>
                        
                        <div class="flex justify-between text-sm pt-3 border-t border-gray-200">
                            <span class="text-gray-800 font-extrabold text-lg">Total</span>
                            <span class="text-secondary font-extrabold text-xl"><?php echo $order->get_formatted_order_total(); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Customer Address Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8 text-left">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-secondary rounded-full"></span>
                        Customer Address
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Billing Address -->
                        <div class="p-5 bg-gray-50 rounded-xl border border-gray-100">
                            <h4 class="text-sm font-extrabold text-gray-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                Billing Address
                            </h4>
                            <address class="text-sm text-gray-600 space-y-1.5 not-italic">
                                <?php echo $order->get_formatted_billing_address() ? $order->get_formatted_billing_address() : 'N/A'; ?>
                                <?php if ( $order->get_billing_phone() ) : ?>
                                    <p class="pt-2 flex items-center gap-1.5 text-gray-800 font-medium border-t border-gray-200/50 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-secondary"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <?php echo esc_html( $order->get_billing_phone() ); ?>
                                    </p>
                                <?php endif; ?>
                            </address>
                        </div>
                        
                        <!-- Shipping Address -->
                        <div class="p-5 bg-gray-50 rounded-xl border border-gray-100">
                            <h4 class="text-sm font-extrabold text-gray-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary"><rect x="1" y="3" width="15" height="13" rx="2" ry="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                Shipping Address
                            </h4>
                            <address class="text-sm text-gray-600 space-y-1.5 not-italic">
                                <?php echo $order->get_formatted_shipping_address() ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address(); ?>
                                <?php if ( $order->get_billing_phone() ) : ?>
                                    <p class="pt-2 flex items-center gap-1.5 text-gray-800 font-medium border-t border-gray-200/50 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-secondary"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <?php echo esc_html( $order->get_billing_phone() ); ?>
                                    </p>
                                <?php endif; ?>
                            </address>
                        </div>
                    </div>
                </div>

                <?php 
                // Standard WooCommerce actions for receipt/thankyou hooks (crucial for GTM/Pixel/GA4 tracking plugins)
                do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
                do_action( 'woocommerce_thankyou', $order->get_id() ); 
                ?>

                <div class="text-center">
                    <p class="text-gray-400 text-sm mb-6">A confirmation email has been sent to <span class="text-gray-600 font-bold"><?php echo $order->get_billing_email(); ?></span></p>
                    <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="px-12 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all inline-block shadow-xl shadow-gray-200">
                        Continue Shopping
                    </a>
                </div>

            <?php endif; ?>

        <?php else : ?>

            <div class="bg-white rounded-xl p-8 text-center shadow-sm border border-gray-100">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></h1>
                <p class="text-gray-500 mb-8">We've received your order and it's being processed.</p>
                <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="px-10 py-4 bg-secondary text-white font-bold rounded-xl hover:bg-secondary/90 transition-all inline-block shadow-lg">
                    Continue Shopping
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>
