<?php
/**
 * Plugin Name: reCAPTCHA
 * Plugin URI: http://ashmatadeen.com
 * Description: Adds Google's reCAPTCHA to WP's login form
 *
 * @package login-form-recaptcha
 * Author: Ash Matadeen
 * Author URI: http://ashmatadeen.com
 * Version: 1.6
 */

add_action( 'admin_menu', 'wr_no_captcha_menu' );
add_action( 'admin_init', 'wr_no_captcha_display_options' );
add_action( 'login_enqueue_scripts', 'wr_no_captcha_login_form_script' );
add_action( 'login_enqueue_scripts', 'wr_no_captcha_css' );
add_action( 'login_form', 'wr_no_captcha_render_login_captcha' );
add_filter( 'wp_authenticate_user', 'wr_no_captcha_verify_login_captcha', 10, 2 );

// Specific support for WooCommerce login form!
// Using WooCommerce specific hooks because WooCommerce's login form does not use the expected wp_login_form().
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'woocommerce_login_form', 'wr_no_captcha_render_login_captcha' );
	add_action( 'wp_enqueue_scripts', 'wr_no_captcha_login_form_script' );
	add_action( 'wp_enqueue_scripts', 'wr_no_captcha_css' );
}

function wr_no_captcha_menu() {
	add_options_page( 'Google reCAPTCHA options', 'reCAPTCHA options', 'manage_options', 'recaptcha-options', 'wr_no_captcha_options_page' );
}

function wr_no_captcha_options_page() {
	?>
		<h2>Google noCAPTCHA reCAPTCHA for WordPress</h2>

		<div class="wrap">

			<div id="icon-options-general" class="icon32"></div>

			<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<div class="postbox">
								<div class="inside">
									<form method="post" action="options.php">
										<?php
											settings_fields( 'keys_section' );
											do_settings_sections( 'recaptcha-options' );
											submit_button();
										?>
									</form>

									<form method="post" action="options.php">
										<?php
											settings_fields( 'messages_section' );
											do_settings_sections( 'recaptcha-text-options' );
											submit_button();
										?>
									</form>

									<form method="post" action="options.php">
										<?php
											settings_fields( 'exlude_ips_section' );
											do_settings_sections( 'recaptcha-exlude_ips-options' );
											submit_button();
										?>
									</form>
								</div>

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables .ui-sortable -->

					</div>
					<!-- post-body-content -->

					<!-- sidebar -->
					<div id="postbox-container-1" class="postbox-container">

						<div class="meta-box-sortables">

							<div class="postbox">

								<h3>Support this plugin</h3>

								<div class="inside">
									<p>Click the donate button below to donate an amount of your choice to support the development of this plugin. All donations go straight to the plugin developer.</p>
									<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
										<input type="hidden" name="cmd" value="_donations">
										<input type="hidden" name="business" value="ash@webblerock.com">
										<input type="hidden" name="lc" value="GB">
										<input type="hidden" name="item_name" value="Ash Matadeen">
										<input type="hidden" name="item_number" value="no-captcha">
										<input type="hidden" name="no_note" value="0">
										<input type="hidden" name="currency_code" value="GBP">
										<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
										<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
										<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
									</form>
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables -->

					</div>
					<!-- #postbox-container-1 .postbox-container -->

				</div>
				<!-- #post-body .metabox-holder .columns-2 -->

				<br class="clear">
			</div>
			<!-- #poststuff -->

		</div> <!-- .wrap -->
	<?php
}

function wr_no_captcha_display_options() {
	add_settings_section( 'keys_section', 'API Credentials', 'wr_no_captcha_display_recaptcha_api_content', 'recaptcha-options' );
	add_settings_field( 'wr_no_captcha_site_key', 'Site key', 'wr_no_captcha_key_input', 'recaptcha-options', 'keys_section' );
	add_settings_field( 'wr_no_captcha_secret_key', 'Secret Key', 'wr_no_captcha_secret_key_input', 'recaptcha-options', 'keys_section' );
	register_setting( 'keys_section', 'wr_no_captcha_site_key' );
	register_setting( 'keys_section', 'wr_no_captcha_secret_key' );

	add_settings_section( 'messages_section', 'Custom error message', 'wr_no_captcha_display_recaptcha_error_message_content', 'recaptcha-text-options' );
	add_settings_field( 'wr_no_captcha_error_message_text', 'Custom error message text', 'wr_no_captcha_error_message_input', 'recaptcha-text-options', 'messages_section' );
	register_setting( 'messages_section', 'wr_no_captcha_error_message_text' );

	add_settings_section( 'exlude_ips_section', 'Exclude IP addresses', 'wr_no_captcha_display_recaptcha_exlude_ips_content', 'recaptcha-exlude_ips-options' );
	add_settings_field( 'wr_no_captcha_exlude_ips', 'Exclude IP addresses', 'wr_no_captcha_exlude_ips_input', 'recaptcha-exlude_ips-options', 'exlude_ips_section' );
	add_settings_field( 'wr_no_captcha_exlude_ips_forwarded_for', 'Website is behind a proxy', 'wr_no_captcha_exlude_ips_forwarded_for_input', 'recaptcha-exlude_ips-options', 'exlude_ips_section' );
	register_setting( 'exlude_ips_section', 'wr_no_captcha_exlude_ips' );
	register_setting( 'exlude_ips_section', 'wr_no_captcha_exlude_ips_forwarded_for' );
}

