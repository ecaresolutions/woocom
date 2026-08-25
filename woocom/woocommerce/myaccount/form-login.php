<?php
/**
 * Login Form — Woocom Theme Override
 * Tabbed Login & Register Switcher inside a single card
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_customer_login_form' );

$reg_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$active_tab = isset( $_POST['register'] ) ? 'register' : 'login';
?>

<div class="woocom-login-page">
    <div class="container mx-auto px-4 py-10">

        <?php /* ── Page title ── */ ?>
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-primary rounded-2xl mb-4" style="margin: 0 auto; display: flex;">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
                     stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
            </div>
            <h1 class="text-[26px] font-bold text-[#253D4E] leading-tight">
                <?php esc_html_e( 'My Account', 'woocom' ); ?>
            </h1>
            <p class="text-[14px] text-gray-400 mt-1">
                <?php esc_html_e( 'Sign in or create a new account', 'woocom' ); ?>
            </p>
        </div>

        <?php /* ── Unified Tabbed Login Card ── */ ?>
        <div class="woocom-login-card-wrapper max-w-md mx-auto">
            <div class="woocom-panel bg-white rounded-2xl p-6 md:p-8">

                <?php if ( $reg_enabled ) : ?>
                    <?php /* ── Tabs Trigger ── */ ?>
                    <div class="woocom-login-tabs flex border-b border-gray-100 mb-6">
                        <button type="button" class="woocom-tab-btn flex-1 text-center pb-3 font-bold text-[15px] border-b-2 <?php echo $active_tab === 'login' ? 'border-primary text-[#253D4E]' : 'border-transparent text-gray-400'; ?>" data-tab="login">
                            <?php esc_html_e( 'Login', 'woocommerce' ); ?>
                        </button>
                        <button type="button" class="woocom-tab-btn flex-1 text-center pb-3 font-bold text-[15px] border-b-2 <?php echo $active_tab === 'register' ? 'border-primary text-[#253D4E]' : 'border-transparent text-gray-400'; ?>" data-tab="register">
                            <?php esc_html_e( 'Register', 'woocommerce' ); ?>
                        </button>
                    </div>
                <?php else : ?>
                    <h3 class="text-[16px] font-bold text-[#253D4E] mb-6 text-center">
                        <?php esc_html_e( 'Login With Credentials', 'woocom' ); ?>
                    </h3>
                <?php endif; ?>

                <?php wc_print_notices(); ?>

                <?php /* ════ TAB 1: LOGIN CONTENT ════ */ ?>
                <div class="woocom-tab-content <?php echo $active_tab === 'login' ? '' : 'hidden'; ?>" id="tab-login-content" <?php echo $active_tab === 'login' ? '' : 'style="display:none;"'; ?>>
                    <form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
                        <?php do_action( 'woocommerce_login_form_start' ); ?>

                        <?php /* Email / Username */ ?>
                        <div class="woocom-field mb-4">
                            <span class="woocom-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                class="woocommerce-Input woocommerce-Input--text input-text woocom-field-input"
                                name="username" id="username"
                                autocomplete="username"
                                placeholder="<?php esc_attr_e( 'Email or phone number', 'woocom' ); ?>"
                                value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
                                required aria-required="true"
                            />
                        </div>

                        <?php /* Password */ ?>
                        <div class="woocom-field mb-4">
                            <span class="woocom-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                class="woocommerce-Input woocommerce-Input--text input-text woocom-field-input"
                                type="password" name="password" id="password"
                                autocomplete="current-password"
                                placeholder="<?php esc_attr_e( 'Password', 'woocom' ); ?>"
                                required aria-required="true"
                            />
                            <button type="button" class="woocom-eye-btn" id="woocom-pw-toggle" tabindex="-1" aria-label="Show/hide password">
                                <svg id="eye-show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="eye-hide" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     style="display:none">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" x2="22" y1="2" y2="22"/>
                                </svg>
                            </button>
                        </div>

                        <?php do_action( 'woocommerce_login_form' ); ?>

                        <?php /* Remember + Forgot */ ?>
                        <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
                            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme flex items-center gap-2 text-[13px] text-gray-500 cursor-pointer select-none">
                                <input class="woocommerce-form__input woocommerce-form__input-checkbox"
                                       name="rememberme" type="checkbox" id="rememberme" value="forever" />
                                <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
                            </label>
                            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
                               class="text-[13px] text-primary font-semibold hover:underline">
                                <?php esc_html_e( 'Forgotten password?', 'woocommerce' ); ?>
                            </a>
                        </div>

                        <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

                        <button type="submit"
                            class="woocommerce-button button woocommerce-form-login__submit w-full bg-primary text-white font-bold py-3 rounded-xl text-[14px] hover:opacity-90 transition-all uppercase tracking-wide"
                            name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
                            <?php esc_html_e( 'Login', 'woocommerce' ); ?>
                        </button>

                        <?php do_action( 'woocommerce_login_form_end' ); ?>
                    </form>
                </div>

                <?php /* ════ TAB 2: REGISTER CONTENT ════ */ ?>
                <?php if ( $reg_enabled ) : ?>
                    <div class="woocom-tab-content <?php echo $active_tab === 'register' ? '' : 'hidden'; ?>" id="tab-register-content" <?php echo $active_tab === 'register' ? '' : 'style="display:none;"'; ?>>
                        <form method="post" class="woocommerce-form woocommerce-form-register register"
                              <?php do_action( 'woocommerce_register_form_tag' ); ?>>
                            <?php do_action( 'woocommerce_register_form_start' ); ?>

                            <?php /* Full Name */ ?>
                            <div class="woocom-field mb-4">
                                <span class="woocom-field-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                <input type="text" class="woocommerce-Input input-text woocom-field-input"
                                       name="billing_first_name" id="reg_billing_first_name"
                                       placeholder="<?php esc_attr_e( 'Full Name *', 'woocom' ); ?>"
                                       value="<?php echo ! empty( $_POST['billing_first_name'] ) ? esc_attr( wp_unslash( $_POST['billing_first_name'] ) ) : ''; ?>"
                                       required />
                            </div>

                            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                                <div class="woocom-field mb-4">
                                    <span class="woocom-field-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <circle cx="12" cy="10" r="3"/>
                                            <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>
                                        </svg>
                                    </span>
                                    <input type="text" class="woocommerce-Input input-text woocom-field-input"
                                           name="username" id="reg_username" autocomplete="username"
                                           placeholder="<?php esc_attr_e( 'Username *', 'woocom' ); ?>"
                                           value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
                                           required aria-required="true" />
                                </div>
                            <?php endif; ?>

                            <?php /* Email */ ?>
                            <div class="woocom-field mb-4">
                                <span class="woocom-field-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                </span>
                                <input type="email" class="woocommerce-Input input-text woocom-field-input"
                                       name="email" id="reg_email" autocomplete="email"
                                       placeholder="<?php esc_attr_e( 'Email Address *', 'woocom' ); ?>"
                                       value="<?php echo ! empty( $_POST['email'] ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>"
                                       required aria-required="true" />
                            </div>

                            <?php /* Phone Number */ ?>
                            <div class="woocom-field mb-4">
                                <span class="woocom-field-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.11 12 19.79 19.79 0 0 1 1.1 3.4 2 2 0 0 1 3.07 1h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                </span>
                                <input type="tel" class="woocommerce-Input input-text woocom-field-input"
                                       name="billing_phone" id="reg_billing_phone"
                                       placeholder="<?php esc_attr_e( 'Phone Number *', 'woocom' ); ?>"
                                       value="<?php echo ! empty( $_POST['billing_phone'] ) ? esc_attr( wp_unslash( $_POST['billing_phone'] ) ) : ''; ?>"
                                       required />
                            </div>

                            <?php /* Full Address */ ?>
                            <div class="woocom-field mb-4">
                                <span class="woocom-field-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </span>
                                <input type="text" class="woocommerce-Input input-text woocom-field-input"
                                       name="billing_address_1" id="reg_billing_address_1"
                                       placeholder="<?php esc_attr_e( 'Full Address *', 'woocom' ); ?>"
                                       value="<?php echo ! empty( $_POST['billing_address_1'] ) ? esc_attr( wp_unslash( $_POST['billing_address_1'] ) ) : ''; ?>"
                                       required />
                            </div>

                            <?php /* Password */ ?>
                            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                                <div class="woocom-field mb-4">
                                    <span class="woocom-field-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    </span>
                                    <input type="password" class="woocommerce-Input input-text woocom-field-input"
                                           name="password" id="reg_password" autocomplete="new-password"
                                           placeholder="<?php esc_attr_e( 'Password *', 'woocom' ); ?>"
                                           required aria-required="true" />
                                    <button type="button" class="woocom-eye-btn" id="woocom-reg-pw-toggle" tabindex="-1" aria-label="Show/hide password">
                                        <svg id="reg-eye-show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg id="reg-eye-hide" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                             style="display:none">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    </button>
                                </div>
                            <?php else : ?>
                                <p class="text-[12px] text-gray-400 mb-4 text-center">
                                    <?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?>
                                </p>
                            <?php endif; ?>

                            <?php do_action( 'woocommerce_register_form' ); ?>
                            <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

                            <button type="submit"
                                class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit w-full bg-primary text-white font-bold py-3 rounded-xl text-[14px] hover:opacity-90 transition-all uppercase tracking-wide"
                                name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
                                <?php esc_html_e( 'Create Account', 'woocommerce' ); ?>
                            </button>

                            <?php do_action( 'woocommerce_register_form_end' ); ?>
                        </form>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

