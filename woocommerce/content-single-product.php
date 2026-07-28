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
$request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
                        <?php echo woocom_render_stock_request_badge( $request_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            if ( $has_gallery ) :
            ?>
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
            <!-- Title -->
            <h1 id="product-title" class="text-xl md:text-3xl font-bold text-gray-900 mb-2 leading-tight"><?php the_title(); ?></h1>
            
            <!-- Price and Save Badge -->
            <div class="flex items-center gap-3 mb-4 flex-wrap">
                <span id="product-price" class="text-secondary font-bold text-2xl md:text-3xl leading-none">
                    <?php echo $product->get_price_html(); ?>
                </span>
                
                <!-- Dynamic Save Badge next to price -->
                <?php
                    $discount_label = '';
                if ( $product->is_on_sale() ) :
                    $regular_price = (float)$product->get_regular_price();
                    $sale_price = (float)$product->get_price();
                    $savings = $regular_price - $sale_price;
                    $discount_label = $savings > 0 ? 'Save ৳' . round($savings) : '';
                endif;
                ?>
                <div id="product-image-sale-badge" class="inline-block <?php echo $discount_label ? '' : 'hidden'; ?>" data-original-label="<?php echo esc_attr( $discount_label ); ?>">
                    <span class="bg-secondary text-white text-[12px] sm:text-[13px] font-bold px-3 py-1 rounded-full shadow-sm align-middle inline-block"><?php echo esc_html( $discount_label ); ?></span>
                </div>
            </div>

            <?php if ( $request_type ) : ?>
                <div class="mb-4">
                    <?php echo woocom_render_stock_request_badge( $request_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endif; ?>

            <!-- Action Buttons Area -->
            <div class="product-actions-wrapper mb-6">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Quantity buttons logic
                const updateQty = (btn, delta) => {
                    const isVariableForm = btn.closest('.variations_form');
                    
                    if (isVariableForm && (!window.currentSelectedVariation || !window.currentSelectedVariation.variation_id)) {
                        alert('Please select a variation first.');
                        return;
                    }

                    const wrapper = btn.parentElement;
                    const input = wrapper.querySelector('input[type="number"]');
                    if (!input) return;
                    
                    let val = parseInt(input.value) || 1;
                    val = Math.max(1, val + delta);
                    input.value = val;
                    
                    // Update hidden quantity inputs to match
                    document.querySelectorAll('.hidden-qty-input').forEach(hiddenInput => {
                        hiddenInput.value = val;
                    });
                    
                    // Trigger change event for WooCommerce compatibility
                    input.dispatchEvent(new Event('change', { bubbles: true }));

                    // Force FBT update directly
                    if (window.updateFbtMainProduct) {
                        if (typeof window.currentSelectedVariation !== 'undefined' && window.currentSelectedVariation) {
                            window.updateFbtMainProduct(window.currentSelectedVariation, val);
                        } else {
                            window.updateFbtMainProduct(null, val);
                        }
                    }
                };

                // Sync quantity on manual change/initialization
                document.querySelectorAll('.qty-input, input[name="quantity"]').forEach(input => {
                    input.addEventListener('change', function() {
                        const val = this.value;
                        document.querySelectorAll('.hidden-qty-input').forEach(hiddenInput => {
                            hiddenInput.value = val;
                        });
                    });
                });

                document.body.addEventListener('click', function(e) {
                    const plusBtn = e.target.closest('.quantity-plus');
                    const minusBtn = e.target.closest('.quantity-minus');
                    
                    if (plusBtn) {
                        updateQty(plusBtn, 1);
                    } else if (minusBtn) {
                        updateQty(minusBtn, -1);
                    }
                }, true); // Use capture phase to bypass any cached stopPropagation

                // Buy Now Logic for Variable products
                document.body.addEventListener('click', function(e) {
                    const buyNowBtn = e.target.closest('#buy-now-btn');
                    if (!buyNowBtn) return;

                    const form = buyNowBtn.closest('form.variations_form');
                    if (!form) return;

                    const unavailableMessage = (window.woocom_ajax && woocom_ajax.variation_unavailable_message)
                        ? woocom_ajax.variation_unavailable_message
                        : 'Sorry, this product is unavailable. Please choose a different combination.';

                    const addButton = form.querySelector('.single_add_to_cart_button');
                    if (
                        buyNowBtn.disabled ||
                        buyNowBtn.classList.contains('disabled') ||
                        form.classList.contains('has-unavailable-variation') ||
                        (addButton && (addButton.disabled || addButton.classList.contains('wc-variation-is-unavailable')))
                    ) {
                        alert(unavailableMessage);
                        return;
                    }

                    // Check if variation is selected
                    const variationId = form.querySelector('input[name="variation_id"]').value;
                    if (!variationId || variationId === "0") {
                        alert('Please select a variant first.');
                        return;
                    }

                    // Add a hidden field to tell WC to redirect to checkout, 
                    // or just change form action to checkout with add-to-cart query
                    const checkoutUrl = '<?php echo esc_url( wc_get_checkout_url() ); ?>';
                    
                    // Method: submit to same page but add a flag or use AJAX
                    // Simpler: submit form normally, but we need to redirect after successful add to cart.
                    // Actually, WooCommerce Buy Now buttons usually just add to cart and then redirect.
                    
                    // Let's use a simpler approach: change form action and submit
                    // But WC forms usually submit to the product page.
                    
                    // Better approach: use a hidden input to signal redirect
                    let redirectInput = form.querySelector('input[name="buy_now_redirect"]');
                    if (!redirectInput) {
                        redirectInput = document.createElement('input');
                        redirectInput.type = 'hidden';
                        redirectInput.name = 'buy_now_redirect';
                        redirectInput.value = '1';
                        form.appendChild(redirectInput);
                    }
                    
                    form.submit();
                });
            });
            </script>

            </div>


        </div>
    </div>

    <!-- Frequently Bought Together Section (Full Width) -->
    <?php
    $related_ids = get_post_meta($product->get_id(), '_fbt_ids', true);
    
    if (!empty($related_ids)) :
    ?>
    <div class="mt-8 bg-white border border-gray-100 rounded-xl p-4 md:p-5 shadow-sm overflow-hidden">
        <h3 class="text-[16px] font-bold text-[#253D4E] mb-3">Frequently bought together</h3>
        
        <div class="fbt-items-row flex flex-col lg:flex-row items-stretch justify-start gap-3">
            <!-- Current Product -->
            <?php
                $main_regular = (float)($product->get_regular_price() ? $product->get_regular_price() : $product->get_price());
                $main_sale = (float)$product->get_price();
                $main_savings = $main_regular - $main_sale;
            ?>
            <div class="fbt-item fbt-product-card fbt-main-item flex flex-row items-center bg-white p-3 rounded-md border border-secondary relative group transition-all w-full"
                 data-base-price="<?php echo esc_attr($product->get_price()); ?>"
                 data-regular-price="<?php echo esc_attr($main_regular); ?>"
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
                <input type="hidden" class="fbt-regular-price" value="<?php echo $main_regular; ?>">
                <input type="hidden" class="fbt-id" value="<?php echo $product->get_id(); ?>">
            </div>

            <!-- Plus Symbol -->
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

            <!-- Equal Symbol -->
            <div class="hidden lg:flex items-center justify-center text-gray-400 font-light text-xl">=</div>

            <!-- Total / Action Card -->
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

    <script>
        const priceDecimals = <?php echo function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2; ?>;

        function toggleFbt(el) {
            const cb = el.querySelector('.fbt-checkbox');
            const customCb = el.querySelector('.custom-cb');
            const customTick = el.querySelector('.custom-cb-tick');
            cb.checked = !cb.checked;
            
            if(cb.checked) {
                el.classList.add('border-secondary');
                el.classList.remove('border-gray-200', 'opacity-60');
                if(customCb) {
                    customCb.classList.add('bg-secondary');
                    customCb.classList.remove('bg-white');
                }
                if(customTick) customTick.classList.remove('hidden');
            } else {
                el.classList.remove('border-secondary');
                el.classList.add('border-gray-200', 'opacity-60');
                if(customCb) {
                    customCb.classList.remove('bg-secondary');
                    customCb.classList.add('bg-white');
                }
                if(customTick) customTick.classList.add('hidden');
            }
            updateFbtTotal();
        }

        function updateFbtTotal() {
            let total = 0;
            let totalRegular = 0;
            let count = 0;
            
            document.querySelectorAll('.fbt-item').forEach(item => {
                const cb = item.querySelector('.fbt-checkbox');
                if (cb && cb.checked) {
                    const price = parseFloat(item.querySelector('.fbt-price').value) || 0;
                    const regPrice = parseFloat(item.querySelector('.fbt-regular-price')?.value || item.querySelector('.fbt-price').value) || 0;
                    
                    total += price;
                    totalRegular += regPrice;
                    count++;
                }
            });
            
            document.getElementById('fbt-total-display').innerText = '৳' + total.toFixed(priceDecimals);
            document.getElementById('fbt-count').innerText = count;
            
            // Calculate and show savings
            const savings = totalRegular - total;
            const savingsBadge = document.getElementById('fbt-save-display');
            if (savingsBadge) {
                if (savings > 0) {
                    savingsBadge.innerText = 'Save ৳ ' + Math.round(savings);
                    savingsBadge.classList.remove('invisible');
                } else {
                    savingsBadge.classList.add('invisible');
                }
            }
        }

        // Global function to allow variable.php to update the main FBT product card
        window.updateFbtMainProduct = function(variationData, qtyOverride) {
            const mainItem = document.querySelector('.fbt-main-item');
            if (!mainItem) return;

            let qty = 1;
            if (qtyOverride !== undefined && qtyOverride !== null) {
                qty = parseInt(qtyOverride) || 1;
            } else {
                const mainForm = document.querySelector('form.cart');
                const qtyInput = mainForm ? mainForm.querySelector('.qty-input, input[name="quantity"], .quantity input[type="number"]') : document.querySelector('.qty-input, input[name="quantity"], .quantity input[type="number"]');
                qty = parseInt(qtyInput ? qtyInput.value : 1) || 1;
            }

            const saveBadge = mainItem.querySelector('.fbt-item-save');

            if (variationData) {
                const price = parseFloat(variationData.display_price) || 0;
                const regPrice = parseFloat(variationData.display_regular_price || variationData.display_price) || 0;
                
                mainItem.querySelector('.fbt-price').value = price * qty;
                mainItem.querySelector('.fbt-regular-price').value = regPrice * qty;
                
                if (mainItem.querySelector('.fbt-main-price-display')) {
                    mainItem.querySelector('.fbt-main-price-display').innerText = '৳' + (price * qty);
                }

                // Update per-item save badge
                const itemSave = (regPrice - price) * qty;
                if (saveBadge) {
                    if (itemSave > 0) {
                        const amtSpan = saveBadge.querySelector('.save-amt');
                        if(amtSpan) amtSpan.innerText = Math.round(itemSave);
                        saveBadge.classList.remove('hidden');
                    } else {
                        saveBadge.classList.add('hidden');
                    }
                }
                
                if (variationData.image && variationData.image.thumb_src) {
                    mainItem.querySelector('.fbt-main-img').src = variationData.image.thumb_src;
                } else if (variationData.image && variationData.image.src) {
                    mainItem.querySelector('.fbt-main-img').src = variationData.image.src;
                }
            } else {
                const basePrice = parseFloat(mainItem.dataset.basePrice) || 0;
                const regPrice = parseFloat(mainItem.dataset.regularPrice || mainItem.dataset.basePrice) || 0;
                
                mainItem.querySelector('.fbt-price').value = basePrice * qty;
                mainItem.querySelector('.fbt-regular-price').value = regPrice * qty;
                
                if (mainItem.querySelector('.fbt-main-price-display')) {
                    mainItem.querySelector('.fbt-main-price-display').innerText = '৳' + (basePrice * qty);
                }

                // Update per-item save badge
                const itemSave = (regPrice - basePrice) * qty;
                if (saveBadge) {
                    if (itemSave > 0) {
                        const amtSpan = saveBadge.querySelector('.save-amt');
                        if(amtSpan) amtSpan.innerText = Math.round(itemSave);
                        saveBadge.classList.remove('hidden');
                    } else {
                        saveBadge.classList.add('hidden');
                    }
                }
                
                mainItem.querySelector('.fbt-main-img').src = mainItem.dataset.originalImg;
            }
            
            updateFbtTotal();
        };

        // Listen for quantity changes to keep FBT perfectly in sync
        document.addEventListener('change', function(e) {
            if (e.target.matches('.qty-input, input[name="quantity"], .quantity input[type="number"]')) {
                const newQty = parseInt(e.target.value) || 1;
                if (window.currentSelectedVariation) {
                    window.updateFbtMainProduct(window.currentSelectedVariation, newQty);
                } else {
                    window.updateFbtMainProduct(null, newQty);
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initial call to set total and savings
            updateFbtTotal();
        });

        document.getElementById('fbt-add-all')?.addEventListener('click', function() {
            const btn = this;
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-secondary mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            const itemsData = [];
            document.querySelectorAll('.fbt-item').forEach(item => {
                const cb = item.querySelector('input[type="checkbox"]');
                if (cb && cb.checked) {
                    if (item.classList.contains('fbt-main-item')) {
                        let variation_id = 0;
                        let variationData = {};
                        
                        if (window.currentSelectedVariation) {
                            variation_id = window.currentSelectedVariation.variation_id;
                            variationData = window.currentSelectedVariation.attributes || {};
                        }

                        const mainForm = document.querySelector('form.cart');
                        const qtyInput = mainForm ? mainForm.querySelector('.qty-input, input[name="quantity"]') : document.querySelector('.qty-input, input[name="quantity"]');
                        const mainQty = parseInt(qtyInput ? qtyInput.value : 1) || 1;

                        itemsData.push({
                            product_id: parseInt(item.querySelector('.fbt-id').value),
                            variation_id: variation_id,
                            quantity: mainQty,
                            variation: variationData
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
                btn.disabled = false;
                btn.innerHTML = originalContent;
                return;
            }

            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'add_multiple_products_to_cart',
                    items: itemsData
                },
                success: function(response) {
                    if (response.success !== false) {
                        jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                        btn.disabled = false;
                        btn.innerText = 'Added Successfully!';
                        setTimeout(() => {
                            btn.innerHTML = originalContent;
                            updateFbtTotal();
                        }, 2000);
                    } else {
                        btn.disabled = false;
                        btn.innerText = 'Error!';
                        setTimeout(() => btn.innerHTML = originalContent, 2000);
                    }
                },
                error: function() {
                    btn.disabled = false;
                    btn.innerText = 'Error!';
                    setTimeout(() => btn.innerHTML = originalContent, 2000);
                }
            });
        });
    </script>
    <?php endif; ?>

    <!-- Product Tabs (Modern Boxed Design) -->
    <?php
    $video_url   = get_post_meta($product->get_id(), '_product_video_url', true);
    $has_video   = ! empty($video_url);
    $has_reviews = $product->get_review_count() > 0;
    $has_content = ! empty( trim( get_the_content() ) );
    $has_tabs    = $has_video || $has_reviews;

    if ( $has_content || $has_video || $has_reviews ) :
    ?>
    <div class="mt-16">
        <?php
        if ( $has_tabs ) :
            // Decide default active tab
            $active_tab = 'description';
            if ( ! $has_content ) {
                $active_tab = $has_video ? 'video' : 'reviews';
            }
        ?>
            <!-- Tab Buttons Container (Card Style) -->
            <div class="flex w-full lg:inline-flex lg:w-auto gap-1 sm:gap-2 mb-6 bg-white border border-gray-100 p-1 rounded-lg shadow-sm">
                <?php if ( $has_content ) : ?>
                <button class="tab-trigger flex-1 lg:flex-none <?php echo ($active_tab === 'description') ? 'active bg-secondary text-white shadow-sm' : 'bg-transparent text-[#7E7E7E]'; ?> px-1 sm:px-6 py-2 sm:py-2.5 rounded-md text-[11px] sm:text-[14px] font-bold transition-all cursor-pointer border-none hover:text-white whitespace-nowrap text-center" data-target="tab-description">
                    Description
                </button>
                <?php endif; ?>
                
                <?php if ( $has_video ) : ?>
                <button class="tab-trigger flex-1 lg:flex-none <?php echo ($active_tab === 'video') ? 'active bg-secondary text-white shadow-sm' : 'bg-transparent text-[#7E7E7E]'; ?> px-1 sm:px-6 py-2 sm:py-2.5 rounded-md text-[11px] sm:text-[14px] font-bold hover:bg-gray-50 hover:text-[#7E7E7E] transition-all cursor-pointer border-none whitespace-nowrap text-center" data-target="tab-video">
                    <span class="sm:hidden">Video</span>
                    <span class="hidden sm:inline">Product Video</span>
                </button>
                <?php endif; ?>
                
                <?php if ( $has_reviews ) : ?>
                <button class="tab-trigger flex-1 lg:flex-none <?php echo ($active_tab === 'reviews') ? 'active bg-secondary text-white shadow-sm' : 'bg-transparent text-[#7E7E7E]'; ?> px-1 sm:px-6 py-2 sm:py-2.5 rounded-md text-[11px] sm:text-[14px] font-bold hover:bg-gray-50 hover:text-[#7E7E7E] transition-all cursor-pointer border-none whitespace-nowrap text-center" data-target="tab-reviews">
                    <span class="sm:hidden">Reviews (<?php echo $product->get_review_count(); ?>)</span>
                    <span class="hidden sm:inline">Customer Reviews (<?php echo $product->get_review_count(); ?>)</span>
                </button>
                <?php endif; ?>
            </div>

            <!-- Tab Content Box -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 md:p-10 shadow-sm min-h-[300px]">
                <?php if ( $has_content ) : ?>
                <!-- Description Panel -->
                <div id="tab-description" class="tab-panel <?php echo ($active_tab === 'description') ? 'active' : 'hidden'; ?> animate-fadeIn">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        Product Details
                    </h3>
                    <div class="prose max-w-none text-gray-600 leading-[1.8] text-[15px]">
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $has_video ) : ?>
                <!-- Video Panel -->
                <div id="tab-video" class="tab-panel <?php echo ($active_tab === 'video') ? 'active' : 'hidden'; ?> animate-fadeIn">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        Product Video
                    </h3>
                    <?php 
                    // Simple YouTube/Vimeo Embed Logic
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
                    <?php else: ?>
                        <div class="aspect-video bg-gray-50 rounded-xl flex items-center justify-center border border-dashed border-gray-200">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mx-auto mb-3"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2" ry="2"/></svg>
                                <p class="text-gray-400 font-medium">Invalid video URL format. Please use a direct YouTube or Vimeo link.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ( $has_reviews ) : ?>
                <!-- Reviews Panel -->
                <div id="tab-reviews" class="tab-panel <?php echo ($active_tab === 'reviews') ? 'active' : 'hidden'; ?> animate-fadeIn">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        Customer Reviews
                    </h3>
                    <div class="max-w-3xl">
                        <?php 
                        if ( comments_open() ) {
                            comments_template();
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <!-- Just show Description directly (No Tabs needed) -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 md:p-10 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    Product Details
                </h3>
                <div class="prose max-w-none text-gray-600 leading-[1.8] text-[15px]">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        .tab-panel.hidden { display: none; }
    </style>

    <script>
        // Gallery Image Changer (Horizontal CSS Translate Slider)
        function changeMainImage(thumbnailEl, index) {
            const slider = document.getElementById('main-image-slider');
            if (!slider) return;

            slider.style.transform = `translateX(-${index * 100}%)`;
            slider.dataset.currentIndex = index;

            // Update thumbnail borders
            document.querySelectorAll('.thumbnail-item').forEach(item => {
                item.classList.remove('border-secondary');
                item.classList.add('border-transparent');
            });
            thumbnailEl.classList.add('border-secondary');
            thumbnailEl.classList.remove('border-transparent');
        }

        function slidePrev() {
            const slider = document.getElementById('main-image-slider');
            if (!slider) return;
            const total = slider.children.length;
            if (total <= 1) return;
            let current = parseInt(slider.dataset.currentIndex || 0);
            current = (current - 1 + total) % total;
            
            const thumbs = document.querySelectorAll('.thumbnail-item');
            if (thumbs[current]) {
                thumbs[current].click();
            }
        }

        function slideNext() {
            const slider = document.getElementById('main-image-slider');
            if (!slider) return;
            const total = slider.children.length;
            if (total <= 1) return;
            let current = parseInt(slider.dataset.currentIndex || 0);
            current = (current + 1) % total;
            
            const thumbs = document.querySelectorAll('.thumbnail-item');
            if (thumbs[current]) {
                thumbs[current].click();
            }
        }

        // Store original main image src and srcset globally for dynamic variation resets
        // Also bind the hover-zoom listener to each slide
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('main-image-slider');
            if (slider) {
                // Variation reset capture
                if (slider.children[0]) {
                    const firstImg = slider.children[0].querySelector('img');
                    if (firstImg) {
                        window.originalMainImageSrc = firstImg.src;
                        window.originalMainImageSrcset = firstImg.srcset || '';
                    }
                }

                // Hover & Touch Zoom Setup
                const slides = slider.children;
                for (let i = 0; i < slides.length; i++) {
                    const slide = slides[i];
                    const img = slide.querySelector('img');
                    if (!img) continue;

                    // Set transition behavior via JS style to ensure compatibility
                    img.style.transition = 'transform 0.15s ease-out';
                    img.style.cursor = 'zoom-in';

                    // 1. Desktop Hover Zoom
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
                            img.style.transformOrigin = 'center center';
                        }
                    });

                    // 2. Mobile Touch Zoom & Pan
                    let isMobileZoomed = false;

                    slide.addEventListener('click', function(e) {
                        if (window.matchMedia('(max-width: 1023px)').matches) {
                            isMobileZoomed = !isMobileZoomed;
                            if (isMobileZoomed) {
                                img.style.transform = 'scale(2.5)';
                                img.style.transformOrigin = '50% 50%';
                            } else {
                                img.style.transform = 'scale(1)';
                                img.style.transformOrigin = 'center center';
                            }
                        }
                    });

                    slide.addEventListener('touchmove', function(e) {
                        if (window.matchMedia('(max-width: 1023px)').matches && isMobileZoomed && e.touches.length === 1) {
                            // Prevent native page scroll while panning the zoomed image
                            e.preventDefault();

                            const touch = e.touches[0];
                            const rect = slide.getBoundingClientRect();

                            let x = ((touch.clientX - rect.left) / rect.width) * 100;
                            let y = ((touch.clientY - rect.top) / rect.height) * 100;

                            // Clamp values to stay within the image bounds
                            x = Math.max(0, Math.min(100, x));
                            y = Math.max(0, Math.min(100, y));

                            img.style.transformOrigin = `${x}% ${y}%`;
                        }
                    }, { passive: false });
                }
            }
        });

        document.querySelectorAll('.tab-trigger').forEach(trigger => {
            trigger.addEventListener('click', function() {
                // Reset all triggers
                document.querySelectorAll('.tab-trigger').forEach(t => {
                    t.classList.remove('active', 'bg-secondary', 'text-white', 'shadow-md', 'hover:text-white');
                    t.classList.add('bg-transparent', 'text-[#7E7E7E]', 'hover:text-[#7E7E7E]', 'hover:bg-gray-50');
                });

                // Set active trigger
                this.classList.add('active', 'bg-secondary', 'text-white', 'shadow-md', 'hover:text-white');
                this.classList.remove('bg-transparent', 'text-[#7E7E7E]', 'hover:text-[#7E7E7E]', 'hover:bg-gray-50');

                // Toggle panels
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId).classList.remove('hidden');
            });
        });
    </script>

    <?php
    // Simple Related Products logic
    $args = array(
        'post_type'            => 'product',
        'ignore_custom_sort'   => true,
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
    <!-- Related Products -->
    <div class="mt-20">
        <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4 relative">
            <div class="relative">
                <h2 class="text-[24px] font-bold text-[#253D4E]">Related Products</h2>
                <div class="absolute -bottom-4 left-0 w-12 h-[2px] bg-secondary"></div>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <?php while ( $related_query->have_posts() ) : $related_query->the_post(); 
                    global $product;
                    $image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
                    if ( ! $image_url ) $image_url = wc_placeholder_img_src();
                    $request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
                ?>
                    <div class="bg-white rounded-[4px] border border-gray-200 p-3 h-full flex flex-col group/card hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
                        <?php if ( $request_type && function_exists( 'woocom_render_stock_request_badge' ) ) : ?>
                            <div class="absolute top-0 left-0 z-10">
                                <?php echo woocom_render_stock_request_badge( $request_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        <?php endif; ?>

                        <!-- Image -->
                        <div class="relative w-full pt-[100%] mb-2 bg-gray-50/30 rounded overflow-hidden group-img-wrapper">
                            <div class="absolute inset-0 flex items-center justify-center p-0">
                                <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="max-w-full max-h-full object-contain scale-110 mx-auto">
                                </a>
                            </div>
                            <button type="button" class="woocom-quick-view-btn absolute bottom-2 left-1/2 -translate-x-1/2 bg-white/95 hover:bg-primary hover:text-white text-gray-800 text-[10px] sm:text-[11px] font-extrabold px-3 py-1.5 rounded-full shadow-md transition-all duration-300 opacity-0 translate-y-2 group-hover/img:opacity-100 group-hover/img:translate-y-0 flex items-center gap-1.5 whitespace-nowrap cursor-pointer z-10" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                Quick View
                            </button>
                        </div>

                        <!-- Info -->
                        <div class="flex-grow">
                            <h3 class="text-[14px] font-medium text-[#253D4E] leading-tight line-clamp-2 mb-1">
                                <a href="<?php the_permalink(); ?>" class="hover:text-secondary transition-colors"><?php the_title(); ?></a>
                            </h3>
                            <div class="flex items-center gap-1.5 mb-3">
                                <span class="text-secondary font-bold text-[15px]">
                                    <?php echo $product->get_price_html(); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Button -->
                        <?php if ( $request_type && function_exists( 'woocom_render_stock_request_form' ) ) : ?>
                            <?php echo woocom_render_stock_request_form( $product->get_id(), $request_type, 'archive' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php else : ?>
                            <a href="?add-to-cart=<?php echo esc_attr( get_the_ID() ); ?>" class="w-full border border-secondary/40 text-secondary hover:bg-secondary hover:text-white font-bold py-2 rounded-[4px] text-center transition-all duration-300 text-[14px] flex items-center justify-center gap-2 mt-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 sm:w-4.5 sm:h-4.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                Add To Cart
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
