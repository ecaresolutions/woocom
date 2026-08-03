<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
$main_product_id = $product->get_id();

/**
 * Determine if the product belongs to grocery/organic categories or if the active demo style is grocery.
 */
if ( ! function_exists( 'woocom_is_grocery_product' ) ) {
    function woocom_is_grocery_product( $product_id ) {
        // Standard grocery and food category slugs
        $grocery_cats = array( 'grocery', 'food', 'organic', 'oil', 'pure', 'dates', 'honey', 'spices', 'ghee', 'dry-fruits', 'fruits', 'vegetables', 'rice', 'dal', 'fish', 'meat' );
        if ( has_term( $grocery_cats, 'product_cat', $product_id ) ) {
            return true;
        }
        
        // Dynamic check: if the site primary customizer color is green (indicating a grocery layout demo import)
        $primary_color = get_option( 'woocom_primary_color', '#2563EB' );
        if ( $primary_color === '#056600' || $primary_color === '#1E5D02' ) {
            return true;
        }
        
        return false;
    }
}

if ( woocom_is_grocery_product( $main_product_id ) ) {
    // ==========================================
    // RENDER ORIGINAL GROCERY LAYOUT
    // ==========================================
    $request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
    
    /**
     * Hook: woocommerce_before_single_product.
     */
    do_action( 'woocommerce_before_single_product' );
    
    if ( post_password_required() ) {
        echo get_the_password_form();
        return;
    }
    ?>
    <div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'single-product-container bg-white rounded-xl shadow-sm overflow-hidden p-4 md:p-8', $product ); ?>>
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            <!-- Left Side: Gallery -->
            <div class="woocom-single-gallery lg:w-1/2 flex flex-col lg:flex-row gap-4">
                <?php
                $gallery_image_ids = $product->get_gallery_image_ids();
                $has_gallery       = ! empty( $gallery_image_ids );
                $attachment_ids    = $gallery_image_ids;
                $main_image_id     = $product->get_image_id();
                
                if ( $main_image_id ) {
                    array_unshift( $attachment_ids, $main_image_id );
                }
                ?>
                
                <!-- Main Image Slider -->
                <div class="woocom-main-slider-wrap flex-grow min-w-0 relative bg-white rounded-lg overflow-hidden border border-gray-200 group aspect-square">
                    <div id="main-image-slider" class="flex w-full h-full transition-transform duration-500 ease-in-out" data-current-index="0">
                        <?php
                        if ( $attachment_ids ) {
                            foreach ( $attachment_ids as $index => $attachment_id ) {
                                ?>
                                <div class="w-full h-full flex-shrink-0 flex items-center justify-center p-0" data-index="<?php echo $index; ?>" data-image-id="<?php echo $attachment_id; ?>">
                                    <?php echo wp_get_attachment_image( $attachment_id, 'large', false, array( 
                                        'class' => 'w-full h-full object-contain' 
                                    ) ); ?>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="w-full h-full flex-shrink-0 flex items-center justify-center p-0">
                                <?php echo wc_placeholder_img( 'large' ); ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    
                    <?php if ( is_array( $attachment_ids ) && count( $attachment_ids ) > 1 ) : ?>
                    <!-- Navigation Arrows -->
                    <button type="button" onclick="slidePrev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white shadow-md rounded-full flex items-center justify-center text-gray-400 hover:text-secondary opacity-50 group-hover:opacity-100 transition-opacity z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button type="button" onclick="slideNext()" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white shadow-md rounded-full flex items-center justify-center text-gray-400 hover:text-secondary opacity-50 group-hover:opacity-100 transition-opacity z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <?php endif; ?>
    
                    <?php if ( $request_type ) : ?>
                        <div class="absolute top-0 left-0 z-10">
                            <?php echo woocom_render_stock_request_badge( $request_type ); ?>
                        </div>
                    <?php endif; ?>
                </div>
    
                <?php if ( $has_gallery ) : ?>
                <!-- Thumbnails (Horizontal below Main Image) -->
                <div class="woocom-gallery-thumbnails flex flex-row gap-2.5 w-full flex-shrink-0 overflow-x-auto pb-2">
                    <?php
                    if ( $attachment_ids ) {
                        foreach ( $attachment_ids as $index => $attachment_id ) {
                            $thumbnail_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                            ?>
                            <div class="thumbnail-item cursor-pointer border <?php echo ( $index === 0 ) ? 'border-secondary' : 'border-gray-200'; ?> hover:border-secondary rounded-lg overflow-hidden transition-all w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 aspect-square p-0" 
                                 onclick="changeMainImage(this, <?php echo $index; ?>)">
                                <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="Product thumbnail" class="w-full h-full object-cover">
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php endif; ?>
            </div>
    
            <!-- Right Side: Info -->
            <div class="lg:w-1/2 flex flex-col">
                <h1 id="product-title" class="text-xl md:text-3xl font-bold text-gray-900 mb-2 leading-tight"><?php the_title(); ?></h1>
                
                <div class="flex items-center gap-3 mb-4 flex-wrap">
                    <span id="product-price" class="text-secondary font-bold text-2xl md:text-3xl leading-none">
                        <?php echo $product->get_price_html(); ?>
                    </span>
                    
                    <?php
                    $discount_label = '';
                    if ( $product->is_on_sale() ) :
                        $reg_p = (float)$product->get_regular_price();
                        $sal_p = (float)$product->get_price();
                        $savings = $reg_p - $sal_p;
                        $discount_label = $savings > 0 ? 'Save ৳' . round($savings) : '';
                    endif;
                    ?>
                    <div id="product-image-sale-badge" class="inline-block <?php echo $discount_label ? '' : 'hidden'; ?>" data-original-label="<?php echo esc_attr( $discount_label ); ?>">
                        <span class="bg-secondary text-white text-[12px] sm:text-[13px] font-bold px-3 py-1 rounded-full shadow-sm align-middle inline-block"><?php echo esc_html( $discount_label ); ?></span>
                    </div>
                </div>
    
                <?php 
                $short_description = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
                if ( $short_description ) : 
                ?>
                    <div class="product-short-description text-[16px] sm:text-[18px] mb-4 leading-relaxed font-normal" style="color: var(--color-primary, #1E5D02);">
                        <?php echo $short_description; ?>
                    </div>
                <?php endif; ?>
    
                <?php if ( $request_type ) : ?>
                    <div class="mb-4">
                        <?php echo woocom_render_stock_request_badge( $request_type ); ?>
                    </div>
                <?php endif; ?>
    
                <!-- Action Buttons Area -->
                <div class="product-actions-wrapper mb-6">
                    <?php woocommerce_template_single_add_to_cart(); ?>
                </div>
            </div>
        </div>
    
        <!-- Frequently Bought Together Section -->
        <?php
        $related_ids = get_post_meta($product->get_id(), '_fbt_ids', true);
        if (!empty($related_ids)) :
        ?>
        <div class="mt-8 bg-white border border-gray-100 rounded-xl p-4 md:p-5 shadow-sm overflow-hidden">
            <h3 class="text-[16px] font-bold text-[#253D4E] mb-3">Frequently bought together</h3>
            
            <div class="fbt-items-row flex flex-col lg:flex-row items-stretch justify-start gap-3">
                <div class="fbt-item fbt-product-card fbt-main-item flex flex-row items-center bg-white p-3 rounded-md border border-secondary relative group transition-all w-full"
                     data-base-price="<?php echo esc_attr($product->get_price()); ?>"
                     data-regular-price="<?php echo esc_attr($regular_price); ?>"
                     data-sale-price="<?php echo esc_attr($product->get_price()); ?>"
                     data-original-img="<?php echo esc_url(get_the_post_thumbnail_url($product->get_id(), 'thumbnail')); ?>">
                    <div class="fbt-product-thumb w-12 h-12 bg-white rounded flex-shrink-0 mr-3 flex items-center justify-center border border-gray-100 overflow-hidden p-0">
                        <img src="<?php echo get_the_post_thumbnail_url($product->get_id(), 'thumbnail'); ?>" class="fbt-main-img w-full h-full object-cover">
                    </div>
                    <div class="fbt-product-copy flex flex-col flex-grow min-w-0 pr-8 justify-center items-start text-left">
                        <h4 class="text-[12px] font-medium text-gray-700 leading-tight line-clamp-1 mb-0.5"><?php the_title(); ?></h4>
                        <div class="min-h-[16px] mb-0.5">
                            <span class="fbt-item-save text-[10px] font-semibold text-green-600 bg-green-50 px-1.5 py-0.5 rounded-full inline-block <?php echo ($main_savings > 0) ? '' : 'hidden'; ?>">
                                Save ৳<span class="save-amt"><?php echo round($main_savings); ?></span>
                            </span>
                        </div>
                        <div class="fbt-main-price-display text-[#1f2937] font-bold text-[13px] leading-none">৳<?php echo $product->get_price(); ?></div>
                    </div>
                    <label class="fbt-check-label absolute bottom-2 right-2 flex items-center justify-center pointer-events-none">
                        <input type="checkbox" checked disabled class="fbt-checkbox hidden">
                        <div class="custom-cb w-4 h-4 rounded-sm border border-secondary bg-secondary flex items-center justify-center transition-colors">
                            <svg class="custom-cb-tick w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </label>
                    <input type="hidden" class="fbt-price" value="<?php echo $product->get_price(); ?>">
                    <input type="hidden" class="fbt-regular-price" value="<?php echo $regular_price; ?>">
                    <input type="hidden" class="fbt-id" value="<?php echo $product->get_id(); ?>">
                </div>
    
                <div class="hidden lg:flex items-center justify-center text-gray-400 font-light text-xl">+</div>
    
                <?php 
                foreach ($related_ids as $index => $rel_id) : 
                    $rel_product = wc_get_product($rel_id);
                    if (!$rel_product) continue;
                    $rel_regular = (float)($rel_product->get_regular_price() ? $rel_product->get_regular_price() : $rel_product->get_price());
                    $rel_sale = (float)$rel_product->get_price();
                    $rel_savings = $rel_regular - $rel_sale;
                ?>
                <div class="fbt-item fbt-product-card flex flex-row items-center bg-white p-3 rounded-md border border-secondary relative group transition-all cursor-pointer w-full" onclick="toggleFbt(this)">
                    <div class="fbt-product-thumb w-12 h-12 bg-white rounded flex-shrink-0 mr-3 flex items-center justify-center border border-gray-100 overflow-hidden p-0">
                        <img src="<?php echo get_the_post_thumbnail_url($rel_id, 'thumbnail'); ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="fbt-product-copy flex flex-col flex-grow min-w-0 pr-8 justify-center items-start text-left">
                        <h4 class="text-[12px] font-medium text-gray-700 leading-tight line-clamp-1 mb-0.5"><?php echo $rel_product->get_name(); ?></h4>
                        <div class="min-h-[16px] mb-0.5">
                            <span class="fbt-item-save text-[10px] font-semibold text-green-600 bg-green-50 px-1.5 py-0.5 rounded-full inline-block <?php echo ($rel_savings > 0) ? '' : 'hidden'; ?>">
                                Save ৳<span class="save-amt"><?php echo round($rel_savings); ?></span>
                            </span>
                        </div>
                        <div class="text-[#1f2937] font-bold text-[13px] leading-none">৳<?php echo $rel_product->get_price(); ?></div>
                    </div>
                    <label class="fbt-check-label absolute bottom-2 right-2 flex items-center justify-center pointer-events-none">
                        <input type="checkbox" checked class="fbt-checkbox hidden">
                        <div class="custom-cb w-4 h-4 rounded-sm border border-secondary bg-secondary flex items-center justify-center transition-colors">
                            <svg class="custom-cb-tick w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </label>
                    <input type="hidden" class="fbt-price" value="<?php echo $rel_product->get_price(); ?>">
                    <input type="hidden" class="fbt-regular-price" value="<?php echo $rel_regular; ?>">
                    <input type="hidden" class="fbt-id" value="<?php echo $rel_id; ?>">
                </div>
    
                <?php if ($index < count($related_ids) - 1) : ?>
                    <div class="hidden lg:flex items-center justify-center text-gray-400 font-light text-xl">+</div>
                <?php endif; ?>
                <?php endforeach; ?>
    
                <div class="hidden lg:flex items-center justify-center text-gray-400 font-light text-xl">=</div>
    
                <div class="fbt-total-card bg-secondary rounded-md p-3 flex flex-col items-center justify-center text-white text-center flex-shrink-0 w-full lg:ml-2">
                    <div class="text-[18px] font-bold leading-none mb-0.5" id="fbt-total-display">৳<?php 
                        $total = (float)$product->get_price();
                        foreach($related_ids as $rid) {
                            $rp = wc_get_product($rid);
                            if($rp) $total += (float)$rp->get_price();
                        }
                        echo number_format($total, 2, '.', '');
                    ?></div>
                    <div class="text-[11px] mb-2 font-medium min-h-[14px] invisible" id="fbt-save-display">Save ৳ 0</div>
                    <button type="button" id="fbt-add-all" class="bg-transparent hover:bg-white/10 text-white text-[12px] font-bold py-1.5 px-3 rounded border border-white/80 transition-colors w-full leading-tight flex items-center justify-center gap-1 whitespace-nowrap">
                        Add <span id="fbt-count"><?php echo count($related_ids) + 1; ?></span> items to cart
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    
        <!-- Product Details & Reviews Stacked Sections -->
        <?php
        $video_url   = get_post_meta($product->get_id(), '_product_video_url', true);
        $has_video   = ! empty($video_url);
        $has_content = ! empty( trim( get_the_content() ) );
        ?>
        <div class="mt-16">
            <div class="flex items-center gap-2 mb-6 bg-white border border-gray-100 p-1 rounded-lg shadow-sm w-full lg:inline-flex lg:w-auto">
                <?php if ( $has_content ) : ?>
                    <a href="#product-details-sec" class="anchor-tab-btn active bg-secondary text-white shadow-sm px-5 py-2 rounded-md text-[13px] sm:text-[14px] font-bold transition-all whitespace-nowrap text-center">Description</a>
                <?php endif; ?>
                
                <?php if ( $has_video ) : ?>
                    <a href="#product-video-sec" class="anchor-tab-btn bg-transparent text-[#7E7E7E] px-5 py-2 rounded-md text-[13px] sm:text-[14px] font-bold hover:bg-gray-50 hover:text-[#7E7E7E] transition-all whitespace-nowrap text-center">Product Video</a>
                <?php endif; ?>
                
                <?php if ( comments_open() ) : ?>
                    <a href="#product-reviews-sec" class="anchor-tab-btn bg-transparent text-[#7E7E7E] px-5 py-2 rounded-md text-[13px] sm:text-[14px] font-bold hover:bg-gray-50 hover:text-[#7E7E7E] transition-all whitespace-nowrap text-center">Reviews (<?php echo $product->get_review_count(); ?>)</a>
                <?php endif; ?>
            </div>
    
            <div class="space-y-8 mt-2">
                <?php if ( $has_content ) : ?>
                    <div id="product-details-sec" class="bg-white border border-gray-100 rounded-xl p-6 md:p-10 shadow-sm scroll-mt-28">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">Product Details</h3>
                        <div class="prose max-w-none text-gray-600 leading-[1.8] text-[15px]"><?php the_content(); ?></div>
                    </div>
                <?php endif; ?>
    
                <?php if ( $has_video ) : ?>
                    <div id="product-video-sec" class="bg-white border border-gray-100 rounded-xl p-6 md:p-10 shadow-sm scroll-mt-28">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">Product Video</h3>
                        <?php 
                        $embed_url = '';
                        if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
                                $embed_url = 'https://www.youtube.com/embed/' . $match[1];
                            }
                        } elseif (strpos($video_url, 'vimeo.com') !== false) {
                            if (preg_match('%vimeo\.com/(?:video/)?([0-9]+)%i', $video_url, $match)) {
                                $embed_url = 'https://player.vimeo.com/video/' . $match[1];
                            }
                        }
                        
                        if ($embed_url) : ?>
                            <div class="aspect-video w-full rounded-xl overflow-hidden bg-black shadow-lg">
                                <iframe src="<?php echo esc_url($embed_url); ?>" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
    
                <?php if ( comments_open() ) : ?>
                    <div id="product-reviews-sec" class="bg-white border border-gray-100 rounded-xl p-6 md:p-10 shadow-sm scroll-mt-28">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">Customer Reviews</h3>
                        <div class="max-w-3xl"><?php comments_template(); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    
        <!-- Dynamic styles and scripts to keep the grocery look intact -->
        <style>
            .star-rating, .woocommerce .star-rating {
                font-size: 0 !important;
                width: 80px !important;
                height: 16px !important;
                display: inline-block !important;
                position: relative !important;
            }
            .star-rating::before, .woocommerce .star-rating::before {
                content: "★★★★★" !important;
                font-size: 14px !important;
                color: #E2E8F0 !important;
                position: absolute !important;
                letter-spacing: 2px !important;
                font-family: Arial, sans-serif !important;
            }
            .star-rating span, .woocommerce .star-rating span {
                position: absolute !important;
                overflow: hidden !important;
            }
            .star-rating span::before, .woocommerce .star-rating span::before {
                content: "★★★★★" !important;
                font-size: 14px !important;
                color: #F59E0B !important;
                position: absolute !important;
                letter-spacing: 2px !important;
                font-family: Arial, sans-serif !important;
            }
        </style>
        
        <script type="text/javascript">
            function changeMainImage(thumbnailEl, index) {
                const slider = document.getElementById('main-image-slider');
                if (!slider) return;
                slider.style.transform = `translateX(-${index * 100}%)`;
                slider.dataset.currentIndex = index;
                document.querySelectorAll('.thumbnail-item').forEach(item => {
                    item.classList.remove('border-secondary');
                    item.classList.add('border-gray-200');
                });
                thumbnailEl.classList.add('border-secondary');
            }
            function slidePrev() {
                const slider = document.getElementById('main-image-slider');
                if (!slider) return;
                const total = slider.children.length;
                let current = parseInt(slider.dataset.currentIndex || 0);
                current = (current - 1 + total) % total;
                const thumbs = document.querySelectorAll('.thumbnail-item');
                if (thumbs[current]) thumbs[current].click();
            }
            function slideNext() {
                const slider = document.getElementById('main-image-slider');
                if (!slider) return;
                const total = slider.children.length;
                let current = parseInt(slider.dataset.currentIndex || 0);
                current = (current + 1) % total;
                const thumbs = document.querySelectorAll('.thumbnail-item');
                if (thumbs[current]) thumbs[current].click();
            }
            function toggleFbt(el) {
                const cb = el.querySelector('.fbt-checkbox');
                const customCb = el.querySelector('.custom-cb');
                cb.checked = !cb.checked;
                if(cb.checked) {
                    el.classList.add('border-secondary');
                    el.classList.remove('border-gray-200', 'opacity-60');
                    if(customCb) customCb.classList.add('bg-secondary');
                } else {
                    el.classList.remove('border-secondary');
                    el.classList.add('border-gray-200', 'opacity-60');
                    if(customCb) customCb.classList.remove('bg-secondary');
                }
                updateFbtTotal();
            }
            function updateFbtTotal() {
                let total = 0;
                document.querySelectorAll('.fbt-item').forEach(item => {
                    const cb = item.querySelector('.fbt-checkbox');
                    if (cb && cb.checked) {
                        total += parseFloat(item.querySelector('.fbt-price').value) || 0;
                    }
                });
                document.getElementById('fbt-total-display').innerText = '৳' + total.toFixed(2);
            }
            
            // Touch zoom binding
            document.addEventListener('DOMContentLoaded', function() {
                const slider = document.getElementById('main-image-slider');
                if (slider) {
                    const slides = slider.children;
                    for (let i = 0; i < slides.length; i++) {
                        const slide = slides[i];
                        const img = slide.querySelector('img');
                        if (!img) continue;
                        img.style.transition = 'transform 0.15s ease-out';
                        img.style.cursor = 'zoom-in';
                        
                        slide.addEventListener('mousemove', function(e) {
                            if (window.matchMedia('(min-width: 1024px)').matches) {
                                const rect = slide.getBoundingClientRect();
                                const x = ((e.clientX - rect.left) / rect.width) * 100;
                                const y = ((e.clientY - rect.top) / rect.height) * 100;
                                img.style.transformOrigin = `${x}% ${y}%`;
                                img.style.transform = 'scale(2.5)';
                            }
                        });
                        slide.addEventListener('mouseleave', function() {
                            if (window.matchMedia('(min-width: 1024px)').matches) {
                                img.style.transform = 'scale(1)';
                            }
                        });
                    }
                }
            });
        </script>
    
        <!-- Related Products List (Grocery) -->
        <?php
        $args = array(
            'post_type'            => 'product',
            'posts_per_page'       => 5,
            'orderby'              => 'rand',
            'post__not_in'         => array( $product->get_id() ),
            'tax_query'            => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'ids' ) ),
                ),
            ),
        );
        $related_query = new WP_Query( $args );
        if ( $related_query->have_posts() ) : ?>
        <div class="mt-20">
            <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4 relative">
                <h2 class="text-[24px] font-bold text-[#253D4E]">Related Products</h2>
                <div class="absolute -bottom-[1px] left-0 w-12 h-[2px] bg-secondary"></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <?php while ( $related_query->have_posts() ) : $related_query->the_post(); 
                    global $product;
                    $image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src();
                ?>
                    <div class="bg-white rounded-[6px] border border-gray-200 p-3 h-full flex flex-col group/card hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
                        <div class="relative w-full pt-[100%] mb-2 bg-gray-50/30 rounded overflow-hidden">
                            <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-contain scale-110 mx-auto">
                            </a>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-[14px] font-medium text-[#253D4E] leading-tight line-clamp-2 mb-1">
                                <a href="<?php the_permalink(); ?>" class="hover:text-secondary transition-colors"><?php the_title(); ?></a>
                            </h3>
                            <div class="flex items-center gap-1.5 mb-3 w-full">
                                <span class="text-secondary font-bold text-[15px] flex justify-between w-full items-baseline">
                                    <?php echo $product->get_price_html(); ?>
                                </span>
                            </div>
                        </div>
                        <a href="?add-to-cart=<?php echo esc_attr( get_the_ID() ); ?>" class="w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-2 rounded-[6px] text-center transition-all duration-300 text-[14px] flex items-center justify-center gap-2 mt-auto">
                            Add To Cart
                        </a>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php
    do_action( 'woocommerce_after_single_product' );

} else {
    // ==========================================
    // RENDER NEW GADGET LAYOUT (Laracom copy)
    // ==========================================
    $request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
    
    /**
     * Hook: woocommerce_before_single_product.
     */
    do_action( 'woocommerce_before_single_product' );
    
    if ( post_password_required() ) {
        echo get_the_password_form();
        return;
    }
    
    // Brand extraction
    $product_title = $product->get_name();
    $brand = strtoupper( strtok( $product_title, ' ' ) );
    
    // Rating stats
    $rating_count = $product->get_rating_count();
    $review_count = $product->get_review_count();
    $average_rating = $product->get_average_rating();
    $rating_display = ! empty( $average_rating ) ? number_format( $average_rating, 1 ) : '4.9';
    $reviews_display = ! empty( $review_count ) ? $review_count : '24';
    
    // Price breakdown
    $price_html = $product->get_price_html();
    $regular_price = (float)($product->get_regular_price() ? $product->get_regular_price() : $product->get_price());
    $sale_price = (float)$product->get_price();
    $discount_pct = 0;
    if ( $regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price ) {
        $discount_pct = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
    }
    
    // Dynamic bulk price lists
    $price2 = '৳' . number_format( round( $sale_price * 0.94 ) );
    $price5 = '৳' . number_format( round( $sale_price * 0.93 ) );
    $price10 = '৳' . number_format( round( $sale_price * 0.90 ) );
    
    // Stock progress bar logic
    $stock_left = $product->get_stock_quantity();
    if ( null === $stock_left || $stock_left <= 0 ) {
        $hash = 0;
        for ( $i = 0; $i < strlen( $product_title ); $i++ ) {
            $hash = ord( $product_title[$i] ) + ( ( $hash << 5 ) - $hash );
        }
        $stock_left = 2 + ( abs( $hash ) % 7 );
    }
    
    // Specs layout setup
    $specs = array();
    $specs[] = array( 'label' => 'Name', 'value' => $product_title );
    
    $cats = wp_get_post_terms( $main_product_id, 'product_cat', array( 'fields' => 'names' ) );
    $specs[] = array( 'label' => 'Category', 'value' => ! empty( $cats ) ? $cats[0] : 'Premium Electronics' );
    
    $net_content = '1 Unit Premium Product, 1 Charging Cable, User Manual, Warranty Card';
    if ( stripos( $product_title, 'cable' ) !== false ) {
        $net_content = '1 Unit USB-C Cable, 1 Cable Strap, User Manual, Warranty Card';
    } elseif ( stripos( $product_title, 'ear' ) !== false || stripos( $product_title, 'audio' ) !== false ) {
        $net_content = '1 Unit TWS Earbuds, 1 Charging Case, 1 USB-C Cable, Eartips, User Manual';
    }
    $specs[] = array( 'label' => 'Net Content', 'value' => $net_content );
    
    $dims = $product->get_dimensions( false );
    $dimensions = ! empty( $dims ) && ! empty( $dims['length'] ) ? $dims['length'] . 'mm x ' . $dims['width'] . 'mm x ' . $dims['height'] . 'mm' : '150mm x 80mm x 15mm';
    $specs[] = array( 'label' => 'Product Dimensions', 'value' => $dimensions );
    
    $specs[] = array( 'label' => 'MRP (Inclusive All Taxes)', 'value' => '৳' . $product->get_price() );
    
    $country = 'China';
    if ( stripos( $product_title, 'ear' ) !== false || stripos( $product_title, 'cable' ) !== false ) {
        $country = 'Vietnam';
    }
    $specs[] = array( 'label' => 'Country Of Origin', 'value' => $country );
    $specs[] = array( 'label' => 'Marketed By', 'value' => 'Laracom Gadget Bangladesh, Dhaka 1212' );
    $specs[] = array( 'label' => 'Manufactured by', 'value' => 'Laracom Imports Ltd.' );
    $specs[] = array( 'label' => 'For consumer complaints contact us at', 'value' => 'support@laracomgadget.com.bd' );
    
    // Reviews comments
    $comments = get_comments( array(
        'post_id'   => $main_product_id,
        'status'    => 'approve',
        'post_type' => 'product',
    ) );
    
    $stars_breakdown = array( 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 );
    $total_review_ratings = 0;
    foreach ( $comments as $comment ) {
        $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
        if ( $rating >= 1 && $rating <= 5 ) {
            $stars_breakdown[$rating]++;
            $total_review_ratings += $rating;
        }
    }
    $average_score = $review_count > 0 ? round( $total_review_ratings / $review_count, 2 ) : 4.89;
    
    // Media attachment list
    $gallery_image_ids = $product->get_gallery_image_ids();
    $attachment_ids = $gallery_image_ids;
    $main_image_id = $product->get_image_id();
    if ( $main_image_id ) {
        array_unshift( $attachment_ids, $main_image_id );
    }
    ?>
    <div class="single-product-container bg-[#f7fafb] text-slate-800 dark:bg-[#0a0f12] dark:text-slate-100 p-0">
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: 'var(--color-primary, #2563EB)',
                            secondary: 'var(--color-secondary, #F7A501)',
                            slate: {
                                50: '#f8fafc',
                                100: '#f1f5f9',
                                105: '#f1f5f9',
                                200: '#e2e8f0',
                                202: '#e2e8f0',
                                205: '#cbd5e1',
                                300: '#cbd5e1',
                                350: '#cbd5e1',
                                400: '#94a3b8',
                                404: '#94a3b8',
                                405: '#64748b',
                                450: '#64748b',
                                455: '#475569',
                                500: '#64748b',
                                505: '#64748b',
                                600: '#475569',
                                650: '#475569',
                                700: '#334155',
                                750: '#334155',
                                800: '#1e293b',
                                805: '#1e293b',
                                808: '#0f172a',
                                850: '#1e293b',
                                855: '#0f172a',
                                900: '#0f172a',
                                905: '#0f172a',
                                950: '#020617',
                                955: '#020617',
                            }
                        }
                    }
                }
            };
        </script>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;950&display=swap" rel="stylesheet" />
    
        <style>
            .font-poppins { font-family: 'Poppins', sans-serif !important; }
            .text-primary { color: var(--color-primary, #2563EB) !important; }
            .bg-primary { background-color: var(--color-primary, #2563EB) !important; }
            .border-primary { border-color: var(--color-primary, #2563EB) !important; }
            .bg-primary-light { background-color: color-mix(in srgb, var(--color-primary, #2563EB) 8%, transparent) !important; }
            
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            
            #main-image-slider-laracom {
                transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .woocommerce-message, .woocommerce-error, .woocommerce-info {
                background-color: #FFFFFF !important;
                border-top: 3px solid var(--color-primary, #2563EB) !important;
                border-radius: 12px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
                padding: 18px 24px !important;
                margin-bottom: 24px !important;
                font-family: 'Poppins', sans-serif !important;
                font-size: 14px !important;
            }
    
            .woocommerce-product-details__short-description { display: none !important; }
            .woocommerce-tabs, .product_meta { display: none !important; }
            form.cart { margin: 0 !important; padding: 0 !important; }
            form.cart table.variations { margin-bottom: 0 !important; width: 100% !important; }
            form.cart table.variations tr { display: flex !important; flex-direction: column !important; gap: 6px !important; margin-bottom: 12px !important; border: 0 !important; }
            form.cart table.variations td.label { padding: 0 !important; font-size: 11px !important; font-weight: 700 !important; color: #64748B !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; }
            form.cart table.variations td.value { padding: 0 !important; }
            form.cart .quantity, form.cart .single_add_to_cart_button { display: none !important; }
            .premium-product-actions { display: none !important; }
        </style>
    
        <!-- Top Navigation -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6 font-poppins px-4 py-2">
            <div class="flex items-center gap-2.5">
                <button 
                    onclick="window.location.href='<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>'"
                    class="flex md:hidden h-8 w-8 items-center justify-center rounded-full border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-350 shadow-sm active:scale-90 cursor-pointer"
                    aria-label="Back to Shop"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                </button>
    
                <nav class="flex items-center gap-1.5 text-xs md:text-sm text-slate-505 dark:text-slate-400 font-medium">
                    <span class="cursor-pointer hover:text-primary transition-colors" onclick="window.location.href='<?php echo esc_url( home_url() ); ?>'">Home</span>
                    <span class="text-slate-300 dark:text-slate-700">/</span>
                    <span class="cursor-pointer hover:text-primary transition-colors" onclick="window.location.href='<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>'">Shop</span>
                    <span class="text-slate-300 dark:text-slate-700">/</span>
                    <span class="text-slate-700 dark:text-slate-202 truncate max-w-[140px] sm:max-w-xs"><?php echo esc_html( $product_title ); ?></span>
                </nav>
            </div>
            
            <button 
                onclick="window.location.href='<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>'"
                class="hidden md:flex items-center gap-1.5 px-4.5 py-1.5 rounded-full border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 text-xs md:text-sm font-bold shadow-sm hover:border-primary hover:text-primary dark:hover:border-primary transition-all duration-200 active:scale-95 cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-4 w-4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Back to Shop</span>
            </button>
        </div>
    
        <!-- Main Grid Container -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10 font-poppins px-4">
            
            <!-- Left Side: Gallery -->
            <div class="lg:col-span-6 flex flex-col gap-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-0 flex items-center justify-center shadow-[0_8px_30px_rgba(0,0,0,0.015)] overflow-hidden relative group w-full aspect-square">
                    <div id="main-image-slider-laracom" class="flex w-full h-full" data-current-index="0">
                        <?php
                        if ( $attachment_ids ) {
                            foreach ( $attachment_ids as $index => $attachment_id ) {
                                $filterClass = "";
                                if ( $index === 1 ) $filterClass = "hue-rotate-[180deg] saturate-[1.2]";
                                if ( $index === 2 ) $filterClass = "hue-rotate-[290deg] saturate-[1.3]";
                                if ( $index === 3 ) $filterClass = "brightness-[0.75] contrast-[1.1] saturate-[0.1]";
                                ?>
                                <div class="w-full h-full flex-shrink-0 flex items-center justify-center p-0 slider-slide overflow-hidden" data-index="<?php echo $index; ?>">
                                    <img src="<?php echo esc_url( wp_get_attachment_image_url( $attachment_id, 'large' ) ); ?>" 
                                         alt="Product Image <?php echo $index; ?>" 
                                         class="w-full h-full object-contain select-none pointer-events-none transition-all duration-500 group-hover:scale-105 <?php echo $filterClass; ?> gallery-main-img">
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="w-full h-full flex-shrink-0 flex items-center justify-center p-0">
                                <img src="<?php echo esc_url( wc_placeholder_img_src( 'large' ) ); ?>" class="w-full h-full object-contain">
                            </div>
                            <?php
                        }
                        ?>
                    </div>
    
                    <?php if ( count( $attachment_ids ) > 1 ) : ?>
                    <button type="button" onclick="slidePrevLaracom()" class="absolute left-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 dark:bg-slate-900/80 shadow-md rounded-full flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-primary hover:bg-white transition-all cursor-pointer opacity-0 group-hover:opacity-100 z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button type="button" onclick="slideNextLaracom()" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 dark:bg-slate-900/80 shadow-md rounded-full flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-primary hover:bg-white transition-all cursor-pointer opacity-0 group-hover:opacity-100 z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <?php endif; ?>
                    
                    <?php if ( $request_type ) : ?>
                        <div class="absolute top-4 left-4 z-10">
                            <?php echo woocom_render_stock_request_badge( $request_type ); ?>
                        </div>
                    <?php endif; ?>
                </div>
    
                <!-- Thumbnails -->
                <div class="grid grid-cols-4 gap-3">
                    <?php
                    for ( $num = 0; $num < 4; $num++ ) {
                        $attachment_id = isset( $attachment_ids[$num] ) ? $attachment_ids[$num] : ( isset( $attachment_ids[0] ) ? $attachment_ids[0] : 0 );
                        if ( ! $attachment_id ) continue;
                        
                        $filterClass = "";
                        if ( $num === 1 ) $filterClass = "hue-rotate-[180deg] saturate-[1.2]";
                        if ( $num === 2 ) $filterClass = "hue-rotate-[290deg] saturate-[1.3]";
                        if ( $num === 3 ) $filterClass = "brightness-[0.75] contrast-[1.1] saturate-[0.1]";
                        ?>
                        <button
                            type="button"
                            onclick="changeMainImageLaracom(this, <?php echo $num; ?>)"
                            class="thumbnail-btn-laracom aspect-square rounded-xl border-2 p-1 bg-white dark:bg-slate-900 overflow-hidden flex items-center justify-center transition-all <?php echo ( $num === 0 ) ? 'border-primary shadow-sm' : 'border-slate-100 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'; ?>"
                        >
                            <img 
                                src="<?php echo esc_url( wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) ); ?>" 
                                alt="Thumbnail <?php echo $num + 1; ?>"
                                class="w-full h-full object-contain rounded-lg <?php echo $filterClass; ?>"
                            />
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
    
            <!-- Right Side: Info -->
            <div class="lg:col-span-6 flex flex-col gap-5">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-xs font-black tracking-widest text-primary bg-primary-light px-3 py-1 rounded-md uppercase">
                        <?php echo esc_html( $brand ); ?>
                    </span>
                    
                    <div class="flex items-center gap-1.5 text-xs text-slate-505 dark:text-slate-400 font-bold bg-slate-100 dark:bg-slate-850 px-3 py-1 rounded-md">
                        <div class="flex items-center text-amber-500">
                            <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                            <?php endfor; ?>
                        </div>
                        <span><?php echo esc_html( $rating_display ); ?> Rating</span>
                        <span class="text-slate-300 dark:text-slate-700">|</span>
                        <span><?php echo esc_html( $reviews_display ); ?> Reviews</span>
                    </div>
    
                    <button
                        type="button"
                        onclick="toggleCompareLaracom(<?php echo $main_product_id; ?>, '<?php echo esc_js( $product_title ); ?>')"
                        id="compare-btn-<?php echo $main_product_id; ?>"
                        class="compare-toggle-btn flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-md border cursor-pointer transition-all active:scale-95 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-350 border-slate-200 dark:border-slate-800 hover:border-primary/40"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-3 w-3 btn-plus-icon"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="h-3 w-3 btn-check-icon hidden"><polyline points="20 6 9 17 4 12"/></svg>
                        <span class="btn-text">Add to Compare</span>
                    </button>
                </div>
    
                <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-slate-100 leading-tight">
                    <?php echo esc_html( $product_title ); ?>
                </h1>
    
                <div class="bg-slate-50/50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-850 rounded-xl p-4 flex items-center justify-between select-none">
                    <div class="flex items-baseline gap-2">
                        <span id="laracom-price-display" class="text-3xl font-black text-primary">
                            ৳<?php echo esc_html( number_format( $sale_price ) ); ?>
                        </span>
                        <?php if ( $regular_price > $sale_price ) : ?>
                            <span class="text-sm md:text-base text-slate-400 dark:text-slate-500 line-through">
                                ৳<?php echo esc_html( number_format( $regular_price ) ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ( $discount_pct > 0 ) : ?>
                        <span id="laracom-save-badge" class="text-xs bg-red-50 text-red-600 dark:bg-red-955/20 dark:text-red-400 px-2.5 py-1 rounded font-bold border border-red-100 dark:border-red-950/30">
                            SAVE <?php echo $discount_pct; ?>% OFF
                        </span>
                    <?php endif; ?>
                </div>
    
                <div class="woocommerce-cart-form-container mt-2">
                    <?php woocommerce_template_single_add_to_cart(); ?>
                </div>
    
                <div class="flex flex-col gap-1.5 mt-2">
                    <div class="flex items-center justify-between text-xs font-bold text-red-500 dark:text-red-400 animate-pulse">
                        <span>🔥 Hurry! Only <span id="stock-left-qty"><?php echo $stock_left; ?></span> items left in stock</span>
                        <span>Selling Fast</span>
                    </div>
                    <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div 
                            id="stock-progress-bar"
                            class="h-full bg-gradient-to-r from-red-500 to-primary rounded-full transition-all duration-1000"
                            style="width: <?php echo ( $stock_left / 10 ) * 100; ?>%"
                        ></div>
                    </div>
                </div>
    
                <div class="flex flex-col gap-3 mt-2">
                    <div class="flex flex-wrap md:flex-nowrap items-center gap-3 w-full">
                        <div class="flex items-center justify-between border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-900 p-2 w-[120px] shrink-0 h-12">
                            <button type="button" class="quantity-minus flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                            <span class="qty-display text-base font-black text-slate-800 dark:text-slate-100 px-2">1</span>
                            <button type="button" class="quantity-plus flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                        </div>
    
                        <button
                            type="button"
                            id="laracom-add-to-cart"
                            class="flex-1 flex items-center justify-center gap-2 h-12 bg-primary hover:opacity-90 text-white text-xs md:text-sm font-bold uppercase rounded-xl tracking-wider transition-all duration-300 shadow-md hover:shadow-lg active:scale-95 cursor-pointer text-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4.5 w-4.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                            <span>Add to Cart</span>
                        </button>
    
                        <button
                            type="button"
                            id="laracom-buy-now"
                            class="hidden md:flex flex-1 items-center justify-center h-12 bg-slate-900 hover:bg-slate-950 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-black text-xs md:text-sm font-bold uppercase rounded-xl tracking-wider transition-all duration-300 shadow-sm active:scale-95 cursor-pointer text-center"
                        >
                            Buy it now
                        </button>
                    </div>
    
                    <button
                        type="button"
                        id="laracom-buy-now-mobile"
                        class="flex md:hidden w-full h-12 bg-slate-900 hover:bg-slate-955 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-black text-sm font-bold uppercase rounded-xl tracking-wider transition-all duration-300 shadow-sm active:scale-95 cursor-pointer items-center justify-center text-center"
                    >
                        Buy it now
                    </button>
                </div>
    
                <!-- Active Offers -->
                <div class="mt-3 font-poppins">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-3.5">
                        Active Offers
                    </h3>
                    <div class="flex overflow-x-auto sm:grid sm:grid-cols-3 gap-5 md:gap-4 select-none snap-x snap-mandatory pb-3 no-scrollbar scroll-smooth">
                        <!-- Card 1 -->
                        <div class="relative pt-3 snap-start shrink-0 w-[calc(60%-10px)] sm:w-auto">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 bg-primary text-white text-[9px] font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-sm z-10">
                                MOST POPULAR
                            </div>
                            <div 
                                onclick="setOfferQuantity(2)"
                                class="ticket-card cursor-pointer transition-all duration-300 active:scale-[0.98] border border-orange-100 hover:border-orange-200 bg-gradient-to-b from-orange-50/40 to-orange-100/10 dark:from-orange-955/10 dark:to-orange-900/5 rounded-2xl p-4 pt-5 flex flex-col items-center justify-between shadow-[0_4px_15px_rgba(254,105,5,0.02)] h-full overflow-hidden"
                                id="ticket-2"
                            >
                                <div class="text-center">
                                    <div class="text-xs md:text-sm font-bold text-slate-800 dark:text-slate-200">Buy 2 or more</div>
                                </div>
                                <div class="w-full border-t border-dashed border-orange-200 dark:border-orange-900/50 my-3.5 relative">
                                    <div class="absolute -left-[22px] -top-1.5 w-3 h-3 rounded-full bg-[#f7fafb] dark:bg-[#0a0f12] border-r border-orange-100 dark:border-orange-900/40"></div>
                                    <div class="absolute -right-[22px] -top-1.5 w-3 h-3 rounded-full bg-[#f7fafb] dark:bg-[#0a0f12] border-l border-orange-100 dark:border-orange-900/40"></div>
                                </div>
                                <div class="text-center w-full mt-auto">
                                    <div class="text-xs font-bold text-primary">Get 6% Off</div>
                                    <div class="text-sm md:text-base font-black text-slate-900 dark:text-slate-100 mt-1"><?php echo $price2; ?>/item</div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Card 2 -->
                        <div class="relative pt-3 snap-start shrink-0 w-[calc(60%-10px)] sm:w-auto">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 bg-[#10B981] text-white text-[9px] font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-sm z-10">
                                BEST VALUE
                            </div>
                            <div 
                                onclick="setOfferQuantity(5)"
                                class="ticket-card cursor-pointer transition-all duration-300 active:scale-[0.98] border border-emerald-100 hover:border-emerald-200 bg-gradient-to-b from-emerald-50/40 to-emerald-100/10 dark:from-emerald-955/10 dark:to-emerald-900/5 rounded-2xl p-4 pt-5 flex flex-col items-center justify-between shadow-[0_4px_15px_rgba(16,185,129,0.02)] h-full overflow-hidden"
                                id="ticket-5"
                            >
                                <div class="text-center">
                                    <div class="text-xs md:text-sm font-bold text-slate-800 dark:text-slate-200">Buy 5 or more</div>
                                </div>
                                <div class="w-full border-t border-dashed border-emerald-200 dark:border-emerald-900/50 my-3.5 relative">
                                    <div class="absolute -left-[22px] -top-1.5 w-3 h-3 rounded-full bg-[#f7fafb] dark:bg-[#0a0f12] border-r border-emerald-100 dark:border-emerald-900/40"></div>
                                    <div class="absolute -right-[22px] -top-1.5 w-3 h-3 rounded-full bg-[#f7fafb] dark:bg-[#0a0f12] border-l border-emerald-100 dark:border-emerald-900/40"></div>
                                </div>
                                <div class="text-center w-full mt-auto">
                                    <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Get 7% Off</div>
                                    <div class="text-sm md:text-base font-black text-slate-900 dark:text-slate-100 mt-1"><?php echo $price5; ?>/item</div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Card 3 -->
                        <div class="relative pt-3 snap-start shrink-0 w-[calc(60%-10px)] sm:w-auto">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 bg-[#4F46E5] text-white text-[9px] font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-sm z-10">
                                MOST SAVINGS
                            </div>
                            <div 
                                onclick="setOfferQuantity(10)"
                                class="ticket-card cursor-pointer transition-all duration-300 active:scale-[0.98] border border-indigo-100 hover:border-indigo-200 bg-gradient-to-b from-indigo-50/40 to-indigo-100/10 dark:from-indigo-955/10 dark:to-indigo-900/5 rounded-2xl p-4 pt-5 flex flex-col items-center justify-between shadow-[0_4px_15px_rgba(79,70,229,0.02)] h-full overflow-hidden"
                                id="ticket-10"
                            >
                                <div class="text-center">
                                    <div class="text-xs md:text-sm font-bold text-slate-800 dark:text-slate-200">Buy 10 or more</div>
                                </div>
                                <div class="w-full border-t border-dashed border-indigo-200 dark:border-indigo-900/50 my-3.5 relative">
                                    <div class="absolute -left-[22px] -top-1.5 w-3 h-3 rounded-full bg-[#f7fafb] dark:bg-[#0a0f12] border-r border-indigo-100 dark:border-indigo-900/40"></div>
                                    <div class="absolute -right-[22px] -top-1.5 w-3 h-3 rounded-full bg-[#f7fafb] dark:bg-[#0a0f12] border-l border-indigo-100 dark:border-indigo-900/40"></div>
                                </div>
                                <div class="text-center w-full mt-auto">
                                    <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400">Get 10% Off</div>
                                    <div class="flex items-center justify-center gap-1.5 mt-1">
                                        <span class="text-[11.5px] font-black text-slate-800 dark:text-slate-200">Code: LARA10</span>
                                        <button 
                                            type="button"
                                            onclick="copyCouponCode(event, 'LARA10')"
                                            class="p-1 hover:bg-slate-200 dark:hover:bg-slate-800 rounded text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-all active:scale-90"
                                            title="Copy Code"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Dynamic Frequently Bought Together Section -->
        <?php
        $related_ids = get_post_meta($main_product_id, '_fbt_ids', true);
        if ( ! empty( $related_ids ) ) :
        ?>
        <div class="mt-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-4 md:p-6 shadow-sm overflow-hidden font-poppins mx-4">
            <h3 class="text-base md:text-lg font-bold text-slate-800 dark:text-slate-200 mb-4">Frequently bought together</h3>
            
            <div class="fbt-items-row flex flex-col lg:flex-row items-stretch justify-start gap-4">
                <?php
                    $main_savings = $regular_price - $sale_price;
                ?>
                <div class="fbt-item fbt-product-card fbt-main-item flex flex-row items-center bg-[#f7fafb] dark:bg-slate-955 p-4 rounded-xl border border-primary relative group transition-all w-full"
                     data-base-price="<?php echo esc_attr( $sale_price ); ?>"
                     data-regular-price="<?php echo esc_attr( $regular_price ); ?>"
                     data-sale-price="<?php echo esc_attr( $sale_price ); ?>"
                     data-original-img="<?php echo esc_url( get_the_post_thumbnail_url( $main_product_id, 'thumbnail' ) ); ?>">
                    <div class="fbt-product-thumb w-14 h-14 bg-white rounded-lg flex-shrink-0 mr-3 flex items-center justify-center border border-slate-100 overflow-hidden p-1">
                        <img src="<?php echo get_the_post_thumbnail_url( $main_product_id, 'thumbnail' ); ?>" class="fbt-main-img w-full h-full object-contain">
                    </div>
                    <div class="fbt-product-copy flex flex-col flex-grow min-w-0 pr-8 justify-center items-start text-left">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug line-clamp-1 mb-1"><?php echo esc_html( $product_title ); ?></h4>
                        <div class="min-h-[16px] mb-1">
                            <span class="fbt-item-save text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-955/20 px-1.5 py-0.5 rounded-md inline-block <?php echo ($main_savings > 0) ? '' : 'hidden'; ?>">
                                Save ৳<span class="save-amt"><?php echo round($main_savings); ?></span>
                            </span>
                        </div>
                        <div class="fbt-main-price-display text-primary font-black text-sm">৳<?php echo esc_html( number_format($sale_price) ); ?></div>
                    </div>
                    <label class="fbt-check-label absolute bottom-3 right-3 flex items-center justify-center pointer-events-none">
                        <input type="checkbox" checked disabled class="fbt-checkbox hidden">
                        <div class="custom-cb w-4 h-4 rounded border border-primary bg-primary flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </label>
                    <input type="hidden" class="fbt-price" value="<?php echo $sale_price; ?>">
                    <input type="hidden" class="fbt-regular-price" value="<?php echo $regular_price; ?>">
                    <input type="hidden" class="fbt-id" value="<?php echo $main_product_id; ?>">
                </div>
    
                <div class="hidden lg:flex items-center justify-center text-slate-400 font-light text-xl">+</div>
    
                <?php 
                foreach ($related_ids as $index => $rel_id) : 
                    $rel_product = wc_get_product($rel_id);
                    if (!$rel_product) continue;
                    $rel_regular = (float)($rel_product->get_regular_price() ? $rel_product->get_regular_price() : $rel_product->get_price());
                    $rel_sale = (float)$rel_product->get_price();
                    $rel_savings = $rel_regular - $rel_sale;
                ?>
                <div class="fbt-item fbt-product-card flex flex-row items-center bg-[#f7fafb] dark:bg-slate-955 p-4 rounded-xl border border-slate-200 dark:border-slate-800 relative group transition-all cursor-pointer w-full" onclick="toggleFbtLaracom(this)">
                    <div class="fbt-product-thumb w-14 h-14 bg-white rounded-lg flex-shrink-0 mr-3 flex items-center justify-center border border-slate-100 overflow-hidden p-1">
                        <img src="<?php echo get_the_post_thumbnail_url($rel_id, 'thumbnail'); ?>" class="w-full h-full object-contain">
                    </div>
                    <div class="fbt-product-copy flex flex-col flex-grow min-w-0 pr-8 justify-center items-start text-left">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug line-clamp-1 mb-1"><?php echo esc_html( $rel_product->get_name() ); ?></h4>
                        <div class="min-h-[16px] mb-1">
                            <span class="fbt-item-save text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-955/20 px-1.5 py-0.5 rounded-md inline-block <?php echo ($rel_savings > 0) ? '' : 'hidden'; ?>">
                                Save ৳<span class="save-amt"><?php echo round($rel_savings); ?></span>
                            </span>
                        </div>
                        <div class="text-primary font-black text-sm">৳<?php echo esc_html( number_format($rel_sale) ); ?></div>
                    </div>
                    <label class="fbt-check-label absolute bottom-3 right-3 flex items-center justify-center pointer-events-none">
                        <input type="checkbox" checked class="fbt-checkbox hidden">
                        <div class="custom-cb w-4 h-4 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-center">
                            <svg class="custom-cb-tick w-3 h-3 text-white hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </label>
                    <input type="hidden" class="fbt-price" value="<?php echo $rel_sale; ?>">
                    <input type="hidden" class="fbt-regular-price" value="<?php echo $rel_regular; ?>">
                    <input type="hidden" class="fbt-id" value="<?php echo $rel_id; ?>">
                </div>
    
                <?php if ($index < count($related_ids) - 1) : ?>
                    <div class="hidden lg:flex items-center justify-center text-slate-400 font-light text-xl">+</div>
                <?php endif; ?>
                <?php endforeach; ?>
    
                <div class="hidden lg:flex items-center justify-center text-slate-400 font-light text-xl">=</div>
    
                <div class="fbt-total-card bg-primary rounded-xl p-4 flex flex-col items-center justify-center text-white text-center flex-shrink-0 w-full lg:w-[220px] shadow-md">
                    <div class="text-[20px] font-black leading-none mb-1" id="fbt-total-display">৳0.00</div>
                    <div class="text-[11px] mb-3 font-semibold min-h-[14px] invisible" id="fbt-save-display">Save ৳0</div>
                    <button type="button" id="fbt-add-all" class="bg-white hover:bg-slate-50 text-primary text-[12px] font-bold py-2.5 px-4 rounded-xl transition-all w-full leading-tight flex items-center justify-center gap-1 cursor-pointer">
                        Add <span id="fbt-count"><?php echo count($related_ids) + 1; ?></span> items to cart
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    
        <!-- Tabs & Sidebar Grid Container -->
        <div class="mt-12 border-t border-slate-200 dark:border-slate-800 pt-8 font-poppins px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: Tabs & Contents -->
                <div class="lg:col-span-9 flex flex-col gap-8">
                    <!-- Tab Buttons Row -->
                    <div class="grid grid-cols-4 md:flex md:w-auto md:justify-start gap-1.5 md:gap-3 border-b border-slate-105 dark:border-slate-850 pb-4 sticky top-16 bg-[#f7fafb]/90 dark:bg-[#0a0f12]/90 backdrop-blur-sm z-20 pt-2 w-full">
                        <button type="button" onclick="scrollToTabSection('specification')" id="tab-btn-specification" class="tab-header-btn w-full md:w-auto text-center px-1.5 md:px-5 py-2.5 rounded-lg text-[10px] sm:text-xs md:text-sm font-black border cursor-pointer truncate bg-primary text-white border-primary shadow-sm">
                            Specification
                        </button>
                        <button type="button" onclick="scrollToTabSection('description')" id="tab-btn-description" class="tab-header-btn w-full md:w-auto text-center px-1.5 md:px-5 py-2.5 rounded-lg text-[10px] sm:text-xs md:text-sm font-black border cursor-pointer truncate bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-202 border-slate-200 dark:border-slate-800 hover:border-primary/40">
                            Description
                        </button>
                        <button type="button" onclick="scrollToTabSection('questions')" id="tab-btn-questions" class="tab-header-btn w-full md:w-auto text-center px-1.5 md:px-5 py-2.5 rounded-lg text-[10px] sm:text-xs md:text-sm font-black border cursor-pointer truncate bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-202 border-slate-200 dark:border-slate-800 hover:border-primary/40">
                            Questions (<span id="qa-count-display">2</span>)
                        </button>
                        <button type="button" onclick="scrollToTabSection('reviews')" id="tab-btn-reviews" class="tab-header-btn w-full md:w-auto text-center px-1.5 md:px-5 py-2.5 rounded-lg text-[10px] sm:text-xs md:text-sm font-black border cursor-pointer truncate bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-202 border-slate-200 dark:border-slate-800 hover:border-primary/40">
                            Reviews (<?php echo count($comments) ?: '5'; ?>)
                        </button>
                    </div>
                    
                    <!-- Tab Content: Specifications -->
                    <div id="specification" class="tab-content-sec scroll-mt-32 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-8 shadow-sm animate-fade-in">
                        <h3 class="text-lg md:text-xl font-bold text-slate-800 dark:text-slate-100 mb-6">Specification</h3>
                        
                        <div class="bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-200 text-xs md:text-sm font-bold px-5 py-3 rounded-xl mb-4 border border-slate-100/50 dark:border-slate-850">
                            General Information
                        </div>
    
                        <div class="flex flex-col border border-slate-100 dark:border-slate-850 rounded-xl overflow-hidden">
                            <?php foreach ( $specs as $sIdx => $spec ) : ?>
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 p-4 border-b border-slate-50 dark:border-slate-850/50 last:border-0 <?php echo ( $sIdx % 2 === 0 ) ? 'bg-slate-50/30 dark:bg-slate-900/10' : 'bg-white dark:bg-slate-900'; ?>">
                                    <div class="sm:col-span-4 text-xs md:text-sm font-semibold text-slate-500 dark:text-slate-400">
                                        <?php echo esc_html( $spec['label'] ); ?>
                                    </div>
                                    <div class="sm:col-span-8 text-xs md:text-sm font-bold text-slate-800 dark:text-slate-100">
                                        <?php echo esc_html( $spec['value'] ); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
    
                    <!-- Tab Content: Description -->
                    <div id="description" class="tab-content-sec scroll-mt-32 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-8 shadow-sm animate-fade-in text-slate-600 dark:text-slate-300 flex flex-col gap-5 leading-relaxed text-xs md:text-sm font-medium">
                        <h3 class="text-lg md:text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Product Description</h3>
                        <div class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-350 leading-[1.8]">
                            <?php 
                            if ( ! empty( $main_content ) ) {
                                echo apply_filters( 'the_content', $main_content ); 
                            } else {
                                ?>
                                <p>Experience premium build quality and high performance with the new <strong><?php echo esc_html( $product_title ); ?></strong>. Engineered carefully to meet standard requirements, this product features an elegant construction matching modern durability standards.</p>
                                <div class="flex flex-col gap-2.5 pl-4 border-l-2 border-primary mt-2">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">Key Features:</span>
                                    <ul class="list-disc pl-5 flex flex-col gap-1.5 text-slate-505 dark:text-slate-450 font-semibold">
                                        <li>Premium material structure for ultimate reliability and prolonged product lifecycle.</li>
                                        <li>Ergonomically engineered configuration optimizing everyday comfort and utility.</li>
                                        <li>Dynamic output components yielding superior compatibility and efficient responsiveness.</li>
                                        <li>Strict quality assurance checks backed by brand certifications and replacement warranty.</li>
                                    </ul>
                                </div>
                                <p class="mt-2">Whether you are using it at home, office, or on the go, the <?php echo esc_html( $product_title ); ?> guarantees to deliver seamless compatibility with your existing ecosystem. Designed with aesthetics and functionality in mind, it is a smart addition to your essential gadgets.</p>
                                <?php
                            }
                            ?>
                        </div>
    
                        <?php if ( ! empty( $video_url ) && $embed_url ) : ?>
                            <div class="mt-6">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3 uppercase tracking-wider">Product Video</h4>
                                <div class="aspect-video w-full rounded-xl overflow-hidden bg-black shadow-md border border-slate-100 dark:border-slate-850">
                                    <iframe src="<?php echo esc_url($embed_url); ?>" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
    
                    <!-- Tab Content: Questions & Answers -->
                    <div id="questions" class="tab-content-sec scroll-mt-32 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-8 shadow-sm animate-fade-in">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <h3 class="text-lg md:text-xl font-bold text-slate-800 dark:text-slate-100">Questions & Answers</h3>
                            <button 
                                type="button"
                                onclick="toggleQuestionForm()"
                                class="px-5 py-2 bg-slate-900 dark:bg-slate-100 hover:opacity-90 text-white dark:text-black text-xs font-bold rounded-full transition-all active:scale-95 cursor-pointer"
                            >
                                Ask a Question
                            </button>
                        </div>
    
                        <div id="question-form-container" class="hidden bg-slate-50 dark:bg-slate-955 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-6 mb-8 shadow-inner">
                            <h4 class="text-xs md:text-sm font-bold text-slate-808 dark:text-slate-200 mb-4 uppercase tracking-wider">Ask a Question</h4>
                            <form onsubmit="submitCustomQuestion(event)" class="flex flex-col gap-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Your Name</label>
                                        <input 
                                            type="text" 
                                            id="qa_name"
                                            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-primary transition-colors"
                                            placeholder="John Doe"
                                            required
                                        />
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Your Email</label>
                                        <input 
                                            type="email" 
                                            id="qa_email"
                                            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-primary transition-colors"
                                            placeholder="john@example.com"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Your Question</label>
                                    <textarea 
                                        rows="4"
                                        id="qa_content"
                                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-200 outline-none focus:border-primary transition-colors resize-none"
                                        placeholder="Ask a question about compatibility, warranty, shipping..."
                                        required
                                    ></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button 
                                        type="submit" 
                                        class="py-2.5 px-6 bg-primary text-white font-bold text-sm rounded-full shadow-md hover:opacity-95 transition-all active:scale-95 cursor-pointer"
                                    >
                                        Submit Question
                                    </button>
                                </div>
                            </form>
                        </div>
    
                        <div id="questions-list-container" class="flex flex-col gap-6"></div>
                    </div>
    
                    <!-- Tab Content: Reviews -->
                    <div id="reviews" class="tab-content-sec scroll-mt-32 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-8 shadow-sm animate-fade-in">
                        <h3 class="text-lg md:text-xl font-bold text-slate-800 dark:text-slate-100 mb-6">Reviews</h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center border-b border-slate-100 dark:border-slate-805 pb-8 mb-8">
                            <div class="lg:col-span-4 flex flex-col items-center justify-center text-center lg:border-r border-slate-100 dark:border-slate-800 lg:pr-6">
                                <div class="flex items-center gap-1.5 text-amber-500 mb-2">
                                    <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-2xl md:text-3xl font-black text-slate-808 dark:text-slate-100"><?php echo esc_html( $average_score ); ?> out of 5</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-bold mt-1"><?php echo esc_html( $review_count ); ?> verified ratings</span>
                            </div>
    
                            <?php
                            $stars_pct = array();
                            foreach ( $stars_breakdown as $star => $count ) {
                                $stars_pct[$star] = $review_count > 0 ? round( ( $count / $review_count ) * 100 ) : 0;
                            }
                            if ( $review_count === 0 ) {
                                $stars_pct = array( 5 => 88, 4 => 12, 3 => 0, 2 => 0, 1 => 0 );
                                $stars_breakdown = array( 5 => 21, 4 => 3, 3 => 0, 2 => 0, 1 => 0 );
                            }
                            ?>
                            <div class="lg:col-span-5 flex flex-col gap-2 lg:px-6 lg:border-r border-slate-100 dark:border-slate-800">
                                <?php foreach ( array( 5, 4, 3, 2, 1 ) as $star ) : ?>
                                    <div class="flex items-center gap-3 text-xs md:text-sm font-semibold text-slate-500 dark:text-slate-400">
                                        <span class="w-3 select-none text-right font-black"><?php echo $star; ?></span>
                                        <div class="h-2 flex-1 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary" style="width: <?php echo $stars_pct[$star]; ?>%"></div>
                                        </div>
                                        <span class="w-8 select-none text-left text-slate-400 font-bold"><?php echo $stars_pct[$star]; ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
    
                            <div class="lg:col-span-3 flex items-center justify-center flex-col gap-3">
                                <button
                                    type="button"
                                    onclick="toggleReviewForm()"
                                    class="w-full py-2.5 bg-primary text-white text-xs md:text-sm font-bold rounded-full shadow-md hover:opacity-90 transition-all active:scale-95 cursor-pointer text-center"
                                >
                                    Write a Customer Review
                                </button>
                            </div>
                        </div>
    
                        <div id="review-form-container" class="hidden bg-slate-50 dark:bg-slate-955 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-6 mb-8 shadow-inner">
                            <h4 class="text-xs md:text-sm font-bold text-slate-800 dark:text-slate-202 mb-4 uppercase tracking-wider">Write a Review</h4>
                            <form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Rating *</label>
                                    <div class="flex items-center gap-1.5 h-10">
                                        <input type="hidden" name="rating" id="review_star_rating_input" value="0" required />
                                        <?php for ( $star = 1; $star <= 5; $star++ ) : ?>
                                            <button
                                                type="button"
                                                onclick="setReviewFormRating(<?php echo $star; ?>)"
                                                class="review-form-star-btn text-slate-300 dark:text-slate-700 hover:scale-110 transition-transform cursor-pointer"
                                                data-star="<?php echo $star; ?>"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                                            </button>
                                        <?php endfor; ?>
                                    </div>
                                </div>
    
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php if ( is_user_logged_in() ) : ?>
                                        <div class="col-span-2 text-xs font-semibold text-slate-500">
                                            Logged in as <strong><?php $user = wp_get_current_user(); echo esc_html($user->display_name); ?></strong>.
                                        </div>
                                    <?php else : ?>
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Your Name *</label>
                                            <input 
                                                type="text" 
                                                name="author"
                                                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-bold text-slate-700 dark:text-slate-202 outline-none focus:border-primary transition-colors"
                                                placeholder="John Doe"
                                                required
                                            />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Your Email *</label>
                                            <input 
                                                type="email" 
                                                name="email"
                                                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-bold text-slate-700 dark:text-slate-202 outline-none focus:border-primary transition-colors"
                                                placeholder="john@example.com"
                                                required
                                            />
                                        </div>
                                    <?php endif; ?>
                                </div>
    
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Review Title *</label>
                                    <input 
                                        type="text" 
                                        name="review_title"
                                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-bold text-slate-700 dark:text-slate-202 outline-none focus:border-primary transition-colors"
                                        placeholder="Awesome product, value for money..."
                                        required
                                    />
                                </div>
    
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Upload Product Image (Optional)</label>
                                        <div class="relative flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2 text-xs md:text-sm font-bold text-slate-700 dark:text-slate-202 h-10">
                                            <input 
                                                type="file" 
                                                name="review_image"
                                                accept="image/*"
                                                onchange="handleReviewImageUpload(this)"
                                                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                            />
                                            <span id="review-image-selected-label" class="truncate max-w-[70%] text-slate-400 font-semibold">
                                                Choose image...
                                            </span>
                                            <button 
                                                type="button"
                                                class="px-3 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-750 text-[11px] font-bold rounded-lg text-slate-600 dark:text-slate-300 pointer-events-none"
                                            >
                                                Browse
                                            </button>
                                        </div>
                                        <span id="review-image-error" class="text-red-505 font-bold text-[11px] mt-1.5 hidden"></span>
                                    </div>
    
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">YouTube Review Link (Optional)</label>
                                        <input 
                                            type="url" 
                                            name="review_youtube"
                                            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-bold text-slate-700 dark:text-slate-202 outline-none focus:border-primary transition-colors h-10"
                                            placeholder="https://www.youtube.com/watch?v=..."
                                        />
                                    </div>
                                </div>
    
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-455 dark:text-slate-400 uppercase tracking-wider">Review Content *</label>
                                    <textarea 
                                        rows="4"
                                        name="comment"
                                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs md:text-sm font-medium text-slate-700 dark:text-slate-202 outline-none focus:border-primary transition-colors resize-none"
                                        placeholder="Write your experience with the product..."
                                        required
                                    ></textarea>
                                </div>
    
                                <input type="hidden" name="comment_post_ID" value="<?php echo $main_product_id; ?>" />
                                <input type="hidden" name="comment_parent" value="0" />
    
                                <div class="flex justify-end">
                                    <button 
                                        type="submit" 
                                        name="submit"
                                        class="py-2.5 px-6 bg-primary text-white font-bold text-sm rounded-full shadow-md hover:opacity-95 transition-all active:scale-95 cursor-pointer"
                                    >
                                        Submit Review
                                    </button>
                                </div>
                            </form>
                        </div>
    
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-850 pb-4 mb-6">
                            <h4 class="text-xs md:text-sm font-bold text-slate-808 dark:text-slate-202 uppercase tracking-wider">Customer Reviews List</h4>
                            <div class="flex items-center gap-2 text-xs md:text-sm">
                                <span class="text-slate-400 dark:text-slate-555 font-bold">Sort:</span>
                                <select 
                                    id="review-sort-select"
                                    onchange="reSortReviews()"
                                    class="bg-slate-50 dark:bg-slate-855 border border-slate-200 dark:border-slate-800 rounded-md px-2 py-1 font-bold text-slate-750 dark:text-slate-202 outline-none cursor-pointer"
                                >
                                    <option value="recent">Most Recent</option>
                                    <option value="highest">Highest Rating</option>
                                    <option value="lowest">Lowest Rating</option>
                                </select>
                            </div>
                        </div>
    
                        <div id="reviews-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
    
                        <div id="reviews-load-more-btn-wrap" class="flex justify-center mt-8 hidden">
                            <button 
                                type="button"
                                onclick="loadMoreReviews()"
                                class="bg-slate-900 dark:bg-slate-100 hover:opacity-90 text-white dark:text-black text-xs md:text-sm font-bold py-2.5 px-8 rounded-full transition-all active:scale-95 cursor-pointer"
                            >
                                Load More Reviews
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Sidebar (3 columns) -->
                <div class="lg:col-span-3 font-poppins">
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-6 shadow-[0_8px_30px_rgba(0,0,0,0.015)] sticky top-6">
                        <h3 class="text-base md:text-lg font-bold text-slate-808 dark:text-slate-100 pb-3 border-b border-slate-100 dark:border-slate-850 mb-5 flex items-center justify-between">
                            <span>Similar Product</span>
                            <span class="text-[10px] text-primary uppercase tracking-wider font-black">Recommendations</span>
                        </h3>
    
                        <div class="flex flex-col gap-5">
                            <?php
                            $sidebar_args = array(
                                'post_type'            => 'product',
                                'ignore_custom_sort'   => true,
                                'posts_per_page'       => 4,
                                'orderby'              => 'rand',
                                'post__not_in'         => array( $main_product_id ),
                                'tax_query'            => array(
                                    array(
                                        'taxonomy' => 'product_cat',
                                        'field'    => 'term_id',
                                        'terms'    => ! empty( $cat_ids ) ? $cat_ids : array( 0 ),
                                    ),
                                ),
                            );
                            $sidebar_query = new WP_Query( $sidebar_args );
                            if ( $sidebar_query->have_posts() ) :
                                while ( $sidebar_query->have_posts() ) : $sidebar_query->the_post();
                                    $side_product = wc_get_product( get_the_ID() );
                                    $side_image = wp_get_attachment_image_url( $side_product->get_image_id(), 'thumbnail' ) ?: wc_placeholder_img_src();
                                    $side_title = $side_product->get_name();
                                    $side_price = $side_product->get_price();
                                    $side_old_price = $side_product->get_regular_price();
                                    ?>
                                    <div class="flex gap-3 pb-4 border-b border-slate-50 dark:border-slate-850/60 last:border-0 last:pb-0">
                                        <div 
                                            onclick="window.location.href='<?php the_permalink(); ?>'"
                                            class="h-16 w-16 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-100 dark:border-slate-850/60 p-1 flex items-center justify-center shrink-0 cursor-pointer overflow-hidden group"
                                        >
                                            <img 
                                                src="<?php echo esc_url( $side_image ); ?>" 
                                                alt="<?php echo esc_attr( $side_title ); ?>" 
                                                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                            />
                                        </div>
    
                                        <div class="flex-1 flex flex-col justify-between min-w-0">
                                            <h4 
                                                onclick="window.location.href='<?php the_permalink(); ?>'"
                                                class="text-xs font-bold text-slate-805 dark:text-slate-205 hover:text-primary transition-colors leading-tight line-clamp-2 cursor-pointer"
                                            >
                                                <?php echo esc_html( $side_title ); ?>
                                            </h4>
                                            
                                            <div class="flex items-baseline gap-1 mt-1">
                                                <span class="text-[13px] font-black text-primary">৳<?php echo esc_html( number_format($side_price) ); ?></span>
                                                <?php if ( $side_old_price > $side_price ) : ?>
                                                    <span class="text-[10px] text-slate-405 line-through font-semibold ml-1">৳<?php echo esc_html( number_format($side_old_price) ); ?></span>
                                                <?php endif; ?>
                                            </div>
    
                                            <button
                                                type="button"
                                                onclick="toggleCompareLaracom(<?php the_ID(); ?>, '<?php echo esc_js( $side_title ); ?>')"
                                                id="compare-btn-<?php the_ID(); ?>"
                                                class="compare-toggle-btn mt-2 flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-full border text-[10px] font-bold transition-all duration-200 active:scale-95 cursor-pointer bg-slate-50 dark:bg-slate-855 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-505 dark:text-slate-400 border-slate-200 dark:border-slate-805"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-3 w-3 btn-plus-icon"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="h-3 w-3 btn-check-icon hidden"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span class="btn-text">Add to Compare</span>
                                            </button>
                                        </div>
                                    </div>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Related Products Footer Grid -->
        <?php
        $related_query_args = array(
            'post_type'            => 'product',
            'ignore_custom_sort'   => true,
            'posts_per_page'       => 5,
            'orderby'              => 'rand',
            'post__not_in'         => array( $main_product_id ),
            'tax_query'            => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => ! empty( $cat_ids ) ? $cat_ids : array( 0 ),
                ),
            ),
        );
        $related_query_bottom = new WP_Query( $related_query_args );
        if ( $related_query_bottom->have_posts() ) :
        ?>
        <div class="mt-16 md:mt-24 border-t border-slate-100 dark:border-slate-850 pt-12 pb-12 font-poppins px-4">
            <h3 class="text-xl md:text-2xl font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider mb-8 text-center">
                Related Products
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-5">
                <?php
                while ( $related_query_bottom->have_posts() ) : $related_query_bottom->the_post();
                    $rel_product = wc_get_product( get_the_ID() );
                    $rel_image = wp_get_attachment_image_url( $rel_product->get_image_id(), 'medium' ) ?: wc_placeholder_img_src();
                    $rel_title = $rel_product->get_name();
                    $rel_price = $rel_product->get_price();
                    $rel_old_price = $rel_product->get_regular_price();
                    $rel_request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $rel_product ) : '';
                    ?>
                    <div 
                        onclick="window.location.href='<?php the_permalink(); ?>'"
                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary dark:hover:border-primary/80 rounded-xl overflow-hidden flex flex-col justify-between hover:shadow-md transition-all duration-300 cursor-pointer group/card relative"
                    >
                        <?php if ( $rel_request_type ) : ?>
                            <div class="absolute top-2 left-2 z-10 scale-90 origin-top-left">
                                <?php echo woocom_render_stock_request_badge( $rel_request_type ); ?>
                            </div>
                        <?php endif; ?>
    
                        <div class="aspect-square w-full overflow-hidden relative p-2 bg-slate-50/50 dark:bg-slate-955/20">
                            <img 
                                src="<?php echo esc_url( $rel_image ); ?>" 
                                alt="<?php echo esc_attr( $rel_title ); ?>" 
                                class="w-full h-full object-contain transition-transform duration-500 group-hover/card:scale-105"
                                loading="lazy"
                            />
                        </div>
    
                        <div class="p-3 flex flex-col justify-between flex-1">
                            <div>
                                <div class="flex items-center gap-0.5 text-amber-500 mb-2">
                                    <?php for ( $s = 0; $s < 5; $s++ ) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                                    <?php endfor; ?>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold ml-1">5.0</span>
                                </div>
    
                                <h3 
                                    class="text-[13px] font-semibold text-slate-808 dark:text-slate-202 leading-tight group-hover/card:text-primary transition-colors line-clamp-2 h-8"
                                    title="<?php echo esc_attr( $rel_title ); ?>"
                                >
                                    <?php echo esc_html( $rel_title ); ?>
                                </h3>
                            </div>
    
                            <div class="mt-2">
                                <div class="border-t border-slate-100 dark:border-slate-800/50 my-2" />
    
                                <div class="flex items-baseline justify-between pb-2.5 flex-wrap">
                                    <span class="text-[17px] font-black text-primary">
                                        ৳<?php echo esc_html( number_format($rel_price) ); ?>
                                    </span>
                                    <?php if ( $rel_old_price > $rel_price ) : ?>
                                        <span class="text-[11px] text-slate-400 line-through ml-2">
                                            ৳<?php echo esc_html( number_format($rel_old_price) ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
    
                                <?php if ( $rel_request_type && function_exists( 'woocom_render_stock_request_form' ) ) : ?>
                                    <?php echo woocom_render_stock_request_form( get_the_ID(), $rel_request_type, 'archive' ); ?>
                                <?php else : ?>
                                    <a 
                                        href="?add-to-cart=<?php the_ID(); ?>" 
                                        onclick="event.stopPropagation();"
                                        class="group w-full flex items-center justify-center gap-1.5 py-2 bg-primary-light hover:bg-primary text-primary hover:text-white text-[12px] font-bold rounded-full transition-all duration-300 active:scale-95"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                        <span>Add to Cart</span>
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
        <?php endif; ?>
    
        <!-- Floating Compare Drawer -->
        <div id="floating-compare-drawer" class="hidden fixed bottom-6 right-6 z-50 max-w-sm w-[calc(100%-48px)] sm:w-[360px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-4 flex flex-col gap-3 font-poppins">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black text-slate-800 dark:text-slate-200">
                    Compare Products (<span id="compare-queue-count">0</span>/3)
                </span>
                <button 
                    type="button"
                    onclick="clearCompareQueue()"
                    class="text-[10px] text-slate-400 hover:text-red-500 font-bold transition-colors cursor-pointer"
                >
                    Clear All
                </button>
            </div>
    
            <div id="compare-queue-thumbs" class="flex gap-2"></div>
    
            <button
                type="button"
                onclick="openComparisonModal()"
                class="w-full py-2 bg-primary text-white text-xs font-black rounded-xl transition-all active:scale-[0.98] shadow-md shadow-primary/10 cursor-pointer text-center"
            >
                Compare Now
            </button>
        </div>
    
        <!-- Comparison Modal -->
        <div id="comparison-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 sm:p-6 font-poppins">
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-3xl w-full max-w-4xl max-h-[85vh] overflow-y-auto shadow-2xl p-6 md:p-8 relative">
                <button
                    type="button"
                    onclick="closeComparisonModal()"
                    class="absolute top-5 right-5 h-8 w-8 rounded-full bg-slate-50 dark:bg-slate-850 border border-slate-100 dark:border-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center text-sm font-bold shadow-sm transition-all active:scale-90 cursor-pointer"
                >
                    ✕
                </button>
    
                <h3 class="text-xl md:text-2xl font-black text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
                    <span>Compare Products</span>
                    <span class="text-xs text-primary bg-primary-light px-2.5 py-0.5 rounded-full font-black">Comparison Matrix</span>
                </h3>
    
                <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-850">
                    <table class="w-full text-left border-collapse text-xs md:text-sm">
                        <thead>
                            <tr id="compare-table-head" class="bg-slate-50 dark:bg-slate-955 border-b border-slate-100 dark:border-slate-850"></tr>
                        </thead>
                        <tbody id="compare-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    
        <!-- Toast Overlay -->
        <div id="laracom-toast-overlay" class="fixed top-6 left-1/2 -translate-x-1/2 z-[999] bg-slate-905 dark:bg-slate-100 text-white dark:text-black text-xs md:text-sm font-bold px-6 py-3 rounded-full shadow-2xl flex items-center gap-2 border border-slate-700/30 dark:border-slate-300/30 hidden">
            <span class="h-2 w-2 rounded-full bg-primary animate-ping" />
            <span id="laracom-toast-message"></span>
        </div>
    </div>
    
    <?php do_action( 'woocommerce_after_single_product' ); ?>
    
    <script type="text/javascript">
        window.addEventListener('error', function(e) {
            console.error("Global JS Error Captured: ", e.message, " in ", e.filename, " at line ", e.lineno);
            const errDiv = document.createElement('div');
            errDiv.style.position = 'fixed';
            errDiv.style.bottom = '20px';
            errDiv.style.left = '20px';
            errDiv.style.background = '#ef4444';
            errDiv.style.color = '#ffffff';
            errDiv.style.padding = '12px 20px';
            errDiv.style.fontSize = '13px';
            errDiv.style.fontWeight = 'bold';
            errDiv.style.zIndex = '999999';
            errDiv.style.borderRadius = '10px';
            errDiv.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
            errDiv.innerText = "🚨 JS Error: " + e.message + " (" + e.filename.split('/').pop() + ":" + e.lineno + ")";
            document.body.appendChild(errDiv);
        });

        const $ = jQuery;

        const currentProductId = <?php echo $main_product_id; ?>;
        const currentProductTitle = "<?php echo esc_js( $product_title ); ?>";
        const currentProductImage = "<?php echo esc_url( wp_get_attachment_image_url( $main_image_id, 'medium' ) ?: wc_placeholder_img_src() ); ?>";
        const currentProductPrice = "৳<?php echo esc_js( number_format($sale_price) ); ?>";
    
        const commentsList = <?php echo json_encode( array_map( function( $comment ) {
            $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
            $title = get_comment_meta( $comment->comment_ID, 'review_title', true ) ?: 'Good Quality';
            $image = get_comment_meta( $comment->comment_ID, 'review_image', true );
            $youtube = get_comment_meta( $comment->comment_ID, 'review_youtube', true );
            
            return array(
                'name'      => $comment->comment_author,
                'rating'    => intval($rating) ?: 5,
                'date'      => date( 'm/d/Y', strtotime( $comment->comment_date ) ),
                'title'     => $title,
                'content'   => $comment->comment_content,
                'image'     => $image ?: null,
                'youtube'   => $youtube ?: null,
                'verified'  => true,
                'helpful'   => 0,
                'unhelpful' => 0
            );
        }, $comments ) ); ?>;
    
        const mockReviews = [
            { name: "Daniel Parihar", rating: 5, date: "04/22/2026", title: "Amazed By the product", content: "Very durable build with high functionality which makes it even more robust for daily use.", verified: true, helpful: 2, unhelpful: 0 },
            { name: "Rahul Sharma", rating: 5, date: "05/18/2026", title: "Extremely satisfied", content: "Excellent performance, construction is heavy-duty. Exactly matches the premium description.", verified: true, helpful: 12, unhelpful: 0 },
            { name: "Neha Gupta", rating: 5, date: "06/02/2026", title: "Incredible value", content: "Fits securely and functions smoothly. The battery timing and durability is amazing.", verified: true, helpful: 18, unhelpful: 0 },
            { name: "Amit K.", rating: 4, date: "03/10/2026", title: "Compact and powerful", content: "Great power output. Highly recommended for travelers and daily commuters.", verified: true, helpful: 5, unhelpful: 1 },
            { name: "Siddharth Kumar", rating: 5, date: "11/11/2025", title: "Superb product", content: "Instrument separation and build quality is top-notch. Truly worth every single penny.", verified: true, helpful: 14, unhelpful: 0 }
        ];
    
        let allReviews = commentsList.length > 0 ? commentsList : mockReviews;
            // Global utility functions assigned to window (accessible to inline onclick attributes)
        window.changeMainImageLaracom = function(thumbnailEl, index) {
            const slider = document.getElementById('main-image-slider-laracom');
            if (!slider) return;
            slider.style.transform = `translateX(-${index * 100}%)`;
            slider.dataset.currentIndex = index;
            $('.thumbnail-btn-laracom').removeClass('border-primary shadow-sm').addClass('border-slate-100 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700');
            $(thumbnailEl).addClass('border-primary shadow-sm').removeClass('border-slate-100 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700');
        };

        window.slidePrevLaracom = function() {
            const slider = document.getElementById('main-image-slider-laracom');
            if (!slider) return;
            const total = slider.children.length;
            let current = parseInt(slider.dataset.currentIndex || 0);
            current = (current - 1 + total) % total;
            const thumbs = document.querySelectorAll('.thumbnail-btn-laracom');
            if (thumbs[current]) thumbs[current].click();
        };

        window.slideNextLaracom = function() {
            const slider = document.getElementById('main-image-slider-laracom');
            if (!slider) return;
            const total = slider.children.length;
            let current = parseInt(slider.dataset.currentIndex || 0);
            current = (current + 1) % total;
            const thumbs = document.querySelectorAll('.thumbnail-btn-laracom');
            if (thumbs[current]) thumbs[current].click();
        };

        window.setOfferQuantity = function(qty) {
            var $input = $('form.cart input[name="quantity"]');
            if ($input.length) {
                $input.val(qty).trigger('change');
                showToastNotification("Bulk quantity of " + qty + " selected!");
            }
        };

        window.copyCouponCode = function(event, code) {
            event.stopPropagation();
            navigator.clipboard.writeText(code).then(() => {
                alert("Coupon code '" + code + "' copied successfully!");
            });
        };

        window.toggleFbtLaracom = function(el) {
            const cb = el.querySelector('.fbt-checkbox');
            const customCb = el.querySelector('.custom-cb');
            const customTick = el.querySelector('.custom-cb-tick');
            cb.checked = !cb.checked;
            
            if(cb.checked) {
                el.classList.add('border-primary');
                el.classList.remove('border-slate-200', 'dark:border-slate-800', 'opacity-60');
                if(customCb) customCb.classList.add('bg-primary', 'border-primary');
                if(customTick) customTick.classList.remove('hidden');
            } else {
                el.classList.remove('border-primary');
                el.classList.add('border-slate-200', 'dark:border-slate-800', 'opacity-60');
                if(customCb) customCb.classList.remove('bg-primary', 'border-primary');
                if(customTick) customTick.classList.add('hidden');
            }
            updateFbtTotalLaracom();
        };

        window.scrollToTabSection = function(id) {
            const el = document.getElementById(id);
            if (el) {
                const headerOffset = 180;
                const elementPosition = el.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                $('.tab-header-btn').removeClass('bg-primary text-white border-primary shadow-sm').addClass('bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-202 border-slate-200 dark:border-slate-800 hover:border-primary/40');
                $('#tab-btn-' + id).addClass('bg-primary text-white border-primary shadow-sm').removeClass('bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-202 border-slate-200 dark:border-slate-800 hover:border-primary/40');
            }
        };

        function updateFbtTotalLaracom() {
            let total = 0;
            let totalRegular = 0;
            let count = 0;
            
            document.querySelectorAll('.fbt-item').forEach(item => {
                const cb = item.querySelector('.fbt-checkbox');
                if (cb && cb.checked) {
                    total += parseFloat(item.querySelector('.fbt-price').value) || 0;
                    totalRegular += parseFloat(item.querySelector('.fbt-regular-price')?.value || item.querySelector('.fbt-price').value) || 0;
                    count++;
                }
            });
            
            const totalDisplay = document.getElementById('fbt-total-display');
            if (totalDisplay) totalDisplay.innerText = '৳' + total.toLocaleString('en-US');
            const fbtCount = document.getElementById('fbt-count');
            if (fbtCount) fbtCount.innerText = count;
            const savings = totalRegular - total;
            const savingsBadge = document.getElementById('fbt-save-display');
            if (savingsBadge) {
                if (savings > 0) {
                    savingsBadge.innerText = 'Save ৳' + Math.round(savings).toLocaleString('en-US');
                    savingsBadge.classList.remove('invisible');
                } else savingsBadge.classList.add('invisible');
            }
        }

        jQuery(document).ready(function($) {
            renderReviewsList();
            initQuestionsSection();
            updateCompareWidgetUI();
            updateFbtTotalLaracom();
    
            const slides = document.querySelectorAll('.slider-slide');
            slides.forEach(slide => {
                const img = slide.querySelector('img');
                if (!img) return;
                img.style.transition = 'transform 0.15s ease-out';
                img.style.cursor = 'zoom-in';
                slide.addEventListener('mousemove', function(e) {
                    if (window.matchMedia('(min-width: 1024px)').matches) {
                        const rect = slide.getBoundingClientRect();
                        const x = ((e.clientX - rect.left) / rect.width) * 100;
                        const y = ((e.clientY - rect.top) / rect.height) * 100;
                        img.style.transformOrigin = `${x}% ${y}%`;
                        img.style.transform = 'scale(2.3)';
                    }
                });
                slide.addEventListener('mouseleave', function() {
                    if (window.matchMedia('(min-width: 1024px)').matches) {
                        img.style.transform = 'scale(1)';
                    }
                });
            });
    
            function setupCustomVariationButtons() {
                $('form.variations_form select').each(function() {
                    var $select = $(this);
                    if ($select.hasClass('laracom-mapped')) return;
                    $select.addClass('laracom-mapped').hide();
                    
                    var $container = $('<div class="flex flex-wrap gap-2 mt-2 select-button-container-wrap"></div>');
                    $select.find('option').each(function() {
                        var val = $(this).val();
                        var text = $(this).text();
                        if (!val) return;
                        
                        var $btn = $('<button type="button" class="px-4 py-1.5 rounded-lg text-xs md:text-sm font-semibold border transition-all border-slate-200 dark:border-slate-800 hover:border-primary text-slate-700 dark:text-slate-355 hover:text-primary cursor-pointer">' + text + '</button>');
                        $btn.attr('data-value', val);
                        
                        if ($select.val() === val) {
                            $btn.addClass('border-primary bg-primary/5 text-primary').removeClass('border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-355');
                        }
                        
                        $btn.on('click', function(e) {
                            e.preventDefault();
                            $select.val(val).trigger('change');
                            $container.find('button').removeClass('border-primary bg-primary/5 text-primary').addClass('border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300');
                            $(this).addClass('border-primary bg-primary/5 text-primary').removeClass('border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300');
                            applyImageColorTint(val);
                        });
                        $container.append($btn);
                    });
                    $select.after($container);
                });
            }
    
            function applyImageColorTint(colorName) {
                const c = colorName.toLowerCase();
                let filterClass = "";
                if (c.includes('blue')) filterClass = "hue-rotate-[180deg] saturate-[1.2]";
                else if (c.includes('pink') || c.includes('cherry')) filterClass = "hue-rotate-[290deg] saturate-[1.3]";
                else if (c.includes('black')) filterClass = "brightness-[0.75] contrast-[1.1] saturate-[0.1]";
                else if (c.includes('gray')) filterClass = "brightness-[0.9] contrast-[1.05] grayscale-[0.5]";
                else if (c.includes('silver')) filterClass = "brightness-[1.1] contrast-[0.95] grayscale-[0.8]";
                else if (c.includes('gold')) filterClass = "hue-rotate-[35deg] saturate-[1.4] sepia-[0.3]";
                $('.gallery-main-img').attr('class', 'w-full h-full object-contain select-none pointer-events-none transition-all duration-500 group-hover:scale-105 gallery-main-img ' + filterClass);
            }
    
            setupCustomVariationButtons();
    
            $(document.body).on('woocommerce_variation_has_changed', function() {
                $('form.variations_form select').each(function() {
                    var $select = $(this);
                    var val = $select.val();
                    var $container = $select.next('.select-button-container-wrap');
                    if ($container.length) {
                        $container.find('button').removeClass('border-primary bg-primary/5 text-primary').addClass('border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300');
                        if (val) {
                            $container.find('button[data-value="' + val + '"]').addClass('border-primary bg-primary/5 text-primary').removeClass('border-slate-200 dark:border-slate-800 text-slate-750 dark:text-slate-355');
                            applyImageColorTint(val);
                        }
                    }
                });
                setTimeout(function() {
                    var variation_price = $('.woocommerce-variation .price .amount').html();
                    if (variation_price) $('#laracom-price-display').html(variation_price);
                }, 100);
            });
    
            function syncQtyDisplay() {
                var val = $('form.cart input[name="quantity"]').val() || 1;
                $('.qty-display').text(val);
                updateOfferHighlight(parseInt(val));
            }
    
            $(document.body).on('change', 'form.cart input[name="quantity"]', function() {
                syncQtyDisplay();
            });
    
            $(document.body).on('click', '.quantity-plus', function() {
                var $input = $('form.cart input[name="quantity"]');
                if ($input.length) {
                    var val = parseInt($input.val()) || 1;
                    $input.val(val + 1).trigger('change');
                }
            });
    
            $(document.body).on('click', '.quantity-minus', function() {
                var $input = $('form.cart input[name="quantity"]');
                if ($input.length) {
                    var val = parseInt($input.val()) || 1;
                    $input.val(Math.max(1, val - 1)).trigger('change');
                }
            });
    
            syncQtyDisplay();
    
            function updateOfferHighlight(qty) {
                $('.ticket-card').removeClass('border-primary ring-2 ring-primary/10 scale-[1.02] bg-primary-light').addClass('border-orange-100');
                if (qty >= 10) $('#ticket-10').addClass('border-primary ring-2 ring-primary/10 scale-[1.02] bg-primary-light').removeClass('border-orange-100');
                else if (qty >= 5) $('#ticket-5').addClass('border-primary ring-2 ring-primary/10 scale-[1.02] bg-primary-light').removeClass('border-orange-100');
                else if (qty >= 2) $('#ticket-2').addClass('border-primary ring-2 ring-primary/10 scale-[1.02] bg-primary-light').removeClass('border-orange-100');
            }
    
            $(document.body).on('click', '#laracom-add-to-cart', function(e) {
                e.preventDefault();
                var $form = $('form.cart');
                if ($form.hasClass('variations_form')) {
                    var variation_id = $form.find('input[name="variation_id"]').val();
                    if (!variation_id || variation_id === "0") {
                        alert('Please select product options first.');
                        return;
                    }
                }
                var $btn = $form.find('.single_add_to_cart_button');
                if ($btn.length) {
                    if ($btn.hasClass('ajax_add_to_cart') || $btn.attr('data-product_id')) $btn.trigger('click');
                    else $form.submit();
                }
            });
    
            function handleBuyNowClick() {
                var $form = $('form.cart');
                if ($form.hasClass('variations_form')) {
                    var variation_id = $form.find('input[name="variation_id"]').val();
                    if (!variation_id || variation_id === "0") {
                        alert('Please select product options first.');
                        return;
                    }
                }
                let redirectInput = $form.find('input[name="buy_now_redirect"]');
                if (!redirectInput.length) {
                    redirectInput = $('<input type="hidden" name="buy_now_redirect" value="1">');
                    $form.append(redirectInput);
                } else redirectInput.val('1');
                $form.submit();
            }
    
            $(document.body).on('click', '#laracom-buy-now, #laracom-buy-now-mobile', function(e) {
                e.preventDefault();
                handleBuyNowClick();
            });
    
            // FBT AJAX execution
            $('#fbt-add-all').on('click', function() {
                const btn = $(this);
                const originalContent = btn.html();
                btn.prop('disabled', true).html('Adding...');
                
                const itemsData = [];
                document.querySelectorAll('.fbt-item').forEach(item => {
                    const cb = item.querySelector('.fbt-checkbox');
                    if (cb && cb.checked) {
                        if (item.classList.contains('fbt-main-item')) {
                            var $form = $('form.cart');
                            var variation_id = $form.find('input[name="variation_id"]').val() || 0;
                            var mainQty = parseInt($form.find('input[name="quantity"]').val()) || 1;
                            
                            itemsData.push({
                                product_id: parseInt(item.querySelector('.fbt-id').value),
                                variation_id: parseInt(variation_id),
                                quantity: mainQty,
                                variation: {}
                            });
                        } else {
                            itemsData.push({
                                product_id: parseInt(item.querySelector('.fbt-id').value),
                                variation_id: 0,
                                quantity: 1,
                                variation: {}
                            });
                        }
                    }
                });
    
                if (itemsData.length === 0) {
                    btn.prop('disabled', false).html(originalContent);
                    return;
                }
    
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'add_multiple_products_to_cart',
                        nonce: (window.woocom_ajax && window.woocom_ajax.cart_nonce) || (window.wc_add_to_cart_params && window.wc_add_to_cart_params.nonce) || '',
                        items: itemsData
                    },
                    success: function(response) {
                        if (response.success !== false) {
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                            btn.prop('disabled', false).text('Added Successfully!');
                            setTimeout(() => {
                                btn.html(originalContent);
                                updateFbtTotalLaracom();
                            }, 2000);
                        } else {
                            btn.prop('disabled', false).text('Error!');
                            setTimeout(() => btn.html(originalContent), 2000);
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).text('Error!');
                        setTimeout(() => btn.html(originalContent), 2000);
                    }
                });
            });
        });-slate-800 hover:border-primary/40');
                }
            };
        });
    
        function getCustomQuestionsKey() { return 'laracom_custom_questions_' + currentProductId; }
    
        function initQuestionsSection() {
            const defaultQuestions = [
                { question: "Is this compatible with iPhone 15 Pro Max?", askedBy: "Imran Khan", date: "04/10/2026", answer: "Yes, it fully supports fast charging and data sync on all iPhone 15 and 16 series models." },
                { question: "Does it come with official brand warranty?", askedBy: "Rashedul Islam", date: "03/22/2026", answer: "Yes! It comes with 6 Months replacement warranty covered directly by Laracom Gadget." }
            ];
            let saved = [];
            try { saved = JSON.parse(localStorage.getItem(getCustomQuestionsKey()) || '[]'); } catch (e) {}
            const allQuestions = [...saved, ...defaultQuestions];
            document.getElementById('qa-count-display').innerText = allQuestions.length;
            const container = document.getElementById('questions-list-container');
            if (!container) return;
            container.innerHTML = '';
            allQuestions.forEach(q => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-slate-50/50 dark:bg-slate-955/20 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 md:p-6 shadow-sm">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="text-xs font-black text-primary bg-primary-light px-2 py-1 rounded">Q</span>
                            <div class="flex-1">
                                <h4 class="text-sm md:text-base font-bold text-slate-808 dark:text-slate-100">${q.question}</h4>
                                <div class="flex items-center gap-2 mt-1.5 text-[10px] text-slate-405 dark:text-slate-500 font-bold">
                                    <span>Asked by ${q.askedBy}</span>
                                    <span>•</span>
                                    <span>${q.date}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 border-t border-slate-100/50 dark:border-slate-850 pt-3 pl-2">
                            <span class="text-xs font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/20 px-2 py-1 rounded">A</span>
                            <p class="text-xs md:text-sm text-slate-650 dark:text-slate-400 leading-relaxed font-medium mt-0.5">${q.answer}</p>
                        </div>
                    </div>
                `);
            });
        }
    
        window.toggleQuestionForm = function() {
            document.getElementById('question-form-container').classList.toggle('hidden');
        };
    
        window.submitCustomQuestion = function(e) {
            e.preventDefault();
            const name = document.getElementById('qa_name').value;
            const email = document.getElementById('qa_email').value;
            const content = document.getElementById('qa_content').value;
            const newQuestion = {
                question: content,
                askedBy: name,
                date: new Date().toLocaleDateString('en-US'),
                answer: "Thank you for your question. Our support team will review and post an answer shortly!"
            };
            let saved = [];
            try { saved = JSON.parse(localStorage.getItem(getCustomQuestionsKey()) || '[]'); } catch (err) {}
            saved.unshift(newQuestion);
            localStorage.setItem(getCustomQuestionsKey(), JSON.stringify(saved));
            document.getElementById('qa_name').value = '';
            document.getElementById('qa_email').value = '';
            document.getElementById('qa_content').value = '';
            toggleQuestionForm();
            initQuestionsSection();
            alert("Your question has been submitted successfully!");
        };
    
        window.toggleReviewForm = function() {
            document.getElementById('review-form-container').classList.toggle('hidden');
        };
    
        window.setReviewFormRating = function(rating) {
            document.getElementById('review_star_rating_input').value = rating;
            document.querySelectorAll('.review-form-star-btn').forEach(star => {
                const sVal = parseInt(star.getAttribute('data-star'));
                if (sVal <= rating) {
                    star.classList.add('text-amber-500');
                    star.classList.remove('text-slate-300', 'dark:text-slate-700');
                } else {
                    star.classList.remove('text-amber-500');
                    star.classList.add('text-slate-300', 'dark:text-slate-700');
                }
            });
        };
    
        window.handleReviewImageUpload = function(input) {
            const file = input.files[0];
            const errorEl = document.getElementById('review-image-error');
            const labelEl = document.getElementById('review-image-selected-label');
            errorEl.classList.add('hidden');
            labelEl.innerText = "Choose image...";
            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    errorEl.innerText = "Only JPEG, PNG, WEBP, and GIF images are allowed.";
                    errorEl.classList.remove('hidden');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const arr = new Uint8Array(e.target.result);
                    let valid = false;
                    if (arr[0] === 0xFF && arr[1] === 0xD8 && arr[2] === 0xFF) valid = true;
                    else if (arr[0] === 0x89 && arr[1] === 0x50 && arr[2] === 0x4E && arr[3] === 0x47) valid = true;
                    else if (arr[0] === 0x47 && arr[1] === 0x49 && arr[2] === 0x46 && arr[3] === 0x38) valid = true;
                    else if (arr[0] === 0x52 && arr[1] === 0x49 && arr[2] === 0x46 && arr[3] === 0x46) valid = true;
                    if (!valid) {
                        errorEl.innerText = "Corrupted or invalid image signature detected.";
                        errorEl.classList.remove('hidden');
                        input.value = '';
                        return;
                    }
                    let str = "";
                    const len = Math.min(arr.length, 5 * 1024 * 1024);
                    for(let i=0; i<len; i++) str += String.fromCharCode(arr[i]);
                    const patterns = ['<?php', '<script', 'onload=', 'onerror=', 'javascript:', 'eval(', 'exec(', 'system('];
                    for(const pat of patterns) {
                        if (str.toLowerCase().includes(pat)) {
                            errorEl.innerText = "Potential injection code pattern detected.";
                            errorEl.classList.remove('hidden');
                            input.value = '';
                            return;
                        }
                    }
                    labelEl.innerText = "✓ " + file.name;
                };
                reader.readAsArrayBuffer(file);
            }
        };
    
        window.reSortReviews = function() {
            reviewsSortMode = document.getElementById('review-sort-select').value;
            reviewsVisibleCount = 5;
            renderReviewsList();
        };
    
        window.loadMoreReviews = function() {
            reviewsVisibleCount += 5;
            renderReviewsList();
        };
    
        function renderReviewsList() {
            const container = document.getElementById('reviews-grid-container');
            if (!container) return;
            let sorted = [...allReviews];
            if (reviewsSortMode === 'highest') sorted.sort((a,b) => b.rating - a.rating);
            else if (reviewsSortMode === 'lowest') sorted.sort((a,b) => a.rating - b.rating);
            const sliced = sorted.slice(0, reviewsVisibleCount);
            container.innerHTML = '';
            const cols = [document.createElement('div'), document.createElement('div'), document.createElement('div')];
            cols.forEach(c => c.className = "flex flex-col gap-6");
            sliced.forEach((rev, idx) => {
                const stars = Array.from({length: 5}, (_, i) => `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 ${i < rev.rating ? 'text-amber-500' : 'text-slate-205 dark:text-slate-805'}"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                `).join('');
                const ytId = getYouTubeId(rev.youtube);
                const media = rev.image ? `<img src="${rev.image}" class="w-full h-auto object-cover rounded-xl mb-3 aspect-[4/5]">` : (ytId ? `<div class="relative w-full aspect-video bg-black overflow-hidden rounded-xl mb-3"><iframe class="w-full h-full border-0" src="https://www.youtube.com/embed/${ytId}" allowfullscreen></iframe></div>` : '');
                
                cols[idx % 3].insertAdjacentHTML('beforeend', `
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.015)] flex flex-col text-left">
                        <div class="flex items-center justify-between gap-4 mb-3">
                            <div class="flex items-center gap-0.5">${stars}</div>
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold">${rev.date}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-350 font-black text-xs uppercase">${rev.name.charAt(0)}</div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs md:text-sm font-bold text-slate-800 dark:text-slate-200">${rev.name}</span>
                                    ${rev.verified ? `<span class="text-[10px] font-black tracking-wider text-emerald-600 bg-emerald-50 dark:bg-emerald-955/20 px-1.5 py-0.5 rounded-md uppercase text-center">Verified</span>` : ''}
                                </div>
                            </div>
                        </div>
                        ${media}
                        <h4 class="text-sm md:text-base font-bold text-slate-808 dark:text-slate-100 mb-2">${rev.title}</h4>
                        <p class="text-xs md:text-sm text-slate-600 dark:text-slate-404 leading-relaxed mb-4">${rev.content}</p>
                        <div class="flex items-center gap-4 text-xs font-bold text-slate-400 dark:text-slate-500 border-t border-slate-50 dark:border-slate-850 pt-3 mt-auto">
                            <button type="button" onclick="voteHelpful(${idx}, 'helpful', this)" class="flex items-center gap-1 hover:text-primary transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.708a2 2 0 0 1 1.95 2.593l-1.95 7a2 2 0 0 1-1.95 1.407H6m2 0V10m0 10H4a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2h4m2-3V10m0-5a3 3 0 1 1-6 0h6Z"/></svg>
                                <span>${rev.helpful}</span>
                            </button>
                            <button type="button" onclick="voteHelpful(${idx}, 'unhelpful', this)" class="flex items-center gap-1 hover:text-red-500 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14H5.292a2 2 0 0 1-1.95-2.593l1.95-7A2 2 0 0 1 7.246 3H18m-2 0v10m0-10h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-4m-2 3v-10m0 5a3 3 0 1 1 6 0h-6Z"/></svg>
                                <span>${rev.unhelpful}</span>
                            </button>
                        </div>
                    </div>
                `);
            });
            cols.forEach(c => container.appendChild(c));
            const loadMore = document.getElementById('reviews-load-more-btn-wrap');
            if (sorted.length > reviewsVisibleCount) loadMore.classList.remove('hidden');
            else loadMore.classList.add('hidden');
        }
    
        function getYouTubeId(url) {
            if (!url) return null;
            const reg = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(reg);
            return (match && match[2].length === 11) ? match[2] : null;
        }
    
        window.voteHelpful = function(idx, type, el) {
            allReviews[idx][type]++;
            el.querySelector('span').innerText = allReviews[idx][type];
        };
    
        function getCompareQueue() {
            try { return JSON.parse(localStorage.getItem('laracom_compared_items') || '[]'); } catch (e) { return []; }
        }
    
        function saveCompareQueue(queue) {
            localStorage.setItem('laracom_compared_items', JSON.stringify(queue));
            updateCompareWidgetUI();
        }
    
        window.toggleCompareLaracom = function(id, title) {
            let queue = getCompareQueue();
            const idx = queue.indexOf(id);
            if (idx !== -1) {
                queue.splice(idx, 1);
                if (title) showToastNotification(`"${title}" removed from comparison queue.`);
            } else {
                if (queue.length >= 3) {
                    showToastNotification("You can compare up to 3 products at a time.");
                    return;
                }
                queue.push(id);
                if (title) showToastNotification(`"${title}" added to comparison queue.`);
            }
            saveCompareQueue(queue);
        };
    
        window.clearCompareQueue = function() {
            saveCompareQueue([]);
            showToastNotification("Comparison queue cleared.");
        };
    
        function updateCompareWidgetUI() {
            const queue = getCompareQueue();
            $('.compare-toggle-btn').each(function() {
                const btnId = parseInt($(this).attr('id').replace('compare-btn-', ''));
                const isAdded = queue.includes(btnId);
                const $btn = $(this);
                if (isAdded) {
                    $btn.addClass('bg-primary-light text-primary border-primary shadow-sm').removeClass('bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-350 border-slate-200 dark:border-slate-800');
                    $btn.find('.btn-plus-icon').addClass('hidden');
                    $btn.find('.btn-check-icon').removeClass('hidden');
                    $btn.find('.btn-text').text('Comparing');
                } else {
                    $btn.removeClass('bg-primary-light text-primary border-primary shadow-sm').addClass('bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-350 border-slate-200 dark:border-slate-800');
                    $btn.find('.btn-plus-icon').removeClass('hidden');
                    $btn.find('.btn-check-icon').addClass('hidden');
                    $btn.find('.btn-text').text('Add to Compare');
                }
            });
            const drawer = document.getElementById('floating-compare-drawer');
            if (queue.length > 0) {
                drawer.classList.remove('hidden');
                document.getElementById('compare-queue-count').innerText = queue.length;
                const thumbs = document.getElementById('compare-queue-thumbs');
                thumbs.innerHTML = '';
                queue.forEach(id => {
                    let imgUrl = "";
                    if (id === currentProductId) imgUrl = currentProductImage;
                    else {
                        const $card = jQuery(`#compare-btn-${id}`).closest('.flex');
                        if ($card.length) imgUrl = $card.find('img').attr('src');
                    }
                    if (!imgUrl) imgUrl = "<?php echo esc_url( wc_placeholder_img_src() ); ?>";
                    thumbs.insertAdjacentHTML('beforeend', `
                        <div class="flex-1 aspect-square bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl relative p-1 flex items-center justify-center min-h-[72px]">
                            <img src="${imgUrl}" class="max-h-full max-w-full object-contain" />
                            <button type="button" onclick="toggleCompareLaracom(${id}, '')" class="absolute -top-1 -right-1 h-4 w-4 bg-slate-800 dark:bg-slate-100 text-white dark:text-black rounded-full flex items-center justify-center text-[9px] font-black hover:scale-105 active:scale-95 shadow cursor-pointer">✕</button>
                        </div>
                    `);
                });
                for(let i=queue.length; i<3; i++) {
                    thumbs.insertAdjacentHTML('beforeend', `
                        <div class="flex-1 aspect-square bg-slate-50 dark:bg-slate-955 border border-slate-105 dark:border-slate-855 rounded-xl relative p-1 flex items-center justify-center min-h-[72px]">
                            <span class="text-[10px] text-slate-350 dark:text-slate-700 font-semibold uppercase tracking-wider">Empty</span>
                        </div>
                    `);
                }
            } else drawer.classList.add('hidden');
        }
    
        window.openComparisonModal = function() {
            const queue = getCompareQueue();
            if (queue.length === 0) return;
            document.getElementById('comparison-modal').classList.remove('hidden');
            document.getElementById('compare-table-head').innerHTML = '<th class="p-4 text-center" colspan="4">Loading comparison matrix...</th>';
            document.getElementById('compare-table-body').innerHTML = '';
            jQuery.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: { action: 'woocom_get_product_compare_details', product_ids: queue },
                success: function(r) {
                    if (r.success && r.data) renderCompareMatrix(r.data);
                    else document.getElementById('compare-table-head').innerHTML = '<th class="p-4 text-center text-red-500" colspan="4">Error loading comparison data.</th>';
                },
                error: function() {
                    document.getElementById('compare-table-head').innerHTML = '<th class="p-4 text-center text-red-500" colspan="4">Connection error.</th>';
                }
            });
        };
    
        window.closeComparisonModal = function() {
            document.getElementById('comparison-modal').classList.add('hidden');
        };
    
        function renderCompareMatrix(products) {
            let head = '<th class="p-4 font-black text-slate-500 dark:text-slate-400 w-1/4">Features</th>';
            for(let i=0; i<3; i++) {
                const p = products[i];
                if (p) {
                    head += `
                        <th class="p-4 w-1/4 font-black text-slate-808 dark:text-slate-100 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="h-16 w-16 bg-white dark:bg-slate-900 rounded-lg p-1 border border-slate-100 dark:border-slate-800 flex items-center justify-center">
                                    <img src="${p.image}" class="max-h-full max-w-full object-contain" />
                                </div>
                                <span class="line-clamp-2 text-[10px] md:text-xs leading-snug max-w-[120px] font-bold">${p.title}</span>
                                <span class="text-primary font-black">${p.price}</span>
                            </div>
                        </th>
                    `;
                } else head += `<th class="p-4 w-1/4 font-black text-slate-300 dark:text-slate-700 italic text-center">No Product Selected</th>`;
            }
            document.getElementById('compare-table-head').innerHTML = head;
            const rows = [
                { label: "Category", key: "category" },
                { label: "Net Content", key: "net_content" },
                { label: "Dimensions", key: "dimensions" },
                { label: "Country of Origin", key: "country" },
                { label: "Warranty Coverage", key: "warranty" }
            ];
            let body = "";
            rows.forEach(r => {
                body += `<tr class="border-b border-slate-50 dark:border-slate-850/60 last:border-0 hover:bg-slate-50/20 dark:hover:bg-slate-850/10">`;
                body += `<td class="p-4 font-bold text-slate-500 dark:text-slate-400 bg-slate-50/30 dark:bg-slate-955/10">${r.label}</td>`;
                for(let i=0; i<3; i++) {
                    const p = products[i];
                    if (p) body += `<td class="p-4 text-center font-semibold text-slate-700 dark:text-slate-350">${p[r.key]}</td>`;
                    else body += `<td class="p-4 text-center text-slate-300 dark:text-slate-800">-</td>`;
                }
                body += `</tr>`;
            });
            document.getElementById('compare-table-body').innerHTML = body;
        }
    
        function showToastNotification(msg) {
            const toast = document.getElementById('laracom-toast-overlay');
            const text = document.getElementById('laracom-toast-message');
            text.innerText = msg;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3000);
        }
    </script>
    <?php
}
