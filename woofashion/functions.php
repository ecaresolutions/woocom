<?php
/**
 * Theme functions and definitions
 */

if ( ! defined( 'WOOFASHION_SPA_VERSION' ) ) {
    define( 'WOOFASHION_SPA_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function woofashion_spa_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'woofashion_spa_setup' );

/**
 * Inject React Refresh preamble for @vitejs/plugin-react in dev mode
 */
function woofashion_spa_vite_preamble() {
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
    if ( ! file_exists( $manifest_path ) ) {
        echo '<script type="module">
            import RefreshRuntime from "http://localhost:5173/@react-refresh"
            RefreshRuntime.injectIntoGlobalHook(window)
            window.$RefreshReg$ = () => {}
            window.$RefreshSig$ = () => (type) => type
            window.__vite_plugin_react_preamble_installed__ = true
        </script>';
    }
}
add_action( 'wp_head', 'woofashion_spa_vite_preamble', 1 );

/**
 * Enqueue scripts and styles.
 */
function woofashion_spa_scripts() {
    $theme_dir_url = get_template_directory_uri();
    
    // In production, enqueue the compiled Vite assets
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
    
    if ( file_exists( $manifest_path ) ) {
        $manifest = json_decode( file_get_contents( $manifest_path ), true );
        
        if ( isset( $manifest['src/main.tsx'] ) ) {
            $main_js = $manifest['src/main.tsx']['file'];
            $main_css = isset($manifest['src/main.tsx']['css'][0]) ? $manifest['src/main.tsx']['css'][0] : null;
            
            $ver = file_exists( get_template_directory() . '/dist/' . $main_js ) ? filemtime( get_template_directory() . '/dist/' . $main_js ) : time();
            
            wp_enqueue_script( 'woofashion-spa-main', $theme_dir_url . '/dist/' . $main_js, array(), $ver, true );
            
            if ( $main_css ) {
                wp_enqueue_style( 'woofashion-spa-style', $theme_dir_url . '/dist/' . $main_css, array(), $ver );
            }
        }
    } else {
        // Development mode (Vite Dev Server)
        wp_enqueue_script( 'vite-client', 'http://localhost:5173/@vite/client', array(), null, false );
        wp_enqueue_script( 'woofashion-spa-dev', 'http://localhost:5173/src/main.tsx', array(), null, true );
    }

    $currency_settings = array(
        'code'               => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'BDT',
        'symbol'             => function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol() ) : '৳',
        'position'           => get_option( 'woocommerce_currency_pos', 'left' ),
        'decimals'           => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : (int) get_option( 'woocommerce_price_num_decimals', 0 ),
        'decimal_separator'  => function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : get_option( 'woocommerce_price_decimal_sep', '.' ),
        'thousand_separator' => function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : get_option( 'woocommerce_price_thousand_sep', ',' ),
    );

    $theme_settings = function_exists( 'woofashion_get_theme_settings' ) ? woofashion_get_theme_settings() : array();

    $wp_data = array(
        'apiUrl'        => esc_url_raw( rest_url() ),
        'nonce'         => wp_create_nonce( 'wp_rest' ),
        'homeUrl'       => home_url(),
        'currency'      => $currency_settings,
        'themeSettings' => $theme_settings,
    );

    // Pass data to React
    wp_localize_script( 'woofashion-spa-main', 'wpData', $wp_data );
    
    // Also pass to dev script
    wp_localize_script( 'woofashion-spa-dev', 'wpData', $wp_data );
}
add_action( 'wp_enqueue_scripts', 'woofashion_spa_scripts' );

/**
 * Add type="module" to scripts loaded from Vite.
 */
function woofashion_spa_module_scripts( $tag, $handle, $src ) {
    if ( in_array( $handle, array( 'vite-client', 'woofashion-spa-dev', 'woofashion-spa-main' ) ) ) {
        return '<script type="module" src="' . esc_url( $src ) . '"></script>';
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'woofashion_spa_module_scripts', 10, 3 );

/**
 * Redirect all non-API and non-admin requests to the index.php template
 * This allows React Router to handle the routing on the frontend.
 */
function woofashion_spa_rewrite_rules() {
    add_rewrite_rule( '^/(.*)?', 'index.php', 'top' );
}
add_action( 'init', 'woofashion_spa_rewrite_rules' );

/**
 * Theme Settings Admin Panel & Custom REST API endpoints
 */
require_once get_template_directory() . '/inc/theme-settings.php';
require_once get_template_directory() . '/inc/api.php';

