<?php
/**
 * The main template file
 *
 * @package Woocom
 */

get_header();
?>

	<main id="primary" class="site-main">
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
        <?php if ( get_option('show_hero_section', '1') === '1' ) : ?>
        <!-- Hero Section -->
        <div class="bg-gray-50 pt-4 sm:pt-6 pb-6 sm:pb-12">
            <div class="container mx-auto px-4">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Left Slider (Full Width) -->
                    <div class="w-full relative group overflow-hidden rounded-2xl shadow-sm bg-white">
                        <div class="swiper hero-swiper h-full">
                            <div class="swiper-wrapper">
                                <?php 
                                $raw_slides = get_option('woocom_hero_slides', '');
                                $slides = array();
                                if ($raw_slides) {
                                    $slides = json_decode($raw_slides, true) ?: array();
                                }
                                
                                // Fallback to legacy options if empty
                                if (empty($slides)) {
                                    $b1 = get_option('hero_banner_1', '');
                                    $b2 = get_option('hero_banner_2', '');
                                    if ($b1) $slides[] = array('image' => $b1, 'link' => get_option('hero_banner_1_link', '#'));
                                    if ($b2) $slides[] = array('image' => $b2, 'link' => get_option('hero_banner_2_link', '#'));
                                }
                                
                                if (!empty($slides)) :
                                    foreach ($slides as $slide) :
                                        $image = isset($slide['image']) ? $slide['image'] : '';
                                        $link = isset($slide['link']) ? $slide['link'] : '#';
                                        
                                        // Support attachment ID or URL
                                        $image_url = is_numeric($image) ? wp_get_attachment_image_url($image, 'full') : $image;
                                        if (empty($image_url)) continue;
                                ?>
                                    <div class="swiper-slide">
                                        <a href="<?php echo esc_url($link); ?>" class="block w-full h-full">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="Slider Image" class="w-full h-full object-cover">
                                        </a>
                                    </div>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            
                            <!-- Slider Pagination -->
                            <div class="swiper-pagination hero-pagination !bottom-3 lg:!bottom-6 !left-0 lg:!left-6 !text-center lg:!text-left !w-full lg:!w-auto px-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>


        <?php if ( get_option('show_featured_categories', '1') === '1' ) : ?>
        <!-- Featured Categories -->
        <div class="py-6 sm:py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8 sm:mb-12">
                    <h2 class="text-[20px] sm:text-[28px] font-bold text-[#253D4E]">Featured Categories</h2>
                </div>

                <div class="relative group">
                    <div class="swiper category-swiper pb-12 pt-4">
                        <div class="swiper-wrapper">
                            <?php
                            $featured_categories = woocom_get_featured_categories();
                            
                            // Fallback to top categories if none selected
                            if (empty($featured_categories) || is_wp_error($featured_categories)) {
                                $featured_categories = get_terms(array(
                                    'taxonomy'   => 'product_cat',
                                    'number'     => 10,
                                    'orderby'    => 'count',
                                    'order'      => 'DESC',
                                    'hide_empty' => true,
                                ));
                            }
                            
                            if (!empty($featured_categories) && !is_wp_error($featured_categories)) :
                                foreach ($featured_categories as $category) :
                                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                                    $image = wp_get_attachment_url($thumbnail_id);
                                    if (!$image) {
                                        $image = wc_placeholder_img_src();
                                    }
                            ?>
                                <div class="swiper-slide !w-36 md:!w-48">
                                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="flex flex-col items-center group/cat">
                                        <div class="w-full aspect-square bg-white rounded-[7px] border border-gray-100 flex items-center justify-center p-2 group-hover/cat:-translate-y-1 transition-all duration-300">
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>" class="max-w-full max-h-full object-contain">
                                        </div>
                                        <span class="mt-4 font-bold text-gray-700 group-hover/cat:text-primary transition-colors text-sm md:text-base"><?php echo esc_html($category->name); ?></span>
                                    </a>
                                </div>
                            <?php
                                endforeach;
                            endif; ?>
                        </div>
                    <!-- Category Pagination -->
                    <div class="swiper-pagination category-pagination !static !mt-4"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( get_option('show_top_selling', '1') === '1' ) : ?>
        <!-- Featured Products (Top Selling) -->
        <div class="top-selling-products py-10 sm:py-16 bg-transparent">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8 sm:mb-12 max-w-2xl mx-auto">
                    <?php 
                    $ts_title = trim(get_option('woocom_top_selling_title')); 
                    if (empty($ts_title)) {
                        $ts_title = 'Featured Products';
                    }
                    ?>
                    <h2 class="text-[20px] sm:text-[28px] font-bold text-[#253D4E] font-family-baloo"><?php echo esc_html($ts_title); ?></h2>
                    


                    <p class="text-gray-500 text-sm sm:text-base leading-relaxed">We offer a wide range of organic foods, carefully sourced for quality, freshness, and authenticity.</p>
                </div>

                <div class="relative group">
                    <div class="swiper featured-swiper pb-16">
                        <div class="swiper-wrapper">
                            <?php
                                $top_selling_products = woocom_get_top_selling_products();
                                if ($top_selling_products->have_posts()) :
                                    while ($top_selling_products->have_posts()) : $top_selling_products->the_post();
                                        global $product;
                                        if (!$product) continue;

                                        $image_url = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
                                        if (!$image_url) $image_url = wc_placeholder_img_src();
                                        
                                        $title = get_the_title();
                                        $price = $product->get_price();
                                        $regular_price = $product->get_regular_price();
                                        $save_amount = 0;
                                        if ($product->is_type('variable')) {
                                            $variation_prices = $product->get_variation_prices(true);
                                            $max_save = 0;
                                            foreach ($variation_prices['regular_price'] as $var_id => $var_regular) {
                                                $var_sale = isset($variation_prices['sale_price'][$var_id]) ? (float) $variation_prices['sale_price'][$var_id] : 0;
                                                $var_regular = (float) $var_regular;
                                                if ($var_regular > $var_sale && $var_sale > 0) {
                                                    $save = $var_regular - $var_sale;
                                                    if ($save > $max_save) $max_save = $save;
                                                }
                                            }
                                            $save_amount = $max_save;
                                        } else {
                                            if ($regular_price && $price && (float) $regular_price > (float) $price) {
                                                $save_amount = (float) $regular_price - (float) $price;
                                            }
                                        }
                                        $request_type = function_exists('woocom_get_product_request_type') ? woocom_get_product_request_type($product) : '';
                                        
                                        // Dynamic Texts
                                        $text_add_to_cart = get_option('woocom_text_add_to_cart', 'Add To Cart') ?: 'Add To Cart';
                                        $text_buy_now     = get_option('woocom_text_buy_now', 'Buy Now') ?: 'Buy Now';
                                        $text_see_details = get_option('woocom_text_see_details', 'See Details') ?: 'See Details';
                                        $text_stock_out   = get_option('woocom_text_stock_out', 'Stock Out') ?: 'Stock Out';
                                        $text_pre_order   = get_option('woocom_text_pre_order', 'Pre Order') ?: 'Pre Order';

                                        // Stock Status Flags
                                        $is_in_stock = $product->is_in_stock();
                                        $is_on_backorder = $product->is_on_backorder() || ( $product->managing_stock() && $product->get_stock_quantity() <= 0 && $product->backorders_allowed() );
                                        $is_variable = $product->is_type('variable');
                                ?>
                                 <div class="swiper-slide !h-auto">
                                     <div class="bg-white rounded-[4px] border border-gray-200 p-2 sm:p-3 h-full flex flex-col group/card transition-shadow duration-300 relative overflow-hidden">
                                         <?php if ($request_type && function_exists('woocom_render_stock_request_badge')) : ?>
                                             <div class="absolute top-0 left-0 z-10">
                                                 <?php echo woocom_render_stock_request_badge($request_type); ?>
                                             </div>
                                         <?php endif; ?>
                                         
                                         <!-- Image -->
                                         <div class="relative w-full pt-[100%] mb-2 bg-gray-50/30 rounded overflow-hidden group-img-wrapper">
                                             <div class="absolute inset-0 flex items-center justify-center p-0">
                                                 <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                                     <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" class="max-w-full max-h-full object-contain scale-110 mx-auto">
                                                 </a>
                                             </div>
                                             <button type="button" class="woocom-quick-view-btn absolute bottom-2 left-1/2 -translate-x-1/2 bg-white/95 hover:bg-primary hover:text-white text-gray-800 text-[10px] sm:text-[11px] font-extrabold px-3 py-1.5 rounded-full shadow-md transition-all duration-300 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer z-10" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                                                 <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                 Quick View
                                             </button>
                                         </div>

                                         <!-- Info -->
                                         <div class="flex-grow">
                                             <h3 class="text-[14px] sm:text-[18px] font-medium text-[#253D4E] leading-tight line-clamp-2 mb-0.5">
                                                 <a href="<?php the_permalink(); ?>" class="hover:text-secondary transition-colors"><?php the_title(); ?></a>
                                             </h3>
                                             <div class="flex items-center gap-1 sm:gap-1.5 mb-2 mt-0 flex-wrap">
                                                 <span class="text-secondary font-bold text-[13px] sm:text-[16px]">
                                                     <?php echo $product->get_price_html(); ?>
                                                 </span>
                                             </div>
                                         </div>
                                         <!-- Action Button -->
                                         <?php if ($request_type && function_exists('woocom_render_stock_request_form')) : ?>
                                             <div class="w-full mt-auto">
                                                 <?php echo woocom_render_stock_request_form($product->get_id(), $request_type, 'archive'); ?>
                                             </div>
                                         <?php else : ?>
                                             <div class="w-full mt-auto flex justify-center">
                                                 <?php if (!$is_in_stock && !$is_on_backorder) : ?>
                                                     <button disabled class="w-full border border-gray-300 bg-gray-100 text-gray-400 font-bold py-1.5 sm:py-2.5 rounded-[4px] text-center text-[13px] sm:text-[15px] cursor-not-allowed">
                                                         <?php echo esc_html($text_stock_out); ?>
                                                     </button>
                                                 <?php elseif ($is_on_backorder) : ?>
                                                     <button type="button" class="woocom-pre-order-btn w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[4px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" data-product-title="<?php echo esc_attr($title); ?>">
                                                         <?php echo esc_html($text_pre_order); ?>
                                                     </button>
                                                 <?php elseif ($is_variable) : ?>
                                                     <a href="<?php echo esc_url($product->get_permalink()); ?>" class="w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[4px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2">
                                                         <?php echo esc_html($text_see_details); ?>
                                                     </a>
                                                 <?php else : ?>
                                                     <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="woocom-custom-add-to-cart w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[4px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2" rel="nofollow">
                                                         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 sm:w-4.5 sm:h-4.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                                         <span class="truncate">Add To Cart</span>
                                                     </a>
                                                 <?php endif; ?>
                                             </div>
                                         <?php endif; ?>
                                     </div>
                                 </div>
                                <?php
                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                                ?>
                        </div>
                        <!-- Swiper Pagination -->
                        <div class="swiper-pagination featured-pagination !static !mt-8"></div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .featured-swiper {
                padding: 12px 8px !important;
                margin: -12px -8px !important;
            }
            .featured-swiper:not(.swiper-initialized) .swiper-wrapper { display: flex; overflow: hidden; }
            .featured-swiper:not(.swiper-initialized) .swiper-slide { width: 20%; flex-shrink: 0; padding: 0 10px; }
            @media (max-width: 1024px) { .featured-swiper:not(.swiper-initialized) .swiper-slide { width: 28.5%; } }
            @media (max-width: 768px) { .featured-swiper:not(.swiper-initialized) .swiper-slide { width: 40%; } }
            @media (max-width: 640px) { .featured-swiper:not(.swiper-initialized) .swiper-slide { width: 50%; } }
            
            .featured-pagination .swiper-pagination-bullet {
                width: 8px; height: 8px; background: #e0e0e0; opacity: 1; margin: 0 5px !important; transition: all 0.3s;
            }
            .featured-pagination .swiper-pagination-bullet-active { background: #70A342 !important; width: 24px; border-radius: 4px; }
            
            /* Inline Hover Visibility Fix */
            a.woocom-custom-add-to-cart:hover,
            a.add_to_cart_button:hover,
            button.woocom-pre-order-btn:hover,
            a.woocom-pre-order-btn:hover {
                background-color: var(--color-secondary, #F7A501) !important;
                background: var(--color-secondary, #F7A501) !important;
                color: #ffffff !important;
                border-color: var(--color-secondary, #F7A501) !important;
            }
            a.woocom-custom-add-to-cart:hover *,
            a.add_to_cart_button:hover * {
                color: #ffffff !important;
                stroke: #ffffff !important;
            }
        </style>
        <?php endif; ?>

        <!-- Latest Products Section -->
        <?php
        $latest_orderby = get_option('woocom_latest_orderby', 'date');
        $latest_order   = get_option('woocom_latest_order', 'DESC');

        $latest_args = array(
            'post_type'      => 'product',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'orderby'        => $latest_orderby,
            'order'          => $latest_order
        );

        if ( 'price' === $latest_orderby ) {
            $latest_args['orderby']  = 'meta_value_num';
            $latest_args['meta_key'] = '_price';
        } elseif ( 'sales' === $latest_orderby ) {
            $latest_args['orderby']  = 'meta_value_num';
            $latest_args['meta_key'] = 'total_sales';
            $latest_args['order']    = 'DESC';
        }

        $latest_query = new WP_Query($latest_args);
        if ($latest_query->have_posts()) :
        ?>
        <div class="latest-products pt-20 pb-10 sm:py-16 bg-[#FBF9F5]">
            <div class="h-8 sm:hidden"></div>
            <div class="container mx-auto px-4">
                <div class="text-center mb-8 sm:mb-12 max-w-2xl mx-auto">
                    <h2 class="text-[20px] sm:text-[28px] font-bold text-[#253D4E] font-family-baloo">Latest Products</h2>
                    


                    <p class="text-gray-500 text-sm sm:text-base leading-relaxed">Discover our latest products, freshly added with quality and care just for you.</p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-2 gap-3 sm:gap-6">
                    <?php
                    while ($latest_query->have_posts()) : $latest_query->the_post();
                        global $product;
                        if (!$product) continue;
                        
                        $image_url = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
                        if (!$image_url) $image_url = wc_placeholder_img_src();
                        
                        $title = get_the_title();
                        $price = $product->get_price();
                        $regular_price = $product->get_regular_price();
                        $request_type = function_exists('woocom_get_product_request_type') ? woocom_get_product_request_type($product) : '';
                        $is_in_stock = $product->is_in_stock();
                        $is_on_backorder = $product->is_on_backorder() || ( $product->managing_stock() && $product->get_stock_quantity() <= 0 && $product->backorders_allowed() );
                        $is_variable = $product->is_type('variable');
                        
                        // Text Translations
                        $text_add_to_cart = get_option('woocom_text_add_to_cart', 'Add To Cart') ?: 'Add To Cart';
                        $text_see_details = get_option('woocom_text_see_details', 'See Details') ?: 'See Details';
                        $text_stock_out   = get_option('woocom_text_stock_out', 'Stock Out') ?: 'Stock Out';
                        $text_pre_order   = get_option('woocom_text_pre_order', 'Pre Order') ?: 'Pre Order';
                    ?>
                        <div class="latest-product-card bg-white rounded-[20px] sm:rounded-[30px] border border-gray-100/80 p-3 sm:p-5 flex gap-3 sm:gap-6 items-stretch group/card shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
                            <!-- Image (Left/Top) -->
                            <div class="latest-product-image-container aspect-square flex-shrink-0 bg-white rounded-none border border-gray-100/60 overflow-hidden relative p-2 sm:p-3 flex items-center justify-center">
                                 <?php if ($product->is_on_sale()) : ?>
                                     <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#70A342] text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 sm:px-3 sm:py-1 rounded-full z-10" style="background-color: var(--color-primary, #70A342) !important;">SALE</span>
                                 <?php endif; ?>
                                 <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                     <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-contain rounded-none">
                                 </a>
                            </div>
                            <!-- Info (Right/Bottom) -->
                            <div class="latest-product-info-container flex-grow flex flex-col justify-between sm:justify-center gap-1 sm:gap-1.5 min-w-0 pt-1 sm:py-1 sm:pl-2">
                                 <div>
                                     <!-- Title -->
                                     <h3 class="text-[13px] sm:text-[20px] font-bold text-[#253D4E] leading-tight mb-0.5 line-clamp-2">
                                         <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors"><?php echo esc_html($title); ?></a>
                                     </h3>
                                     <!-- Category -->
                                     <span class="text-[11px] sm:text-[13px] text-gray-400 mb-0.5 block font-medium">
                                         <?php 
                                         $cat_terms = get_the_terms(get_the_ID(), 'product_cat');
                                         echo (!empty($cat_terms) && !is_wp_error($cat_terms)) ? esc_html($cat_terms[0]->name) : '&nbsp;'; 
                                         ?>
                                     </span>
                                     <!-- Price -->
                                     <div class="flex items-center gap-1.5 mb-1 sm:mb-0.5 flex-wrap">
                                         <?php if ($regular_price && $regular_price > $price) : ?>
                                             <span class="text-[11px] sm:text-[14px] text-gray-400 line-through">৳<?php echo number_format((float)$regular_price, 2); ?></span>
                                         <?php endif; ?>
                                         <span class="text-[13px] sm:text-[20px] font-bold" style="color: var(--color-secondary, #F7A501);"><?php echo number_format((float)$price, 2); ?>৳</span>
                                     </div>
                                     <!-- Weight/Attributes -->
                                     <div class="hidden sm:block text-[12px] sm:text-[14px] text-gray-500 mb-0.5 font-semibold">
                                         <?php
                                         if ($product->has_weight()) {
                                             echo esc_html($product->get_weight()) . ' ' . esc_html(get_option('woocommerce_weight_unit'));
                                         } else {
                                             $weight_attr = $product->get_attribute('weight');
                                             if ($weight_attr) {
                                                 echo esc_html($weight_attr);
                                             } else {
                                                 echo '&nbsp;';
                                             }
                                         }
                                         ?>
                                     </div>
                                 </div>
                                 
                                 <!-- Action / Qty Selector (Desktop) -->
                                 <div class="latest-product-desktop-actions items-center gap-1.5 flex-nowrap mt-1">
                                     <?php if (!$is_variable && $is_in_stock && !$request_type) : ?>
                                         <!-- Qty controls -->
                                         <div class="flex items-center border border-gray-300 rounded-full overflow-hidden bg-white text-[10px] h-8 px-0.5 flex-shrink-0">
                                             <button type="button" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:bg-gray-100 font-bold qty-minus rounded-full">-</button>
                                             <input type="text" class="w-5 text-center border-none p-0 qty-input font-bold text-xs" value="1" readonly>
                                             <button type="button" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:bg-gray-100 font-bold qty-plus rounded-full">+</button>
                                         </div>
                                         <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="woocom-custom-add-to-cart bg-[#70A342] hover:bg-[#5f8c37] text-white font-bold h-8 px-2.5 sm:px-4 rounded-full text-[10px] sm:text-[12px] tracking-wide uppercase transition-all duration-300 flex items-center justify-center gap-1 flex-shrink-0" style="background-color: var(--color-primary, #70A342) !important; color: #fff !important;" rel="nofollow">
                                             <span class="whitespace-nowrap">ADD TO CART</span>
                                         </a>
                                     <?php else : ?>
                                         <?php if (!$is_in_stock && !$is_on_backorder) : ?>
                                             <button disabled class="h-8 px-3 rounded-full bg-gray-300 text-white font-bold text-[10px] sm:text-[12px] tracking-wider uppercase cursor-not-allowed flex-shrink-0">
                                                 <span class="whitespace-nowrap"><?php echo esc_html($text_stock_out); ?></span>
                                             </button>
                                         <?php elseif ($is_on_backorder) : ?>
                                             <button type="button" class="woocom-pre-order-btn h-8 px-3 rounded-full text-white font-bold text-[10px] sm:text-[12px] tracking-wider uppercase transition-colors flex-shrink-0" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" data-product-title="<?php echo esc_attr($title); ?>" style="background-color: var(--color-primary, #70A342) !important; color: #fff !important;">
                                                 <span class="whitespace-nowrap"><?php echo esc_html($text_pre_order); ?></span>
                                             </button>
                                         <?php else : ?>
                                             <a href="<?php echo esc_url($product->get_permalink()); ?>" class="h-8 px-3 rounded-full text-white font-bold text-[10px] sm:text-[12px] tracking-wider uppercase text-center flex items-center justify-center flex-shrink-0" style="background-color: var(--color-primary, #70A342) !important; color: #fff !important;">
                                                 <span class="whitespace-nowrap"><?php echo esc_html($text_see_details); ?></span>
                                             </a>
                                         <?php endif; ?>
                                     <?php endif; ?>
                                 </div>
                                 
                                 <!-- Action Buttons (Mobile) -->
                                 <div class="latest-product-mobile-actions mt-2">
                                     <?php if (!$is_in_stock && !$is_on_backorder) : ?>
                                         <button disabled class="w-full h-8 rounded-[4px] border border-gray-200 bg-gray-100 text-gray-400 font-bold text-[11px] uppercase cursor-not-allowed">
                                             <?php echo esc_html($text_stock_out); ?>
                                         </button>
                                     <?php elseif ($is_on_backorder) : ?>
                                         <button type="button" class="woocom-pre-order-btn w-full h-8 rounded-[4px] border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold text-[11px] uppercase transition-colors duration-300" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" data-product-title="<?php echo esc_attr($title); ?>">
                                             <?php echo esc_html($text_pre_order); ?>
                                         </button>
                                     <?php elseif ($is_variable) : ?>
                                         <a href="<?php echo esc_url($product->get_permalink()); ?>" class="w-full h-8 rounded-[4px] border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold text-[11px] uppercase flex items-center justify-center transition-all duration-300">
                                             <?php echo esc_html($text_see_details); ?>
                                         </a>
                                     <?php else : ?>
                                         <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="woocom-custom-add-to-cart w-full h-8 rounded-[4px] border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold text-[11px] uppercase flex items-center justify-center gap-1.5 transition-all duration-300" rel="nofollow">
                                             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                             <span>Add To Cart</span>
                                         </a>
                                     <?php endif; ?>
                                 </div>
                             </div>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
        <style>
            .latest-product-card {
                display: flex;
                flex-direction: column;
            }
            .latest-product-image-container {
                width: 100%;
            }
            .latest-product-info-container {
                width: 100%;
                display: flex;
                flex-direction: column;
            }
            @media (min-width: 640px) {
                .latest-product-card {
                    flex-direction: row !important;
                }
                .latest-product-image-container {
                    width: 38% !important;
                }
                .latest-product-info-container {
                    width: 62% !important;
                    padding-left: 8px !important;
                    justify-content: center !important;
                    gap: 6px !important;
                }
                .latest-product-desktop-actions {
                    display: flex !important;
                }
                .latest-product-mobile-actions {
                    display: none !important;
                }
            }
            @media (max-width: 639px) {
                .latest-product-card {
                    flex-direction: column !important;
                }
                .latest-product-image-container {
                    width: 100% !important;
                }
                .latest-product-info-container {
                    width: 100% !important;
                    padding-left: 0 !important;
                }
                .latest-product-desktop-actions {
                    display: none !important;
                }
                .latest-product-mobile-actions {
                    display: flex !important;
                }
            }
        </style>
        <?php endif; ?>

        <?php if ( get_option('show_category_sections', '1') === '1' ) : ?>
        <?php
        $category_sections = woocom_get_category_sections();
        $rendered_count = 0;
        
        if (!empty($category_sections) && !is_wp_error($category_sections)) :
            $is_even = false; // Alternate background colors

            foreach ($category_sections as $index => $category) :
                $bg_class = $is_even ? 'bg-white' : 'bg-[#F9F9F9]';
                
                // Query products for this category
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 10,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'term_id',
                            'terms'    => $category->term_id,
                        ),
                    ),
                );
                $products_query = new WP_Query($args);
                
                if ($products_query->have_posts()) :
                    $rendered_count++;
                    $is_even = !$is_even;
        ?>
        <!-- Dynamic Category Section: <?php echo esc_html($category->name); ?> -->
        <div class="py-8 sm:py-12 <?php echo $bg_class; ?> category-product-section">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between mb-8 sm:mb-12 border-b border-gray-200 pb-4 relative">
                    <div class="relative">
                        <h2 class="text-[20px] sm:text-[28px] font-bold text-[#253D4E]"><?php echo esc_html($category->name); ?></h2>
                        <div class="absolute -bottom-4 left-0 w-12 h-[2px] bg-secondary"></div>
                    </div>
                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="text-secondary font-bold text-[13px] flex items-center gap-1 hover:translate-x-1 transition-transform uppercase tracking-wider underline underline-offset-4">
                        View All Items
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="relative group">
                    <div class="swiper dynamic-category-swiper pb-16" data-category-id="<?php echo esc_attr($category->term_id); ?>">
                        <div class="swiper-wrapper">
                            <?php
                            while ($products_query->have_posts()) : $products_query->the_post();
                                global $product;
                                
                                $price_html = $product->get_price_html();
                                $image_url = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
                                if (!$image_url) $image_url = wc_placeholder_img_src();
                                $request_type = function_exists('woocom_get_product_request_type') ? woocom_get_product_request_type($product) : '';
                            ?>
                                <div class="swiper-slide !h-auto">
                                    <div class="bg-white rounded-[4px] border border-gray-200 p-2 sm:p-3 h-full flex flex-col group/card hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
                                        <?php if ($request_type && function_exists('woocom_render_stock_request_badge')) : ?>
                                            <div class="absolute top-0 left-0 z-10">
                                                <?php echo woocom_render_stock_request_badge($request_type); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Image -->
                                         <div class="relative w-full pt-[100%] mb-2 bg-gray-50/30 rounded overflow-hidden group-img-wrapper">
                                            <div class="absolute inset-0 flex items-center justify-center p-0">
                                                <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" class="max-w-full max-h-full object-contain scale-110 mx-auto">
                                                </a>
                                            </div>
                                            <button type="button" class="woocom-quick-view-btn absolute bottom-2 left-1/2 -translate-x-1/2 bg-white/95 hover:bg-primary hover:text-white text-gray-800 text-[10px] sm:text-[11px] font-extrabold px-3 py-1.5 rounded-full shadow-md transition-all duration-300 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer z-10" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                Quick View
                                            </button>
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-grow">
                                            <h3 class="text-[14px] sm:text-[18px] font-medium text-[#253D4E] leading-tight line-clamp-2 mb-0.5">
                                                <a href="<?php the_permalink(); ?>" class="hover:text-secondary transition-colors"><?php the_title(); ?></a>
                                            </h3>
                                            <div class="flex items-center gap-1 sm:gap-1.5 mb-2 mt-0 flex-wrap">
                                                <span class="text-secondary font-bold text-[13px] sm:text-[16px]">
                                                    <?php echo $product->get_price_html(); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <?php if ($request_type && function_exists('woocom_render_stock_request_form')) : ?>
                                            <?php echo woocom_render_stock_request_form($product->get_id(), $request_type, 'archive'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>" class="add_to_cart_button ajax_add_to_cart w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[4px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2 mt-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 sm:w-4.5 sm:h-4.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                                <span class="truncate">Add To Cart</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                        <!-- Pagination -->
                        <div class="swiper-pagination dynamic-pagination-<?php echo esc_attr($category->term_id); ?> !static !mt-10"></div>
                    </div>
                </div>
            </div>
            
            <style>
                .dynamic-category-swiper:not(.swiper-initialized) .swiper-wrapper {
                    display: flex;
                    overflow: hidden;
                }
                .dynamic-category-swiper:not(.swiper-initialized) .swiper-slide {
                    width: 20%;
                    flex-shrink: 0;
                    padding: 0 12px;
                }
                @media (max-width: 1280px) {
                    .dynamic-category-swiper:not(.swiper-initialized) .swiper-slide { width: 25%; }
                }
                @media (max-width: 1024px) {
                    .dynamic-category-swiper:not(.swiper-initialized) .swiper-slide { width: 33.33%; }
                }
                @media (max-width: 768px) {
                    .dynamic-category-swiper:not(.swiper-initialized) .swiper-slide { width: 40%; }
                }
                @media (max-width: 640px) {
                    .dynamic-category-swiper:not(.swiper-initialized) .swiper-slide { width: 50%; }
                }

                .dynamic-pagination-<?php echo esc_attr($category->term_id); ?> .swiper-pagination-bullet {
                    width: 10px;
                    height: 10px;
                    background: transparent;
                    border: 2px solid var(--color-secondary, #F7A501);
                    opacity: 1;
                    margin: 0 5px !important;
                }
                .dynamic-pagination-<?php echo esc_attr($category->term_id); ?> .swiper-pagination-bullet-active {
                    background: var(--color-secondary, #F7A501) !important;
                }
            </style>
        </div>
        <?php 
                endif; // end if have_posts
            endforeach; 
        endif; // end if category_sections
        endif; // end if show_category_sections
        ?>

        <?php if ( get_option('show_combo_offers', '1') === '1' ) : ?>
        <!-- Exclusive Combo Deals Section -->
        <div class="py-12">
            <div class="container mx-auto px-4">
                <div class="bg-[#FFF5F0] rounded-2xl p-5 sm:p-8">
                    <div class="flex items-center justify-between mb-8 pb-4 relative border-b border-orange-200/60 gap-2">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="bg-secondary p-1 sm:p-1.5 rounded text-white flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sm:w-[18px] sm:h-[18px]"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect width="20" height="5" x="2" y="7"></rect><line x1="12" x2="12" y1="22" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                            </div>
                            <h2 class="text-[17px] sm:text-[24px] font-bold text-[#253D4E] leading-tight"><?php echo esc_html(get_option('woocom_combo_title', 'Exclusive Combo Deals')); ?></h2>
                        </div>
                        <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="bg-secondary text-white font-bold text-[11px] sm:text-[14px] px-2.5 sm:px-5 py-2 sm:py-2.5 rounded flex items-center gap-1 hover:bg-secondary/90 transition-colors flex-shrink-0 whitespace-nowrap">
                            View All Combos &rarr;
                        </a>
                    </div>

                    <div class="relative group">
                        <div class="swiper combo-swiper pb-16">
                            <div class="swiper-wrapper">
                                <?php
                                $combo_bundles = woocom_get_combo_bundles();

                                if ( ! empty( $combo_bundles ) ) :
                                    foreach ( $combo_bundles as $b_index => $bundle ) :
                                        $b_title    = ! empty( $bundle['title'] )    ? $bundle['title']    : 'Combo Bundle';
                                        $b_price    = ! empty( $bundle['price'] )    ? $bundle['price']    : '';
                                        $b_image    = ! empty( $bundle['image'] )    ? $bundle['image']    : '';
                                        $b_products = ! empty( $bundle['products'] ) ? array_map( 'absint', (array) $bundle['products'] ) : array();

                                        // Gather product names and fallback image
                                        $product_names  = array();
                                        $fallback_image = '';
                                        foreach ( $b_products as $pid ) {
                                            $p = wc_get_product( $pid );
                                            if ( ! $p ) continue;
                                            $product_names[] = $p->get_name();
                                            if ( ! $fallback_image ) {
                                                $fallback_image = wp_get_attachment_image_url( $p->get_image_id(), 'woocommerce_thumbnail' );
                                            }
                                        }
                                        $display_image = $b_image ?: $fallback_image ?: wc_placeholder_img_src();
                                ?>
                                <div class="swiper-slide !h-auto">
                                    <div class="bg-white rounded-[4px] border border-gray-200 h-full flex flex-col group/card hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
                                        <div class="absolute top-0 right-0 bg-secondary text-white text-[10px] sm:text-[11px] font-bold px-2 sm:px-3 py-1 z-10">Combo Offer</div>

                                        <!-- Image -->
                                        <div class="relative w-full pt-[100%] bg-white overflow-hidden mt-6">
                                            <div class="absolute inset-0 flex items-center justify-center p-4">
                                                <img src="<?php echo esc_url( $display_image ); ?>" alt="<?php echo esc_attr( $b_title ); ?>" class="max-w-full max-h-full object-contain scale-105 mx-auto">
                                            </div>
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-grow p-4 pt-3 text-left flex flex-col">
                                            <h3 class="text-[14px] sm:text-[18px] font-medium text-[#253D4E] leading-tight mb-2">
                                                <?php echo esc_html( $b_title ); ?>
                                            </h3>

                                            <?php if ( ! empty( $product_names ) ) : ?>
                                            <div class="flex flex-wrap gap-1 mb-3">
                                                <?php foreach ( $product_names as $pname ) : ?>
                                                <span class="text-[10px] bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 rounded-full leading-snug"><?php echo esc_html( $pname ); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ( $b_price ) : ?>
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="text-secondary font-bold text-[15px] sm:text-[20px]">৳<?php echo esc_html( number_format( (float) $b_price ) ); ?></span>
                                            </div>
                                            <?php endif; ?>

                                            <form method="post" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mt-auto">
                                                <input type="hidden" name="woocom_combo_order" value="<?php echo esc_attr( $b_index ); ?>">
                                                <?php wp_nonce_field( 'woocom_combo_order', 'woocom_combo_nonce' ); ?>
                                                <button type="submit" class="block w-full bg-secondary text-white font-bold py-2 sm:py-2.5 rounded-[4px] text-center transition-all duration-300 text-[13px] sm:text-[14px] hover:bg-secondary/90">
                                                    Order Now
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            <!-- Pagination -->
                            <div class="swiper-pagination combo-pagination !static !mt-10"></div>
                        </div>
                    </div>
                </div>
                </div>
                <style>
                    .combo-swiper:not(.swiper-initialized) .swiper-wrapper { display: flex; overflow: hidden; }
                    .combo-swiper:not(.swiper-initialized) .swiper-slide { width: 20%; flex-shrink: 0; padding: 0 12px; }
                    @media (max-width: 1280px) { .combo-swiper:not(.swiper-initialized) .swiper-slide { width: 25%; } }
                    @media (max-width: 1024px) { .combo-swiper:not(.swiper-initialized) .swiper-slide { width: 33.33%; } }
                    @media (max-width: 768px) { .combo-swiper:not(.swiper-initialized) .swiper-slide { width: 50%; } }
                    @media (max-width: 640px) { .combo-swiper:not(.swiper-initialized) .swiper-slide { width: 80%; } }
                    
                    .combo-pagination .swiper-pagination-bullet {
                        width: 10px; height: 10px; background: transparent; border: 2px solid var(--color-secondary, #F7A501); opacity: 1; margin: 0 5px !important;
                    }
                    .combo-pagination .swiper-pagination-bullet-active { background: var(--color-secondary, #F7A501) !important; }
                </style>
        </div>
        <?php endif; ?>

        <?php if ( get_option('show_dual_banners', '1') === '1' ) : ?>
        <!-- Dual Banner Section -->
        <div class="py-8 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    $p1 = get_option('promo_banner_1');
                    $p1_link = get_option('promo_banner_1_link', '#');
                    $p2 = get_option('promo_banner_2');
                    $p2_link = get_option('promo_banner_2_link', '#');
                    ?>
                    <a href="<?php echo esc_url($p1_link); ?>" class="block overflow-hidden rounded-xl shadow-sm hover:shadow-md transition-shadow group">
                        <img src="<?php echo esc_url($p1 ? $p1 : ''); ?>" alt="Promo Banner 1" class="w-full h-auto object-cover group-hover:scale-[1.02] transition-transform duration-500">
                    </a>
                    <a href="<?php echo esc_url($p2_link); ?>" class="block overflow-hidden rounded-xl shadow-sm hover:shadow-md transition-shadow group">
                        <img src="<?php echo esc_url($p2 ? $p2 : ''); ?>" alt="Promo Banner 2" class="w-full h-auto object-cover group-hover:scale-[1.02] transition-transform duration-500">
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (get_option('woocom_show_just_for_you', '1') === '1') : ?>
        <!-- Just For You Section -->
        <div class="py-8 sm:py-12 bg-[#F9F9F9]">
            <div class="container mx-auto px-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8 sm:mb-12 border-b border-gray-200 pb-4 relative">
                    <div class="relative">
                        <?php 
                        $jfy_title = trim(get_option('woocom_just_for_you_title')); 
                        if (empty($jfy_title)) {
                            $jfy_title = 'Just For You';
                        }
                        ?>
                        <h2 class="text-[20px] sm:text-[28px] font-bold text-[#253D4E]"><?php echo esc_html($jfy_title); ?></h2>
                    </div>
                    <!-- Category Tabs -->
                    <div class="w-full lg:w-auto overflow-hidden foryou-tabs-wrapper">
                        <div class="flex flex-nowrap items-center gap-1.5 sm:gap-2 overflow-x-scroll no-scrollbar scroll-smooth -mx-4 px-4 lg:mx-0 lg:px-0 pb-1 lg:pb-0 w-full">
                            <button class="foryou-tab-btn active" data-slug="all">All</button>
                            <?php
                            $terms = get_terms( array(
                                'taxonomy'   => 'product_cat',
                                'hide_empty' => true,
                                'number'     => 6,
                            ) );
                            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
                                foreach ( $terms as $term ) :
                            ?>
                                <button class="foryou-tab-btn" data-slug="<?php echo esc_attr( $term->slug ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </button>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div id="foryou-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-4">
                        <?php
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => 40, // Load a good amount of products for the 'Load More' to work
                            'post_status' => 'publish',
                        );
                        $foryou_query = new WP_Query($args);
                        $index = 0;
                        
                        if ($foryou_query->have_posts()) :
                            while ($foryou_query->have_posts()) : $foryou_query->the_post();
                                global $product;
                                
                                $price_html = $product->get_price_html();
                                $image_url = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
                                if (!$image_url) $image_url = wc_placeholder_img_src();
                                $request_type = function_exists('woocom_get_product_request_type') ? woocom_get_product_request_type($product) : '';
                                
                                $product_cats = get_the_terms(get_the_ID(), 'product_cat');
                                $cat_slugs = array();
                                if (!empty($product_cats) && !is_wp_error($product_cats)) {
                                    foreach ($product_cats as $cat) {
                                        $cat_slugs[] = $cat->slug;
                                    }
                                }
                                $cat_data = implode(' ', $cat_slugs);
                        ?>
                            <div class="foryou-item h-full flex flex-col" data-categories="<?php echo esc_attr($cat_data); ?>" style="display: <?php echo $index < 10 ? 'flex' : 'none'; ?>;">
                                <div class="bg-white rounded-[4px] border border-gray-200 p-2 sm:p-3 h-full flex flex-col group/card hover:shadow-md transition-shadow duration-300 relative w-full overflow-hidden">
                                    <?php if ($request_type && function_exists('woocom_render_stock_request_badge')) : ?>
                                        <div class="absolute top-0 left-0 z-10">
                                            <?php echo woocom_render_stock_request_badge($request_type); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Image -->
                                    <div class="relative w-full pt-[100%] mb-2 bg-gray-50/30 rounded overflow-hidden group-img-wrapper">
                                        <div class="absolute inset-0 flex items-center justify-center p-0">
                                            <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" class="max-w-full max-h-full object-contain scale-110 mx-auto">
                                            </a>
                                        </div>
                                        <button type="button" class="woocom-quick-view-btn absolute bottom-2 left-1/2 -translate-x-1/2 bg-white/95 hover:bg-primary hover:text-white text-gray-800 text-[10px] sm:text-[11px] font-extrabold px-3 py-1.5 rounded-full shadow-md transition-all duration-300 opacity-0 translate-y-2 group-hover/img:opacity-100 group-hover/img:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer z-10" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Quick View
                                        </button>
                                    </div>

                                    <!-- Info -->
                                    <div class="flex-grow">
                                        <h3 class="text-[14px] sm:text-[16px] font-medium text-[#253D4E] leading-tight line-clamp-2 mb-0.5">
                                            <a href="<?php the_permalink(); ?>" class="hover:text-secondary transition-colors"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="flex items-center gap-1 sm:gap-1.5 mb-2 mt-0 flex-wrap">
                                            <span class="text-secondary font-bold text-[13px] sm:text-[16px]">
                                                <?php echo $product->get_price_html(); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Button -->
                                    <?php if ($request_type && function_exists('woocom_render_stock_request_form')) : ?>
                                        <?php echo woocom_render_stock_request_form($product->get_id(), $request_type, 'archive'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>" class="add_to_cart_button ajax_add_to_cart w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[4px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2 mt-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 sm:w-4.5 sm:h-4.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                            <span class="truncate">Add To Cart</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php 
                                $index++;
                            endwhile; 
                            wp_reset_postdata();
                        endif; 
                        ?>
                    </div>
                    
                    <!-- Load More Button -->
                    <div class="mt-10 flex justify-center">
                        <button id="foryou-load-more" class="border-2 border-secondary text-secondary hover:bg-secondary hover:text-white font-bold py-2.5 px-8 rounded-[4px] transition-all duration-300 flex items-center gap-2 text-[14px]">
                            Load More
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-refresh-cw"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
            
            <!-- Shared CSS for duplicated Swipers -->
            <style>
                .foryou-swiper:not(.swiper-initialized) .swiper-wrapper {
                    display: flex;
                    overflow: hidden;
                }
                .foryou-swiper:not(.swiper-initialized) .swiper-slide {
                    width: 20%;
                    flex-shrink: 0;
                    padding: 0 12px;
                }
                @media (max-width: 1280px) {
                    .foryou-swiper:not(.swiper-initialized) .swiper-slide { width: 25%; }
                }
                @media (max-width: 1024px) {
                    .foryou-swiper:not(.swiper-initialized) .swiper-slide { width: 33.33%; }
                }
                @media (max-width: 768px) {
                    .foryou-swiper:not(.swiper-initialized) .swiper-slide { width: 40%; }
                }
                @media (max-width: 640px) {
                    .foryou-swiper:not(.swiper-initialized) .swiper-slide { width: 50%; }
                }

                .foryou-pagination .swiper-pagination-bullet {
                    width: 10px;
                    height: 10px;
                    background: transparent;
                    border: 2px solid var(--color-secondary, #F7A501);
                    opacity: 1;
                    margin: 0 5px !important;
                }
                /* Custom styles for categories tabs scrollbar */
                .no-scrollbar::-webkit-scrollbar {
                    display: none;
                }
                .no-scrollbar {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                    -webkit-overflow-scrolling: touch;
                    overflow-x: scroll !important;
                }
                
                /* Custom styles for category tabs */
                .foryou-tab-btn {
                    background-color: #ffffff;
                    color: #4b5563;
                    border: 1px solid #e5e7eb;
                    padding: 8px 18px;
                    border-radius: 9999px;
                    font-size: 14px;
                    font-weight: 600;
                    white-space: nowrap;
                    transition: all 0.3s ease;
                    cursor: pointer;
                    display: inline-block;
                    flex-shrink: 0;
                }
                .foryou-tab-btn:hover {
                    background-color: #f9fafb;
                    color: var(--color-secondary, #F7A501);
                    border-color: var(--color-secondary, #F7A501);
                }
                .foryou-tab-btn.active {
                    background-color: var(--color-secondary, #F7A501) !important;
                    border-color: var(--color-secondary, #F7A501) !important;
                    color: #ffffff !important;
                }
            </style>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabs = document.querySelectorAll('.foryou-tab-btn');
                const items = document.querySelectorAll('.foryou-item');
                const loadMoreBtn = document.getElementById('foryou-load-more');
                let activeSlug = 'all';

                function filterProducts() {
                    let shownCount = 0;
                    const maxInitial = 10;

                    items.forEach((item) => {
                        const categoriesAttr = item.getAttribute('data-categories') || '';
                        const itemCats = categoriesAttr.split(' ');
                        
                        if (activeSlug === 'all') {
                            // Show first 10 items for 'All'
                            if (shownCount < maxInitial) {
                                item.style.display = 'flex';
                                shownCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        } else {
                            // Filter items by category slug
                            if (itemCats.includes(activeSlug)) {
                                item.style.display = 'flex';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });

                    // Manage Load More button display
                    if (loadMoreBtn) {
                        if (activeSlug === 'all' && items.length > maxInitial) {
                            loadMoreBtn.style.display = 'flex';
                        } else {
                            loadMoreBtn.style.display = 'none';
                        }
                    }
                }

                tabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        tabs.forEach(t => t.classList.remove('active'));
                        this.classList.add('active');

                        activeSlug = this.getAttribute('data-slug');
                        filterProducts();
                    });
                });

                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', function() {
                        if (activeSlug === 'all') {
                            items.forEach(item => {
                                item.style.display = 'flex';
                            });
                            loadMoreBtn.style.display = 'none';
                        }
                    });
                }
            });
            </script>
        <?php endif; ?>


        </div>
        <?php else : ?>
            <div class="container mx-auto px-4 py-20 text-center">
                <h2 class="text-2xl font-bold mb-4">Please activate WooCommerce</h2>
                <p>This theme requires WooCommerce to function properly.</p>
            </div>
        <?php endif; ?>
	</main><!-- #main -->

<?php
get_footer();
