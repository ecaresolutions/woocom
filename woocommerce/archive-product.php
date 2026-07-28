<?php
/**
 * The Template for displaying product archives
 *
 * @package Woocom
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="bg-gray-50 py-8 min-h-screen">
    <div class="container mx-auto px-4">
        
        <!-- Header Section: Breadcrumb & Title -->
        <div class="flex items-center justify-between mb-8 gap-4">
            <h1 class="text-[20px] md:text-[24px] font-bold text-primary leading-tight"><?php woocommerce_page_title(); ?></h1>
            <div class="hidden sm:block">
                <?php 
                woocommerce_breadcrumb(array(
                    'delimiter'   => ' <span class="mx-1 text-gray-300">›</span> ',
                    'wrap_before' => '<nav class="woocommerce-breadcrumb flex items-center text-[13px] text-gray-400 font-medium">',
                    'wrap_after'  => '</nav>',
                    'before'      => '',
                    'after'       => '',
                    'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
                )); 
                ?>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar Area -->
            <aside id="shop-sidebar-container" class="hidden lg:block lg:w-1/4 flex-shrink-0 fixed lg:sticky inset-0 z-[60] lg:z-0 lg:top-24 bg-black/50 lg:bg-transparent transition-opacity duration-300 opacity-0 lg:opacity-100 pointer-events-none lg:pointer-events-auto">
                <div id="shop-sidebar-content" class="w-80 lg:w-full h-full lg:h-auto bg-gray-50 lg:bg-transparent p-6 lg:p-0 overflow-y-auto lg:overflow-visible transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-xl lg:shadow-none relative">
                    <!-- Close Button Mobile -->
                    <button id="mobile-filter-close" class="lg:hidden absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    
                    <div class="shop-sidebar-inner">
                        <?php 
                        /**
                         * Hook: woocommerce_sidebar.
                         * @hooked woocommerce_get_sidebar - 10
                         */
                        do_action( 'woocommerce_sidebar' ); 
                        ?>
                    </div>
                </div>
            </aside>

            <!-- Main Product Area -->
            <div class="lg:w-3/4">
                
                <?php if ( woocommerce_product_loop() ) : ?>
                    
                    <!-- Toolbar: Sorting & Results -->
                    <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm mb-6 border border-gray-100">
                        <!-- Desktop Toolbar -->
                        <div class="hidden md:flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-bold text-gray-500 uppercase tracking-wider leading-none">Sort By:</span>
                                <div class="catalog-ordering-wrapper">
                                    <?php woocommerce_catalog_ordering(); ?>
                                </div>
                            </div>
                            <div class="text-sm text-gray-400 font-medium">
                                <?php woocommerce_result_count(); ?>
                            </div>
                        </div>

                        <!-- Mobile Toolbar -->
                        <div class="flex md:hidden items-center justify-between gap-2">
                            <button id="mobile-filter-toggle" class="flex items-center justify-center gap-2 border-2 border-secondary text-secondary font-bold px-3 py-2 rounded-lg text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="2" y1="14" x2="6" y2="14"/><line x1="10" y1="8" x2="14" y2="8"/><line x1="18" y1="16" x2="22" y2="16"/></svg>
                                FILTERS
                            </button>
                            <div class="catalog-ordering-wrapper flex-grow">
                                <?php woocommerce_catalog_ordering(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                        <?php
                        while ( have_posts() ) :
                            the_post();
                            /**
                             * Hook: woocommerce_shop_loop.
                             */
                            do_action( 'woocommerce_shop_loop' );
                            wc_get_template_part( 'content', 'product' );
                        endwhile;
                        ?>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12">
                        <?php 
                        /**
                         * Hook: woocommerce_after_shop_loop.
                         * @hooked woocommerce_pagination - 10
                         */
                        do_action( 'woocommerce_after_shop_loop' ); 
                        ?>
                    </div>

                <?php else : ?>
                    <div class="bg-white rounded-2xl p-20 text-center shadow-sm border border-gray-100">
                        <div class="max-w-xs mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-gray-200 mx-auto mb-6"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <h2 class="text-xl font-bold text-gray-800 mb-2">No products found</h2>
                            <p class="text-gray-500">Try adjusting your filters or search terms to find what you're looking for.</p>
                            <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="inline-block mt-8 bg-secondary text-white font-bold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all">
                                Return to Shop
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 * @hooked woocommerce_output_content_wrapper_end - 10
 */
do_action( 'woocommerce_after_main_content' );

get_footer();
