<?php
/**
 * The template for displaying the footer
 *
 * @package Woocom
 */
?>

	<!-- Premium Footer -->
		<footer id="colophon" class="site-footer mt-0">
			<?php
			$footer_logo_url = get_option('footer_logo') ? get_option('footer_logo') : get_option('theme_logo');
			$footer_link_groups = array(
				'information',
				'shop',
				'support',
				'policy',
			);
			?>
			<!-- Main Footer -->
			<div class="bg-white border-t border-gray-100 text-gray-700">
				<div class="container mx-auto px-4 py-12">
					<div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">

						<!-- Brand Column -->
						<div class="lg:col-span-1">
							<?php if ($footer_logo_url) : ?>
							<img src="<?php echo esc_url($footer_logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="h-12 mb-6">
							<?php endif; ?>
							<p class="text-gray-500 text-[14px] leading-relaxed mb-8">
								<?php bloginfo('name'); ?> is an e-commerce platform dedicated to providing safe and reliable food to every home.
							</p>
						
						<div class="space-y-4 text-[14px]">
							<?php if($address = get_option('contact_address')): ?>
							<div class="flex items-start gap-3 text-gray-600">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 mt-0.5 flex-shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
								<span><?php echo esc_html($address); ?></span>
							</div>
							<?php endif; ?>

							<?php if($phone = get_option('contact_phone')): ?>
							<div class="flex items-center gap-3 text-gray-600">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 flex-shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
								<span><?php echo esc_html($phone); ?></span>
							</div>
							<?php endif; ?>

							<?php if($email = get_option('contact_email')): ?>
							<div class="flex items-center gap-3 text-gray-600">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 flex-shrink-0"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
								<span><?php echo esc_html($email); ?></span>
							</div>
							<?php endif; ?>
						</div>

						<div class="flex items-center gap-3 mt-8">
							<?php if($fb = get_option('social_facebook')): ?>
							<a href="<?php echo esc_url($fb); ?>" class="w-10 h-10 rounded-full border border-gray-100 flex items-center justify-center text-secondary hover:bg-secondary/5 transition-all">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
							</a>
							<?php endif; ?>
							<?php if($tw = get_option('social_twitter')): ?>
							<a href="<?php echo esc_url($tw); ?>" class="w-10 h-10 rounded-full border border-gray-100 flex items-center justify-center text-secondary hover:bg-secondary/5 transition-all">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
							</a>
							<?php endif; ?>
							<?php if($ig = get_option('social_instagram')): ?>
							<a href="<?php echo esc_url($ig); ?>" class="w-10 h-10 rounded-full border border-gray-100 flex items-center justify-center text-secondary hover:bg-secondary/5 transition-all">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y2="6.5" y1="6.5"/></svg>
							</a>
							<?php endif; ?>
							<?php if($yt = get_option('social_youtube')): ?>
							<a href="<?php echo esc_url($yt); ?>" class="w-10 h-10 rounded-full border border-gray-100 flex items-center justify-center text-secondary hover:bg-secondary/5 transition-all">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-youtube"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17Z"/><path d="m10 15 5-3-5-3z"/></svg>
							</a>
							<?php endif; ?>
						</div>

					</div>

						<div class="grid grid-cols-2 gap-x-6 gap-y-10 lg:contents">
						<?php foreach ($footer_link_groups as $group_key) : ?>
							<?php
							$footer_links = function_exists('woocom_get_footer_links') ? woocom_get_footer_links($group_key) : array();
							$group_title = function_exists('woocom_get_footer_title') ? woocom_get_footer_title($group_key) : '';
							if (empty($footer_links)) {
								continue;
							}
							?>
							<div class="min-w-0">
								<h3 class="text-[15px] sm:text-[16px] font-bold text-gray-800 mb-5 lg:mb-8"><?php echo esc_html($group_title); ?></h3>
								<ul class="space-y-3 lg:space-y-4 text-[13px] sm:text-[14px]">
									<?php foreach ($footer_links as $footer_link) : ?>
										<?php
										$link_url = !empty($footer_link['url']) ? $footer_link['url'] : '#';
										$link_label = !empty($footer_link['label']) ? $footer_link['label'] : '';
										?>
										<li><a href="<?php echo esc_url($link_url); ?>" class="text-gray-500 hover:text-secondary transition-all"><?php echo esc_html($link_label); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
				</div>

			<!-- Bottom Footer -->
			<div class="border-t border-gray-100 py-8">
				<div class="container mx-auto px-4 text-center">
					<p class="text-[14px] text-gray-400">
						Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
					</p>
				</div>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php if ( function_exists( 'WC' ) && WC()->cart && get_option('enable_cart_drawer', 1) ) : ?>
