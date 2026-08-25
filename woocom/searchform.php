<?php
/**
 * The template for displaying search forms
 *
 * @package Woocom
 */
?>
<form role="search" method="get" class="relative w-full" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="search" class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-secondary rounded-full py-2.5 px-5 pr-12 transition-all text-sm outline-none hover:border-slate-300" placeholder="Search In..." value="<?php echo get_search_query(); ?>" name="s" />
    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-secondary transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-5 w-5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
    </button>
    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
        <input type="hidden" name="post_type" value="product" />
    <?php endif; ?>
</form>
