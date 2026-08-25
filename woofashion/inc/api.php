<?php
/**
 * REST API Endpoints for WooFashion SPA
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get WooCommerce currency settings for the frontend
 */
function woofashion_get_currency_settings() {
    return [
        'code'               => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'BDT',
        'symbol'             => function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol() ) : '৳',
        'position'           => get_option( 'woocommerce_currency_pos', 'left' ),
        'decimals'           => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : (int) get_option( 'woocommerce_price_num_decimals', 0 ),
        'decimal_separator'  => function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : get_option( 'woocommerce_price_decimal_sep', '.' ),
        'thousand_separator' => function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : get_option( 'woocommerce_price_thousand_sep', ',' ),
    ];
}

/**
 * Format a WC_Product for the React frontend
 */
function woofashion_format_product( $product ) {
    if ( ! is_a( $product, 'WC_Product' ) ) {
        $product = wc_get_product( $product );
    }
    if ( ! $product ) {
        return null;
    }

    $id = $product->get_id();
    $categories = wp_get_post_terms( $id, 'product_cat' );
    $primary_cat = ! empty( $categories ) && ! is_wp_error( $categories ) ? $categories[0] : null;

    $image_id = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
    
    // Fallback if no attachment image
    if ( empty( $image_url ) ) {
        $image_url = get_template_directory_uri() . '/public/zenis/images/best_sell_pro_img_1.jpg';
    }

    $gallery_ids = $product->get_gallery_image_ids();
    $gallery_urls = [];
    if ( ! empty( $image_url ) ) {
        $gallery_urls[] = $image_url;
    }
    foreach ( $gallery_ids as $gid ) {
        $gurl = wp_get_attachment_image_url( $gid, 'full' );
        if ( $gurl ) {
            $gallery_urls[] = $gurl;
        }
    }
    if ( count( $gallery_urls ) < 2 ) {
        // Provide secondary gallery fallback images from theme
        $gallery_urls[] = get_template_directory_uri() . '/public/zenis/images/best_sell_pro_img_2.jpg';
        $gallery_urls[] = get_template_directory_uri() . '/public/zenis/images/best_sell_pro_img_3.jpg';
        $gallery_urls[] = get_template_directory_uri() . '/public/zenis/images/best_sell_pro_img_4.jpg';
    }

    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();
    $current_price = (float) $product->get_price();
    if ( ! $current_price ) {
        $current_price = $regular_price ?: 50.0;
    }

    $discount = 0;
    if ( $product->is_on_sale() && $regular_price > $current_price ) {
        $discount = (int) round( ( ( $regular_price - $current_price ) / $regular_price ) * 100 );
    }

    // Color map helper
    $color_hex_map = [
        'red' => '#ef4444',
        'blue' => '#3b82f6',
        'black' => '#000000',
        'white' => '#ffffff',
        'grey' => '#64748b',
        'gray' => '#64748b',
        'pink' => '#ec4899',
        'green' => '#22c55e',
        'orange' => '#f97316',
        'purple' => '#a855f7',
        'yellow' => '#eab308',
        'khaki' => '#d4b996',
        'navy' => '#1e3a8a',
        'olive' => '#556b2f',
        'brown' => '#8b4513',
        'tan' => '#d2b48c',
        'gold' => '#ffd700',
        'silver' => '#c0c0c0',
        'rose gold' => '#b76e79',
        'coral' => '#f87171',
        'beige' => '#f5f5dc',
        'cyan' => '#06b6d4',
        'charcoal' => '#374151',
        'emerald' => '#10b981',
        'wine' => '#722f37',
        'cream' => '#fffdd0',
        'indigo' => '#4f46e5',
        'stonewash' => '#6b7280',
        'cherry' => '#de3163',
        'peach' => '#ffcba4',
        'floral yellow' => '#fde047',
        'sky' => '#38bdf8',
        'sky blue' => '#38bdf8',
        'dark blue' => '#1e3a8a',
        'plaid grey' => '#9ca3af',
        'glossy black' => '#111827',
        'dark brown' => '#451a03',
        'lavender' => '#e6e6fa'
    ];

    $colors = [];
    $raw_colors = $product->get_attribute( 'Color' );
    if ( $raw_colors ) {
        $color_names = array_filter( array_map( 'trim', preg_split( '/[,|]/', $raw_colors ) ) );
        foreach ( $color_names as $cname ) {
            $lower = strtolower( $cname );
            $colors[] = [
                'name' => $cname,
                'hex' => isset( $color_hex_map[$lower] ) ? $color_hex_map[$lower] : '#3b82f6'
            ];
        }
    }
    if ( empty( $colors ) ) {
        $colors = [
            [ 'name' => 'Red', 'hex' => '#ef4444' ],
            [ 'name' => 'Blue', 'hex' => '#3b82f6' ],
            [ 'name' => 'Black', 'hex' => '#000000' ]
        ];
    }

    $sizes = [];
    $raw_sizes = $product->get_attribute( 'Size' );
    if ( $raw_sizes ) {
        $sizes = array_values( array_filter( array_map( 'trim', preg_split( '/[,|]/', $raw_sizes ) ) ) );
    }
    if ( empty( $sizes ) ) {
        $sizes = [ 'S', 'M', 'L', 'XL', 'XXL' ];
    }

    $rating = (float) $product->get_average_rating();
    if ( ! $rating ) {
        $rating = 4.8;
    }
    $reviews_count = (int) $product->get_review_count();
    if ( ! $reviews_count ) {
        $reviews_count = 124;
    }

    $tags = wp_get_post_terms( $id, 'product_tag', [ 'fields' => 'names' ] );
    if ( is_wp_error( $tags ) || empty( $tags ) ) {
        $tags = [ 'Fashion', 'Clothing', 'Premium' ];
    }

    return [
        'id'               => $id,
        'name'             => $product->get_name(),
        'slug'             => $product->get_slug(),
        'category'         => $primary_cat ? $primary_cat->name : "Men's Fashion",
        'category_slug'    => $primary_cat ? $primary_cat->slug : 'men-s-fashion',
        'price'            => $current_price,
        'oldPrice'         => $regular_price > $current_price ? $regular_price : null,
        'discount'         => $discount > 0 ? $discount : null,
        'isNew'            => ( time() - get_post_time( 'U', false, $id ) ) < ( 30 * DAY_IN_SECONDS ),
        'stockStatus'      => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
        'rating'           => $rating,
        'reviewsCount'     => $reviews_count,
        'description'      => $product->get_description() ?: 'Experience premium comfort and style with this high-quality item.',
        'shortDescription' => $product->get_short_description() ?: 'High-quality fashion apparel for everyday elegance.',
        'img'              => $image_url,
        'images'           => $gallery_urls,
        'colors'           => $colors,
        'sizes'            => $sizes,
        'sku'              => $product->get_sku() ?: 'LF-MD-' . $id,
        'tags'             => $tags,
    ];
}