<?php
$woocom_price_decimals           = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2;
$woocom_price_decimal_separator  = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.';
$woocom_price_thousand_separator = function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',';
$cart_floating_visibility        = get_option( 'cart_drawer_floating_visibility', 'hide_mobile' );
$cart_floating_classes           = array(
	'show_all'     => 'hidden lg:flex',
	'hide_mobile'  => 'hidden lg:flex',
	'hide_desktop' => 'hidden',
	'hide_all'     => 'hidden',
);
$cart_floating_class             = isset( $cart_floating_classes[ $cart_floating_visibility ] ) ? $cart_floating_classes[ $cart_floating_visibility ] : $cart_floating_classes['hide_mobile'];
$show_cart_floating_widget       = ! ( function_exists( 'is_checkout' ) && is_checkout() );
?>
<!-- Sticky Cart Widget -->
<?php if ( $show_cart_floating_widget ) : ?>
<div id="cart-drawer-open-sticky" class="<?php echo esc_attr( $cart_floating_class ); ?> cart-floating-widget fixed right-0 top-1/2 -translate-y-1/2 z-[60] cursor-pointer text-white shadow-[0_12px_25px_rgba(0,0,0,0.15)] flex items-center gap-3 hover:-translate-y-[55%] hover:scale-[1.05] transition-all duration-300 group" style="background-color: var(--color-primary, #1E5D02); border-radius: 9999px 0 0 9999px; padding: 12px 20px 12px 24px;">
    <!-- Pulse Ring Effect -->
    <span class="absolute inset-0 bg-white/20 animate-ping group-hover:hidden duration-1000 opacity-75" style="border-radius: 9999px 0 0 9999px;"></span>
    
    <!-- Icon Container -->
    <div class="relative flex items-center justify-center bg-white/20 p-2 rounded-full group-hover:scale-110 transition-transform duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
    </div>
    
    <!-- Details -->
    <div class="flex flex-col text-left leading-none">
        <span id="sticky-cart-count" class="text-[10px] font-bold uppercase tracking-wider text-white/95 mb-0.5">
            <?php echo WC()->cart->get_cart_contents_count(); ?> Items
        </span>
        <span id="sticky-cart-total" class="text-[13px] font-black tracking-wide">
            <span class="mr-0.5 font-bold">৳</span><?php echo number_format( WC()->cart->get_subtotal(), $woocom_price_decimals, $woocom_price_decimal_separator, $woocom_price_thousand_separator ); ?>
        </span>
    </div>
</div>
<?php endif; ?>

<!-- Cart Drawer Overlay -->
<div id="cart-drawer-overlay" class="fixed inset-0 bg-black/50 z-[209] hidden opacity-0 transition-opacity duration-300"></div>

<!-- Product Quick View Modal -->
<div id="qv-modal" class="fixed inset-0 z-[250] hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
    <!-- Overlay -->
    <div id="qv-overlay" class="absolute inset-0 bg-black/60 backdrop-blur-sm cursor-pointer"></div>
    
    <!-- Content Card -->
    <div class="relative w-full bg-white rounded-xl shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 z-10" style="max-width: 680px !important; width: 100% !important;">
        <!-- Close button -->
        <button id="qv-close" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 hover:rotate-90 transition-all duration-300 z-20 p-2 bg-gray-50 rounded-full shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
        
        <!-- Inside Content (Loaded via AJAX) -->
        <div id="qv-content" class="min-h-[300px] flex items-center justify-center p-4 md:p-6">
            <!-- Spinner -->
            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
</div>

