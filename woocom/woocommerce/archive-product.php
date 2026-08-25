<?php
/**
 * The Template for displaying product archives (Laracom style)
 *
 * @package Woocom
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

// Retrieve filter arguments from GET request
$min_price = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : 0;
$max_price = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : 10000;
$selected_brands = isset( $_GET['brand'] ) && is_array( $_GET['brand'] ) ? array_map( 'sanitize_text_field', $_GET['brand'] ) : array();
$selected_availabilities = isset( $_GET['availability'] ) && is_array( $_GET['availability'] ) ? array_map( 'sanitize_text_field', $_GET['availability'] ) : array();
$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'default';
$show_count = isset( $_GET['show_count'] ) ? intval( $_GET['show_count'] ) : 20;
$current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;

$current_cat_slug = '';
$current_cat_name = 'Shop Products';
if ( is_product_category() ) {
    $current_term = get_queried_object();
    $current_cat_slug = $current_term->slug;
    $current_cat_name = $current_term->name;
}

// 1. Helper function to extract brand from title
if ( ! function_exists( 'woocom_get_brand_from_title' ) ) {
    function woocom_get_brand_from_title( $title ) {
        $first_word = explode( ' ', $title )[0];
        $known_brands = array( 'Baseus', 'Remax', 'Mcdodo', 'Joyroom', 'Anker', 'Ugreen', 'Lumevax', 'Maxhub', 'Philips' );
        if ( in_array( $first_word, $known_brands ) ) {
            return $first_word;
        }
        
        $lower = strtolower( $title );
        foreach ( $known_brands as $brand ) {
            if ( strpos( $lower, strtolower( $brand ) ) !== false ) {
                return $brand;
            }
        }
        
        return 'Other';
    }
}

// 2. Helper function to determine availability
if ( ! function_exists( 'woocom_get_availability_from_product' ) ) {
    function woocom_get_availability_from_product( $product, $index ) {
        $request_type = function_exists( 'woocom_get_product_request_type' ) ? woocom_get_product_request_type( $product ) : '';
        if ( $request_type === 'pre_order' ) {
            return 'Pre Order';
        }
        if ( $request_type === 'out_of_stock' ) {
            return 'Up Coming';
        }
        if ( ! $product->is_in_stock() ) {
            return 'Up Coming';
        }
        return 'In Stock';
    }
}

// 3. Query all products matching the current category (without pagination)
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
);

if ( is_product_category() ) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $current_cat_slug,
        ),
    );
}

$all_query = new WP_Query( $args );
$all_products = array();
$index = 0;

if ( $all_query->have_posts() ) {
    while ( $all_query->have_posts() ) {
        $all_query->the_post();
        global $product;
        $price = (float) $product->get_price();
        $all_products[] = array(
            'id'           => $product->get_id(),
            'title'        => $product->get_name(),
            'price'        => $price,
            'regular_price'=> (float) $product->get_regular_price(),
            'brand'        => woocom_get_brand_from_title( $product->get_name() ),
            'availability' => woocom_get_availability_from_product( $product, $index ),
            'product_obj'  => clone $product,
        );
        $index++;
    }
    wp_reset_postdata();
}

// 4. Process all available brands for the sidebar checkboxes
$all_brands_list = array( 'Baseus', 'Remax', 'Mcdodo', 'Joyroom', 'Anker', 'Ugreen', 'InFocus', 'BenQ', 'Hitachi', 'ViewSonic', 'BoxLight', 'ARMOR', 'Optoma', 'LG', 'Dahua' );
foreach ( $all_products as $p ) {
    if ( ! in_array( $p['brand'], $all_brands_list ) ) {
        $all_brands_list[] = $p['brand'];
    }
}
$all_brands_list = array_unique( $all_brands_list );
sort( $all_brands_list );

// 5. Apply PHP Filters
$filtered_products = array();
foreach ( $all_products as $p ) {
    // Price Filter
    if ( $p['price'] < $min_price || $p['price'] > $max_price ) {
        continue;
    }
    // Brand Filter
    if ( ! empty( $selected_brands ) && ! in_array( $p['brand'], $selected_brands ) ) {
        continue;
    }
    // Availability Filter
    if ( ! empty( $selected_availabilities ) && ! in_array( $p['availability'], $selected_availabilities ) ) {
        continue;
    }
    $filtered_products[] = $p;
}

// 6. Apply Sorting
if ( $orderby === 'price_asc' ) {
    usort( $filtered_products, function( $a, $b ) {
        return $a['price'] <=> $b['price'];
    } );
} elseif ( $orderby === 'price_desc' ) {
    usort( $filtered_products, function( $a, $b ) {
        return $b['price'] <=> $a['price'];
    } );
} elseif ( $orderby === 'title_asc' ) {
    usort( $filtered_products, function( $a, $b ) {
        return strcmp( $a['title'], $b['title'] );
    } );
}

// 7. Paginate the filtered list
$total_filtered = count( $filtered_products );
$total_pages = ceil( $total_filtered / $show_count );
$start_offset = ( $current_page - 1 ) * $show_count;
$displayed_products = array_slice( $filtered_products, $start_offset, $show_count );


// 8. Fetch product categories for sidebar
$categories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
) );

get_header();
?>

<style>
    /* Premium Poppins font imported */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Hind:wght@400;500;600;700&display=swap');

    .woocom-shop-outer {
        font-family: 'Hind', sans-serif;
        background: #f8fafc;
        min-height: 100vh;
        padding-bottom: 60px;
    }

    /* Breadcrumbs Banner */
    .woocom-breadcrumbs-banner {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 24px 0;
        margin-bottom: 35px;
    }
    .woocom-breadcrumbs-container {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .woocom-breadcrumbs-title {
        font-family: 'Poppins', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .woocom-breadcrumbs-trail {
        font-size: 13px;
        font-weight: 600;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .woocom-breadcrumbs-trail a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s;
    }
    .woocom-breadcrumbs-trail a:hover {
        color: var(--color-primary, #2563EB);
    }
    .woocom-breadcrumbs-trail span.active {
        color: var(--color-primary, #2563EB);
    }

    /* Shop Content Wrapper */
    .woocom-shop-content-wrapper {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Layout structure */
    .woocom-shop-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
    }
    .woocom-shop-sidebar {
        width: 290px;
        flex-shrink: 0;
    }
    .woocom-shop-main {
        flex: 1;
        min-width: 0;
    }

    /* Sidebar Cards */
    .woocom-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.01);
    }
    .woocom-filter-header-card {
        display: flex;
        align-items: center;
        justify-content: justify-between;
    }
    .woocom-filter-title {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #0f172a;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        padding: 0;
        outline: none;
    }
    .woocom-filter-title svg {
        transition: transform 0.2s;
        stroke: #94a3b8;
    }
    .woocom-filter-title.collapsed svg {
        transform: rotate(-90deg);
    }
    .woocom-filter-content {
        margin-top: 18px;
        transition: max-height 0.3s ease-out;
        overflow: visible;
    }
    .woocom-filter-content.collapsed {
        display: none;
    }

    /* Custom Input Controls */
    .woocom-filter-item {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        margin-bottom: 12px;
        user-select: none;
        font-weight: 600;
        font-size: 13.5px;
        color: #475569;
        transition: color 0.2s;
    }
    .woocom-filter-item:hover {
        color: #0f172a;
    }
    .woocom-filter-item input[type="radio"],
    .woocom-filter-item input[type="checkbox"] {
        display: none;
    }

    /* Radio elements */
    .woocom-radio-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: #ffffff;
        flex-shrink: 0;
    }
    .woocom-filter-item input[type="radio"]:checked + .woocom-radio-dot {
        border-color: var(--color-primary, #2563EB);
        background: var(--color-primary, #2563EB);
    }
    .woocom-radio-dot::after {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ffffff;
        display: none;
    }
    .woocom-filter-item input[type="radio"]:checked + .woocom-radio-dot::after {
        display: block;
    }

    /* Checkbox elements */
    .woocom-checkbox-box {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        border: 2px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: #ffffff;
        flex-shrink: 0;
    }
    .woocom-filter-item input[type="checkbox"]:checked + .woocom-checkbox-box {
        border-color: var(--color-primary, #2563EB);
        background: var(--color-primary, #2563EB);
    }
    .woocom-checkbox-box::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid white;
        border-width: 0 2.5px 2.5px 0;
        transform: rotate(45deg);
        margin-top: -2px;
        display: none;
    }
    .woocom-filter-item input[type="checkbox"]:checked + .woocom-checkbox-box::after {
        display: block;
    }

    /* Range Slider */
    .woocom-range-slider-container {
        position: relative;
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        margin: 20px 0 10px 0;
    }
    .woocom-range-slider-track {
        position: absolute;
        height: 100%;
        background: var(--color-primary, #2563EB);
        border-radius: 3px;
    }
    .woocom-range-slider-input {
        position: absolute;
        width: 100%;
        background: transparent;
        pointer-events: none;
        -webkit-appearance: none;
        appearance: none;
        height: 6px;
        top: 0;
        left: 0;
        outline: none;
        margin: 0;
    }
    .woocom-range-slider-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--color-primary, #2563EB);
        border: 2px solid #ffffff;
        cursor: pointer;
        pointer-events: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        transition: transform 0.1s ease;
    }
    .woocom-range-slider-input::-webkit-slider-thumb:hover {
        transform: scale(1.15);
    }
    .woocom-range-slider-input::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--color-primary, #2563EB);
        border: 2px solid #ffffff;
        cursor: pointer;
        pointer-events: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        transition: transform 0.1s ease;
    }
    .woocom-range-slider-input::-moz-range-thumb:hover {
        transform: scale(1.15);
    }

    .woocom-range-inputs {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }
    .woocom-range-inputs input {
        width: 100%;
        text-align: center;
        padding: 7px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        outline: none;
        background: #f8fafc;
        transition: border-color 0.2s;
    }
    .woocom-range-inputs input:focus {
        border-color: var(--color-primary, #2563EB);
        background: #ffffff;
    }

    /* Brand Search */
    .woocom-brand-search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 12px;
        margin-bottom: 15px;
    }
    .woocom-brand-search-wrapper svg {
        stroke: #94a3b8;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .woocom-brand-search-wrapper input {
        border: none;
        background: transparent;
        width: 100%;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        outline: none;
    }
    .woocom-brand-scrollbox {
        max-height: 220px;
        overflow-y: auto;
        padding-right: 5px;
    }

    /* Reset button */
    .woocom-reset-btn {
        font-size: 11px;
        font-weight: 700;
        color: var(--color-primary, #2563EB);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        text-decoration: none;
    }
    .woocom-reset-btn:hover {
        text-decoration: underline;
    }

    /* Main Toolbar */
    .woocom-shop-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.01);
        flex-wrap: wrap;
        gap: 15px;
    }
    .woocom-shop-toolbar-title {
        font-size: 13.5px;
        font-weight: 600;
        color: #64748b;
    }
    .woocom-shop-toolbar-controls {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .woocom-toolbar-control-group {
        display: flex;
        align-items: center;
    }
    .woocom-toolbar-select-label {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-right: 8px;
    }
    .woocom-toolbar-select {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .woocom-toolbar-select:hover {
        border-color: var(--color-primary, #2563EB);
    }

    /* Grid Layout */
    .woocom-product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
    }
    
    /* Pagination */
    .woocom-pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 40px;
    }
    .woocom-page-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        color: #475569;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.2s;
        background: #ffffff;
    }
    .woocom-page-num:hover,
    .woocom-page-num.active {
        background: var(--color-primary, #2563EB);
        border-color: var(--color-primary, #2563EB);
        color: #ffffff;
    }

    /* Mobile toggle */
    .woocom-mobile-filter-bar {
        display: none;
        width: 100%;
        margin-bottom: 20px;
    }
    .woocom-mobile-toggle-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 2px solid var(--color-primary, #2563EB);
        background: transparent;
        color: var(--color-primary, #2563EB);
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        outline: none;
    }

    @media (max-width: 1300px) and (min-width: 1025px) {
        .woocom-product-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 1024px) {
        .woocom-shop-layout {
            flex-direction: column;
        }
        .woocom-shop-sidebar {
            width: 100%;
            display: none; /* Controlled by JS toggle on mobile */
        }
        .woocom-shop-sidebar.active {
            display: block;
        }
        .woocom-mobile-filter-bar {
            display: block;
        }
        .woocom-product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .woocom-product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .woocom-breadcrumbs-container {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    .woocom-product-grid.loading {
        opacity: 0.45;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
</style>

<div class="woocom-shop-outer">
    
    <!-- Breadcrumbs Banner -->
    <div class="woocom-breadcrumbs-banner">
        <div class="woocom-breadcrumbs-container">
            <h1 class="woocom-breadcrumbs-title">
                <?php echo esc_html( $current_cat_name ); ?>
            </h1>
            <div class="woocom-breadcrumbs-trail">
                <a href="<?php echo esc_url( home_url('/') ); ?>">Home</a>
                <span>/</span>
                <span class="active">Shop</span>
            </div>
        </div>
    </div>

    <!-- Shop Content Container -->
    <div class="woocom-shop-content-wrapper">
        
        <!-- Mobile Filter Bar -->
        <div class="woocom-mobile-filter-bar">
            <button type="button" class="woocom-mobile-toggle-btn" onclick="toggleMobileSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="2" y1="14" x2="6" y2="14"/><line x1="10" y1="8" x2="14" y2="8"/><line x1="18" y1="16" x2="22" y2="16"/></svg>
                <span>FILTERS</span>
            </button>
        </div>

        <div class="woocom-shop-layout">
            
            <!-- Sidebar Filters -->
            <aside class="woocom-shop-sidebar" id="shop-sidebar">
                <form method="GET" id="shop-filter-form" action="">
                    
                    <!-- Form Preserve Current Search if any -->
                    <?php if ( isset( $_GET['s'] ) ) : ?>
                        <input type="hidden" name="s" value="<?php echo esc_attr( $_GET['s'] ); ?>" />
                    <?php endif; ?>

                    <!-- Filter Header -->
                    <div class="woocom-filter-card" style="padding: 15px 18px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 13.5px; text-transform: uppercase; color: #0f172a; letter-spacing: 0.05em;">Filters</span>
                            <?php if ( $min_price > 0 || $max_price < 10000 || ! empty( $selected_brands ) || ! empty( $selected_availabilities ) || $orderby !== 'default' ) : ?>
                                <a href="<?php echo esc_url( is_product_category() ? get_term_link( $current_cat_slug, 'product_cat' ) : get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="woocom-reset-btn">Reset All</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Price Card -->
                    <div class="woocom-filter-card">
                        <button type="button" class="woocom-filter-title" onclick="toggleCardCollapse('price-card-content', this)">
                            <span>Price Range</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="woocom-filter-content" id="price-card-content">
                            <div class="woocom-range-slider-container">
                                <div class="woocom-range-slider-track" id="slider-track"></div>
                                <input type="range" min="0" max="10000" step="100" value="<?php echo esc_attr($min_price); ?>" class="woocom-range-slider-input" id="slider-1" oninput="slideOne()">
                                <input type="range" min="0" max="10000" step="100" value="<?php echo esc_attr($max_price); ?>" class="woocom-range-slider-input" id="slider-2" oninput="slideTwo()">
                            </div>
                            <div class="woocom-range-inputs">
                                <input type="number" name="min_price" id="range1" value="<?php echo esc_attr($min_price); ?>" />
                                <span style="font-size: 12px; font-weight: bold; color: #94a3b8;">—</span>
                                <input type="number" name="max_price" id="range2" value="<?php echo esc_attr($max_price); ?>" />
                            </div>
                        </div>
                    </div>

                    <!-- Category Card -->
                    <div class="woocom-filter-card">
                        <button type="button" class="woocom-filter-title" onclick="toggleCardCollapse('cat-card-content', this)">
                            <span>Category</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="woocom-filter-content" id="cat-card-content">
                            <!-- All Categories -->
                            <label class="woocom-filter-item">
                                <input type="radio" name="cat_slug" value="" <?php checked( $current_cat_slug, '' ); ?> data-url="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
                                <span class="woocom-radio-dot"></span>
                                <span style="<?php echo empty($current_cat_slug) ? 'color: var(--color-primary, #2563EB);' : ''; ?>">All Categories</span>
                            </label>
                            <!-- List categories -->
                            <?php foreach ( $categories as $cat ) : ?>
                                <label class="woocom-filter-item">
                                    <input type="radio" name="cat_slug" value="<?php echo esc_attr( $cat->slug ); ?>" <?php checked( $current_cat_slug, $cat->slug ); ?> data-url="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                                    <span class="woocom-radio-dot"></span>
                                    <span style="<?php echo $current_cat_slug === $cat->slug ? 'color: var(--color-primary, #2563EB);' : ''; ?>"><?php echo esc_html( $cat->name ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Availability Card -->
                    <div class="woocom-filter-card">
                        <button type="button" class="woocom-filter-title" onclick="toggleCardCollapse('avail-card-content', this)">
                            <span>Availability</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="woocom-filter-content" id="avail-card-content">
                            <?php foreach ( array( 'In Stock', 'Pre Order', 'Up Coming' ) as $avail ) : 
                                $isChecked = in_array( $avail, $selected_availabilities );
                            ?>
                                <label class="woocom-filter-item">
                                    <input type="checkbox" name="availability[]" value="<?php echo esc_attr( $avail ); ?>" <?php checked( $isChecked ); ?> />
                                    <span class="woocom-checkbox-box"></span>
                                    <span><?php echo esc_html( $avail ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Brand Card -->
                    <div class="woocom-filter-card">
                        <button type="button" class="woocom-filter-title" onclick="toggleCardCollapse('brand-card-content', this)">
                            <span>Brand</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="woocom-filter-content" id="brand-card-content">
                            <!-- Search brand -->
                            <div class="woocom-brand-search-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                <input type="text" id="brand-search-input" placeholder="Search brands..." />
                            </div>
                            <div class="woocom-brand-scrollbox">
                                <?php foreach ( $all_brands_list as $brand ) : 
                                    $isChecked = in_array( $brand, $selected_brands );
                                ?>
                                    <label class="woocom-filter-item">
                                        <input type="checkbox" name="brand[]" value="<?php echo esc_attr( $brand ); ?>" <?php checked( $isChecked ); ?> />
                                        <span class="woocom-checkbox-box"></span>
                                        <span><?php echo esc_html( $brand ); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Carry orderby and show_count state inside the form submission -->
                    <input type="hidden" name="orderby" id="filter-orderby-hidden" value="<?php echo esc_attr( $orderby ); ?>" />
                    <input type="hidden" name="show_count" id="filter-showcount-hidden" value="<?php echo esc_attr( $show_count ); ?>" />

                </form>
            </aside>

            <!-- Main Shop Products Grid -->
            <main class="woocom-shop-main">
                
                <!-- Toolbar controls -->
                <div class="woocom-shop-toolbar">
                    <span class="woocom-shop-toolbar-title">
                        Showing <strong><?php echo count($displayed_products); ?></strong> of <strong><?php echo $total_filtered; ?></strong> Products
                    </span>

                    <div class="woocom-shop-toolbar-controls">
                        <!-- Show Count -->
                        <div class="woocom-toolbar-control-group">
                            <span class="woocom-toolbar-select-label">Show:</span>
                            <select class="woocom-toolbar-select" id="toolbar-show-count">
                                <option value="10" <?php selected( $show_count, 10 ); ?>>10</option>
                                <option value="20" <?php selected( $show_count, 20 ); ?>>20</option>
                                <option value="50" <?php selected( $show_count, 50 ); ?>>50</option>
                            </select>
                        </div>
                        
                        <!-- Orderby -->
                        <div class="woocom-toolbar-control-group">
                            <span class="woocom-toolbar-select-label">Sort:</span>
                            <select class="woocom-toolbar-select" id="toolbar-orderby">
                                <option value="default" <?php selected( $orderby, 'default' ); ?>>Default</option>
                                <option value="price_asc" <?php selected( $orderby, 'price_asc' ); ?>>Price: Low to High</option>
                                <option value="price_desc" <?php selected( $orderby, 'price_desc' ); ?>>Price: High to Low</option>
                                <option value="title_asc" <?php selected( $orderby, 'title_asc' ); ?>>Name: A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid container -->
                <div class="woocom-product-grid">
                    <?php if ( ! empty( $displayed_products ) ) : ?>
                        <?php
                        foreach ( $displayed_products as $p_item ) {
                            $post_object = get_post( $p_item['id'] );
                            setup_postdata( $post_object );
                            
                            // Set WooCommerce global product
                            global $product;
                            $product = $p_item['product_obj'];
                            
                            wc_get_template_part( 'content', 'product' );
                        }
                        wp_reset_postdata();
                        ?>
                    <?php else : ?>
                        <!-- Empty State inside Grid wrapper to preserve element for AJAX updates -->
                        <div style="grid-column: 1 / -1; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 60px 20px; text-align: center; width: 100%;">
                            <div style="max-width: 320px; margin: 0 auto;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin: 0 auto 24px auto; display: block;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                <h2 style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">No products found</h2>
                                <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">Try adjusting your filters or search terms to find what you're looking for.</p>
                                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" style="display: inline-block; background: var(--color-primary, #2563EB); color: #ffffff; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 12px; text-decoration: none; box-shadow: 0 4px 6px -1px color-mix(in srgb, var(--color-primary, #2563EB) 20%, transparent); transition: all 0.2s;">Return to Shop</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Custom Pagination container -->
                <div class="woocom-pagination">
                    <?php if ( $total_pages > 1 ) : ?>
                        <?php
                        for ( $i = 1; $i <= $total_pages; $i++ ) {
                            $active_class = ( $i === $current_page ) ? 'active' : '';
                            $query_args = $_GET;
                            $query_args['paged'] = $i;
                            $page_url = add_query_arg( $query_args, is_product_category() ? get_term_link( $current_cat_slug, 'product_cat' ) : get_permalink( wc_get_page_id( 'shop' ) ) );
                            echo '<a href="' . esc_url( $page_url ) . '" class="woocom-page-num ' . $active_class . '">' . $i . '</a>';
                        }
                        ?>
                    <?php endif; ?>
                </div>

            </main>

        </div>

    </div>

</div>

<!-- Filter Javascript integration -->
<script type="text/javascript">
    var sliderOne = document.getElementById("slider-1");
    var sliderTwo = document.getElementById("slider-2");
    var displayValOne = document.getElementById("range1");
    var displayValTwo = document.getElementById("range2");
    var minGap = 500;
    var sliderTrack = document.getElementById("slider-track");
    var sliderMaxValue = document.getElementById("slider-1").max;

    function slideOne() {
        if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
            sliderOne.value = parseInt(sliderTwo.value) - minGap;
        }
        displayValOne.value = sliderOne.value;
        fillColor();
    }
    function slideTwo() {
        if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
            sliderTwo.value = parseInt(sliderOne.value) + minGap;
        }
        displayValTwo.value = sliderTwo.value;
        fillColor();
    }
    function fillColor() {
        var percent1 = (sliderOne.value / sliderMaxValue) * 100;
        var percent2 = (sliderTwo.value / sliderMaxValue) * 100;
        sliderTrack.style.left = percent1 + "%";
        sliderTrack.style.right = (100 - percent2) + "%";
    }

    function toggleCardCollapse(cardId, button) {
        var content = document.getElementById(cardId);
        if (content.style.display === "none" || content.classList.contains("collapsed")) {
            content.style.display = "block";
            content.classList.remove("collapsed");
            button.classList.remove("collapsed");
        } else {
            content.style.display = "none";
            content.classList.add("collapsed");
            button.classList.add("collapsed");
        }
    }

    function toggleMobileSidebar() {
        var sidebar = document.getElementById("shop-sidebar");
        sidebar.classList.toggle("active");
    }

    function submitFilterForm(pageNumber) {
        var page = pageNumber || 1;
        var grid = jQuery('.woocom-product-grid');
        var pagination = jQuery('.woocom-pagination');
        var countTitle = jQuery('.woocom-shop-toolbar-title');
        var breadcrumbsTitle = jQuery('.woocom-breadcrumbs-title');
        
        grid.addClass('loading');
        
        var formData = jQuery('#shop-filter-form').serializeArray();
        
        formData.push({ name: 'action', value: 'woocom_ajax_filter_products' });
        formData.push({ name: 'paged', value: page });
        
        var activeRadio = jQuery('input[name="cat_slug"]:checked');
        var activeRadioCat = activeRadio.length > 0 ? activeRadio.val() : "";
        formData.push({ name: 'cat_slug', value: activeRadioCat });
        
        jQuery.ajax({
            url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
            type: 'GET',
            data: formData,
            success: function(response) {
                grid.removeClass('loading');
                if (response.success) {
                    grid.html(response.data.products_html);
                    pagination.html(response.data.pagination_html);
                    countTitle.html(response.data.count_text);
                    breadcrumbsTitle.text(response.data.cat_name);
                    
                    updateBrowserURL(formData);
                    
                    var toolbar = jQuery('.woocom-shop-toolbar');
                    if (toolbar.length > 0) {
                        jQuery('html, body').animate({
                            scrollTop: toolbar.offset().top - 100
                        }, 300);
                    }
                }
            },
            error: function() {
                grid.removeClass('loading');
                alert('Connection error occurred.');
            }
        });
    }

    function updateBrowserURL(formData) {
        var params = {};
        jQuery.each(formData, function(i, field) {
            if (field.name === 'action' || field.name === 'cat_slug' || field.name === 'paged') {
                return;
            }
            if (field.name.indexOf('[]') !== -1) {
                var cleanName = field.name.replace('[]', '');
                if (!params[cleanName]) {
                    params[cleanName] = [];
                }
                params[cleanName].push(field.value);
            } else {
                params[field.name] = field.value;
            }
        });
        
        var page = 1;
        jQuery.each(formData, function(i, field) {
            if (field.name === 'paged') {
                page = field.value;
            }
        });
        if (page > 1) {
            params['paged'] = page;
        }

        var activeRadio = jQuery('input[name="cat_slug"]:checked');
        var baseUrl = activeRadio.length > 0 && activeRadio.data('url') ? activeRadio.data('url') : window.location.pathname;
        
        var queryString = jQuery.param(params);
        var newUrl = baseUrl + (queryString ? '?' + queryString : '');
        
        history.pushState(null, '', newUrl);
    }

    function resetAllFilters(event) {
        if (event) {
            event.preventDefault();
        }
        jQuery('#slider-1').val(0);
        jQuery('#slider-2').val(10000);
        jQuery('#range1').val(0);
        jQuery('#range2').val(10000);
        if (typeof fillColor === 'function') {
            fillColor();
        }
        
        jQuery('.woocom-filter-item input[type="checkbox"]').prop('checked', false);
        jQuery('input[name="cat_slug"]').prop('checked', false);
        jQuery('input[name="cat_slug"][value=""]').prop('checked', true);
        
        jQuery('input[name="cat_slug"]').siblings('span:not(.woocom-radio-dot)').css('color', '');
        jQuery('input[name="cat_slug"][value=""]').siblings('span:not(.woocom-radio-dot)').css('color', 'var(--color-primary, #2563EB)');
        
        submitFilterForm(1);
    }

    // Trigger calculation initial state of slider
    if (sliderOne && sliderTwo) {
        slideOne();
        slideTwo();
    }

    function ajaxAddToCart(e, button) {
        e.preventDefault();
        e.stopPropagation();
        
        var $button = jQuery(button);
        var productId = $button.data('product_id');
        var qty = $button.data('quantity') || 1;
        
        if (!productId) {
            return;
        }
        
        $button.addClass('loading');
        $button.css('pointer-events', 'none');
        
        jQuery.ajax({
            url: '<?php echo esc_url( home_url( "/?wc-ajax=add_to_cart" ) ); ?>',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: qty
            },
            success: function(response) {
                $button.removeClass('loading');
                $button.css('pointer-events', 'auto');
                
                if (response.error && response.product_url) {
                    window.location.href = response.product_url;
                    return;
                }
                
                jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                
                var label = $button.find('span');
                var originalText = label.text();
                label.text('Added!');
                setTimeout(function() {
                    label.text(originalText);
                }, 2000);
            },
            error: function() {
                $button.removeClass('loading');
                $button.css('pointer-events', 'auto');
                alert('Error adding product to cart.');
            }
        });
    }

    // Bind AJAX-like automatic page submissions
    jQuery(document).ready(function($) {
        // Intercept reset all link
        $(document).on('click', '.woocom-reset-btn', function(e) {
            resetAllFilters(e);
        });

        // Change category dynamically via AJAX
        $('input[name="cat_slug"]').on('change', function() {
            $('input[name="cat_slug"]').siblings('span:not(.woocom-radio-dot)').css('color', '');
            $(this).siblings('span:not(.woocom-radio-dot)').css('color', 'var(--color-primary, #2563EB)');
            submitFilterForm(1);
        });

        // Trigger auto submit on checkbox toggle
        $('.woocom-filter-item input[type="checkbox"]').on('change', function() {
            submitFilterForm(1);
        });

        // Intercept pagination clicks dynamically
        $(document).on('click', '.woocom-pagination a', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (page) {
                submitFilterForm(page);
            }
        });

        // On manual input box changes
        $('#range1').on('change', function() {
            if (parseInt($(this).val()) < 0) $(this).val(0);
            if (parseInt($(this).val()) > parseInt(sliderTwo.value) - minGap) {
                $(this).val(parseInt(sliderTwo.value) - minGap);
            }
            sliderOne.value = $(this).val();
            fillColor();
            submitFilterForm(1);
        });
        $('#range2').on('change', function() {
            if (parseInt($(this).val()) > sliderMaxValue) $(this).val(sliderMaxValue);
            if (parseInt($(this).val()) < parseInt(sliderOne.value) + minGap) {
                $(this).val(parseInt(sliderOne.value) + minGap);
            }
            sliderTwo.value = $(this).val();
            fillColor();
            submitFilterForm(1);
        });

        // Submit form when releasing sliders
        $('.woocom-range-slider-input').on('mouseup touchend', function() {
            submitFilterForm(1);
        });

        // Toolbar control syncs
        $('#toolbar-orderby').on('change', function() {
            $('#filter-orderby-hidden').val($(this).val());
            submitFilterForm(1);
        });
        $('#toolbar-show-count').on('change', function() {
            $('#filter-showcount-hidden').val($(this).val());
            submitFilterForm(1);
        });

        // Brand checkbox keyword filtering search
        $('#brand-search-input').on('keyup', function() {
            var q = $(this).val().toLowerCase();
            $('.woocom-brand-scrollbox .woocom-filter-item').each(function() {
                var txt = $(this).text().toLowerCase();
                if (txt.indexOf(q) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>

<?php
get_footer();