/**
 * Register REST Routes
 */
function woofashion_register_rest_routes() {
    $namespace = 'woofashion/v1';

    // 1. Home Data
    register_rest_route( $namespace, '/home', [
        'methods'             => 'GET',
        'callback'            => 'woofashion_rest_get_home',
        'permission_callback' => '__return_true',
    ] );

    // 2. Products List
    register_rest_route( $namespace, '/products', [
        'methods'             => 'GET',
        'callback'            => 'woofashion_rest_get_products',
        'permission_callback' => '__return_true',
    ] );

    // 3. Single Product by slug or id
    register_rest_route( $namespace, '/products/(?P<slug>[a-zA-Z0-9-]+)', [
        'methods'             => 'GET',
        'callback'            => 'woofashion_rest_get_single_product',
        'permission_callback' => '__return_true',
    ] );

    // 4. Checkout Options (Shipping & Payment Methods)
    register_rest_route( $namespace, '/checkout-options', [
        'methods'             => 'GET',
        'callback'            => 'woofashion_rest_get_checkout_options',
        'permission_callback' => '__return_true',
    ] );

    // 5. Create Order
    register_rest_route( $namespace, '/order', [
        'methods'             => 'POST',
        'callback'            => 'woofashion_rest_create_order',
        'permission_callback' => '__return_true',
    ] );

    // 6. Track Order
    register_rest_route( $namespace, '/track-order', [
        'methods'             => 'GET',
        'callback'            => 'woofashion_rest_track_order',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'woofashion/v1', '/theme-settings', [
        'methods'             => 'GET',
        'callback'            => 'woofashion_rest_get_theme_settings',
        'permission_callback' => '__return_true',
    ] );
}
add_action( 'rest_api_init', 'woofashion_register_rest_routes' );

/**
 * GET /woofashion/v1/theme-settings
 */
function woofashion_rest_get_theme_settings() {
    return rest_ensure_response( function_exists( 'woofashion_get_theme_settings' ) ? woofashion_get_theme_settings() : [] );
}

/**
 * GET /woofashion/v1/home
 */
function woofashion_rest_get_home() {
    $theme_uri = get_template_directory_uri();

    // Get categories
    $cat_terms = get_terms( [
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ] );

    $category_icons = [
        'men-s-fashion'     => 'Shirt',
        'women-s-fashion'   => 'Heart',
        'kids-fashion'      => 'Smile',
        'western-wear'      => 'Sparkles',
        'beauty-care'       => 'Sparkles',
        'fashion-jewellery' => 'Crown',
        'sport-wear'        => 'Activity',
        'footwear'          => 'Footprints',
    ];

    $categories = [];
    if ( ! is_wp_error( $cat_terms ) ) {
        foreach ( $cat_terms as $cat ) {
            if ( $cat->slug === 'uncategorized' ) continue;
            $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
            $image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : $theme_uri . '/public/zenis/images/best_sell_pro_img_1.jpg';
            
            $categories[] = [
                'id'    => $cat->term_id,
                'name'  => $cat->name,
                'slug'  => $cat->slug,
                'count' => $cat->count,
                'icon'  => isset( $category_icons[$cat->slug] ) ? $category_icons[$cat->slug] : 'Shirt',
                'img'   => $image,
            ];
        }
    }

    // Fetch All WooCommerce Products
    $all_wc_products = wc_get_products( [ 'limit' => -1, 'status' => 'publish' ] );
    $formatted_products = [];
    foreach ( $all_wc_products as $p ) {
        $formatted = woofashion_format_product( $p );
        if ( $formatted ) {
            $formatted_products[] = $formatted;
        }
    }

    $flash_sale = array_values( array_filter( $formatted_products, function( $p ) {
        return ! empty( $p['discount'] ) || ! empty( $p['oldPrice'] );
    } ) );

    $featured = array_values( array_filter( $formatted_products, function( $p ) {
        return ! empty( $p['featured'] );
    } ) );

    // Sliders
    $sliders = [
        [
            'id'       => 1,
            'title'    => 'New Trending Fashion Collection',
            'subtitle' => 'Exclusive Autumn / Winter 2026',
            'discount' => 'Up To 50% Off',
            'btnText'  => 'Shop Now',
            'link'     => '/shop',
            'bgImg'    => $theme_uri . '/public/zenis/images/slider_1.jpg',
        ],
        [
            'id'       => 2,
            'title'    => 'Luxury Streetwear & Accessories',
            'subtitle' => 'Modern Urban Style',
            'discount' => 'Flat 30% Off',
            'btnText'  => 'Discover More',
            'link'     => '/shop',
            'bgImg'    => $theme_uri . '/public/zenis/images/slider_2.jpg',
        ]
    ];

    return rest_ensure_response( [
        'sliders'     => $sliders,
        'categories'  => $categories,
        'flashSale'   => ! empty( $flash_sale ) ? $flash_sale : array_slice( $formatted_products, 0, 8 ),
        'bestSelling' => array_slice( $formatted_products, 0, 10 ),
        'featured'      => ! empty( $featured ) ? $featured : array_slice( $formatted_products, 0, 8 ),
        'allProducts'   => $formatted_products,
        'currency'      => woofashion_get_currency_settings(),
        'themeSettings' => function_exists( 'woofashion_get_theme_settings' ) ? woofashion_get_theme_settings() : null,
    ] );
}

/**
 * GET /woofashion/v1/products
 */
function woofashion_rest_get_products( $request ) {
    $category   = $request->get_param( 'category' );
    $search     = $request->get_param( 'search' );
    $sort       = $request->get_param( 'sort' ) ?: 'date';
    $min_price  = $request->get_param( 'min_price' );
    $max_price  = $request->get_param( 'max_price' );
    $page       = (int) ( $request->get_param( 'page' ) ?: 1 );
    $per_page   = (int) ( $request->get_param( 'per_page' ) ?: 12 );

    $args = [
        'status'   => 'publish',
        'limit'    => $per_page,
        'page'     => $page,
        'paginate' => true,
    ];

    if ( ! empty( $category ) && $category !== 'all' ) {
        $args['category'] = [ $category ];
    }

    if ( ! empty( $search ) ) {
        $args['s'] = sanitize_text_field( $search );
    }

    if ( ! empty( $min_price ) || ! empty( $max_price ) ) {
        $args['min_price'] = (float) $min_price;
        if ( ! empty( $max_price ) ) {
            $args['max_price'] = (float) $max_price;
        }
    }

    // Sorting
    switch ( $sort ) {
        case 'price':
            $args['orderby'] = 'meta_value_num';
            $args['order']   = 'ASC';
            $args['meta_key'] = '_price';
            break;
        case 'price-desc':
            $args['orderby'] = 'meta_value_num';
            $args['order']   = 'DESC';
            $args['meta_key'] = '_price';
            break;
        case 'rating':
            $args['orderby'] = 'meta_value_num';
            $args['order']   = 'DESC';
            $args['meta_key'] = '_wc_average_rating';
            break;
        case 'popularity':
            $args['orderby'] = 'meta_value_num';
            $args['order']   = 'DESC';
            $args['meta_key'] = 'total_sales';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    $results = wc_get_products( $args );
    $products = [];
    foreach ( $results->products as $p ) {
        $formatted = woofashion_format_product( $p );
        if ( $formatted ) {
            $products[] = $formatted;
        }
    }

    // All categories for sidebar filter
    $cat_terms = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] );
    $categories = [];
    if ( ! is_wp_error( $cat_terms ) ) {
        foreach ( $cat_terms as $cat ) {
            if ( $cat->slug === 'uncategorized' ) continue;
            $categories[] = [
                'id'    => $cat->term_id,
                'name'  => $cat->name,
                'slug'  => $cat->slug,
                'count' => $cat->count,
            ];
        }
    }

    return rest_ensure_response( [
        'products'   => $products,
        'total'      => $results->total,
        'totalPages' => $results->max_num_pages,
        'page'       => $page,
        'categories' => $categories,
        'currency'   => woofashion_get_currency_settings(),
    ] );
}

