<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Ajax;

class AccountHandler {
	/**
	 * @var AccountHandler
	 */
	private static $instance;

	/**
	 * @return AccountHandler
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {
		add_action( 'wp_error_added', array( $this, 'password_incorrect_response' ), null, 4 );
	}

	public function login_handler() {
		if ( ! empty( $_POST['jnews_nonce'] ) && check_ajax_referer( 'jnews_nonce', 'jnews_nonce', false ) ) {
			try {
				$creds = array();

				$validation_error = new \WP_Error();

				if ( $validation_error->get_error_code() ) {
					throw new \Exception( $validation_error->get_error_message() );
				}

				if ( ! \JNews\Captcha::getInstance()->check_recaptcha( false, 'login' ) ) {
					throw new \Exception( jnews_return_translation( 'Invalid Recaptcha!', 'jnews', 'invalid_recaptcha' ) );
				}

				if ( empty( $_POST['username'] ) ) {
					throw new \Exception( jnews_return_translation( 'Username / email is required.', 'jnews', 'username_email_required' ) );
				}

				if ( empty( $_POST['password'] ) ) {
					throw new \Exception( jnews_return_translation( 'Password is required.', 'jnews', 'password_required' ) );
				}
				$username = sanitize_user( wp_unslash( $_POST['username'] ) );

				if ( jnews_is_emails( $username ) ) {
					$user = get_user_by( 'email', $username );

					if ( isset( $user->user_login ) ) {
						$creds['user_login'] = $user->user_login;
					} else {
						throw new \Exception( jnews_return_translation( 'A user could not be found with this email address.', 'jnews', 'user_with_email_not_found' ) );
					}
				} else {
					$creds['user_login'] = $username;
				}

				$creds['remember'] = true;
				$remember_me       = isset( $_POST['remember_me'] ) ? sanitize_text_field( $_POST['remember_me'] ) : '';
				if ( 'true' === $remember_me ) {
					$creds['remember'] = true;
				} else {
					$creds['remember'] = false;
				}

				$creds['user_password'] = $_POST['password'];
				$secure_cookie          = is_ssl() ? true : false;
				$user                   = wp_signon( $creds, $secure_cookie );

				if ( is_wp_error( $user ) ) {
					throw new \Exception( $user->get_error_message() );
				} else {
					// refresh
					wp_send_json(
						array(
							'response' => 1,
							'refresh'  => 1,
							'string'   => jnews_return_translation( 'Login successful. Please wait while you are being redirected.', 'jnews', 'login_success_wait_redirecting' ),
						)
					);
				}
			} catch ( \Exception $e ) {

				wp_send_json(
					array(
						'response' => '0',
						'string'   => $e->getMessage(),
					),
					200
				);

			}
		}

		exit;
	}

	/**
	 * Password Incorrect Response
	 *
	 * @param string|int $code     Error code.
	 * @param string     $message  Error message.
	 * @param mixed      $data     Error data. Might be empty.
	 * @param WP_Error   $class The WP_Error object.
	 */
	public function password_incorrect_response( $code, $message, $data, $class ) {
		if ( 'incorrect_password' === $code ) {
			if ( is_email( $_POST['username'] ) ) {
				$string = sprintf(
					/* translators: %s: Email address. */
					__( '<strong>Error</strong>: The password you entered for the email address %s is incorrect.' ),
					'<strong>' . $_POST['username'] . '</strong>'
				);
			} else {
				$string = sprintf(
					/* translators: %s: User name. */
					__( '<strong>Error</strong>: The password you entered for the username %s is incorrect.' ),
					'<strong>' . $_POST['username'] . '</strong>'
				);
			}
			$class->errors[ $code ][0] = $string . ' <a href="#jeg_forgotform" class="jeg_popuplink">' . __( 'Lost your password?' ) . '</a>';
		}
	}

