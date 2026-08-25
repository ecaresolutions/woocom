<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

	<?php wp_head(); ?>
	<style>
		/* Global add to cart button hover fixes */
		a.woocom-custom-add-to-cart:hover,
		a.add_to_cart_button:hover,
		a.ajax_add_to_cart:hover,
		.add_to_cart_button:hover,
		button.woocom-pre-order-btn:hover,
		a.woocom-pre-order-btn:hover {
			background-color: var(--product-add-to-cart-bg, var(--color-primary, #1E5D02)) !important;
			background: var(--product-add-to-cart-bg, var(--color-primary, #1E5D02)) !important;
			color: #ffffff !important;
			color: #fff !important;
			border-color: var(--product-add-to-cart-bg, var(--color-primary, #1E5D02)) !important;
		}
		a.woocom-custom-add-to-cart:hover *,
		a.add_to_cart_button:hover *,
		a.ajax_add_to_cart:hover *,
		.add_to_cart_button:hover * {
			color: #ffffff !important;
			color: #fff !important;
			stroke: #ffffff !important;
			stroke: #fff !important;
		}
		/* Swiper Navigation Button override for custom SVG icons */
		.hero-prev::after,
		.hero-next::after {
			display: none !important;
		}
		/* Force container max-width to 1320px on desktop */
		@media (min-width: 1280px) {
			.container, .md\:container {
				max-width: 1320px !important;
			}
		}

		/* Mini Cart Dropdown Styles */
		#woocom-mini-cart-dropdown {
			background: #ffffff !important;
			box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05) !important;
			transform: translateY(10px);
			transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
			opacity: 0;
			visibility: hidden;
			display: block;
			border: 1px solid #f1f5f9 !important;
			border-radius: 16px !important;
			padding: 20px !important;
			width: 380px !important;
			box-sizing: border-box !important;
		}
		#woocom-mini-cart-dropdown.active {
			opacity: 1 !important;
			visibility: visible !important;
			transform: translateY(0) !important;
		}
		#woocom-mini-cart-dropdown .widget_shopping_cart_content {
			padding: 0 !important;
			background: #ffffff !important;
		}
		#woocom-mini-cart-dropdown ul.woocommerce-mini-cart {
			list-style: none !important;
			padding: 0 !important;
			margin: 0 !important;
			max-height: 280px !important;
			overflow-y: auto !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-items-wrapper {
			max-height: 280px !important;
			overflow-y: auto !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-item {
			display: flex !important;
			align-items: center !important;
			justify-content: space-between !important;
			gap: 12px !important;
			padding: 12px 0 !important;
			border-bottom: 1px solid #f1f5f9 !important;
			position: relative !important;
			width: 100% !important;
			float: none !important;
			box-sizing: border-box !important;
			background: #ffffff !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-item:last-child {
			border-bottom: none !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-item img {
			width: 54px !important;
			height: 54px !important;
			object-fit: cover !important;
			border-radius: 8px !important;
			border: 1px solid #f1f5f9 !important;
		}
		#woocom-mini-cart-dropdown .mini-cart-remove-link {
			color: #94a3b8 !important;
			font-size: 20px !important;
			text-decoration: none !important;
			cursor: pointer !important;
			transition: color 0.2s !important;
			line-height: 1 !important;
			display: inline-block !important;
			padding: 4px !important;
		}
		#woocom-mini-cart-dropdown .mini-cart-remove-link:hover {
			color: #ef4444 !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-item h4 {
			margin: 0 0 4px 0 !important;
			padding: 0 !important;
			font-size: 13px !important;
			font-weight: 600 !important;
			color: #1e293b !important;
			line-height: 1.3 !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-item h4 a {
			color: #1e293b !important;
			text-decoration: none !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-item h4 a:hover {
			color: var(--color-primary, #2563EB) !important;
		}
		#woocom-mini-cart-dropdown .woocom-mini-cart-item .quantity {
			font-size: 12px !important;
			color: #64748b !important;
			display: block !important;
			margin-top: 4px !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__total {
			display: flex !important;
			justify-content: space-between !important;
			align-items: center !important;
			padding: 16px 0 !important;
			border-top: 1px solid #f1f5f9 !important;
			border-bottom: 1px solid #f1f5f9 !important;
			margin: 16px 0 !important;
			font-size: 14px !important;
			font-weight: 700 !important;
			color: #1e293b !important;
			background: #ffffff !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__total strong {
			font-weight: 600 !important;
			color: #64748b !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__buttons {
			display: flex !important;
			gap: 12px !important;
			margin: 0 !important;
			padding: 4px 0 0 0 !important;
			background: #ffffff !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__buttons a {
			flex: 1 !important;
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			height: 40px !important;
			border-radius: 8px !important;
			font-size: 12.5px !important;
			font-weight: 700 !important;
			text-decoration: none !important;
			transition: all 0.2s !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout) {
			background: #f1f5f9 !important;
			color: #475569 !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__buttons a.button.wc-forward:not(.checkout):hover {
			background: #e2e8f0 !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__buttons a.checkout {
			background: var(--color-primary, #2563EB) !important;
			color: #ffffff !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__buttons a.checkout:hover {
			opacity: 0.9 !important;
		}
		#woocom-mini-cart-dropdown .woocommerce-mini-cart__empty-message {
			text-align: center !important;
			padding: 24px 0 !important;
			color: #64748b !important;
			font-size: 13.5px !important;
			font-weight: 500 !important;
		}

		/* Custom Hero Section CSS Overrides */
		@media (min-width: 1024px) {
			.hero-section-row {
				display: flex !important;
				flex-direction: row !important;
				align-items: stretch !important;
			}
			.hero-slider-col {
				width: 80.5% !important;
				flex-grow: 1 !important;
			}
			.hero-slide-link {
				aspect-ratio: 1476 / 450 !important;
				height: auto !important;
			}
			.hero-sidebar-col {
				width: 19.5% !important;
				flex-shrink: 0 !important;
				display: flex !important;
			}
			.hero-sidebar-link {
				display: block !important;
				width: 100% !important;
				height: 100% !important;
			}
			.hero-sidebar-img {
				width: 100% !important;
				height: 100% !important;
				object-fit: contain !important;
				display: block !important;
			}
		}

		/* Store Highlights Features Section */
		.woocom-features-container {
			background: #ffffff;
			border: 1px solid #f1f5f9;
			border-radius: 0.5rem;
			padding: 1.25rem 1.5rem;
			box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
			margin-top: 1.5rem;
		}
		.woocom-features-grid {
			display: grid;
			grid-template-columns: 1fr;
			gap: 1.5rem;
		}
		@media (min-width: 640px) {
			.woocom-features-grid {
				grid-template-columns: repeat(2, 1fr);
			}
		}
		@media (min-width: 1024px) {
			.woocom-features-grid {
				grid-template-columns: repeat(4, 1fr);
				gap: 0;
			}
		}
		.woocom-feature-item {
			display: flex;
			align-items: center;
			gap: 0.875rem;
			padding: 0.5rem 0;
		}
		@media (min-width: 1024px) {
			.woocom-feature-item {
				padding: 0.25rem 1.5rem;
				border-right: 1px solid #e2e8f0;
			}
			.woocom-feature-item:first-child {
				padding-left: 0;
			}
			.woocom-feature-item:last-child {
				border-right: none;
				padding-right: 0;
			}
		}
		.woocom-feature-icon-wrapper {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
		}

		.woocom-feature-icon {
			width: 2rem;
			height: 2rem;
			color: var(--color-primary, #2563eb);
		}

		.woocom-feature-title {
			font-size: 0.875rem;
			font-weight: 600;
			color: #1e293b;
			margin: 0;
			line-height: 1.4;
		}
		.woocom-feature-desc {
			font-size: 0.75rem;
			font-weight: 400;
			color: #64748b;
			margin: 0.125rem 0 0 0;
			line-height: 1.4;
		}
	</style>
</head>

<body <?php body_class( 'bg-gray-50 overflow-x-hidden pb-20 lg:pb-0' ); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
<?php if ( ! ( function_exists( 'is_checkout' ) && is_checkout() ) ) : ?>
	<header id="masthead" class="site-header bg-white shadow-sm relative z-[60]">
        <!-- Desktop Header -->
		<div class="container mx-auto px-4 py-4 hidden lg:flex items-center justify-between gap-6">
            <!-- Logo -->
            <div class="site-branding flex-shrink-0">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="flex items-center">
                    <?php 
                    $logo_url = get_option('theme_logo');
                    if ( $logo_url ) :
                    ?>
                        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-10 lg:h-12 w-auto object-contain max-w-[200px]">
                    <?php else : ?>
                        <!-- Tech Hexagonal Styled SVG Logo fallback -->
                        <div class="flex items-center gap-2 font-black text-xl tracking-tight text-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary, #2563EB)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cpu h-7 w-7"><rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/><path d="M9 1v3"/><path d="M15 1v3"/><path d="M9 20v3"/><path d="M15 20v3"/><path d="M20 9h3"/><path d="M20 15h3"/><path d="M1 9h3"/><path d="M1 15h3"/></svg>
                            <span>Woocom<span class="text-primary font-extrabold">Gadget</span></span>
                        </div>
                    <?php endif; ?>
                </a>
            </div>

            <?php if ( get_option( 'woocom_setup_complete' ) === '1' ) : ?>
            <!-- Categories Dropdown next to logo -->
            <div class="relative flex-shrink-0" id="header-cat-dropdown-wrap">
                <button type="button" onclick="toggleHeaderCatDropdown(event)" class="flex items-center gap-2 bg-[#F3F4F6] text-gray-700 font-bold px-4 py-2.5 rounded-lg border border-gray-100 hover:bg-gray-200/80 transition-colors cursor-pointer text-sm outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-4.5 w-4.5"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    <span>Categories</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 transition-transform duration-200" id="header-cat-arrow"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="header-cat-dropdown-menu" class="absolute left-0 top-full mt-2 w-56 bg-white border border-gray-100 rounded-md shadow-xl overflow-hidden opacity-0 invisible translate-y-2 transition-all duration-200 z-[150]">
                    <?php
                    $cats = get_terms( array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                    ) );
                    if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) :
                        foreach ( $cats as $cat ) :
                    ?>
                        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white transition-colors font-medium">
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <span class="block px-4 py-2 text-sm text-gray-400 font-medium">No categories found</span>
                    <?php endif; ?>
                </div>
            </div>

            <script type="text/javascript">
            function toggleHeaderCatDropdown(e) {
                e.stopPropagation();
                var menu = document.getElementById('header-cat-dropdown-menu');
                var arrow = document.getElementById('header-cat-arrow');
                if (menu) {
                    if (menu.classList.contains('invisible')) {
                        // Open
                        menu.classList.remove('invisible', 'opacity-0');
                        menu.classList.add('visible', 'opacity-100', 'translate-y-0');
                        if (arrow) arrow.classList.add('rotate-180');
                    } else {
                        // Close
                        menu.classList.add('invisible', 'opacity-0');
                        menu.classList.remove('visible', 'opacity-100', 'translate-y-0');
                        if (arrow) arrow.classList.remove('rotate-180');
                    }
                }
            }

            // Close when clicking anywhere else
            document.addEventListener('click', function(e) {
                var menu = document.getElementById('header-cat-dropdown-menu');
                var arrow = document.getElementById('header-cat-arrow');
                var wrap = document.getElementById('header-cat-dropdown-wrap');
                if (menu && !menu.classList.contains('invisible')) {
                    if (wrap && !wrap.contains(e.target)) {
                        menu.classList.add('invisible', 'opacity-0');
                        menu.classList.remove('visible', 'opacity-100', 'translate-y-0');
                        if (arrow) arrow.classList.remove('rotate-180');
                    }
                }
            });
            </script>

            <!-- Search -->
            <div class="flex-grow max-w-2xl relative">
                <form role="search" method="get" class="w-full" action="<?php echo esc_url( home_url( '/' ) ); ?>" id="ajax-search-form">
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-lg overflow-hidden focus-within:border-primary w-full transition-all focus-within:bg-white">
                        <span class="pl-4 text-slate-400 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </span>
                        <input type="search" id="desktop-search-input" class="w-full bg-transparent py-2.5 px-3 outline-none text-sm placeholder-slate-400 text-gray-800" placeholder="Search for gadgets, phones, accessories..." value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                        <button type="submit" class="bg-primary hover:bg-primary/90 text-white text-sm font-bold px-6 py-2.5 transition-colors duration-200 cursor-pointer">
                            Search
                        </button>
                    </div>
                    <input type="hidden" name="post_type" value="product" />
                </form>
                <!-- AJAX Results -->
                <div id="search-results" class="absolute top-full left-0 w-full bg-white shadow-2xl rounded-2xl z-[100] mt-2 hidden max-h-[400px] overflow-y-auto border border-slate-100"></div>
            </div>
            <?php endif; ?>

            <?php if ( get_option( 'woocom_setup_complete' ) === '1' ) : ?>
            <!-- Header Icons -->
            <div class="flex items-center gap-6 text-gray-700">
                <?php
                // Detect wishlist URL dynamically
                $wishlist_url = '#';
                if ( function_exists( 'YITH_WCWL' ) ) {
                    $wishlist_url = YITH_WCWL()->get_wishlist_url();
                } elseif ( function_exists( 'tinv_url_wishlist_default' ) ) {
                    $wishlist_url = tinv_url_wishlist_default();
                } else {
                    $wishlist_page = get_page_by_path( 'wishlist' );
                    if ( $wishlist_page ) {
                        $wishlist_url = get_permalink( $wishlist_page->ID );
                    }
                }
                ?>
                <!-- Wishlist -->
                <a href="<?php echo esc_url( $wishlist_url ); ?>" class="flex flex-col items-center group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6.5 w-6.5 group-hover:text-primary transition-colors"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    <span class="text-[10px] sm:text-[11px] mt-1.5 font-bold uppercase tracking-tight group-hover:text-primary transition-colors">Wishlist</span>
                </a>

                <!-- Cart -->
                <?php if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->cart ) : ?>
                <div class="relative group py-2">
                    <button id="cart-drawer-open-desktop" class="flex flex-col items-center group relative cursor-pointer outline-none border-none bg-transparent">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6.5 w-6.5 group-hover:text-primary transition-colors"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        <span class="text-[10px] sm:text-[11px] mt-1.5 font-bold uppercase tracking-tight group-hover:text-primary transition-colors">Cart</span>
                        <span class="absolute -top-1.5 -right-2 bg-primary text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white cart-count-global min-w-4 text-center">
                            <?php echo WC()->cart->get_cart_contents_count(); ?>
                        </span>
                    </button>
                    <!-- Mini Cart Dropdown -->
                    <div id="woocom-mini-cart-dropdown" class="absolute right-0 top-full mt-2 w-[360px] bg-white border border-gray-100 rounded-xl shadow-xl z-[150]">
                        <div class="widget_shopping_cart_content">
                            <?php woocommerce_mini_cart(); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Account -->
                <?php if ( is_user_logged_in() ) : 
                    $current_user = wp_get_current_user();
                    $display_name = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
                    $my_account_url = get_permalink( get_option('woocommerce_myaccount_page_id') );
                ?>
                    <div class="woocom-user-dropdown-wrapper relative flex flex-col items-center group py-2">
                        <a href="<?php echo esc_url( $my_account_url ); ?>" class="flex flex-col items-center">
                            <div class="h-6.5 w-6.5 rounded-full overflow-hidden border border-gray-200 group-hover:border-primary transition-all flex items-center justify-center">
                                <?php echo get_avatar( $current_user->ID, 26, '', '', array('class' => 'rounded-full object-cover w-full h-full') ); ?>
                            </div>
                            <span class="text-[10px] sm:text-[11px] mt-1.5 font-bold uppercase tracking-tight group-hover:text-primary transition-colors"><?php echo esc_html( $display_name ); ?></span>
                        </a>
                        
                        <!-- Dropdown Menu -->
                        <div class="woocom-user-dropdown absolute right-0 top-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-[150] overflow-hidden" style="width: 200px !important;">
                            <a href="<?php echo esc_url( $my_account_url ); ?>" class="woocom-dropdown-item flex items-center gap-3 px-4 py-2 text-[13px] text-gray-700 font-semibold transition-colors" style="white-space: nowrap !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400"><rect width="7" height="7" x="3" y="3"/><rect width="7" height="7" x="14" y="3"/><rect width="7" height="7" x="14" y="14"/><rect width="7" height="7" x="3" y="14"/></svg>
                                <?php esc_html_e( 'Dashboard', 'woocom' ); ?>
                            </a>
                            <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', $my_account_url ) ); ?>" class="woocom-dropdown-item flex items-center gap-3 px-4 py-2 text-[13px] text-gray-700 font-semibold transition-colors" style="white-space: nowrap !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                <?php esc_html_e( 'Orders', 'woocom' ); ?>
                            </a>
                            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account', '', $my_account_url ) ); ?>" class="woocom-dropdown-item flex items-center gap-3 px-4 py-2 text-[13px] text-gray-700 font-semibold transition-colors" style="white-space: nowrap !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <?php esc_html_e( 'Account Details', 'woocom' ); ?>
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="<?php echo esc_url( wc_logout_url( $my_account_url ) ); ?>" class="woocom-dropdown-item logout-link flex items-center gap-3 px-4 py-2 text-[13px] text-red-600 font-semibold transition-colors" style="white-space: nowrap !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-red-500"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                <?php esc_html_e( 'Log out', 'woocommerce' ); ?>
                            </a>
                        </div>
                    </div>
                <?php else : ?>
                    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="flex flex-col items-center group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-6.5 w-6.5 group-hover:text-primary transition-colors"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span class="text-[10px] sm:text-[11px] mt-1.5 font-bold uppercase tracking-tight group-hover:text-primary transition-colors">Sign In</span>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
		</div>
        
        <!-- Desktop Navigation Bar -->
        <div class="hidden lg:block desktop-nav border-t border-white/10 <?php echo get_option('sticky_header', 1) ? 'sticky top-0 z-[100] shadow-xl' : 'relative z-[50]'; ?>">
            <div class="container mx-auto px-4">
                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'menu_class'     => 'flex items-center gap-x-12 text-[14px] font-bold uppercase tracking-wider',
                            'fallback_cb'    => false,
                        )
                    );
                    ?>
                </nav>
            </div>
        </div>

        <!-- Mobile Header -->
        <div class="lg:hidden bg-white border-b border-gray-100">
            <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-open" onclick="var d = document.getElementById('mobile-drawer'); var o = document.getElementById('mobile-drawer-overlay'); var c = document.getElementById('mobile-drawer-content'); if(d && o && c){ d.classList.remove('invisible'); setTimeout(function(){ o.style.setProperty('opacity', '1', 'important'); c.style.setProperty('transform', 'translateX(0)', 'important'); c.classList.remove('-translate-x-full'); c.classList.add('translate-x-0'); }, 10); }" class="text-gray-700 hover:text-primary transition-colors p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                </button>

                <!-- Mobile Logo -->
                <div class="site-branding flex-shrink-0">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php $logo_url = get_option('theme_logo'); ?>
                        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-8 w-auto object-contain">
                    </a>
                </div>

                <!-- Mobile Cart -->
                <?php if ( get_option( 'woocom_setup_complete' ) === '1' && class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->cart ) : ?>
                <button id="cart-drawer-open-mobile" onclick="if (typeof window.woocomOpenCart === 'function') { window.woocomOpenCart(false); }" class="relative text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span class="absolute -top-1 -right-2 bg-primary text-white text-[10px] font-bold h-5 w-5 flex items-center justify-center rounded-full ring-2 ring-white cart-count-global">
                        <?php echo WC()->cart->get_cart_contents_count(); ?>
                    </span>
                </button>
                <?php endif; ?>
            </div>
            <?php if ( get_option( 'woocom_setup_complete' ) === '1' ) : ?>
            <!-- Mobile Search -->
            <div class="px-4 pb-3 relative">
                <form role="search" method="get" class="relative" action="<?php echo esc_url( home_url( '/' ) ); ?>" id="mobile-header-search-form">
                    <input type="search" id="mobile-header-search-input" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-secondary rounded-full py-2 px-5 pr-10 transition-all text-sm outline-none hover:border-slate-300" placeholder="Search in..." value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-secondary transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </button>
                    <input type="hidden" name="post_type" value="product" />
                </form>
                <!-- AJAX Results -->
                <div id="mobile-header-search-results" class="absolute top-full left-0 w-full bg-white shadow-2xl rounded-2xl z-[100] mt-2 hidden max-h-[300px] overflow-y-auto border border-slate-100"></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-drawer" class="fixed inset-0 z-[200] invisible">
            <!-- Overlay -->
            <div id="mobile-drawer-overlay" onclick="var d = document.getElementById('mobile-drawer'); var o = document.getElementById('mobile-drawer-overlay'); var c = document.getElementById('mobile-drawer-content'); if(d && o && c){ o.style.setProperty('opacity', '0', 'important'); c.style.setProperty('transform', 'translateX(-100%)', 'important'); c.classList.remove('translate-x-0'); c.classList.add('-translate-x-full'); setTimeout(function(){ d.classList.add('invisible'); }, 300); }" class="absolute inset-0 bg-black/50 transition-opacity duration-300 opacity-0"></div>
            
            <!-- Drawer Content -->
            <div id="mobile-drawer-content" class="absolute top-0 left-0 w-[85%] max-w-[320px] h-full bg-white transform -translate-x-full transition-transform duration-300 overflow-y-auto" style="padding-bottom: 90px !important;">
                <!-- Close Button -->
                <button id="mobile-menu-close" onclick="var d = document.getElementById('mobile-drawer'); var o = document.getElementById('mobile-drawer-overlay'); var c = document.getElementById('mobile-drawer-content'); if(d && o && c){ o.style.setProperty('opacity', '0', 'important'); c.style.setProperty('transform', 'translateX(-100%)', 'important'); c.classList.remove('translate-x-0'); c.classList.add('-translate-x-full'); setTimeout(function(){ d.classList.add('invisible'); }, 300); }" class="absolute top-4 right-4 p-2 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>

                <!-- User Header -->
                <?php if ( is_user_logged_in() ) : 
                    $current_user = wp_get_current_user();
                    $display_name = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
                    $my_account_url = get_permalink( get_option('woocommerce_myaccount_page_id') );
                ?>
                    <a href="<?php echo esc_url( $my_account_url ); ?>" class="bg-secondary p-6 flex items-center gap-4 m-4 rounded-2xl text-white shadow-lg group hover:opacity-95 transition-opacity">
                        <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-white/40 flex items-center justify-center bg-white/10">
                            <?php echo get_avatar( $current_user->ID, 48, '', '', array('class' => 'rounded-full object-cover w-full h-full') ); ?>
                        </div>
                        <div>
                            <div class="font-bold text-lg leading-tight">Hello, <?php echo esc_html( $display_name ); ?>!</div>
                            <div class="text-white/80 text-xs font-semibold uppercase tracking-wider mt-0.5">My Account</div>
                        </div>
                    </a>
                <?php else : 
                    $my_account_url = get_permalink( get_option('woocommerce_myaccount_page_id') );
                ?>
                    <a href="<?php echo esc_url( $my_account_url ); ?>" class="bg-secondary p-6 flex items-center gap-4 m-4 rounded-2xl text-white shadow-lg group hover:opacity-95 transition-opacity">
                        <div class="bg-white/20 p-2 rounded-full ring-2 ring-white/30">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-lg leading-tight">Hello there!</div>
                            <div class="text-white/80 text-sm">Sign In</div>
                        </div>
                    </a>
                <?php endif; ?>

                <style>
                    /* Mobile Menu Dynamic Styling */
                    #mobile-menu .menu-item {
                        border-bottom: 1px solid #f3f4f6;
                        width: 100%;
                    }
                    #mobile-menu .menu-item:last-child {
                        border-bottom: none;
                    }
                    #mobile-menu .menu-item a {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 12px 16px;
                        color: #374151;
                        font-weight: 500;
                        font-size: 14px;
                        transition: all 0.2s;
                    }
                    #mobile-menu .menu-item a:hover {
                        background: #f9fafb;
                        color: var(--color-secondary);
                    }
                    #mobile-menu .sub-menu {
                        background: #fdfdfd;
                        padding-left: 15px;
                        display: none; /* Can be toggled with JS or just shown */
                    }
                    #mobile-menu .menu-item-has-children:hover > .sub-menu {
                        display: block;
                    }
                    #mobile-menu .sub-menu .menu-item a {
                        padding: 10px 16px;
                        font-size: 13px;
                        color: #6b7280;
                    }
                </style>
                <?php if ( get_option( 'woocom_setup_complete' ) === '1' ) : ?>
                <!-- Categories -->
                <div class="px-4 mb-8" id="mobile-menu">
                    <h3 class="text-xl font-bold text-gray-700 mb-4 border-l-4 border-secondary pl-3">Categories</h3>
                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <?php
                        $categories = get_terms( array(
                            'taxonomy'   => 'product_cat',
                            'hide_empty' => true,
                        ) );
                        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                            foreach ( $categories as $cat ) :
                                $cat_link = get_term_link( $cat );
                                ?>
                                <a href="<?php echo esc_url( $cat_link ); ?>" class="flex items-center justify-between p-4 border-b border-gray-100 last:border-0 hover:bg-gray-100 transition-colors">
                                    <span class="font-bold text-gray-700 text-sm"><?php echo esc_html( $cat->name ); ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                                <?php
                            endforeach;
                        else:
                            ?>
                            <div class="p-4 text-center text-gray-500 text-sm">No categories found.</div>
                            <?php
                        endif;
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quick Links -->
                <div class="px-4 pb-28">
                    <h3 class="text-xl font-bold text-gray-700 mb-4 border-l-4 border-secondary pl-3">Quick Links</h3>
                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <a href="#" class="flex items-center gap-4 p-4 hover:bg-gray-100 transition-colors border-b border-gray-100 last:border-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-gray-500"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                            <span class="font-bold text-gray-700">About Us</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 p-4 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-gray-500"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                            <span class="font-bold text-gray-700">Faqs</span>
                        </a>
                    </div>
                </div>
        </div>

	</header><!-- #masthead -->
