<?php


/**
 * @author Jegtheme
 */
namespace JNews;

class Captcha {
	private static $instance;

	private static $site_key, $secret_key, $enable, $login, $comment;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	private function __construct() {
		$this->setup_hook();
		static::$site_key   = get_theme_mod( 'jnews_recaptcha_site_key', '' );
		static::$secret_key = get_theme_mod( 'jnews_recaptcha_secret_key', '' );
		static::$login      = get_theme_mod( 'jnews_enable_recaptcha', false );
		static::$enable     = get_theme_mod( 'jnews_enable_recaptcha_new', false );
		static::$comment    = get_theme_mod( 'jnews_enable_recaptcha_comment', false );
	}

	private function setup_hook() {
		add_action( 'wp_footer', array( $this, 'captcha_script' ) );

		add_filter( 'theme_mod_jnews_enable_recaptcha_new', array( $this, 'compatibility' ) );
	}

	public function compatibility( $value ) {
        if ( ! get_option( 'jnews_captcha_compatibility', false ) ) {
            update_option( 'jnews_captcha_compatibility', true );
			$value = get_theme_mod( 'jnews_enable_recaptcha', false );
			set_theme_mod( 'jnews_enable_recaptcha_new', $value );
		}

		return $value;
	}

	public function captcha_script() {
		if ( $this->can_render_script() && apply_filters( 'jnews_captcha_rendered', array() ) ) {
			?>
			<script>
				window.jnewsgrecaptcha = function () {
					Array.from(document.getElementsByClassName('g-recaptcha')).forEach(function (value) {
						grecaptcha.render(value, value.dataset.sitekey);
					});
				}
			</script>
			<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=jnewsgrecaptcha" async defer></script>
			<?php
		}
	}

	public function can_render_script( $type = '' ) {
		switch ( $type ) {
			case 'comment':
				$type = static::$comment;
				break;
			case 'login':
				$type = static::$login;
				break;
			default:
				$type = true;
				break;
		}
		return $type &&
			static::$site_key &&
			static::$secret_key &&
			static::$enable;
	}

	public function render_element( $type, $echo = true ) {
		if ( $this->can_render_script( $type ) ) {
			add_filter(
				'jnews_captcha_rendered',
				function ( $list ) use ( $type ) {
					$list[] = $type;
					return $list;
				}
			);
			if ( ! $echo ) {
				ob_start();
			}
			?>
			<div class="g-recaptcha" data-sitekey="<?php echo static::$site_key; ?>"></div>
			<?php
			if ( ! $echo ) {
				return ob_get_clean();
			}
		}
	}

	public function check_recaptcha( $die = false, $type = '' ) {
		$recaptcha = true;
		if ( isset( $_POST['g-recaptcha-response'] ) || $this->can_render_script( $type ) ) {
			$grecaptcha_response = trim( $_POST['g-recaptcha-response'] );
			$recaptcha           = false;
			$post_data           = array(
				'secret'   => static::$secret_key,
				'response' => $grecaptcha_response,
				'remoteip' => $_SERVER['REMOTE_ADDR'],
			);

			$verify = wp_remote_post(
				'https://www.google.com/recaptcha/api/siteverify',
				array(
					'header' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
					'body'   => $post_data,
					'method' => 'POST',
				)
			);

			if ( ! is_wp_error( $verify ) && $verify['response']['code'] == '200' ) {
				$verify = json_decode( $verify['body'] );
				if ( isset( $verify->success ) ) {
					$recaptcha = $verify->success;
				}
			}
		}

		if ( $die && ! $recaptcha ) {
			$message = jnews_return_translation( 'Invalid Recaptcha!', 'jnews', 'invalid_recaptcha' );
			if ( wp_doing_ajax() ) {
				die( $message );
			}
			wp_die( $message, 403 );
		}

		return $recaptcha;
	}
}
