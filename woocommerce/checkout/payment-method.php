<?php
/**
 * Output a single payment method
 *
 * @package WooCommerce/Templates
 * @version 10.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
	<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio hidden" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="cursor-pointer block border-2 rounded-xl py-2 px-3 h-full transition-all hover:border-secondary/20 <?php echo $gateway->chosen ? 'border-secondary bg-[#eff6ff]' : 'border-gray-100 bg-white'; ?>">
		<div class="flex items-center justify-between gap-2 h-full">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                    <?php 
                    if ( $gateway->id == 'cod' ) {
                        echo '<span class="text-[26px]">💸</span>';
                    } else if ( strpos(strtolower($gateway->id), 'bkash') !== false ) {
                        echo '<div class="w-8 h-8 bg-[#D12053] rounded-lg flex items-center justify-center p-1.5 flex-shrink-0"><img src="https://www.logo.wine/a/logo/BKash/BKash-Icon-Logo.wine.svg" class="w-full h-full object-contain brightness-0 invert"></div>';
                    } else if ( strpos(strtolower($gateway->id), 'rocket') !== false ) {
                        echo '<div class="w-8 h-8 bg-[#8C3494] rounded-lg flex items-center justify-center p-1 flex-shrink-0"><img src="https://www.dutchbanglabank.com/images/rocket_logo.png" class="w-full h-full object-contain brightness-0 invert"></div>';
                    } else if ( strpos(strtolower($gateway->id), 'nagad') !== false ) {
                        echo '<div class="w-8 h-8 bg-[#ED1C24] rounded-lg flex items-center justify-center p-1 flex-shrink-0"><img src="https://www.logo.wine/a/logo/Nagad/Nagad-Logo.wine.svg" class="w-full h-full object-contain brightness-0 invert"></div>';
                    } else {
                        echo '<div class="w-10 h-7 bg-[#1e3a8a] rounded flex flex-col p-1 relative overflow-hidden flex-shrink-0">
                            <div class="w-full h-1.5 bg-gray-800/50 mb-1"></div>
                            <div class="flex gap-0.5"><div class="w-0.5 h-0.5 bg-white/30"></div><div class="w-0.5 h-0.5 bg-white/30"></div><div class="w-0.5 h-0.5 bg-white/30"></div></div>
                            <div class="absolute bottom-1 right-1 flex gap-[-2px]"><div class="w-2.5 h-2.5 bg-[#EB001B] rounded-full"></div><div class="w-2.5 h-2.5 bg-[#F79E1B] rounded-full opacity-80 ml-[-4px]"></div></div>
                        </div>';
                    }
                    ?>
                </div>
                <span class="text-[14px] font-bold text-[#253D4E] whitespace-nowrap"><?php echo $gateway->get_title(); ?></span>
            </div>
            
            <?php if ( $gateway->chosen ) : ?>
                <div class="w-6 h-6 rounded-full bg-secondary flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            <?php endif; ?>
		</div>
	</label>
</li>
