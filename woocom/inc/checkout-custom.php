<?php
/**
 * Custom Checkout Fields and Options for Woom-1 Theme
 *
 * @package Woocom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customize Checkout Fields
 */
add_filter( 'woocommerce_checkout_fields', 'woocom_customize_checkout_fields' );
function woocom_customize_checkout_fields( $fields ) {
    // 1. Name & Phone (Stack on mobile)
    $fields['billing']['billing_first_name']['priority'] = 10;
    $fields['billing']['billing_first_name']['placeholder'] = 'Your Full Name *';
    $fields['billing']['billing_first_name']['label'] = 'Full Name';
    $fields['billing']['billing_first_name']['class'] = array('form-row-wide', 'mb-4');
    
    $fields['billing']['billing_phone']['priority'] = 20;
    $fields['billing']['billing_phone']['placeholder'] = 'Phone Number *';
    $fields['billing']['billing_phone']['label'] = 'Phone Number';
    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_phone']['class'] = array('form-row-wide', 'mb-4', 'phone-prefix-88');

    // 2. District (Dropdown) & Thana (Select)
    $fields['billing']['billing_state']['priority'] = 30;
    $fields['billing']['billing_state']['label'] = 'District';
    $fields['billing']['billing_state']['placeholder'] = 'Select District';
    $fields['billing']['billing_state']['class'] = array('form-row-first', '!w-[48%]', '!float-left', 'mb-4');

    $fields['billing']['billing_city']['type'] = 'select'; // Changed to select
    $fields['billing']['billing_city']['priority'] = 40;
    $fields['billing']['billing_city']['label'] = 'Thana / Area';
    $fields['billing']['billing_city']['options'] = array('' => 'Select Thana / Area');
    $fields['billing']['billing_city']['class'] = array('form-row-last', '!w-[48%]', '!float-right', 'mb-4', 'thana-dropdown-field');

    // 3. Address (Full Width)
    $fields['billing']['billing_address_1']['priority'] = 50;
    $fields['billing']['billing_address_1']['placeholder'] = 'ex: House no. / building / street / area';
    $fields['billing']['billing_address_1']['label'] = 'Address';
    $fields['billing']['billing_address_1']['class'] = array('form-row-wide', 'w-full', 'clear-both', 'mb-4');

    // Hide Country but force it to Bangladesh
    $fields['billing']['billing_country']['type'] = 'hidden';
    $fields['billing']['billing_country']['default'] = 'BD';
    $fields['billing']['billing_country']['label'] = 'Country';

    // Apply same to shipping (Displayed in Billing Toggle)
    if (isset($fields['shipping'])) {
        $fields['shipping']['shipping_first_name']['priority'] = 10;
        $fields['shipping']['shipping_first_name']['placeholder'] = 'Your Full Name *';
        $fields['shipping']['shipping_first_name']['label'] = 'Shipping Full Name';
        $fields['shipping']['shipping_first_name']['class'] = array('form-row-first', 'mb-4');
        
        $fields['shipping']['shipping_phone']['type'] = 'tel';
        $fields['shipping']['shipping_phone']['priority'] = 20;
        $fields['shipping']['shipping_phone']['placeholder'] = '017*********';
        $fields['shipping']['shipping_phone']['label'] = 'Shipping Phone Number';
        $fields['shipping']['shipping_phone']['class'] = array('form-row-last', 'mb-4', 'phone-prefix-88');
        $fields['shipping']['shipping_phone']['required'] = false;

        $fields['shipping']['shipping_country']['type'] = 'hidden';
        $fields['shipping']['shipping_country']['default'] = 'BD';
        $fields['shipping']['shipping_country']['label'] = 'Shipping Country';

        $fields['shipping']['shipping_state']['priority'] = 30;
        $fields['shipping']['shipping_state']['label'] = 'Shipping District';
        $fields['shipping']['shipping_state']['placeholder'] = 'Select District';
        $fields['shipping']['shipping_state']['class'] = array('form-row-first', 'mb-4');

        $fields['shipping']['shipping_city']['type'] = 'select';
        $fields['shipping']['shipping_city']['options'] = array('' => 'Select Thana (Optional)');
        $fields['shipping']['shipping_city']['label'] = 'Shipping Thana';
        $fields['shipping']['shipping_city']['priority'] = 40;
        $fields['shipping']['shipping_city']['class'] = array('form-row-last', 'mb-4', 'thana-dropdown-field');

        $fields['shipping']['shipping_address_1']['priority'] = 50;
        $fields['shipping']['shipping_address_1']['label'] = 'Shipping Address';
        $fields['shipping']['shipping_address_1']['placeholder'] = 'ex: House no. / building / street / area';
        $fields['shipping']['shipping_address_1']['class'] = array('form-row-wide', 'w-full', 'clear-both', 'mb-4');
    }

    // Remove unwanted fields
    unset($fields['billing']['billing_postcode'], $fields['shipping']['shipping_postcode']);
    unset($fields['billing']['billing_last_name'], $fields['shipping']['shipping_last_name']);
    unset($fields['billing']['billing_company'], $fields['shipping']['shipping_company']);
    unset($fields['billing']['billing_address_2'], $fields['shipping']['shipping_address_2']);
    unset($fields['billing']['billing_email']);

    // Force account password field to NOT be required by default to allow checkout to proceed
    if ( isset( $fields['account']['account_password'] ) ) {
        $fields['account']['account_password']['required'] = false;
    }

    return $fields;
}

