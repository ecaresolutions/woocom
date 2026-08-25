<?php
/**
 * The main template file
 *
 * @package Woocom
 */

get_header();
?>

	<main id="primary" class="site-main">
        <?php if ( class_exists( 'WooCommerce' ) && is_front_page() && ! is_home() ) : ?>
        <?php if ( get_option('show_hero_section', '1') === '1' ) : ?>
        <!-- Hero Section -->
        <div class="bg-gray-50 pt-4 sm:pt-6 pb-6 sm:pb-12">
            <div class="container-fluid mx-auto w-full md:container px-4">
                <div class="flex flex-col lg:flex-row gap-6 hero-section-row">
                    <!-- Left Slider (80.5% Width on Desktop) -->
                    <div class="w-full lg:w-[80.5%] hero-slider-col relative group overflow-hidden flex-grow">
                        <div class="swiper hero-swiper">
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
                                    <div class="swiper-slide bg-transparent">
                                        <a href="<?php echo esc_url($link); ?>" class="block w-full aspect-[16/7] md:aspect-[24/8] hero-slide-link overflow-hidden rounded-2xl bg-slate-100 shadow-sm border border-slate-200/50">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="Slider Image" class="w-full h-full object-cover transition-transform duration-500 hover:scale-[1.02] rounded-2xl">
                                        </a>
                                    </div>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            
                            <!-- Slider Pagination -->
                            <div class="swiper-pagination hero-pagination !bottom-3 lg:!bottom-6 !left-0 lg:!left-6 !text-center lg:!text-left !w-full lg:!w-auto px-4"></div>
                            
                            <!-- Slider Navigation -->
                            <button class="swiper-button-prev hero-prev absolute left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 bg-white/90 hover:bg-white text-gray-800 rounded-full flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100 cursor-pointer border-none outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button class="swiper-button-next hero-next absolute right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 bg-white/90 hover:bg-white text-gray-800 rounded-full flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100 cursor-pointer border-none outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Sidebar Banner (Desktop only, 19.5% Width) -->
                    <?php 
                    $sidebar_banner = get_option('hero_side_banner', '');
                    $sidebar_link = get_option('hero_side_banner_link', '#');
                    if ($sidebar_banner) :
                    ?>
                    <div class="hidden lg:block lg:w-[19.5%] hero-sidebar-col flex-shrink-0">
                        <a href="<?php echo esc_url($sidebar_link); ?>" class="block w-full hero-sidebar-link overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-200/50">
                            <img src="<?php echo esc_url($sidebar_banner); ?>" alt="Sidebar Banner" class="w-full h-auto block rounded-2xl hero-sidebar-img">
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Store Highlights/Features Section -->
                <div class="woocom-features-container">
                    <div class="woocom-features-grid">
                        <!-- Item 1 -->
                        <div class="woocom-feature-item">
                            <div class="woocom-feature-icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="woocom-feature-icon">
                                    <rect x="2" y="10" width="16" height="11" rx="2" fill="currentColor" opacity="0.3"/>
                                    <rect x="4" y="6" width="16" height="11" rx="2" fill="currentColor" opacity="0.6"/>
                                    <rect x="6" y="2" width="16" height="11" rx="2" fill="currentColor"/>
                                    <text x="14" y="10" fill="white" font-size="8" font-family="sans-serif" font-weight="bold" text-anchor="middle">$</text>
                                </svg>
                            </div>
                            <div>
                                <h4 class="woocom-feature-title">Competitive Price</h4>
                                <p class="woocom-feature-desc">Get The Best Prices Everyday</p>
                            </div>
                        </div>
                        <!-- Item 2 -->
                        <div class="woocom-feature-item">
                            <div class="woocom-feature-icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="woocom-feature-icon">
                                    <path d="M12 1L14.4 3.7L17.9 3.1L18.8 6.5L22.2 7.7L21.3 11.2L23 14.4L20.8 17.2L21 20.8L17.5 21L15.6 24L12 22.8L8.4 24L6.5 21L3 20.8L3.2 17.2L1 14.4L2.7 11.2L1.8 7.7L5.2 6.5L6.1 3.1L9.6 3.7L12 1Z" fill="currentColor"/>
                                    <path d="m9 12.5l2 2 5-5" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="woocom-feature-title">Authentic Products</h4>
                                <p class="woocom-feature-desc">Secured with Brand Warranty</p>
                            </div>
                        </div>
                        <!-- Item 3 -->
                        <div class="woocom-feature-item">
                            <div class="woocom-feature-icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="woocom-feature-icon">
                                    <rect x="2" y="5" width="20" height="14" rx="2" fill="currentColor"/>
                                    <rect x="2" y="8" width="20" height="3" fill="white"/>
                                    <rect x="5" y="13" width="3" height="2" rx="0.5" fill="white" opacity="0.8"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="woocom-feature-title">Easy & Secured Payment</h4>
                                <p class="woocom-feature-desc">Pre-payment, Cash on Delivery</p>
                            </div>
                        </div>
                        <!-- Item 4 -->
                        <div class="woocom-feature-item">
                            <div class="woocom-feature-icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="woocom-feature-icon">
                                    <path d="M2 5C2 3.89 2.89 3 4 3H16C17.11 3 18 3.89 18 5V14H2V5Z" fill="currentColor"/>
                                    <path d="M18 7H21C22.11 7 23 7.89 23 9V14H18V7Z" fill="currentColor"/>
                                    <circle cx="6" cy="18" r="3" fill="currentColor"/>
                                    <circle cx="6" cy="18" r="1.2" fill="white"/>
                                    <circle cx="17" cy="18" r="3" fill="currentColor"/>
                                    <circle cx="17" cy="18" r="1.2" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="woocom-feature-title">Fast Delivery</h4>
                                <p class="woocom-feature-desc">Rapid delivery At Your Doorstep</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                $bottom_banner = get_option('hero_bottom_banner', '');
                $bottom_banner_link = get_option('hero_bottom_banner_link', '#');
                if ($bottom_banner) :
                ?>
                <!-- Bottom Banner Section -->
                <div class="mt-8">
                    <a href="<?php echo esc_url($bottom_banner_link); ?>" class="block w-full overflow-hidden rounded-2xl bg-white shadow-sm border border-slate-200/50">
                        <img src="<?php echo esc_url($bottom_banner); ?>" alt="Promotion Banner" class="w-full h-auto block rounded-2xl">
                    </a>
                </div>
                <?php endif; ?>

                <!-- Flash Sale Section -->
                <?php
                $flash_product_ids = array();
                for ($i = 1; $i <= 5; $i++) {
                    $pid = intval(get_option('woocom_flash_sale_p' . $i));
                    if ($pid > 0) {
                        $flash_product_ids[] = $pid;
                    }
                }
                
                if (empty($flash_product_ids)) {
                    $flash_product_ids = wc_get_product_ids_on_sale();
                }
                if (!empty($flash_product_ids)) :
                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 5,
                        'post__in'       => $flash_product_ids,
                        'orderby'        => 'post__in',
                    );
                    $loop = new WP_Query($args);
                    if ($loop->have_posts()) :
                ?>
                <style>
                    .flash-sale-badge {
                        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    }
                    .flash-time-box {
                        background: #0f172a;
                        color: #ffffff;
                        border-radius: 6px;
                        padding: 6px 12px;
                        font-family: monospace;
                        font-weight: 800;
                        font-size: 15px;
                        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
                        display: inline-block;
                        min-width: 38px;
                        text-align: center;
                    }
                    .flash-sale-grid {
                        display: grid !important;
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                        gap: 16px !important;
                    }
                    @media (min-width: 640px) {
                        .flash-sale-grid {
                            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                        }
                    }
                    @media (min-width: 768px) {
                        .flash-sale-grid {
                            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                        }
                    }
                    @media (min-width: 1024px) {
                        .flash-sale-grid {
                            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                        }
                    }
                </style>
                <div class="mt-12">
                    <!-- Header -->
                    <div class="flex flex-row items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200/60">
                        <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                            <!-- Title -->
                            <div class="flex items-center gap-2">
                                <span class="text-yellow-500 animate-pulse">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6"><path d="M13 2L3 14h9l-1 9 10-12h-9l1-9z"/></svg>
                                </span>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight uppercase">Flash Sale</h3>
                            </div>
                            
                            <!-- Timer -->
                            <div class="flex items-center gap-2">
                                <span class="flash-time-box" id="flash-hours">00</span>
                                <span class="text-slate-800 font-extrabold text-lg">:</span>
                                <span class="flash-time-box" id="flash-minutes">00</span>
                                <span class="text-slate-800 font-extrabold text-lg">:</span>
                                <span class="flash-time-box" id="flash-seconds">00</span>
                            </div>
                        </div>

                        <!-- Shop More Button -->
                        <div class="flex-shrink-0">
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>?on_sale=1" class="inline-flex items-center gap-2 text-primary border border-primary/20 bg-white hover:bg-primary hover:text-white rounded-full px-5 py-2 text-xs font-bold transition-all shadow-sm whitespace-nowrap" style="white-space: nowrap !important; display: inline-flex !important;">
                                <span>Shop More</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="flex-shrink-0"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="flash-sale-grid">
                        <?php
                        while ($loop->have_posts()) : $loop->the_post();
                            global $product;
                            $product_id = get_the_ID();
                            $image_url = wp_get_attachment_image_url($product->get_image_id(), 'medium') ?: wc_placeholder_img_src();
                            
                            // Prices
                            $reg_price = $product->get_regular_price();
                            $sale_price = $product->get_sale_price();
                            
                            // Calculate discount percentage
                            $discount = 0;
                            if ($reg_price > 0 && $sale_price > 0) {
                                $discount = round((($reg_price - $sale_price) / $reg_price) * 100);
                            }
                            
                            // Fake items sold
                            $total_stock = 50;
                            $sold_count = ($product_id % 20) + 15; // Stable fake sold count between 15 and 35
                            $sold_percent = round(($sold_count / $total_stock) * 100);
                        ?>
                        <?php wc_get_template_part( 'content', 'product' ); ?>
                        <?php endwhile; ?>
                </div>

                <script>
                    function updateFlashCountdown() {
                        const now = new Date();
                        const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
                        let diff = endOfDay - now;
                        if (diff < 0) diff = 0;
                        
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        
                        const hrsEl = document.getElementById('flash-hours');
                        const minsEl = document.getElementById('flash-minutes');
                        const secsEl = document.getElementById('flash-seconds');
                        
                        if(hrsEl) hrsEl.innerText = String(hours).padStart(2, '0');
                        if(minsEl) minsEl.innerText = String(minutes).padStart(2, '0');
                        if(secsEl) secsEl.innerText = String(seconds).padStart(2, '0');
                    }
                    setInterval(updateFlashCountdown, 1000);
                    updateFlashCountdown();
                </script>
                <?php
                    endif;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
        <?php endif; ?>


        <style>
            .featured-category-grid {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 12px !important;
            }
            @media (min-width: 640px) {
                .featured-category-grid {
                    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                    gap: 16px !important;
                }
            }
            @media (min-width: 768px) {
                .featured-category-grid {
                    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                    gap: 18px !important;
                }
            }
            @media (min-width: 1024px) {
                .featured-category-grid {
                    grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
                    gap: 20px !important;
                }
            }
            @media (max-width: 639px) {
                .featured-category-grid > div:nth-child(n+7) {
                    display: none !important;
                }
            }
        </style>

        <?php if ( get_option('show_featured_categories', '1') === '1' ) : ?>
        <!-- Featured Categories -->
        <div class="py-8 sm:py-12 bg-transparent">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between mb-6 sm:mb-10">
                    <div>
                        <h2 class="text-[20px] sm:text-[26px] font-extrabold text-slate-800 leading-tight">Featured Categories</h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-1">Get your favorite gadgets from top categories</p>
                    </div>
                </div>

                <div class="featured-category-grid justify-center items-stretch">
                    <?php
                    $args = array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                        'meta_query' => array(
                            array(
                                'key'     => '_show_in_featured',
                                'value'   => 'yes',
                                'compare' => '='
                            )
                        )
                    );
                    $featured_cats = get_terms( $args );
                    
                    if ( ! empty( $featured_cats ) && ! is_wp_error( $featured_cats ) ) :
                        foreach ( $featured_cats as $cat ) :
                            $link = get_term_link( $cat );
                            $name = $cat->name;
                            
                            // Get WooCommerce Category Thumbnail
                            $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                            $image_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : wc_placeholder_img_src();
                    ?>
                        <div class="flex items-center justify-center">
                            <a href="<?php echo esc_url($link); ?>" class="flex flex-col items-center group/cat w-full text-center">
                                <div class="w-[85px] h-[85px] xs:w-[100px] xs:h-[100px] sm:w-[120px] sm:h-[120px] bg-slate-50 hover:bg-slate-100/50 border border-slate-100/60 hover:border-primary/20 rounded-2xl flex items-center justify-center p-3 sm:p-4 shadow-sm hover:shadow-md transition-all duration-300">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($name); ?>" class="w-full h-full object-contain transition-transform duration-500 group-hover/cat:scale-110">
                                </div>
                                <p class="mt-3 line-clamp-2 text-xs sm:text-sm font-semibold text-slate-600 group-hover/cat:text-primary transition-colors leading-tight max-w-[85px] xs:max-w-[100px] sm:max-w-[120px]"><?php echo esc_html($name); ?></p>
                            </a>
                        </div>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (get_option('woocom_show_just_for_you', '1') === '1') : ?>
        <!-- Just For You Section -->
        <div class="py-8 sm:py-12 bg-[#F9F9F9]">
            <div class="container mx-auto px-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8 sm:mb-12 border-b border-gray-200 pb-4 relative">
                    <div class="relative flex-shrink-0">
                        <?php 
                        $jfy_title = trim(get_option('woocom_just_for_you_title')); 
                        if (empty($jfy_title)) {
                            $jfy_title = 'Just For You';
                        }
                        ?>
                        <h2 class="text-[18px] sm:text-[22px] font-bold text-[#253D4E] whitespace-nowrap"><?php echo esc_html($jfy_title); ?></h2>
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
                    <div id="foryou-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-4">
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
                             <div class="foryou-item h-full flex flex-col" data-categories="<?php echo esc_attr($cat_data); ?>" style="display: <?php echo $index < 10 ? 'flex' : 'none !important'; ?>;">
                                    <?php wc_get_template_part( 'content', 'product' ); ?>
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
                        <button id="foryou-load-more" class="border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold py-2.5 px-8 rounded-[6px] transition-all duration-300 flex items-center gap-2 text-[14px]">
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
                    border: 2px solid var(--color-primary, #2563EB);
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
                    color: var(--color-primary, #2563EB);
                    border-color: var(--color-primary, #2563EB);
                }
                .foryou-tab-btn.active {
                    background-color: var(--color-primary, #2563EB) !important;
                    border-color: var(--color-primary, #2563EB) !important;
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
                                item.style.setProperty('display', 'flex', 'important');
                                shownCount++;
                            } else {
                                item.style.setProperty('display', 'none', 'important');
                            }
                        } else {
                            // Filter items by category slug
                            if (itemCats.includes(activeSlug)) {
                                item.style.setProperty('display', 'flex', 'important');
                            } else {
                                item.style.setProperty('display', 'none', 'important');
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
                                item.style.setProperty('display', 'flex', 'important');
                            });
                            loadMoreBtn.style.display = 'none';
                        }
                    });
                }

                // Initial filter run to set up correct layout on load
                filterProducts();
            });
            </script>
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
                                    <?php wc_get_template_part( 'content', 'product' ); ?>
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
        </div>
        <?php else : ?>
            <!-- Standard Blog / Archive Layout (when not static front page or WooCommerce not active) -->
            <div class="py-12 bg-gray-50">
                <div class="container mx-auto px-4 max-w-4xl">
                    <header class="page-header mb-8 pb-4 border-b border-gray-200">
                        <h1 class="page-title text-3xl font-bold text-gray-800">
                            <?php
                            if ( is_home() && ! is_front_page() ) {
                                single_post_title();
                            } elseif ( is_search() ) {
                                printf( esc_html__( 'Search Results for: %s', 'woocom' ), '<span>' . get_search_query() . '</span>' );
                            } elseif ( is_archive() ) {
                                the_archive_title( '<span class="archive-title-label">', '</span>' );
                            } else {
                                esc_html_e( 'Blog', 'woocom' );
                            }
                            ?>
                        </h1>
                    </header>

                    <?php if ( have_posts() ) : ?>
                        <div class="space-y-8">
                            <?php
                            while ( have_posts() ) : the_post();
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-200'); ?>>
                                <header class="entry-header mb-4">
                                    <?php the_title( sprintf( '<h2 class="entry-title text-2xl font-bold text-gray-800 hover:text-primary transition-colors"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                                    <div class="entry-meta text-xs text-gray-500 mt-2">
                                        <span class="posted-on">Posted on <?php echo get_the_date(); ?></span>
                                        <span class="byline"> by <?php the_author(); ?></span>
                                    </div>
                                </header>
                                
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div class="post-thumbnail my-4 rounded-lg overflow-hidden max-h-96">
                                        <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover')); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="entry-content text-gray-600 leading-relaxed mb-4">
                                    <?php
                                    if ( is_single() ) {
                                        the_content();
                                    } else {
                                        the_excerpt();
                                    }
                                    ?>
                                </div>
                                
                                <?php if ( ! is_single() ) : ?>
                                    <footer class="entry-footer">
                                        <a href="<?php the_permalink(); ?>" class="text-primary font-semibold hover:underline text-sm">Read More →</a>
                                    </footer>
                                <?php endif; ?>
                            </article>
                            <?php
                            endwhile;
                            
                            the_posts_navigation( array(
                                'prev_text' => esc_html__( 'Older posts', 'woocom' ),
                                'next_text' => esc_html__( 'Newer posts', 'woocom' ),
                            ) );
                            ?>
                        </div>
                    <?php else : ?>
                        <section class="no-results not-found bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                            <header class="page-header">
                                <h2 class="page-title text-2xl font-bold text-gray-800 mb-4"><?php esc_html_e( 'Nothing Found', 'woocom' ); ?></h2>
                            </header>
                            <div class="page-content">
                                <p class="text-gray-600 mb-6"><?php esc_html_e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'woocom' ); ?></p>
                                <?php get_search_form(); ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
	</main><!-- #main -->

<?php
get_footer();




