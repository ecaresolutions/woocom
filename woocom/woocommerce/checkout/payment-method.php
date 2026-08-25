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

	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="cursor-pointer block border rounded-lg py-1.5 px-3 h-full transition-all hover:border-secondary/20 <?php echo $gateway->chosen ? 'border-secondary bg-[#eff6ff]' : 'border-gray-150 bg-white'; ?>">
		<div class="flex items-center justify-between gap-2 h-full">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                    <?php 
                    if ( $gateway->id == 'cod' ) {
                        echo '<span class="text-[20px]">💸</span>';
                    } else if ( strpos(strtolower($gateway->id), 'bkash') !== false ) {
                        echo '<div class="w-6 h-6 bg-[#D12053] rounded flex items-center justify-center p-1 flex-shrink-0"><img src="https://www.logo.wine/a/logo/BKash/BKash-Icon-Logo.wine.svg" class="w-full h-full object-contain brightness-0 invert"></div>';
                    } else if ( strpos(strtolower($gateway->id), 'rocket') !== false ) {
                        echo '<div class="w-6 h-6 bg-[#8C3494] rounded flex items-center justify-center p-0.5 flex-shrink-0"><img src="https://www.dutchbanglabank.com/images/rocket_logo.png" class="w-full h-full object-contain brightness-0 invert"></div>';
                    } else if ( strpos(strtolower($gateway->id), 'nagad') !== false ) {
                        echo '<div class="w-6 h-6 bg-[#ED1C24] rounded flex items-center justify-center p-0.5 flex-shrink-0"><img src="https://www.logo.wine/a/logo/Nagad/Nagad-Logo.wine.svg" class="w-full h-full object-contain brightness-0 invert"></div>';
                    } else {
                        echo '<div class="w-8 h-5.5 bg-[#1e3a8a] rounded flex flex-col p-0.5 relative overflow-hidden flex-shrink-0">
                            <div class="w-full h-1 bg-gray-800/50 mb-0.5"></div>
                            <div class="flex gap-0.5"><div class="w-0.5 h-0.5 bg-white/30"></div><div class="w-0.5 h-0.5 bg-white/30"></div></div>
                            <div class="absolute bottom-0.5 right-0.5 flex gap-[-2px]"><div class="w-2 h-2 bg-[#EB001B] rounded-full"></div><div class="w-2 h-2 bg-[#F79E1B] rounded-full opacity-80 ml-[-3px]"></div></div>
                        </div>';
                    }
                    ?>
                </div>
                <span class="text-[12px] font-bold text-[#253D4E] whitespace-nowrap"><?php echo $gateway->get_title(); ?></span>
            </div>
            
            <?php if ( $gateway->chosen ) : ?>
                <div class="w-4.5 h-4.5 rounded-full bg-secondary flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            <?php endif; ?>
		</div>
	</label>
</li>