/**
 * Add Tailwind classes and Thana Sync Script
 */
add_action('wp_footer', 'woocom_checkout_thana_sync_script');
function woocom_checkout_thana_sync_script() {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) return;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        const thanaData = {
            'BD-01': ['Bandarban Sadar', 'Alikadam', 'Lama', 'Naikhongchhari', 'Rowangchhari', 'Ruma', 'Thanchi'],
            'BD-02': ['Barguna Sadar', 'Amtali', 'Bamna', 'Betagi', 'Patharghata', 'Taltali'],
            'BD-03': ['Bogura Sadar', 'Adamdighi', 'Dhunat', 'Dhupchanchia', 'Gabtali', 'Kahaloo', 'Nandigram', 'Sariakandi', 'Shajahanpur', 'Sherpur', 'Shibganj', 'Sonatala'],
            'BD-04': ['Brahmanbaria Sadar', 'Akhaura', 'Bancharampur', 'Bijoynagar', 'Kasba', 'Nabinagar', 'Nasirnagar', 'Sarail', 'Ashuganj'],
            'BD-05': ['Bagerhat Sadar', 'Chitalmari', 'Fakirhat', 'Kachua', 'Mollahat', 'Mongla', 'Morrelganj', 'Rampal', 'Sarankhola'],
            'BD-06': ['Barishal Sadar', 'Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gaurnadi', 'Hizla', 'Mehendiganj', 'Muladi', 'Wazirpur'],
            'BD-07': ['Bhola Sadar', 'Burhanuddin', 'Char Fasson', 'Daulatkhan', 'Lalmohan', 'Manpura', 'Tazumuddin'],
            'BD-08': ['Cumilla Sadar', 'Barura', 'Brahmanpara', 'Burichong', 'Chandina', 'Chauddagram', 'Daudkandi', 'Debidwar', 'Homna', 'Laksam', 'Muradnagar', 'Nangalkot', 'Titas', 'Monohargonj', 'Meghna'],
            'BD-09': ['Chandpur Sadar', 'Faridganj', 'Haimchar', 'Haziganj', 'Kachua', 'Matlab North', 'Matlab South', 'Shahrasti'],
            'BD-10': ['Chattogram City', 'Anwara', 'Banshkhali', 'Boalkhali', 'Chandanaish', 'Fatikchhari', 'Hathazari', 'Lohagara', 'Mirsharai', 'Patiya', 'Rangunia', 'Raozan', 'Sandwip', 'Satkania', 'Sitakunda'],
            'BD-11': ['Cox\'s Bazar Sadar', 'Chakaria', 'Maheshkhali', 'Ramu', 'Teknaf', 'Ukhia', 'Pekua', 'Kutubdia'],
            'BD-12': ['Chuadanga Sadar', 'Alamdanga', 'Damurhuda', 'Jiban Nagar'],
            'BD-13': ['Dhaka City', 'Savar', 'Dhamrai', 'Keraniganj', 'Nawabganj', 'Dohar'],
            'BD-14': ['Dinajpur Sadar', 'Birampur', 'Birganj', 'Birol', 'Bochaganj', 'Chirirbandar', 'Phulbari', 'Ghoraghat', 'Hakimpur', 'Kaharole', 'Khansama', 'Nawabganj', 'Parbatipur'],
            'BD-15': ['Faridpur Sadar', 'Alfadanga', 'Bhanga', 'Boalmari', 'Charbhadrasan', 'Madhukhali', 'Nagarkanda', 'Sadarpur', 'Saltha'],
            'BD-16': ['Feni Sadar', 'Chhagalnaiya', 'Daganbhuiyan', 'Parshuram', 'Sonagazi', 'Fulgazi'],
            'BD-17': ['Gopalganj Sadar', 'Kashiani', 'Kotalipara', 'Muksudpur', 'Tungipara'],
            'BD-18': ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur'],
            'BD-19': ['Gaibandha Sadar', 'Fulchhari', 'Gobindaganj', 'Palashbari', 'Sadullapur', 'Sughatta', 'Sundarganj'],
            'BD-20': ['Habiganj Sadar', 'Ajmiriganj', 'Bahubal', 'Baniyachong', 'Chunarughat', 'Lakhai', 'Madhabpur', 'Nabiganj', 'Sayestaganj'],
            'BD-21': ['Jamalpur Sadar', 'Bakshiganj', 'Dewanganj', 'Islampur', 'Madarganj', 'Melenandaha', 'Sarishabari'],
            'BD-22': ['Jashore Sadar', 'Abhaynagar', 'Bagherpara', 'Chaugachha', 'Jhikargachha', 'Keshabpur', 'Manirampur', 'Sharsha'],
            'BD-23': ['Jhenaidah Sadar', 'Harinakunda', 'Kaliganj', 'Kotchandpur', 'Maheshpur', 'Shailkupa'],
            'BD-24': ['Joypurhat Sadar', 'Akkelpur', 'Kalai', 'Khetlal', 'Panchbibi'],
            'BD-25': ['Jhalokathi Sadar', 'Kathalia', 'Nalchity', 'Rajapur'],
            'BD-26': ['Kishoreganj Sadar', 'Itna', 'Katiadi', 'Bhairab', 'Tarail', 'Hossainpur', 'Pakundia', 'Kuliarchar', 'Karimganj', 'Bajitpur', 'Austagram', 'Mithamain', 'Nikli'],
            'BD-27': ['Khulna Sadar', 'Batiaghata', 'Dacope', 'Dumuria', 'Dighalia', 'Koyra', 'Paikgachha', 'Phultala', 'Rupsha'],
            'BD-28': ['Kurigram Sadar', 'Bhurungamari', 'Char Rajibpur', 'Chilmari', 'Phulbari', 'Nageshwari', 'Rajarhat', 'Roumari', 'Ulipur'],
            'BD-29': ['Khagrachhari Sadar', 'Dighinala', 'Lakshmichhari', 'Mahalchhari', 'Manikchhari', 'Matiranga', 'Panchhari', 'Ramgarh'],
            'BD-30': ['Kushtia Sadar', 'Bheramara', 'Daulatpur', 'Khoksa', 'Kumarkhali', 'Mirpur'],
            'BD-31': ['Lakshmipur Sadar', 'Raipur', 'Ramganj', 'Ramgati', 'Kamalnagar'],
            'BD-32': ['Lalmonirhat Sadar', 'Aditmari', 'Hatibandha', 'Kaliganj', 'Patgram'],
            'BD-33': ['Manikganj Sadar', 'Singair', 'Shibalaya', 'Saturia', 'Harirampur', 'Gheor', 'Daulatpur'],
            'BD-34': ['Mymensingh Sadar', 'Bhaluka', 'Trishal', 'Haluaghat', 'Muktagachha', 'Dhobaura', 'Fulbaria', 'Gaffargaon', 'Gauripur', 'Ishwarganj', 'Nandail', 'Phulpur', 'Tara Khanda'],
            'BD-35': ['Munshiganj Sadar', 'Sreenagar', 'Sirajdikhan', 'Lauhajang', 'Gajaria', 'Tongibari'],
            'BD-36': ['Madaripur Sadar', 'Kalkini', 'Rajoir', 'Shibchar'],
            'BD-37': ['Magura Sadar', 'Mohammadpur', 'Shalkha', 'Sreepur'],
            'BD-38': ['Moulvibazar Sadar', 'Barlekha', 'Juri', 'Kamalganj', 'Kulaura', 'Rajnagar', 'Sreemangal'],
            'BD-39': ['Meherpur Sadar', 'Gangni', 'Mujibnagar'],
            'BD-40': ['Narayanganj Sadar', 'Araihazar', 'Bandar', 'Rupganj', 'Sonargaon'],
            'BD-41': ['Netrokona Sadar', 'Atpara', 'Barhatta', 'Durgapur', 'Khaliajuri', 'Kalmakanda', 'Kendua', 'Madan', 'Mohanganj', 'Purbadhala'],
            'BD-42': ['Narsingdi Sadar', 'Belabo', 'Monohardi', 'Palash', 'Raipura', 'Shibpur'],
            'BD-43': ['Narail Sadar', 'Kalia', 'Lohagara'],
            'BD-44': ['Natore Sadar', 'Bagatipara', 'Baraigram', 'Gurudaspur', 'Lalpur', 'Singra', 'Naldanga'],
            'BD-45': ['Chapai Nawabganj Sadar', 'Bholahat', 'Gomastapur', 'Nachole', 'Shibganj'],
            'BD-46': ['Nilphamari Sadar', 'Dimla', 'Domar', 'Jaldhaka', 'Kishoreganj', 'Saidpur'],
            'BD-47': ['Noakhali Sadar', 'Begumganj', 'Chatkhil', 'Companiganj', 'Hatiya', 'Senbagh', 'Sonaimuri', 'Subarnachar', 'Kabirhat'],
            'BD-48': ['Naogaon Sadar', 'Atrai', 'Badalgachhi', 'Dhamoirhat', 'Manda', 'Mahadevpur', 'Niamatpur', 'Patnitala', 'Porsha', 'Raninagar', 'Sapahar'],
            'BD-49': ['Pabna Sadar', 'Atgharia', 'Bera', 'Bhangura', 'Chatmohar', 'Faridpur', 'Ishwardi', 'Santhia', 'Sujanagar'],
            'BD-50': ['Pirojpur Sadar', 'Bhandaria', 'Kawkhali', 'Mathbaria', 'Nazirpur', 'Nesarabad', 'Indurkani'],
            'BD-51': ['Patuakhali Sadar', 'Bauphal', 'Dashmina', 'Galachipa', 'Kalapara', 'Mirzaganj', 'Dumki', 'Rangabali'],
            'BD-52': ['Panchagarh Sadar', 'Atwari', 'Boda', 'Debiganj', 'Tetulia'],
            'BD-53': ['Rajbari Sadar', 'Baliakandi', 'Goalandaghat', 'Pangsha', 'Kalukhali'],
            'BD-54': ['Rajshahi Sadar', 'Bagha', 'Bagmara', 'Charghat', 'Durgapur', 'Godagari', 'Mohanpur', 'Paba', 'Puthia', 'Tanore'],
            'BD-55': ['Rangpur Sadar', 'Badarganj', 'Gangachhara', 'Kaunia', 'Mithapukur', 'Pirgachha', 'Pirganj', 'Taraganj'],
            'BD-56': ['Rangamati Sadar', 'Baghaichhari', 'Barkal', 'Kawkhali', 'Belaichhari', 'Kaptai', 'Jurachhari', 'Langadu', 'Nanearchar', 'Rajasthali'],
            'BD-57': ['Sherpur Sadar', 'Jhenaigati', 'Nakla', 'Nalitabari', 'Sreebardi'],
            'BD-58': ['Satkhira Sadar', 'Assasuni', 'Debhata', 'Kalaroa', 'Kaliganj', 'Shyamnagar', 'Tala'],
            'BD-59': ['Sirajganj Sadar', 'Belkuchi', 'Chauhali', 'Kamarkhanda', 'Kazipur', 'Raiganj', 'Shahjadpur', 'Tarash', 'Ullahpara'],
            'BD-60': ['Sylhet Sadar', 'Dakshin Surma', 'Bishwanath', 'Balaganj', 'Fenchuganj', 'Golapganj', 'Beanibazar', 'Zakiganj', 'Kanaighat', 'Jaintiapur', 'Gowainghat', 'Companiganj', 'Osmani Nagar'],
            'BD-61': ['Sunamganj Sadar', 'Bishwamvapur', 'Chhatak', 'Derai', 'Dharamapasha', 'Dowarabazar', 'Jagannathpur', 'Jamalganj', 'Sullah', 'Tahirpur', 'South Sunamganj'],
            'BD-62': ['Shariatpur Sadar', 'Damudya', 'Gosairhat', 'Naria', 'Zajira', 'Bhedarganj'],
            'BD-63': ['Tangail Sadar', 'Basail', 'Bhuapur', 'Delduar', 'Ghatail', 'Gopalpur', 'Kalihati', 'Madhupur', 'Mirzapur', 'Nagarpur', 'Sakhipur', 'Dhanbari'],
            'BD-64': ['Thakurgaon Sadar', 'Baliadangi', 'Haripur', 'Pirganj', 'Ranisankail'],
        };

        function updateThanas(countryField, stateField, cityField) {
            const district = $(stateField).val();
            const $city = $(cityField);
            
            // Save current value if any
            const currentVal = $city.val();
            
            // Clear existing
            $city.empty().append('<option value="">Select Thana / Area</option>');
            
            if (district && thanaData[district]) {
                thanaData[district].forEach(function(thana) {
                    $city.append('<option value="' + thana + '">' + thana + '</option>');
                });
            } else if (district) {
                $city.append('<option value="Other">Other / Not Listed</option>');
            }
            
            // Restore value if it exists in new options
            if (currentVal && $city.find('option[value="' + currentVal + '"]').length > 0) {
                $city.val(currentVal);
            }

            // Trigger change for Select2 and others
            $city.trigger('change');
            if ($city.data('select2')) {
                $city.trigger('change.select2');
            }

            // Trigger WooCommerce checkout update
            $(document.body).trigger('update_checkout');
        }

        // Billing
        $(document.body).on('change', '#billing_state', function() {
            updateThanas('#billing_country', '#billing_state', '#billing_city');
        });

        // Shipping
        $(document.body).on('change', '#shipping_state', function() {
            updateThanas('#shipping_country', '#shipping_state', '#shipping_city');
        });

        // Trigger update when City changes
        $(document.body).on('change', '#billing_city, #shipping_city', function() {
            $(document.body).trigger('update_checkout');
        });

        // Force Select2 for District and Thana
        function initCheckoutSelect2() {
            $('#billing_state, #shipping_state, .thana-dropdown-field select').select2({
                minimumResultsForSearch: 10,
                width: '100%'
            });
        }

        $(document.body).on('updated_checkout', function() {
            initCheckoutSelect2();
        });

        // Trigger on load
        setTimeout(function() {
            initCheckoutSelect2();
            if ($('#billing_state').val()) $('#billing_state').trigger('change');
            if ($('#shipping_state').val()) $('#shipping_state').trigger('change');
        }, 1000);
    });
    </script>
    <?php
}

