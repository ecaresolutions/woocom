<?php
/**
 * The template for displaying product content within loops
 *
 * @package Woocom
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$text_add_to_cart = get_option( 'woocom_text_add_to_cart', 'Add To Cart' );
$text_see_details = get_option( 'woocom_text_see_details', 'See Details' );

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
if ( ! $image_url ) {
	$image_url = wc_placeholder_img_src();
}

$discount_percentage = woocom_get_discount_percentage( $product );
$request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
$product_id = $product->get_id();
$product_title = $product->get_name();
$price = $product->get_price();
$old_price = $product->get_regular_price();
?>
<div <?php wc_product_class( 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary dark:hover:border-primary/80 rounded-xl overflow-hidden flex flex-col justify-between hover:shadow-md transition-all duration-300 cursor-pointer group/card relative w-full h-full', $product ); ?> onclick="window.location.href='<?php the_permalink(); ?>'">
    
    <?php if ( $request_type ) : ?>
        <div class="absolute top-2 left-2 z-10 scale-90 origin-top-left">
            <?php echo woocom_render_stock_request_badge( $request_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    <?php endif; ?>

    <!-- Image Container -->
    <div class="aspect-square w-full overflow-hidden relative bg-slate-50/50 dark:bg-slate-955/20 group/img">
        <img 
            src="<?php echo esc_url( $image_url ); ?>" 
            alt="<?php echo esc_attr( $product_title ); ?>" 
            class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105"
            loading="lazy"
        />
        
        <button type="button" class="woocom-quick-view-btn absolute bottom-2 left-1/2 -translate-x-1/2 bg-white/95 hover:bg-primary hover:text-white text-gray-800 text-[10px] sm:text-[11px] font-extrabold px-3 py-1.5 rounded-full shadow-md transition-all duration-300 opacity-0 translate-y-2 group-hover/img:opacity-100 group-hover/img:translate-y-0 group-hover/card:opacity-100 group-hover/card:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer z-10" data-product_id="<?php echo esc_attr($product_id); ?>" onclick="event.stopPropagation(); if (typeof window.woocomOpenQuickView === 'function') { window.woocomOpenQuickView(<?php echo $product_id; ?>); }">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            Quick View
        </button>
    </div>

    <!-- Info -->
    <div class="p-3 flex flex-col justify-between flex-1">
        <div>
            <!-- Ratings -->
            <div class="flex items-center gap-0.5 text-amber-500 mb-2">
                <?php for ( $s = 0; $s < 5; $s++ ) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                <?php endfor; ?>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold ml-1">5.0</span>
            </div>

            <!-- Title -->
            <h3 
                class="text-[13px] font-semibold text-slate-808 dark:text-slate-202 leading-tight group-hover/card:text-primary transition-colors line-clamp-2 h-7"
                title="<?php echo esc_attr( $product_title ); ?>"
            >
                <?php echo esc_html( $product_title ); ?>
            </h3>
        </div>

        <div class="mt-0.5">
            <div class="border-t border-slate-100 dark:border-slate-800/50 mt-1 mb-2"></div>

            <!-- Price -->
            <div class="flex items-baseline justify-between pb-2.5 flex-wrap">
                <span class="text-[17px] font-black text-primary">
                    ৳<?php echo esc_html( number_format($price) ); ?>
                </span>
                <?php if ( $old_price > $price ) : ?>
                    <span class="text-[11px] text-slate-400 line-through ml-2">
                        ৳<?php echo esc_html( number_format($old_price) ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Add to Cart -->
            <?php
            if ( $request_type && function_exists( 'woocom_render_stock_request_form' ) ) :
                echo woocom_render_stock_request_form( $product_id, $request_type, 'archive' );
            elseif ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) :
                ?>
                <a 
                    href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" 
                    data-product_id="<?php echo esc_attr( $product_id ); ?>" 
                    class="add_to_cart_button ajax_add_to_cart group w-full flex items-center justify-center gap-1.5 py-2 bg-primary-light hover:bg-primary text-primary hover:text-white text-[12px] font-bold rounded-full transition-all duration-300 active:scale-95"
                    rel="nofollow"
                    onclick="if (typeof ajaxAddToCart === 'function') { ajaxAddToCart(event, this); } else { event.stopPropagation(); }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span>Add to Cart</span>
                </a>
                <?php
            else :
                ?>
                <a 
                    href="<?php the_permalink(); ?>" 
                    class="group w-full flex items-center justify-center gap-1.5 py-2 bg-primary-light hover:bg-primary text-primary hover:text-white text-[12px] font-bold rounded-full transition-all duration-300 active:scale-95"
                    onclick="event.stopPropagation();"
                >
                    <span>See Details</span>
                </a>
                <?php
            endif;
            ?>
        </div>
    </div>
</div>
