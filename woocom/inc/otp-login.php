<?php
/**
 * OTP Authentication & Customer Registration Customizations
 *
 * @package Woocom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add custom fields to WooCommerce registration form.
 */
// add_action( 'woocommerce_register_form', 'woocom_add_registration_fields' );
function woocom_add_registration_fields() {
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_first_name"><?php esc_html_e( 'Full Name', 'woocom' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text woocommerce-Input" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) echo esc_attr( wp_unslash( $_POST['billing_first_name'] ) ); ?>" required />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_phone"><?php esc_html_e( 'Phone Number', 'woocom' ); ?> <span class="required">*</span></label>
        <input type="tel" class="input-text woocommerce-Input" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) echo esc_attr( wp_unslash( $_POST['billing_phone'] ) ); ?>" required />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_address_1"><?php esc_html_e( 'Full Address', 'woocom' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text woocommerce-Input" name="billing_address_1" id="reg_billing_address_1" value="<?php if ( ! empty( $_POST['billing_address_1'] ) ) echo esc_attr( wp_unslash( $_POST['billing_address_1'] ) ); ?>" placeholder="<?php esc_attr_e( 'House/Road, Area, City', 'woocom' ); ?>" required />
    </p>
    <?php
}

/**
 * Validate custom fields on WooCommerce registration.
 */
add_filter( 'woocommerce_registration_errors', 'woocom_validate_registration_fields', 10, 3 );
function woocom_validate_registration_fields( $errors, $username, $email ) {
    if ( empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( '<strong>Error:</strong> Full Name is required.', 'woocom' ) );
    }
    if ( empty( $_POST['billing_phone'] ) ) {
        $errors->add( 'billing_phone_error', __( '<strong>Error:</strong> Phone Number is required.', 'woocom' ) );
    } elseif ( strlen( preg_replace( '/\D+/', '', $_POST['billing_phone'] ) ) < 8 ) {
        $errors->add( 'billing_phone_error', __( '<strong>Error:</strong> Please enter a valid phone number.', 'woocom' ) );
    }
    if ( empty( $_POST['billing_address_1'] ) ) {
        $errors->add( 'billing_address_1_error', __( '<strong>Error:</strong> Full Address is required.', 'woocom' ) );
    }
    return $errors;
}

/**
 * Save custom fields to user metadata upon successful registration.
 */
add_action( 'woocommerce_created_customer', 'woocom_save_registration_fields' );
function woocom_save_registration_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) );
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) );
    }
    if ( isset( $_POST['billing_phone'] ) ) {
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) );
    }
    if ( isset( $_POST['billing_address_1'] ) ) {
        update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) );
    }
}

/**
 * ═══════════════════════════════════════════════════════════
 * OTP Login — Send OTP
 * ═══════════════════════════════════════════════════════════
 */
add_action( 'wp_ajax_nopriv_woocom_send_otp', 'woocom_ajax_send_otp' );
add_action( 'wp_ajax_woocom_send_otp',        'woocom_ajax_send_otp' );
function woocom_ajax_send_otp() {
    check_ajax_referer( 'woocom_otp_nonce', 'nonce' );
    $phone = isset( $_POST['phone'] ) ? preg_replace( '/\D/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';

    if ( strlen( $phone ) < 10 || strlen( $phone ) > 15 ) {
        wp_send_json_error( array( 'message' => __( 'Please enter a valid phone number.', 'woocom' ) ) );
    }

    $otp = str_pad( (string) wp_rand( 0, 999999 ), 6, '0', STR_PAD_LEFT );
    set_transient( 'woocom_otp_' . md5( $phone ), $otp, 5 * MINUTE_IN_SECONDS );

    /**
     * Hook: woocom_send_otp_sms
     * Connect your SMS gateway here.
     *
     * add_action( 'woocom_send_otp_sms', function( $phone, $otp ) {
     *     // Call SMS API
     * }, 10, 2 );
     */
    do_action( 'woocom_send_otp_sms', $phone, $otp );

    $response = array( 'message' => __( 'OTP sent successfully!', 'woocom' ) );
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        $response['debug_otp'] = $otp; // Remove before going live!
    }
    wp_send_json_success( $response );
}

/**
 * ═══════════════════════════════════════════════════════════
 * OTP Login — Verify OTP & Log In
 * ═══════════════════════════════════════════════════════════
 */
add_action( 'wp_ajax_nopriv_woocom_verify_otp', 'woocom_ajax_verify_otp' );
add_action( 'wp_ajax_woocom_verify_otp',        'woocom_ajax_verify_otp' );
function woocom_ajax_verify_otp() {
    check_ajax_referer( 'woocom_otp_nonce', 'nonce' );
    $phone = isset( $_POST['phone'] ) ? preg_replace( '/\D/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
    $otp   = isset( $_POST['otp'] )   ? sanitize_text_field( wp_unslash( $_POST['otp'] ) )   : '';

    if ( ! $phone || ! $otp ) {
        wp_send_json_error( array( 'message' => __( 'Invalid request.', 'woocom' ) ) );
    }

    $stored_otp = get_transient( 'woocom_otp_' . md5( $phone ) );
    if ( false === $stored_otp || ! hash_equals( $stored_otp, $otp ) ) {
        wp_send_json_error( array( 'message' => __( 'Invalid or expired OTP. Please try again.', 'woocom' ) ) );
    }

    delete_transient( 'woocom_otp_' . md5( $phone ) );

    // Find user by billing phone
    $users = get_users( array(
        'meta_key'   => 'billing_phone',
        'meta_value' => $phone,
        'number'     => 1,
        'fields'     => 'ids',
    ) );

    if ( ! empty( $users ) ) {
        $user_id = (int) $users[0];
    } else {
        // Auto-create account for new OTP users
        $username = 'user_' . $phone;
        $password = wp_generate_password( 16 );
        $email    = $phone . '@otp.woocom.local';
        $user_id  = wp_create_user( $username, $password, $email );
        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Could not create account. Please register first.', 'woocom' ) ) );
        }
        update_user_meta( $user_id, 'billing_phone', $phone );
        $u = new WP_User( $user_id );
        $u->set_role( 'customer' );
    }

    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );
    $user_data = get_userdata( $user_id );
    do_action( 'wp_login', $user_data->user_login, $user_data );

    wp_send_json_success( array(
        'message'  => __( 'Login successful!', 'woocom' ),
        'redirect' => wc_get_account_endpoint_url( 'dashboard' ),
    ) );
}
