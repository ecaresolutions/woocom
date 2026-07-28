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
			background-color: var(--color-secondary, #F7A501) !important;
			background: var(--color-secondary, #F7A501) !important;
			color: #ffffff !important;
			color: #fff !important;
			border-color: var(--color-secondary, #F7A501) !important;
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
	</style>
</head>

<body <?php body_class( 'bg-gray-50 overflow-x-hidden pb-20 lg:pb-0' ); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<header id="masthead" class="site-header bg-white shadow-sm relative z-[60]">
        <!-- Desktop Header -->
		<div class="container mx-auto px-4 py-4 hidden lg:flex items-center justify-between gap-12">
            <!-- Logo -->
            <div class="site-branding flex-shrink-0">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                    <?php 
                    $logo_url = get_option('theme_logo');
                    ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-12 lg:h-14 w-auto object-contain max-w-[200px] lg:max-w-[240px]">
                </a>
            </div>

            <!-- Search -->
            <div class="flex-grow max-w-3xl relative">
                <form role="search" method="get" class="relative" action="<?php echo esc_url( home_url( '/' ) ); ?>" id="ajax-search-form">
                    <input type="search" id="desktop-search-input" class="w-full bg-gray-50 border-2 border-secondary/30 rounded-lg py-3.5 px-6 pr-14 focus:ring-4 focus:ring-secondary/10 focus:border-secondary transition-all text-base outline-none" placeholder="Search in..." value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                    <button type="submit" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-6 w-6"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </button>
                    <input type="hidden" name="post_type" value="product" />
                </form>
                <!-- AJAX Results -->
                <div id="search-results" class="absolute top-full left-0 w-full bg-white shadow-xl rounded-b-lg z-[100] mt-1 hidden max-h-[400px] overflow-y-auto border border-gray-100"></div>
            </div>

            <!-- Header Icons -->
            <div class="flex items-center gap-8 text-gray-700">
                <?php
                // Detect order tracking page dynamically
                $track_order_url = '#';
                if ( class_exists( 'WooCommerce' ) ) {
                    $tracking_pages = get_posts( array(
                        'post_type'      => 'page',
                        'post_status'    => 'publish',
                        'posts_per_page' => 1,
                        's'              => '[woocommerce_order_tracking]',
                    ) );
                    if ( ! empty( $tracking_pages ) ) {
                        $track_order_url = get_permalink( $tracking_pages[0]->ID );
                    } else {
                        $tracking_page_by_slug = get_page_by_path( 'order-tracking' );
                        if ( $tracking_page_by_slug ) {
                            $track_order_url = get_permalink( $tracking_page_by_slug->ID );
                        } else {
                            $tracking_page_by_slug_alt = get_page_by_path( 'track-order' );
                            if ( $tracking_page_by_slug_alt ) {
                                $track_order_url = get_permalink( $tracking_page_by_slug_alt->ID );
                            } else {
                                $track_order_url = home_url( '/order-tracking/' );
                            }
                        }
                    }
                }
                ?>
                <a href="<?php echo esc_url( $track_order_url ); ?>" class="flex flex-col items-center group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-7 w-7 group-hover:text-primary transition-colors"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                    <span class="text-[11px] mt-1.5 font-semibold uppercase tracking-tight group-hover:text-primary transition-colors">Track Order</span>
                </a>
                <?php if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->cart ) : ?>
                <button id="cart-drawer-open-desktop" class="flex flex-col items-center group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-7 w-7 group-hover:text-primary transition-colors"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span class="text-[11px] mt-1.5 font-semibold uppercase tracking-tight group-hover:text-primary transition-colors">Cart</span>
                    <span class="absolute -top-1.5 -right-2.5 bg-secondary text-white text-[10px] font-bold px-2 py-0.5 rounded-full ring-2 ring-white cart-count-global">
                        <?php echo WC()->cart->get_cart_contents_count(); ?>
                    </span>
                </button>
                <?php endif; ?>
                <?php if ( is_user_logged_in() ) : 
                    $current_user = wp_get_current_user();
                    $display_name = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
                    $my_account_url = get_permalink( get_option('woocommerce_myaccount_page_id') );
                ?>
                    <div class="woocom-user-dropdown-wrapper relative flex flex-col items-center group py-2">
                        <a href="<?php echo esc_url( $my_account_url ); ?>" class="flex flex-col items-center">
                            <div class="h-7 w-7 rounded-full overflow-hidden border border-gray-200 group-hover:border-primary transition-all flex items-center justify-center">
                                <?php echo get_avatar( $current_user->ID, 28, '', '', array('class' => 'rounded-full object-cover w-full h-full') ); ?>
                            </div>
                            <span class="text-[11px] mt-1.5 font-semibold uppercase tracking-tight group-hover:text-primary transition-colors"><?php echo esc_html( $display_name ); ?></span>
                        </a>
                        
                        <!-- Dropdown Menu -->
                        <div class="woocom-user-dropdown absolute right-0 top-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-[150] overflow-hidden" style="width: 220px !important;">
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-7 w-7 group-hover:text-primary transition-colors"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span class="text-[11px] mt-1.5 font-semibold uppercase tracking-tight group-hover:text-primary transition-colors">Sign In</span>
                    </a>
                <?php endif; ?>
            </div>
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
                <button id="mobile-menu-open" class="text-gray-700 hover:text-primary transition-colors p-1">
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
                <?php if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && WC()->cart ) : ?>
                <button id="cart-drawer-open-mobile" class="relative text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span class="absolute -top-1 -right-2 bg-secondary text-white text-[10px] font-bold h-5 w-5 flex items-center justify-center rounded-full ring-2 ring-white cart-count-global">
                        <?php echo WC()->cart->get_cart_contents_count(); ?>
                    </span>
                </button>
                <?php endif; ?>
            </div>
            <!-- Mobile Search -->
            <div class="px-4 pb-3 relative">
                <form role="search" method="get" class="relative" action="<?php echo esc_url( home_url( '/' ) ); ?>" id="mobile-header-search-form">
                    <input type="search" id="mobile-header-search-input" class="w-full bg-gray-50 border-2 border-secondary/30 rounded-lg py-2 px-4 pr-10 focus:ring-4 focus:ring-secondary/10 focus:border-secondary transition-all text-sm outline-none" placeholder="Search in..." value="<?php echo get_search_query(); ?>" name="s" autocomplete="off" />
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </button>
                    <input type="hidden" name="post_type" value="product" />
                </form>
                <!-- AJAX Results -->
                <div id="mobile-header-search-results" class="absolute top-full left-0 w-full bg-white shadow-xl rounded-b-lg z-[100] mt-1 hidden max-h-[300px] overflow-y-auto border border-gray-100"></div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-drawer" class="fixed inset-0 z-[200] invisible">
            <!-- Overlay -->
            <div id="mobile-drawer-overlay" class="absolute inset-0 bg-black/50 transition-opacity duration-300 opacity-0"></div>
            
            <!-- Drawer Content -->
            <div id="mobile-drawer-content" class="absolute top-0 left-0 w-[85%] max-w-[320px] h-full bg-white transform -translate-x-full transition-transform duration-300 overflow-y-auto" style="padding-bottom: 90px !important;">
                <!-- Close Button -->
                <button id="mobile-menu-close" class="absolute top-4 right-4 p-2 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors z-10">
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

        <?php if ( ( is_front_page() || is_home() ) && ( get_option( 'ticker_enabled', '1' ) == 1 ) ) : ?>
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
