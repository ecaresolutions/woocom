<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="woocom-mini-cart-header flex items-center justify-between pb-3 mb-3 border-b border-gray-100">
    <h3 class="text-[17px] font-bold text-gray-800" style="font-family: 'Poppins', sans-serif;">Cart</h3>
</div>

<?php if ( ! WC()->cart->is_empty() ) : ?>

    <div class="woocommerce-mini-cart-items-wrapper max-h-[260px] overflow-y-auto pr-1">
        <?php
        do_action( 'woocommerce_before_mini_cart_contents' );

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array(54, 54) ), $cart_item, $cart_item_key );
                $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                ?>
                <div class="woocom-mini-cart-item flex items-center justify-between gap-3 py-3 border-b border-gray-100 last:border-b-0" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
                    
                    <!-- Product Image -->
                    <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-lg flex-shrink-0 overflow-hidden flex items-center justify-center p-1">
                        <?php if ( ! empty( $product_permalink ) ) : ?>
                            <a href="<?php echo esc_url( $product_permalink ); ?>" class="block w-full h-full">
                                <?php echo $thumbnail; ?>
                            </a>
                        <?php else : ?>
                            <?php echo $thumbnail; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Title and Price Info -->
                    <div class="flex-grow min-w-0">
                        <h4 class="text-[13px] font-semibold text-gray-800 leading-tight mb-1 truncate">
                            <?php if ( ! empty( $product_permalink ) ) : ?>
                                <a href="<?php echo esc_url( $product_permalink ); ?>" class="hover:text-primary transition-colors">
                                    <?php echo esc_html( $product_name ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html( $product_name ); ?>
                            <?php endif; ?>
                        </h4>
                        <div class="text-[12px] font-bold text-gray-500">
                            <?php echo $product_price; ?>
                        </div>
                    </div>

                    <!-- Quantity Controls and Remove -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="flex items-center border border-gray-200 rounded-md h-7 overflow-hidden bg-white">
                            <button type="button" class="mini-cart-qty-btn px-2 text-gray-500 hover:bg-gray-100 h-full flex items-center justify-center transition-colors font-bold text-xs" data-action="decrease" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">−</button>
                            <span class="mini-cart-qty-display w-7 text-center text-[12px] font-bold border-x border-gray-200 h-full flex items-center justify-center text-gray-800"><?php echo $cart_item['quantity']; ?></span>
                            <button type="button" class="mini-cart-qty-btn px-2 text-gray-500 hover:bg-gray-100 h-full flex items-center justify-center transition-colors font-bold text-xs" data-action="increase" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">+</button>
                        </div>
                        
                        <!-- Remove button -->
                        <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="mini-cart-remove-link text-gray-400 hover:text-red-500 transition-colors text-lg font-light leading-none px-1" aria-label="Remove item" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">&times;</a>
                    </div>

                </div>
                <?php
            }
        }

        do_action( 'woocommerce_mini_cart_contents' );
        ?>
    </div>

    <!-- Subtotal -->
    <div class="flex items-center justify-between py-3 my-3 border-t border-gray-100">
        <span class="text-sm font-semibold text-gray-500">Subtotal:</span>
        <span class="text-base font-bold text-primary" style="color: var(--color-primary, #2563EB); font-family: 'Poppins', sans-serif;">
            <?php echo WC()->cart->get_cart_subtotal(); ?>
        </span>
    </div>

    <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

    <!-- Buttons -->
    <div class="flex gap-3">
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="flex-1 inline-flex items-center justify-center h-10 border border-primary text-primary hover:bg-primary-light font-bold text-xs rounded-lg transition-all duration-200" style="border-color: var(--color-primary, #2563EB); color: var(--color-primary, #2563EB);">
            View cart
        </a>
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="flex-grow flex-1 inline-flex items-center justify-center h-10 bg-primary text-white hover:opacity-90 font-bold text-xs rounded-lg transition-all duration-200" style="background-color: var(--color-primary, #2563EB);">
            Check out
        </a>
    </div>

    <?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

    <div class="py-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mx-auto mb-2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        <p class="text-gray-500 font-semibold text-[13.5px]">Your cart is empty</p>
    </div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