/**
 * GET /woofashion/v1/products/{slug}
 */
function woofashion_rest_get_single_product( $request ) {
    $slug = $request->get_param( 'slug' );

    $product_obj = get_page_by_path( $slug, OBJECT, 'product' );
    if ( ! $product_obj && is_numeric( $slug ) ) {
        $product = wc_get_product( (int) $slug );
    } elseif ( $product_obj ) {
        $product = wc_get_product( $product_obj->ID );
    } else {
        // Search by slug fallback
        $found = wc_get_products( [ 'slug' => $slug, 'limit' => 1 ] );
        $product = ! empty( $found ) ? $found[0] : null;
    }

    if ( ! $product ) {
        // Fallback: return first product so page doesn't break
        $fallback = wc_get_products( [ 'limit' => 1 ] );
        if ( ! empty( $fallback ) ) {
            $product = $fallback[0];
        } else {
            return new WP_Error( 'not_found', 'Product not found', [ 'status' => 404 ] );
        }
    }

    $formatted = woofashion_format_product( $product );

    // Get related products in same category
    $related = [];
    $related_ids = wc_get_related_products( $product->get_id(), 5 );
    foreach ( $related_ids as $rid ) {
        $rp = wc_get_product( $rid );
        if ( $rp ) {
            $r_formatted = woofashion_format_product( $rp );
            if ( $r_formatted ) {
                $related[] = $r_formatted;
            }
        }
    }
    // If not enough related, fetch latest
    if ( count( $related ) < 4 ) {
        $more = wc_get_products( [ 'limit' => 5, 'exclude' => [ $product->get_id() ] ] );
        foreach ( $more as $mp ) {
            $m_formatted = woofashion_format_product( $mp );
            if ( $m_formatted && ! in_array( $m_formatted['id'], array_column( $related, 'id' ) ) ) {
                $related[] = $m_formatted;
            }
        }
    }

    return rest_ensure_response( [
        'product'         => $formatted,
        'relatedProducts' => $related,
        'currency'        => woofashion_get_currency_settings(),
    ] );
}