/**
 * Add Tailwind classes to checkout inputs
 */
add_filter('woocommerce_form_field_args', 'woocom_form_field_args', 10, 3);
function woocom_form_field_args($args, $key, $value) {
    $args['input_class'][] = 'w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:border-secondary focus:ring-0 focus:outline-none';
    return $args;
}

/**
 * Helper to calculate discount percentage
 */
function woocom_get_discount_percentage( $product ) {
    if ( ! $product->is_on_sale() ) return 0;
    
    $regular_price = (float) $product->get_regular_price();
    $sale_price    = (float) $product->get_price();
    
    if ( $regular_price > 0 && $sale_price > 0 ) {
        $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
        return $percentage;
    }
    return 0;
}

/**
 * Disable "Added to Cart" messages globally
 */
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

/**
 * Force Default Checkout Country to Bangladesh
 */
add_filter( 'default_checkout_billing_country', 'woocom_default_checkout_country' );
function woocom_default_checkout_country() {
    return 'BD';
}

/**
 * Update custom shipping fragments via AJAX
 */
add_filter( 'woocommerce_update_order_review_fragments', 'woocom_checkout_shipping_fragments' );
function woocom_checkout_shipping_fragments( $fragments ) {
    ob_start();
    ?>
    <div class="shipping-card-wrapper bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 custom-shipping-ui" style="display: block !important;">
        <?php 
        if ( function_exists('WC') && WC()->cart && WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
            wc_cart_totals_shipping_html();
        } else {
            echo '<p class="text-xs text-gray-400">Please enter your address to view shipping methods.</p>';
        }
        ?>
    </div>
    <?php
    $fragments['.shipping-card-wrapper'] = ob_get_clean();

    ob_start();
    ?>
    <div id="checkout-totals-fragment">
        <div class="flex justify-between text-sm text-gray-500 pt-2 border-t border-gray-50">
            <span>Sub total</span>
            <span class="font-bold text-gray-800"><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>
        <div class="flex justify-between text-sm text-gray-500">
            <span>Delivery cost</span>
            <span class="font-bold text-gray-800"><?php echo (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_shipping_total() : ''; ?></span>
        </div>
        <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
            <span class="text-base font-bold text-gray-800">Total</span>
            <span class="text-lg font-bold text-gray-800"><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
    </div>
    <?php
    $fragments['#checkout-totals-fragment'] = ob_get_clean();

    return $fragments;
}

/**
 * Hide 'Shipment' text via translation filter
 */
add_filter( 'gettext', 'woocom_hide_shipment_text', 20, 3 );
function woocom_hide_shipment_text( $translated_text, $text, $domain ) {
    if ( function_exists( 'is_checkout' ) && is_checkout() && $text === 'Shipment' ) {
        return '';
    }
    return $translated_text;
}

/**
 * Disable WooCommerce shipping rate transient caching to ensure rates update instantly on address change
 */
add_filter( 'woocommerce_shipping_use_shipping_transient', '__return_false' );

/**
 * Dynamically filter shipping rates based on customer District and Thana
 */
add_filter( 'woocommerce_package_rates', 'woocom_filter_shipping_rates_by_address', 100, 2 );
function woocom_filter_shipping_rates_by_address( $rates, $package ) {
    // Get state/district
    $district = isset( $package['destination']['state'] ) ? trim( $package['destination']['state'] ) : '';
    if ( empty( $district ) && class_exists( 'WooCommerce' ) && WC()->customer ) {
        $district = WC()->customer->get_billing_state();
    }

    // Get city/thana
    $thana = isset( $package['destination']['city'] ) ? trim( $package['destination']['city'] ) : '';
    if ( empty( $thana ) && class_exists( 'WooCommerce' ) && WC()->customer ) {
        $thana = WC()->customer->get_billing_city();
    }

    // Check if customer is inside Dhaka City
    // District must be BD-13 (Dhaka) and Thana must be 'Dhaka City'
    $is_inside_dhaka = ( $district === 'BD-13' && strtolower( $thana ) === 'dhaka city' );

    $filtered_rates = array();

    foreach ( $rates as $rate_id => $rate ) {
        $label = strtolower( $rate->get_label() );
        
        if ( $is_inside_dhaka ) {
            // Keep only inside Dhaka rates
            if ( strpos( $label, 'inside' ) !== false ) {
                $filtered_rates[ $rate_id ] = $rate;
            }
        } else {
            // Keep only outside Dhaka rates
            if ( strpos( $label, 'outside' ) !== false ) {
                $filtered_rates[ $rate_id ] = $rate;
            }
        }
    }

    // Fallback: If no rates matched, return original rates
    if ( empty( $filtered_rates ) ) {
        return $rates;
    }

    return $filtered_rates;
}