	public function register_handler() {
		if ( ! empty( $_POST['jnews_nonce'] ) && check_ajax_referer( 'jnews_nonce', 'jnews_nonce', false ) ) {
			try {
				$user_login = sanitize_user( wp_unslash( $_POST['username'] ) );
				$user_email = sanitize_user( wp_unslash( $_POST['email'] ) );

				$validation_error = new \WP_Error();

				if ( $validation_error->get_error_code() ) {
					throw new \Exception( $validation_error->get_error_message() );
				}

				if ( ! \JNews\Captcha::getInstance()->check_recaptcha( false, 'login' ) ) {
					throw new \Exception( jnews_return_translation( 'Invalid Recaptcha!', 'jnews', 'invalid_recaptcha' ) );
				}

				if ( username_exists( $user_login ) ) {
					throw new \Exception( jnews_return_translation( 'Username is already taken', 'jnews', 'username_already_taken' ) );
				}

				if ( ! validate_username( $user_login ) ) {
					throw new \Exception( jnews_return_translation( 'Invalid username', 'jnews', 'invalid_username' ) );
				}

				if ( $user_login == '' ) {
					throw new \Exception( jnews_return_translation( 'Please enter a username', 'jnews', 'please_enter_username' ) );
				}

				if ( ! jnews_is_emails( $user_email ) ) {
					throw new \Exception( jnews_return_translation( 'Invalid email', 'jnews', 'invalid_email' ) );
				}

				if ( email_exists( $user_email ) ) {
					throw new \Exception( jnews_return_translation( 'Email is already registered', 'jnews', 'email_already_registered' ) );
				}

				// generate random pass
				$user_pass = wp_generate_password( 12, false );

				$default_role = get_option( 'default_role', 'subscriber' );
				$default_role = apply_filters( 'jnews_register_default_role', $default_role );

				$new_user = wp_insert_user(
					array(
						'user_login'      => $user_login,
						'user_pass'       => $user_pass,
						'user_email'      => $user_email,
						'user_registered' => date( 'Y-m-d H:i:s' ),
						'role'            => $default_role,
					)
				);

				if ( is_wp_error( $new_user ) ) {
					throw new \Exception( $new_user->get_error_message() );
				} else {
					// send an email to the admin alerting them of the registration
					wp_new_user_notification( $new_user, null, 'both' );

					wp_send_json(
						array(
							'response' => 1,
							'refresh'  => 0,
							'string'   => jnews_return_translation( 'Register successful. Please check your email (index or spam folder), the password was sent there.', 'jnews', 'register_success_check_email' ),
						)
					);
				}
			} catch ( \Exception $e ) {

				wp_send_json(
					array(
						'response' => '0',
						'string'   => $e->getMessage(),
					),
					200
				);

			}
		}

		exit;
	}

	public function forget_password_handler() {
		if ( ! empty( $_POST['jnews_nonce'] ) && check_ajax_referer( 'jnews_nonce', 'jnews_nonce', false ) ) {

			try {

				if ( ! \JNews\Captcha::getInstance()->check_recaptcha( false, 'login' ) ) {
					throw new \Exception( jnews_return_translation( 'Invalid Recaptcha!', 'jnews', 'invalid_recaptcha' ) );
				}

				if ( empty( $_POST['user_login'] ) ) {
					throw new \Exception( jnews_return_translation( 'enter a username or e-mail address', 'jnews', 'enter_username_email_address' ) );
				} else {
					$login     = sanitize_user( wp_unslash( $_POST['user_login'] ) );
					$user_data = get_user_by( 'login', $login );
				}

				// If no user found, check if it login is email and lookup user based on email.
				if ( ! $user_data && jnews_is_emails( $login ) ) {
					$user_data = get_user_by( 'email', $login );
				}

				if ( ! $user_data ) {
					throw new \Exception( jnews_return_translation( 'email or username is not registered into this site', 'jnews', 'email_username_not_registered' ) );
				}

				if ( is_multisite() && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					throw new \Exception( jnews_return_translation( 'email or username is not registered into this site', 'jnews', 'email_username_not_registered' ) );
				}

				// redefining user_login ensures we return the right case in the email
				$user_login = $user_data->user_login;
				$key        = get_password_reset_key( $user_data );

				// todo : kirim email untuk reset password
				$message  = jnews_return_translation( 'Someone has requested a password reset for the following account:', 'jnews', 'someone_request_password_reset' ) . "\r\n\r\n";
				$message .= network_home_url( '/' ) . "\r\n\r\n";
				$message .= sprintf( jnews_return_translation( 'Username: %s', 'jnews', 'username_s' ), $user_login ) . "\r\n\r\n";
				$message .= jnews_return_translation( 'If this was a mistake, just ignore this email and nothing will happen.', 'jnews', 'ignore_mistake_email' ) . "\r\n\r\n";
				$message .= jnews_return_translation( 'To reset your password, visit the following address:', 'jnews', 'reset_password_visit_address' ) . "\r\n\r\n";
				$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";

				if ( is_multisite() ) {
					$blogname = $GLOBALS['current_site']->site_name;
				} else // The blogname option is escaped with esc_html on the way into the database in sanitize_option
					// we want to reverse this for the plain text arena of emails.
				{
					$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				}

				$title = sprintf( jnews_return_translation( '[%s] Password Reset', 'jnews', 's_password_reset' ), $blogname );

				$title        = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
				$message      = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
				$send_message = apply_filters( 'jnews_send_message', false, $user_data->user_email, $title, $message );

				if ( $message && ! $send_message ) {
					wp_send_json(
						array(
							'response' => 0,
							'string'   => jnews_return_translation( 'The e-mail could not be sent. Your host may have disabled the mail function...', 'jnews', 'email_not_sent_host_disable_mail_function' ),
						)
					);
				} else {
					wp_send_json(
						array(
							'response' => 1,
							'refresh'  => 0,
							'string'   => jnews_return_translation( 'Please check your e-mail for the confirmation link', 'jnews', 'check_email_confirmation_link' ),
						)
					);
				}
			} catch ( \Exception $e ) {

				wp_send_json(
					array(
						'response' => '0',
						'string'   => $e->getMessage(),
					),
					200
				);

			}
		}

		exit;
	}
}