/**
 * GET /woofashion/v1/checkout-options
 */
function woofashion_rest_get_checkout_options() {
    // Shipping methods
    $shipping_methods = [];
    $zones = WC_Shipping_Zones::get_zones();
    
    // Add default zone
    $default_zone = new WC_Shipping_Zone( 0 );
    $zones[] = [ 'zone_id' => 0, 'shipping_methods' => $default_zone->get_shipping_methods() ];

    foreach ( $zones as $zone_data ) {
        $methods = isset( $zone_data['shipping_methods'] ) ? $zone_data['shipping_methods'] : ( isset( $zone_data['instance'] ) ? $zone_data['instance']->get_shipping_methods() : [] );
        foreach ( $methods as $method ) {
            if ( $method->is_enabled() ) {
                $cost = (float) ( isset( $method->cost ) ? $method->cost : ( isset( $method->instance_settings['cost'] ) ? $method->instance_settings['cost'] : 0 ) );
                $shipping_methods[] = [
                    'id'          => $method->id . ':' . $method->get_instance_id(),
                    'method_id'   => $method->id,
                    'instance_id' => $method->get_instance_id(),
                    'title'       => $method->get_title(),
                    'cost'        => $cost,
                    'free_min'    => isset( $method->min_amount ) ? (float) $method->min_amount : null,
                ];
            }
        }
    }

    if ( empty( $shipping_methods ) ) {
        $shipping_methods[] = [
            'id'        => 'flat_rate:1',
            'method_id' => 'flat_rate',
            'title'     => 'Standard Shipping',
            'cost'      => 10.0,
        ];
    }

    // Payment Gateways
    $payment_gateways = [];
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    foreach ( $available_gateways as $gateway ) {
        $payment_gateways[] = [
            'id'          => $gateway->id,
            'title'       => $gateway->get_title(),
            'description' => $gateway->get_description(),
        ];
    }

    if ( empty( $payment_gateways ) ) {
        $payment_gateways[] = [
            'id'          => 'cod',
            'title'       => 'Cash on Delivery (COD)',
            'description' => 'Pay with cash upon delivery.',
        ];
    }

    return rest_ensure_response( [
        'shippingMethods' => $shipping_methods,
        'paymentGateways' => $payment_gateways,
        'currency'        => woofashion_get_currency_settings(),
    ] );
}