function wr_no_captcha_display_recaptcha_error_message_content() {
	echo '<p>You can set your own error message here for when the bot test fails:</p>';
}

function wr_no_captcha_display_recaptcha_exlude_ips_content() {
	echo '<p>You can exclude specific IP addresses (separated by comma) from displaying the captcha:</p>';
}

function wr_no_captcha_error_message_input() {
	echo '<input size="60" type="text" name="wr_no_captcha_error_message_text" id="wr_no_captcha_error_message_text" value="' . get_option( 'wr_no_captcha_error_message_text' ) . '" />';
}

function wr_no_captcha_exlude_ips_input() {
	echo '<input size="60" type="text" name="wr_no_captcha_exlude_ips" id="wr_no_captcha_exlude_ips" value="' . get_option( 'wr_no_captcha_exlude_ips' ) . '" />';
}

function wr_no_captcha_display_recaptcha_api_content() {
	echo '<p>Please <a href="https://www.google.com/recaptcha/admin">register you domain</a> with Google to obtain the API keys and enter them below.</p>';
}

function wr_no_captcha_key_input() {
	echo '<input type="text" name="wr_no_captcha_site_key" id="captcha_site_key" value="' . get_option( 'wr_no_captcha_site_key' ) . '" />';
}

function wr_no_captcha_secret_key_input() {
	echo '<input type="text" name="wr_no_captcha_secret_key" id="captcha_secret_key" value="' . get_option( 'wr_no_captcha_secret_key' ) . '" />';
}

function wr_no_captcha_exlude_ips_forwarded_for_input() {
	echo '<input type="checkbox" id="wr_no_captcha_exlude_ips_forwarded_for" name="wr_no_captcha_exlude_ips_forwarded_for" value="1"' . checked( 1, get_option( 'wr_no_captcha_exlude_ips_forwarded_for' ), false ) . '/>';
}

function wr_no_captcha_login_form_script() {
	if ( ! wr_no_captcha_is_ip_excluded() ) {
		wp_register_script( 'no_captcha_login', 'https://www.google.com/recaptcha/api.js' );
		wp_enqueue_script( 'no_captcha_login' );
	}
}

function wr_no_captcha_render_login_captcha() {
	if ( wr_no_captcha_api_keys_set() && ! wr_no_captcha_is_ip_excluded() ) {
		echo '<div class="g-recaptcha" data-sitekey="' . get_option( 'wr_no_captcha_site_key' ) . '"></div>';
		require_once( plugin_dir_path( __FILE__ ) . 'noscript/noscript.php' );
	}
}

function wr_no_captcha_verify_login_captcha( $user, $password ) {
	if ( isset( $_POST['g-recaptcha-response'] ) ) {
		$no_captcha_secret = get_option( 'wr_no_captcha_secret_key' );
		$response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $no_captcha_secret . '&response=' . $_POST['g-recaptcha-response'] );
		$response = json_decode( $response['body'], true );
		if ( true === $response['success'] ) {
			return $user;
		} else {
			return new WP_Error( 'Captcha Invalid',  wr_no_captcha_get_error_message() );
		}
	} elseif ( ! wr_no_captcha_api_keys_set() || wr_no_captcha_is_ip_excluded() ) {
		return $user;
	}
}

function wr_no_captcha_css() {
	$src = plugins_url( 'css/no-captcha.css', __FILE__ );
	wp_enqueue_style( 'no_captcha_css',  $src );
}

function wr_no_captcha_get_error_message() {
	$custom_error = get_option( 'wr_no_captcha_error_message_text' );
	if ( $custom_error ) {
		return $custom_error;
	} else {
		return __( '<strong>Robot test error</strong>: I suggest a new strategy, R2, let the Wookie win.' );
	}
}

function wr_no_captcha_api_keys_set() {
	if ( get_option( 'wr_no_captcha_secret_key' ) && get_option( 'wr_no_captcha_site_key' ) ) {
		return true;
	} else {
		return false;
	}
}

function wr_no_captcha_get_exlude_ips() {
	$exlude_ips = get_option( 'wr_no_captcha_exlude_ips' );
	if ( $exlude_ips ) {
		return array_map( 'trim', explode( ',', $exlude_ips ) );
	} else {
		return array();
	}
}

function wr_no_captcha_get_client_ip() {
	$ipaddress = '';
	if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	} elseif ( get_option( 'wr_no_captcha_exlude_ips_forwarded_for' ) === '1' && isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ipaddress = 'UNKNOWN';
	}
	return $ipaddress;
}

function wr_no_captcha_is_ip_excluded() {
	if ( wr_no_captcha_get_client_ip() === 'UNKNOWN' ) {
		return false;
	} else {
		return in_array( wr_no_captcha_get_client_ip(), wr_no_captcha_get_exlude_ips() );
	}
}
