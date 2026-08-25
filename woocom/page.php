<?php
/**
 * The template for displaying all pages
 *
 * @package Woocom
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php if ( function_exists( 'is_cart' ) && is_cart() ) : ?>
        <div class="woocommerce-page-shell py-8 min-h-screen">
            <div class="container mx-auto px-4">
                <?php echo do_shortcode( '[woocommerce_cart]' ); ?>
            </div>
        </div>
    <?php elseif ( function_exists( 'is_checkout' ) && is_checkout() ) : ?>
        <?php echo do_shortcode( '[woocommerce_checkout]' ); ?>
    <?php elseif ( function_exists( 'is_account_page' ) && is_account_page() ) : ?>
        <?php /* My Account — full-width (login form template controls its own layout) */ ?>
        <div class="woocommerce-account min-h-screen">
            <?php
            while ( have_posts() ) :
                the_post();
                the_content();
            endwhile;
            ?>
        </div>
    <?php else : ?>
        <div class="page-content-shell py-8 min-h-screen">
            <div class="container mx-auto px-4">
                <?php
                while ( have_posts() ) :
                    the_post();
                    the_content();
                endwhile;
                ?>
            </div>
        </div>
    <?php endif; ?>
</main><!-- #main -->

<?php
get_footer();
