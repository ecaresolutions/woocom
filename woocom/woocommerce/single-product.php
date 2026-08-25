<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>

    <div class="bg-gray-50 py-8 min-h-screen">
        <div class="container mx-auto px-4">
            <?php
                /**
                 * woocommerce_before_main_content hook.
                 * Note: We remove woocommerce_breadcrumb and woocommerce_output_content_wrapper
                 * to prevent double navigation and extra wrapper divs.
                 */
                remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
                remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
                remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
            ?>

                <?php while ( have_posts() ) : ?>
                    <?php the_post(); ?>

                    <?php wc_get_template_part( 'content', 'single-product' ); ?>

                <?php endwhile; // end of the loop. ?>

        </div>
    </div>

<?php
get_footer();

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