<?php endif; ?>

        <?php if ( get_option( 'woocom_setup_complete' ) === '1' && ( is_front_page() || is_home() ) && ( get_option( 'ticker_enabled', '1' ) == 1 ) ) : ?>
            <?php
            // Fetch raw text and split by lines, with a default fallback
            $raw_text = get_option( 'ticker_text', '' );
            if ( empty( $raw_text ) ) {
                $raw_text = "ডেলিভারির সময় প্রোডাক্ট দেখে নিতে পারবেন\nসিজন ফ্রেশ মধু চলে এসেছে\nআমাদের বাগানের ফ্রেশ আমের প্রি-অর্ডার চলছে\nঅগ্রীম ছাড়াই অর্ডার করতে পারবেন";
            }
            $lines = array_filter( array_map( 'trim', explode( "\n", $raw_text ) ) );

            // Only render if we have actual items to display
            if ( ! empty( $lines ) ) :
                $icon_type = get_option( 'ticker_icon', 'mango' );
                $separator_svg = function_exists( 'woocom_get_ticker_separator_svg' ) ? woocom_get_ticker_separator_svg( $icon_type ) : '';

                $ticker_bg = esc_attr( get_option( 'ticker_bg_color', '#1E5D02' ) ?: '#1E5D02' );
                $ticker_color = esc_attr( get_option( 'ticker_text_color', '#ffffff' ) ?: '#ffffff' );
                $ticker_padding = intval( get_option( 'ticker_padding', '8' ) );
                $ticker_font_size = intval( get_option( 'ticker_font_size', '14' ) ?: '14' );
                $ticker_speed = intval( get_option( 'ticker_speed', '20' ) ?: '20' );
                ?>
                <style>
                    .woocom-ticker-container {
                        overflow: hidden;
                        background-color: <?php echo $ticker_bg; ?> !important;
                        color: <?php echo $ticker_color; ?> !important;
                        padding: <?php echo $ticker_padding; ?>px 0 !important;
                        font-size: <?php echo $ticker_font_size; ?>px !important;
                        width: 100%;
                        display: flex;
                        box-sizing: border-box;
                        border-bottom: 1px solid rgba(0,0,0,0.05);
                        z-index: 50;
                        position: relative;
                    }
                    .woocom-ticker-content {
                        display: flex;
                        white-space: nowrap;
                        animation: woocom-ticker-marquee <?php echo $ticker_speed; ?>s linear infinite !important;
                        will-change: transform;
                    }
                    .woocom-ticker-track {
                        display: flex;
                        align-items: center;
                    }
                    .woocom-ticker-item {
                        display: inline-flex;
                        align-items: center;
                        padding: 0 1.5rem !important;
                        font-weight: 700;
                    }
                    .woocom-ticker-item svg {
                        margin-right: 0.5rem;
                        flex-shrink: 0;
                        width: 1.25em;
                        height: 1.25em;
                    }
                    @keyframes woocom-ticker-marquee {
                        0% { transform: translate3d(0, 0, 0); }
                        100% { transform: translate3d(-50%, 0, 0); }
                    }
                    .woocom-ticker-container:hover .woocom-ticker-content {
                        animation-play-state: paused !important;
                    }

                    /* Accessibility: prefers-reduced-motion */
                    @media (prefers-reduced-motion: reduce) {
                        .woocom-ticker-content {
                            animation: none !important;
                            white-space: normal !important;
                            flex-wrap: wrap !important;
                            justify-content: center !important;
                        }
                        .woocom-ticker-track:last-child {
                            display: none !important;
                        }
                    }

                    /* Mobile Responsive Spacing and Font Adjustments */
                    @media (max-width: 768px) {
                        .woocom-ticker-container {
                            font-size: clamp(12px, 3.5vw, <?php echo $ticker_font_size * 0.9; ?>px) !important;
                            padding: <?php echo $ticker_padding * 0.8; ?>px 0 !important;
                        }
                        .woocom-ticker-item {
                            padding: 0 1rem !important;
                        }
                    }
                </style>
                <div class="woocom-ticker-container">
                    <div class="woocom-ticker-content">
                        <!-- First Track -->
                        <div class="woocom-ticker-track">
                            <?php foreach ( $lines as $line ) : ?>
                                <div class="woocom-ticker-item">
                                    <?php if ( $separator_svg ) echo $separator_svg; ?>
                                    <span><?php echo esc_html( $line ); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Second Track for seamless infinite scrolling loop -->
                        <div class="woocom-ticker-track" aria-hidden="true">
                            <?php foreach ( $lines as $line ) : ?>
                                <div class="woocom-ticker-item">
                                    <?php if ( $separator_svg ) echo $separator_svg; ?>
                                    <span><?php echo esc_html( $line ); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Toggle mini cart dropdown on desktop click
    $(document).on('click', '#cart-drawer-open-desktop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#woocom-mini-cart-dropdown').toggleClass('active');
    });

    // Close mini cart dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#cart-drawer-open-desktop, #woocom-mini-cart-dropdown').length) {
            $('#woocom-mini-cart-dropdown').removeClass('active');
        }
    });

    // Handle mini-cart quantity change via AJAX
    $(document).on('click', '.mini-cart-qty-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var button = $(this);
        var key = button.attr('data-cart_item_key');
        var action = button.attr('data-action');
        
        if (!key) return;
        
        var dropdown = $('#woocom-mini-cart-dropdown');
        dropdown.css('opacity', '0.6');
        dropdown.css('pointer-events', 'none');
        
        $.ajax({
            url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
            type: 'POST',
            data: {
                action: 'woocom_update_mini_cart_qty',
                cart_item_key: key,
                qty_action: action
            },
            success: function(response) {
                dropdown.css('opacity', '1');
                dropdown.css('pointer-events', 'auto');
                
                // Set flag to prevent toast notification
                window.woocom_is_updating_qty = true;
                
                // Trigger WooCommerce fragment refresh with the new fragments
                if (response && response.fragments) {
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                } else {
                    $(document.body).trigger('added_to_cart');
                }
                
                window.woocom_is_updating_qty = false;
                
                if (window.woocom_ajax) {
                    $.ajax({
                        url: window.woocom_ajax.ajax_url + '?action=woocom_get_cart_data&nonce=' + window.woocom_ajax.cart_nonce,
                        type: 'GET',
                        success: function(cartData) {
                            if (typeof cartUpdateStateAndCrossSells === 'function') {
                                cartUpdateStateAndCrossSells(cartData);
                            }
                        }
                    });
                }
            },
            error: function() {
                dropdown.css('opacity', '1');
                dropdown.css('pointer-events', 'auto');
                alert('Error updating quantity.');
            }
        });
    });

    // Handle mini-cart item removal via AJAX
    $(document).on('click', '.mini-cart-remove-link', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var button = $(this);
        var key = button.attr('data-cart_item_key');
        
        if (!key) return;
        
        var dropdown = $('#woocom-mini-cart-dropdown');
        dropdown.css('opacity', '0.6');
        dropdown.css('pointer-events', 'none');
        
        $.ajax({
            url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
            type: 'POST',
            data: {
                action: 'woocom_remove_mini_cart_item',
                cart_item_key: key
            },
            success: function(response) {
                dropdown.css('opacity', '1');
                dropdown.css('pointer-events', 'auto');
                
                // Trigger WooCommerce fragment refresh with the new fragments
                if (response && response.fragments) {
                    $(document.body).trigger('removed_from_cart', [response.fragments, response.cart_hash]);
                } else {
                    $(document.body).trigger('removed_from_cart');
                }
                
                if (window.woocom_ajax) {
                    $.ajax({
                        url: window.woocom_ajax.ajax_url + '?action=woocom_get_cart_data&nonce=' + window.woocom_ajax.cart_nonce,
                        type: 'GET',
                        success: function(cartData) {
                            if (typeof cartUpdateStateAndCrossSells === 'function') {
                                cartUpdateStateAndCrossSells(cartData);
                            }
                        }
                    });
                }
            },
            error: function() {
                dropdown.css('opacity', '1');
                dropdown.css('pointer-events', 'auto');
                alert('Error removing item.');
            }
        });
    });
});
</script>
