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
                        <div class="flex justify-between text-sm pt-3 border-t border-gray-200">
                            <span class="text-gray-800 font-extrabold text-lg">Total</span>
                            <span class="text-secondary font-extrabold text-xl"><?php echo $order->get_formatted_order_total(); ?></span>
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
