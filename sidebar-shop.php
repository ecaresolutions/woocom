<?php
/**
 * The sidebar containing the shop widget area
 *
 * @package Woocom
 */

if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
    // If no widgets, we can output some default filters or just return
	// return;
}
?>

<div id="shop-sidebar" class="shop-sidebar space-y-8">
    <?php if ( is_active_sidebar( 'sidebar-shop' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-shop' ); ?>
    <?php else : ?>
        <!-- Default Fallback Filters if no widgets are set -->
        
        <!-- Filter by Category -->
        <section class="widget bg-white rounded-xl shadow-sm border border-gray-100 p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-4 cursor-pointer group/header" onclick="this.parentElement.classList.toggle('is-collapsed')">
                <h2 class="widget-title text-[13px] font-bold text-gray-800 uppercase tracking-wider">
                    Filter by Category
                </h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover/header:text-secondary transition-colors"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="space-y-3 widget-content transition-all duration-300">
                <div class="h-px bg-gray-100 mb-4"></div>
                <?php
                $categories = get_terms( array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'parent'     => 0
                ) );
                
                if ( ! empty( $categories ) ) :
                    foreach ( $categories as $cat ) :
                        $link = get_term_link( $cat );
                        $count = $cat->count;
                        ?>
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-secondary focus:ring-secondary cursor-pointer mr-3" <?php checked( is_product_category( $cat->slug ) ); ?> onchange="window.location.href='<?php echo esc_url($link); ?>'">
                            <span class="text-[14px] text-gray-600 group-hover:text-secondary transition-colors"><?php echo esc_html($cat->name); ?></span>
                        </label>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
        </section>

        <!-- Price Range -->
        <section class="widget bg-white rounded-xl shadow-sm border border-gray-100 p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-4 cursor-pointer group/header" onclick="this.parentElement.classList.toggle('is-collapsed')">
                <h2 class="widget-title text-[13px] font-bold text-gray-800 uppercase tracking-wider">
                    Price Range
                </h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover/header:text-secondary transition-colors"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="widget-content transition-all duration-300">
                <div class="h-px bg-gray-100 mb-6"></div>
                <div class="px-2">
                    <div class="relative h-1 bg-gray-100 rounded-full">
                        <div class="absolute h-full bg-secondary rounded-full" style="left: 0%; right: 0%;"></div>
                        <div class="absolute -top-2 left-0 w-5 h-5 bg-white border-[3px] border-secondary rounded-full shadow-md cursor-pointer"></div>
                        <div class="absolute -top-2 right-0 w-5 h-5 bg-white border-[3px] border-secondary rounded-full shadow-md cursor-pointer"></div>
                    </div>
                    <div class="flex items-center justify-between mt-6">
                        <div class="text-[13px] font-bold text-gray-800 flex items-center">
                            <span class="text-gray-400 font-normal mr-1">৳</span> 0
                        </div>
                        <div class="text-[13px] font-bold text-gray-800 flex items-center">
                            <span class="text-gray-400 font-normal mr-1">৳</span> 5,000
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Brands -->
        <section class="widget bg-white rounded-xl shadow-sm border border-gray-100 p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-4 cursor-pointer group/header" onclick="this.parentElement.classList.toggle('is-collapsed')">
                <h2 class="widget-title text-[13px] font-bold text-gray-800 uppercase tracking-wider">
                    Brands
                </h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover/header:text-secondary transition-colors"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="space-y-3 widget-content transition-all duration-300">
                <div class="h-px bg-gray-100 mb-4"></div>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-secondary focus:ring-secondary cursor-pointer mr-3">
                    <span class="text-[14px] text-gray-600 group-hover:text-secondary transition-colors">GhorerBazar</span>
                </label>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-secondary focus:ring-secondary cursor-pointer mr-3">
                    <span class="text-[14px] text-gray-600 group-hover:text-secondary transition-colors">Honeyraj</span>
                </label>
            </div>
        </section>

        <!-- Product Flag -->
        <section class="widget bg-white rounded-xl shadow-sm border border-gray-100 p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-4 cursor-pointer group/header" onclick="this.parentElement.classList.toggle('is-collapsed')">
                <h2 class="widget-title text-[13px] font-bold text-gray-800 uppercase tracking-wider">
                    Product Flag
                </h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover/header:text-secondary transition-colors"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div class="space-y-3 widget-content transition-all duration-300">
                <div class="h-px bg-gray-100 mb-4"></div>
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-secondary focus:ring-secondary cursor-pointer mr-3">
                    <span class="text-[14px] text-gray-600 group-hover:text-secondary transition-colors">New Arrival</span>
                </label>
            </div>
        </section>
    <?php endif; ?>
</div>

<style>
    /* Styling WooCommerce default widgets to match our UI */
    .widget ul { list-style: none; padding: 0; margin: 0; }
    .widget li { margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between; }
    .widget li a { font-size: 14px; color: #4B5563; font-weight: 500; transition: all 0.2s; }
    .widget li a:hover { color: var(--color-secondary); }
    .widget .count { font-size: 12px; color: #9CA3AF; font-weight: 700; background: #F9FAFB; padding: 2px 8px; border-radius: 999px; }
    
    /* Price filter widget customization */
    .widget_price_filter .price_slider { margin-bottom: 1.5rem; background: #F3F4F6 !important; height: 6px !important; border: none !important; border-radius: 999px !important; }
    .widget_price_filter .ui-slider-range { background: var(--color-secondary) !important; border-radius: 999px !important; }
    .widget_price_filter .ui-slider-handle { width: 1.25rem !important; height: 1.25rem !important; background: #fff !important; border: 4px solid var(--color-secondary) !important; border-radius: 50% !important; top: -0.5rem !important; cursor: pointer !important; outline: none !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important; }
    .widget_price_filter .price_slider_amount { display: flex; flex-direction: column; gap: 1rem; }
    .widget_price_filter .price_slider_amount .button { width: 100%; order: 2; background: var(--color-secondary); color: #fff; font-weight: 700; border-radius: 8px; padding: 10px; border: none; cursor: pointer; }
    .widget_price_filter .price_slider_amount .price_label { order: 1; font-size: 14px; font-weight: 700; color: #1F2937; text-align: center; }
</style>