/**
 * POST /woofashion/v1/order
 */
function woofashion_rest_create_order( $request ) {
    $params = $request->get_json_params();

    $full_name      = sanitize_text_field( isset( $params['fullName'] ) ? $params['fullName'] : '' );
    $phone          = sanitize_text_field( isset( $params['phone'] ) ? $params['phone'] : '' );
    $address        = sanitize_text_field( isset( $params['address'] ) ? $params['address'] : '' );
    $email          = sanitize_email( isset( $params['email'] ) ? $params['email'] : 'customer@woofashion.local' );
    $items          = isset( $params['items'] ) && is_array( $params['items'] ) ? $params['items'] : [];
    $payment_method = sanitize_text_field( isset( $params['paymentMethod'] ) ? $params['paymentMethod'] : 'cod' );
    $shipping_cost  = (float) ( isset( $params['shippingCost'] ) ? $params['shippingCost'] : 10.0 );

    if ( empty( $full_name ) || empty( $phone ) || empty( $address ) || empty( $items ) ) {
        return new WP_Error( 'missing_fields', 'Please provide Full Name, Phone, Address and items.', [ 'status' => 400 ] );
    }

    // Split name into first and last
    $name_parts = explode( ' ', $full_name, 2 );
    $first_name = $name_parts[0];
    $last_name  = isset( $name_parts[1] ) ? $name_parts[1] : '';

    // Disable email sending to prevent localhost mail timeouts
    add_filter( 'woocommerce_email_enabled_new_order', '__return_false' );
    add_filter( 'woocommerce_email_enabled_customer_processing_order', '__return_false' );

    // Create WooCommerce Order
    $order = wc_create_order();

    // Add items
    foreach ( $items as $item ) {
        $product_id = (int) ( isset( $item['id'] ) ? $item['id'] : 0 );
        $qty = (int) ( isset( $item['quantity'] ) ? $item['quantity'] : 1 );
        $product = wc_get_product( $product_id );

        if ( $product ) {
            $item_id = $order->add_product( $product, $qty );
            if ( isset( $item['color'] ) ) {
                wc_add_order_item_meta( $item_id, 'Color', $item['color'] );
            }
            if ( isset( $item['size'] ) ) {
                wc_add_order_item_meta( $item_id, 'Size', $item['size'] );
            }
        }
    }

    // Billing & Shipping Address
    $address_data = [
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'company'    => '',
        'email'      => $email,
        'phone'      => $phone,
        'address_1'  => $address,
        'address_2'  => '',
        'city'       => 'Dhaka',
        'state'      => '',
        'postcode'   => '1200',
        'country'    => 'BD',
    ];

    $order->set_address( $address_data, 'billing' );
    $order->set_address( $address_data, 'shipping' );

    // Shipping item
    if ( $shipping_cost > 0 ) {
        $shipping_item = new WC_Order_Item_Shipping();
        $shipping_item->set_method_title( 'Standard Shipping' );
        $shipping_item->set_method_id( 'flat_rate' );
        $shipping_item->set_total( $shipping_cost );
        $order->add_item( $shipping_item );
    }

    // Payment Method
    $order->set_payment_method( $payment_method );
    $order->set_payment_method_title( $payment_method === 'cod' ? 'Cash on Delivery (COD)' : ucfirst( $payment_method ) );

    // Calculate totals and set status
    $order->calculate_totals();
    $order->update_status( 'processing', 'Order placed via WooFashion React SPA.' );
    $order->save();

    return rest_ensure_response( [
        'success'    => true,
        'orderId'    => $order->get_id(),
        'orderKey'   => $order->get_order_key(),
        'orderTotal' => (float) $order->get_total(),
        'status'     => $order->get_status(),
        'message'    => 'Order created successfully!',
    ] );
}