<style>
/* ── Login page ── */
.woocom-login-page {
    background: <?php echo esc_attr(get_option('woocom_main_background_color', '#FBF9F5')); ?>;
    min-height: 75vh;
}

/* Panel */
.woocom-panel {
    background: #ffffff !important;
    border: 1px solid #eef2f6 !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
}

/* Input with icon */
.woocom-field {
    position: relative;
    display: block;
}
.woocom-field-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    display: flex;
    align-items: center;
    pointer-events: none;
    z-index: 1;
}
.woocom-field-input {
    width: 100% !important;
    padding: 11px 14px 11px 40px !important;
    border: 1.5px solid #e5e7eb !important;
    border-radius: 10px !important;
    background: #ffffff !important;
    font-size: 14px !important;
    color: #374151 !important;
    outline: none !important;
    transition: border-color 0.2s !important;
    box-sizing: border-box !important;
}
.woocom-field-input:focus {
    border-color: var(--color-primary, #2563EB) !important;
}
.woocom-field-input::placeholder {
    color: #b0b7c3;
    font-size: 13px;
}

/* Password eye toggle */
.woocom-eye-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    color: #9ca3af;
    display: flex;
    align-items: center;
    transition: color 0.2s;
    line-height: 1;
}
.woocom-eye-btn:hover { color: var(--color-primary, #2563EB); }

/* Remember checkbox */
.woocommerce-form-login__rememberme input[type="checkbox"] {
    accent-color: var(--color-primary, #2563EB);
    width: 14px !important;
    height: 14px !important;
    padding: 0 !important;
}

/* Submit Buttons override */
.woocommerce-form-login__submit,
.woocommerce-form-register__submit {
    background-color: var(--color-primary, #2563EB) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    padding: 12px 20px !important;
    border-radius: 10px !important;
    border: none !important;
    text-transform: uppercase !important;
    cursor: pointer !important;
    transition: opacity 0.2s !important;
}
.woocommerce-form-login__submit:hover,
.woocommerce-form-register__submit:hover {
    opacity: 0.9 !important;
}

/* Privacy Policy Notice */
.woocommerce-privacy-policy-text {
    font-size: 11px !important;
    line-height: 1.45 !important;
    color: #8892a0 !important;
    margin-top: 15px !important;
    margin-bottom: 15px !important;
    text-align: center !important;
    display: block !important;
}
</style>

<script>
(function ($) {
    var pw      = document.getElementById('password');
    var toggle  = document.getElementById('woocom-pw-toggle');
    var eyeShow = document.getElementById('eye-show');
    var eyeHide = document.getElementById('eye-hide');
    if (toggle && pw) {
        toggle.addEventListener('click', function () {
            if (pw.type === 'password') {
                pw.type = 'text';
                eyeShow.style.display = 'none';
                eyeHide.style.display = '';
            } else {
                pw.type = 'password';
                eyeShow.style.display = '';
                eyeHide.style.display = 'none';
            }
        });
    }

    var regPw      = document.getElementById('reg_password');
    var regToggle  = document.getElementById('woocom-reg-pw-toggle');
    var regEyeShow = document.getElementById('reg-eye-show');
    var regEyeHide = document.getElementById('reg-eye-hide');
    if (regToggle && regPw) {
        regToggle.addEventListener('click', function () {
            if (regPw.type === 'password') {
                regPw.type = 'text';
                regEyeShow.style.display = 'none';
                regEyeHide.style.display = '';
            } else {
                regPw.type = 'password';
                regEyeShow.style.display = '';
                regEyeHide.style.display = 'none';
            }
        });
    }

    // Tabs logic
    jQuery(document).ready(function($) {
        $('.woocom-tab-btn').on('click', function(e) {
            e.preventDefault();
            var tab = $(this).data('tab');
            
            // Toggle active state on buttons
            $('.woocom-tab-btn').removeClass('border-primary text-[#253D4E]').addClass('border-transparent text-gray-400');
            $(this).removeClass('border-transparent text-gray-400').addClass('border-primary text-[#253D4E]');
            
            // Toggle content divs
            $('.woocom-tab-content').hide().addClass('hidden');
            $('#tab-' + tab + '-content').show().removeClass('hidden');
        });
    });
})(jQuery);
</script>