<style>
/* Force Quick View Button Styling and Animations (Prevents Caching Issues) */
.woocom-quick-view-btn {
    position: absolute !important;
    left: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    transform: translateY(100%) !important;
    opacity: 0 !important;
    transition: all 0.3s ease !important;
    border-radius: 0 !important;
    height: 38px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background-color: #ffffff !important;
    color: var(--color-primary, #1E5D02) !important;
    border: 2px solid var(--color-primary, #1E5D02) !important;
    z-index: 10 !important;
}

.woocom-quick-view-btn svg {
    stroke: var(--color-primary, #1E5D02) !important;
}

.group-img-wrapper:hover .woocom-quick-view-btn,
.group\/img:hover .woocom-quick-view-btn {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

.woocom-quick-view-btn:hover,
.woocom-quick-view-btn:active {
    border: 2px solid var(--color-primary, #1E5D02) !important;
    background-color: var(--color-primary, #1E5D02) !important;
    color: #ffffff !important;
}
.woocom-quick-view-btn:hover svg,
.woocom-quick-view-btn:active svg {
    stroke: #ffffff !important;
}

/* Explicit Stacking Context Z-Indexes for Modals & Drawers */
#qv-modal {
    z-index: 999999 !important;
}
#qv-overlay {
    z-index: 999998 !important;
}
#qv-modal > div {
    z-index: 999999 !important;
}
#cart-drawer {
    z-index: 999999 !important;
}
#cart-drawer-overlay {
    z-index: 999998 !important;
}

/* Quick View Modal Layout Fixes */
#qv-content {
    width: 100% !important;
    padding: 12px !important;
    display: block !important;
    min-height: auto !important;
}
#qv-content > div {
    width: 100% !important;
    display: flex !important;
    flex-direction: row !important;
    gap: 24px !important;
    background: #ffffff !important;
    padding: 20px !important;
}
#qv-content > div > div {
    width: 50% !important;
    display: flex !important;
    flex-direction: column !important;
}
#qv-content .relative.w-full.pt-\[100\%\] {
    padding-top: 0 !important;
    height: 310px !important;
    position: relative !important;
    width: 100% !important;
}
@media (max-w: 768px) {
    #qv-content > div {
        flex-direction: column !important;
        gap: 16px !important;
        padding: 12px !important;
    }
    #qv-content > div > div {
        width: 100% !important;
    }
    #qv-content .relative.w-full.pt-\[100\%\] {
        height: 280px !important;
    }
}

/* Mobile Category List UX Improvements */
@media (max-w: 1024px) {
    .foryou-tabs-wrapper {
        position: relative !important;
    }
    .foryou-tabs-wrapper::after {
        content: '' !important;
        position: absolute !important;
        right: 0 !important;
        top: 0 !important;
        height: 100% !important;
        width: 48px !important;
        background: linear-gradient(to right, rgba(249, 249, 249, 0) 0%, rgba(249, 249, 249, 0.98) 100%) !important;
        pointer-events: none !important;
        z-index: 5 !important;
    }
}

/* Hide Floating Cart Widget on Mobile */
@media (max-w: 768px) {
    #woocom-floating-cart {
        display: none !important;
    }
}
</style>