/**
 * GET /woofashion/v1/track-order
 */
function woofashion_rest_track_order( $request ) {
    $order_id = $request->get_param( 'order_id' );
    $phone    = $request->get_param( 'phone' );

    if ( empty( $order_id ) ) {
        return new WP_Error( 'missing_id', 'Order ID is required.', [ 'status' => 400 ] );
    }

    // Clean order id (e.g. #ORD-12345 or 12345)
    $clean_id = (int) preg_replace( '/[^0-9]/', '', $order_id );
    $order = wc_get_order( $clean_id );

    if ( ! $order ) {
        return new WP_Error( 'order_not_found', 'Order not found with ID #' . $order_id, [ 'status' => 404 ] );
    }

    $items = [];
    foreach ( $order->get_items() as $item_id => $item ) {
        $items[] = [
            'name'     => $item->get_name(),
            'quantity' => $item->get_quantity(),
            'total'    => (float) $item->get_total(),
        ];
    }

    return rest_ensure_response( [
        'orderId'   => $order->get_id(),
        'status'    => wc_get_order_status_name( $order->get_status() ),
        'statusCode'=> $order->get_status(),
        'date'      => $order->get_date_created()->date( 'F j, Y, g:i a' ),
        'total'     => (float) $order->get_total(),
        'customer'  => [
            'name'    => $order->get_formatted_billing_full_name(),
            'phone'   => $order->get_billing_phone(),
            'address' => $order->get_billing_address_1(),
        ],
        'items'     => $items,
    ] );
}
