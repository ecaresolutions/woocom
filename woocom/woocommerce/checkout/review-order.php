<?php
/**
 * Review order table
 *
 * @package WooCommerce/Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="shop_table woocommerce-checkout-review-order-table space-y-4">
    <?php
    do_action( 'woocommerce_review_order_before_cart_contents' );

    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
            ?>
            <div class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'checkout-cart-item flex items-center justify-between p-3 border border-gray-50 rounded-xl hover:bg-gray-50/50 transition-colors', $cart_item, $cart_item_key ) ); ?>">
                
                <div class="flex items-center gap-4 flex-grow">
                    <!-- Product Image -->
                    <div class="w-16 h-16 rounded-lg bg-gray-100 flex-shrink-0 p-1 border border-gray-100 overflow-hidden">
                        <?php echo $_product->get_image(); ?>
                    </div>

                    <!-- Product Details -->
                    <div class="flex-grow">
                        <h4 class="text-[14px] font-bold text-gray-800 line-clamp-1 mb-1">
                            <?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;'; ?>
                        </h4>
                        
                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-3">
                            <span class="text-[12px] text-gray-400 font-medium">Qty:</span>
                            <div class="flex items-center border border-gray-200 rounded-md bg-white">
                                <button type="button" class="checkout-qty-minus px-2 py-0.5 text-gray-400 hover:text-secondary">-</button>
                                <input type="number" class="w-8 text-center text-[12px] font-bold border-none p-0 focus:ring-0" value="<?php echo $cart_item['quantity']; ?>" data-cart_item_key="<?php echo $cart_item_key; ?>" readonly>
                                <button type="button" class="checkout-qty-plus px-2 py-0.5 text-gray-400 hover:text-secondary">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price and Remove -->
                <div class="flex flex-col items-end gap-2 ml-4">
                    <span class="text-[14px] font-bold text-gray-800">
                        <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                    </span>
                    <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="remove-item flex h-7 w-7 items-center justify-center text-red-500 hover:text-white bg-red-50 hover:bg-red-500 border border-red-100 hover:border-red-500 rounded-md transition-colors" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Remove this item', 'woocommerce' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    </a>
                </div>

            </div>
            <?php
        }
    }

    do_action( 'woocommerce_review_order_after_cart_contents' );
    ?>
</div>