<!-- Cart Drawer -->
<div id="cart-drawer" class="fixed right-0 top-0 h-full w-full sm:w-[400px] bg-white z-[210] transform translate-x-full transition-transform duration-300 flex flex-col shadow-2xl">

    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-bold text-gray-800 tracking-widest uppercase"><?php echo esc_html(get_option('cart_drawer_title', 'Shopping Cart')); ?></h2>
        <button id="cart-drawer-close" class="flex items-center gap-1.5 text-gray-500 hover:text-secondary transition-colors text-sm font-medium">
            Close
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>
    </div>

    <!-- Scrollable Body -->
    <div class="flex-grow overflow-y-auto flex flex-col min-h-0">
        <!-- Promo Unlock Banner -->
        <?php 
        $promo_enabled = get_option('cart_promo_enabled');
        $promo_title = get_option('cart_promo_title');
        $min_amount = get_option('cart_promo_min_amount');
        
        if ($promo_enabled && !empty($promo_title) && !empty($min_amount)) : 
        ?>
        <div id="cart-promo-banner" class="mx-4 mt-4 mb-3 bg-[#FFFAF5] border border-[#FFD6B0] rounded-xl p-3 relative overflow-hidden">
            <button id="cart-promo-close" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 transition-colors z-10">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <div class="flex items-start gap-3">
                <div class="bg-secondary rounded-lg p-1.5 text-white flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5C11 3 12 8 12 8s1-5 4.5-5a2.5 2.5 0 0 1 0 5"/></svg>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-gray-800 leading-snug"><?php echo esc_html($promo_title); ?></p>
                    <p id="cart-promo-text" class="text-[12px] text-gray-500 mt-0.5">Add <span class="text-secondary font-bold">৳<?php echo number_format( $min_amount, $woocom_price_decimals, $woocom_price_decimal_separator, $woocom_price_thousand_separator ); ?></span> more to unlock!</p>
                </div>
            </div>
            <div class="mt-2.5 h-1.5 w-full bg-secondary/20 rounded-full overflow-hidden">
                <div id="cart-promo-progress" class="h-full bg-secondary rounded-full transition-all duration-500" style="width:0%"></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cart Items List -->
        <div id="cart-items-list" class="px-4 py-2 flex-grow">
            <?php
            if ( function_exists( 'WC' ) && WC()->cart ) {
                $cart_items = WC()->cart->get_cart();
                $item_idx = 0;
                foreach ( $cart_items as $cart_item_key => $cart_item ) {
                    $product = $cart_item['data'];
                    $product_id = $cart_item['product_id'];
                    $title = $product->get_name();
                    $price = $product->get_price();
                    $qty = $cart_item['quantity'];
                    $image = function_exists( 'woocom_get_cart_item_image_url' ) ? woocom_get_cart_item_image_url( $cart_item, 'thumbnail' ) : get_the_post_thumbnail_url( $product_id, 'thumbnail' );
                    ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-3 flex gap-3 shadow-sm relative mb-3 hover:border-gray-200 transition-colors">
                        <button class="cart-remove-btn absolute top-2 right-2 transition-colors" style="color: #ef4444;" data-idx="<?php echo $item_idx; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                        <div class="w-16 h-16 bg-gray-50 rounded-lg border border-gray-100 flex-shrink-0 flex items-center justify-center p-1 overflow-hidden">
                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="max-w-full max-h-full object-contain mix-blend-multiply">
                        </div>
                        <div class="flex-grow pt-0.5 pr-5">
                            <a href="<?php echo get_permalink($product_id); ?>" class="hover:text-secondary">
                                <h3 class="text-[13px] text-gray-800 font-medium leading-tight mb-2 line-clamp-2"><?php echo esc_html( $title ); ?></h3>
                            </a>
                            <div class="flex items-center gap-3 flex-wrap">
                                <div class="flex items-center border border-gray-200 rounded-md h-7 overflow-hidden">
                                    <button class="cart-qty-btn px-2 text-gray-500 hover:bg-gray-100 h-full flex items-center justify-center transition-colors" data-idx="<?php echo $item_idx; ?>" data-action="dec">−</button>
                                    <span class="cart-qty-display w-7 text-center text-[13px] font-semibold border-x border-gray-200 h-full flex items-center justify-center"><?php echo $qty; ?></span>
                                    <button class="cart-qty-btn px-2 text-gray-500 hover:bg-gray-100 h-full flex items-center justify-center transition-colors" data-idx="<?php echo $item_idx; ?>" data-action="inc">+</button>
                                </div>
                                <div class="text-[12px] text-gray-600 flex items-center gap-1">
                                    <span class="text-gray-400 text-[10px]">×</span>
                                    <span class="text-secondary font-bold text-sm">৳<?php echo number_format( $price, $woocom_price_decimals, $woocom_price_decimal_separator, $woocom_price_thousand_separator ); ?></span>
                                    <span class="text-gray-400 mx-1">=</span>
                                    <span class="font-bold text-[14px] text-gray-800">৳<?php echo number_format( $price * $qty, $woocom_price_decimals, $woocom_price_decimal_separator, $woocom_price_thousand_separator ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
<?php $item_idx++; ?>
                    <?php
                }
            }
            ?>
        </div>

        <!-- Empty State -->
        <div id="cart-empty-state" class="flex flex-col items-center justify-center py-16 text-center px-6 <?php echo WC()->cart->is_empty() ? '' : 'hidden'; ?>">
            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
            </div>
            <p class="text-gray-600 font-semibold text-base mb-1">Your cart is empty</p>
            <p class="text-gray-400 text-sm">Add some items to get started!</p>
        </div>

        <?php if (get_option('show_cross_sell', 1)) : ?>
        <!-- You May Also Like -->
        <div id="cart-drawer-cross-sells-container" class="bg-[#F9F9F9] border-t border-gray-100 p-4 pb-5 <?php echo WC()->cart->is_empty() ? 'hidden' : ''; ?>">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[14px] font-bold text-[#253D4E] relative pb-1">
                    <?php echo esc_html(get_option('cross_sell_title', 'You May Also Like') ?: 'You May Also Like'); ?>
                    <span class="absolute bottom-0 left-0 w-8 h-0.5 bg-secondary block"></span>
                </h3>
                <div class="flex gap-1.5">
                    <button id="cs-prev" class="w-6 h-6 rounded-full bg-secondary text-white flex items-center justify-center hover:bg-secondary/80 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button id="cs-next" class="w-6 h-6 rounded-full bg-secondary text-white flex items-center justify-center hover:bg-secondary/80 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
            <div id="cross-sell-track" class="flex gap-3 overflow-x-hidden">
                <?php
                $cart_items = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart() : array();
                $product_ids = array();
                $categories = array();
                
                foreach ($cart_items as $item) {
                    $product_ids[] = $item['product_id'];
                    if (!empty($item['variation_id'])) {
                        $product_ids[] = $item['variation_id'];
                    }
                    $item_cats = wp_get_post_terms($item['product_id'], 'product_cat', array('fields' => 'all'));
                    if (!is_wp_error($item_cats)) {
                        foreach ($item_cats as $term) {
                            // Check if this term is a parent of any other term in the list
                            $is_parent = false;
                            foreach ($item_cats as $other_term) {
                                if ($other_term->parent === $term->term_id) {
                                    $is_parent = true;
                                    break;
                                }
                            }
                            // Ignore broad/uncategorized terms if other specific categories are available
                            if ($term->slug === 'uncategorized' && count($item_cats) > 1) {
                                $is_parent = true;
                            }
                            if (!$is_parent) {
                                $categories[] = $term->slug;
                            }
                        }
                    }
                }
                
                $categories = array_unique($categories);
                
                $args = array(
                    'limit' => 10,
                    'status' => 'publish',
                    'visibility' => 'visible',
                    'exclude' => $product_ids,
                    'orderby' => 'rand',
                );
                
                if (!empty($categories)) {
                    $args['category'] = $categories;
                }
                
                $products = wc_get_products($args);
                
                if (!empty($products)) :
                    foreach ($products as $product) :
                        $price = $product->get_price();
                        $image = wp_get_attachment_image_src($product->get_image_id(), 'thumbnail');
                        $image_url = $image ? $image[0] : wc_placeholder_img_src();
                        ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-2.5 flex gap-2.5 shadow-sm flex-shrink-0 w-[calc(50%-6px)] hover:shadow-md transition-shadow">
                            <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-gray-50 rounded overflow-hidden">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" class="max-w-full max-h-full object-contain mix-blend-multiply">
                                </a>
                            </div>
                            <div class="flex flex-col justify-between flex-grow overflow-hidden">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <p class="text-[11px] font-medium text-gray-800 leading-tight line-clamp-2"><?php echo esc_html($product->get_name()); ?></p>
                                </a>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-secondary font-bold text-[12px]">৳<?php echo number_format( $price, $woocom_price_decimals, $woocom_price_decimal_separator, $woocom_price_thousand_separator ); ?></span>
                                    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>" class="add_to_cart_button ajax_add_to_cart bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center hover:bg-secondary/80 transition-colors" title="Add to Cart">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    endforeach;
                else :
                    echo '<p class="text-[11px] text-gray-500">No products found.</p>';
                endif;
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer Total + Checkout -->
    <div id="cart-drawer-footer" class="border-t border-gray-200 bg-white px-5 py-4 pb-8 sm:pb-4 flex-shrink-0 <?php echo WC()->cart->is_empty() ? 'hidden' : ''; ?>">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[14px] font-semibold text-gray-700">Total:</span>
            <span id="cart-drawer-total" class="text-[18px] font-bold text-[#253D4E]">৳<?php echo number_format( WC()->cart->get_subtotal(), $woocom_price_decimals, $woocom_price_decimal_separator, $woocom_price_thousand_separator ); ?></span>
        </div>
        <a href="<?php echo wc_get_checkout_url(); ?>" class="w-full block bg-secondary text-white font-bold text-center py-3.5 rounded text-[13px] uppercase tracking-wider hover:bg-secondary/90 transition-colors <?php echo get_option('checkout_button_shake', 1) ? 'checkout-shake' : ''; ?>">
            Checkout
        </a>
    </div>
</div>

<?php endif; ?>

<script>
    window.woocom_initial_cart = <?php 
        $initial_items = array();
        if ( function_exists( 'WC' ) && WC()->cart ) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $item ) {
                $product = $item['data'];
                $initial_items[] = array(
                    'key'   => $cart_item_key,
                    'title' => $product->get_name(),
                    'image' => function_exists( 'woocom_get_cart_item_image_url' ) ? woocom_get_cart_item_image_url( $item, 'thumbnail' ) : get_the_post_thumbnail_url( $item['product_id'], 'thumbnail' ),
                    'price' => (float) $product->get_price(),
                    'qty'   => (int) $item['quantity'],
                    'permalink' => get_permalink( $item['product_id'] ),
                );
            }
            echo json_encode( array(
                'items' => $initial_items,
                'total' => (float) WC()->cart->get_subtotal()
            ) );
        } else {
            echo json_encode( array( 'items' => array(), 'total' => 0 ) );
        }
    ?>;
    window.woocom_settings = {
        cart_promo_enabled: <?php echo get_option('cart_promo_enabled', 1) ? 'true' : 'false'; ?>,
        cart_promo_title: "<?php echo esc_js(get_option('cart_promo_title', 'Get a Free Gift!')); ?>",
        cart_promo_min_amount: <?php echo (int) get_option('cart_promo_min_amount', 3000); ?>,
        cross_sell_autoslide: <?php echo get_option('cross_sell_autoslide', 1) ? 'true' : 'false'; ?>
    };
