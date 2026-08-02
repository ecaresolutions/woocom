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
?>
<div <?php wc_product_class( 'bg-white rounded-[6px] border border-gray-200 p-2 sm:p-3 h-full flex flex-col group/card hover:shadow-md transition-shadow duration-300 relative w-full overflow-hidden', $product ); ?>>
    
    <?php if ( $request_type ) : ?>
        <div class="absolute top-0 left-0 z-10">
            <?php echo woocom_render_stock_request_badge( $request_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    <?php endif; ?>

    <!-- Image -->
    <div class="relative w-full pt-[100%] mb-2 bg-gray-50/30 rounded overflow-hidden group-img-wrapper">
        <div class="absolute inset-0 flex items-center justify-center p-0">
            <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-contain scale-110 mx-auto">
            </a>
        </div>
        <button type="button" class="woocom-quick-view-btn absolute bottom-2 left-1/2 -translate-x-1/2 bg-white/95 hover:bg-primary hover:text-white text-gray-800 text-[10px] sm:text-[11px] font-extrabold px-3 py-1.5 rounded-full shadow-md transition-all duration-300 opacity-0 translate-y-2 group-hover/img:opacity-100 group-hover/img:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer z-10" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            Quick View
        </button>
    </div>

    <!-- Info -->
    <div class="flex-grow">
        <h3 class="text-[12px] sm:text-[14px] font-medium text-[#253D4E] leading-tight line-clamp-2 mb-0.5">
            <a href="<?php the_permalink(); ?>" class="hover:text-secondary transition-colors"><?php the_title(); ?></a>
        </h3>
        <div class="flex items-center gap-1 sm:gap-1.5 mb-2 mt-0 w-full">
            <span class="text-secondary font-bold text-[13px] sm:text-[15px] flex justify-between w-full items-baseline [&>ins]:order-first [&>del]:order-last [&>ins]:text-secondary [&>ins]:no-underline [&>del]:text-xs sm:[&>del]:text-sm [&>del]:text-slate-400 [&>del]:font-medium">
                <?php echo $product->get_price_html(); ?>
            </span>
        </div>
    </div>

    <!-- Button -->
    <?php
    if ( $request_type && function_exists( 'woocom_render_stock_request_form' ) ) :
        echo woocom_render_stock_request_form( $product->get_id(), $request_type, 'archive' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    elseif ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) :
        echo sprintf(
            '<a href="%s" data-product_id="%s" class="add_to_cart_button ajax_add_to_cart w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[6px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2 mt-auto" rel="nofollow">%s %s</a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( $product->get_id() ),
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 sm:w-4.5 sm:h-4.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>',
            esc_html( $text_add_to_cart )
        );
    else :
        echo sprintf(
            '<a href="%s" class="w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[6px] text-center transition-all duration-300 text-[11px] sm:text-[14px] flex items-center justify-center gap-1 sm:gap-2 mt-auto">%s</a>',
            esc_url( get_permalink() ),
            esc_html( $text_see_details )
        );
    endif;
    ?>
</div>