</script>

<?php if ( ! ( function_exists( 'is_checkout' ) && is_checkout() ) ) : ?>
<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-navigation lg:hidden fixed bottom-0 left-0 bg-secondary text-white z-[100] px-4 py-3 flex items-center justify-between shadow-[0_-4px_12px_rgba(0,0,0,0.15)] pb-safe" style="width:100dvw;max-width:100dvw;overflow:hidden;box-sizing:border-box;">
    <!-- Home -->
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex flex-col items-center gap-1.5 group">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span class="text-[10px] font-bold uppercase tracking-wider">Home</span>
    </a>
    <!-- Menu -->
    <button id="mobile-bottom-menu-open" class="flex flex-col items-center gap-1.5 group">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
        <span class="text-[10px] font-bold uppercase tracking-wider">Menu</span>
    </button>
    <!-- Cart -->
    <button id="cart-drawer-open-bottom" class="flex flex-col items-center gap-1.5 group relative">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span class="absolute -top-2 -right-2 bg-black text-white text-[9px] font-extrabold min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-secondary cart-count-global shadow-sm">
                <?php echo ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0; ?>
            </span>
        </div>
        <span class="text-[10px] font-bold uppercase tracking-wider">Cart</span>
    </button>
    <!-- Search -->
    <button id="mobile-bottom-search-open" class="flex flex-col items-center gap-1.5 group">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <span class="text-[10px] font-bold uppercase tracking-wider">Search</span>
    </button>
    <!-- Account -->
    <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" class="flex flex-col items-center gap-1.5 group">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span class="text-[10px] font-bold uppercase tracking-wider">Account</span>
    </a>
</div>
<?php endif; ?>

<!-- Mobile Search Overlay -->
<div id="mobile-search-overlay" class="fixed inset-0 bg-black/40 z-[200] invisible transition-opacity duration-300 opacity-0"></div>
<div id="mobile-search-container" class="fixed top-0 left-0 right-0 bg-white z-[201] transform -translate-y-full transition-transform duration-300 shadow-xl">
    <div class="px-4 py-2.5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-[16px] font-bold text-[#253D4E]">Search Products</h2>
        <button id="mobile-search-close" class="text-[#FF4D4D] hover:text-red-600 transition-colors p-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </div>
    <div class="p-3.5">
        <form role="search" method="get" class="flex items-stretch h-10" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="search" id="mobile-search-input" class="flex-grow bg-white border border-gray-200 rounded-l-md py-2 px-4 focus:ring-0 focus:border-secondary transition-all text-sm outline-none text-[#253D4E]" placeholder="Search in..." value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
            <button type="submit" class="bg-secondary text-white px-4 rounded-r-md flex items-center justify-center hover:bg-secondary/90 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
            <input type="hidden" name="post_type" value="product" />
        </form>
        <div id="mobile-search-results" class="mt-4 max-h-[60vh] overflow-y-auto hidden"></div>
    </div>
</div>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-24 right-6 lg:bottom-10 lg:right-10 z-[50] bg-secondary text-white p-3 rounded-full shadow-2xl opacity-0 translate-y-10 invisible transition-all duration-300 hover:bg-secondary/90 hover:-translate-y-1 group">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-up"><path d="m18 15-6-6-6 6"/></svg>
    <span class="absolute right-full mr-3 bg-gray-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none uppercase tracking-widest font-bold">Back to top</span>
</button>

<!-- Floating Multi-Chat Widget (Bottom Left) -->
<div class="fixed left-6 bottom-20 z-[999] flex flex-col items-center gap-3 transition-all duration-300 transform scale-0 translate-y-10 origin-bottom opacity-0" id="woocom-floating-chat-actions">
    <!-- WhatsApp -->
    <a href="https://wa.me/8801700934555" target="_blank" class="woocom-chat-whatsapp w-12 h-12 rounded-full text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform relative group">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79 1.043h.003c4.368 0 7.927-3.558 7.929-7.93a7.9 7.9 0 0 0-2.326-5.618zm-5.607 11.39h-.002c-1.185 0-2.348-.318-3.358-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.697-4.97c-.202-.1-1.199-.591-1.385-.658-.186-.068-.322-.1-.458.1-.136.2-.527.658-.646.793-.119.135-.238.15-.44.05-.202-.1-.853-.314-1.624-1.002-.599-.533-1.004-1.193-1.121-1.393-.117-.2-.013-.308.088-.408.09-.09.202-.238.303-.357.1-.12.136-.2.203-.334.067-.134.034-.251-.017-.352-.05-.101-.458-1.102-.627-1.509-.164-.398-.328-.344-.458-.351-.12-.007-.258-.007-.396-.007s-.362.052-.552.259c-.19.208-.724.708-.724 1.729s.743 2.012.847 2.151c.148.2 2.08 3.179 5.04 4.461.704.305 1.253.487 1.68.623.708.225 1.353.193 1.863.118.568-.083 1.785-.73 2.039-1.436.252-.706.252-1.312.177-1.437-.076-.125-.278-.202-.58-.353z"/></svg>
        <span class="absolute left-14 bg-gray-900 text-white text-[11px] font-bold px-2.5 py-1 rounded shadow-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">WhatsApp</span>
    </a>
    <!-- Messenger -->
    <a href="https://m.me/jashorifood" target="_blank" class="woocom-chat-messenger w-12 h-12 rounded-full text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform relative group">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.915 1.458 5.513 3.755 7.159V22l3.411-1.872a11.163 11.163 0 0 0 2.834.38c5.523 0 10-4.146 10-9.25C22 6.145 17.523 2 12 2zm1.09 12.39-2.396-2.56-4.686 2.56 5.143-5.467 2.456 2.56 4.626-2.56-5.143 5.467z"/></svg>
        <span class="absolute left-14 bg-gray-900 text-white text-[11px] font-bold px-2.5 py-1 rounded shadow-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Messenger</span>
    </a>
    <!-- Phone -->
    <a href="tel:+8801700934555" class="woocom-chat-phone w-12 h-12 rounded-full text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform relative group">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg>
        <span class="absolute left-14 bg-gray-900 text-white text-[11px] font-bold px-2.5 py-1 rounded shadow-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Phone</span>
    </a>
</div>

<!-- Main Toggle Button -->
<button onclick="toggleFloatingChat()" class="fixed left-6 bottom-6 z-[999] w-12 h-12 rounded-full bg-[#b08bbb] text-white flex items-center justify-center shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer" id="woocom-floating-chat-toggle" aria-label="Toggle chat widget">
    <!-- Chat Icon -->
    <svg id="chat-icon-svg" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-all duration-300"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    <!-- Close Icon (Hidden by default) -->
    <svg id="close-icon-svg" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" class="hidden transition-all duration-300"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
</button>

<script>
function toggleFloatingChat() {
    const actions = document.getElementById('woocom-floating-chat-actions');
    const chatIcon = document.getElementById('chat-icon-svg');
    const closeIcon = document.getElementById('close-icon-svg');
    
    if (actions.classList.contains('chat-open')) {
        actions.classList.remove('chat-open');
        chatIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    } else {
        actions.classList.add('chat-open');
        chatIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
    }
}
</script>

<?php wp_footer(); ?>

</body>
</html>
