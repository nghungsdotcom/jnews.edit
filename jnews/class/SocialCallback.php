<?php
/**
 * @author Jegtheme
 */

namespace JNews;

/**
 * Class JNews Social Callback
 */
class SocialCallback {
	private static $instance;

	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	private function __construct() {
		$this->setup_hook();
	}

	protected function setup_hook() {
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'token_query_vars' ) );
			add_filter( 'parse_request', array( $this, 'social_parse_request' ) );
			add_action( 'jnews_ajax_save_facebook_token', array( $this, 'save_facebook_token' ) );
			add_action( 'jnews_ajax_save_twitch_token', array( $this, 'save_twitch_token' ) );
		}

		add_action( 'init', array( $this, 'add_rewrite_rule' ) );
		add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules' ) );
	}

	public function token_query_vars( $vars ) {
		$vars[] = 'social-token';
		return $vars;
	}

	function social_parse_request( $wp ) {
		if ( array_key_exists( 'social-token', $wp->query_vars ) ) {
			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
				$url = "https://";
			} else {
				$url = "http://";
			}
			$url .= $_SERVER['HTTP_HOST'];
			FrontendAjax::getInstance()->frontend_ajax_script();

			switch ($wp->query_vars['social-token']) {
				case 'twitch':
					?>
                    <script>
                        (function ($$) {
                            param = new URLSearchParams(document.location.hash.substr(1));
                            xhttp = new XMLHttpRequest();
                            xhttp.open("GET", 'https://id.twitch.tv/oauth2/validate', true);
                            xhttp.setRequestHeader('Authorization', `Bearer ${param.get('access_token')}`);
                            xhttp.onload = function () {
                                if (this.status >= 200 && this.status < 400) {
                                    validate_success(JSON.parse(xhttp.response), param.get('access_token'));
                                } else {
                                    $$('error', xhttp.response);
                                }
                            };
                            xhttp.onerror = function () {
                                $$('connection error');
                            };
                            xhttp.send();

                            validate_success = function (response, token) {
                                form = new FormData();
                                xhttp = new XMLHttpRequest();
                                request = new URL(`<?=$url?>${jnews_ajax_url}`);
                                request.searchParams.set('action', 'save_twitch_token');
                                xhttp.open("POST", request.toString(), true);
                                form.append('token', token);
                                form.append('expire', response.expires_in);
                                form.append('user', response.login);
                                form.append('nonce', '<?=wp_create_nonce( 'save_twitch_token' )?>');
                                xhttp.onload = function () {
                                    if (this.status >= 200 && this.status < 400) {
                                        window.close();
                                    } else {
                                        $$('error', xhttp.response);
                                    }
                                };
                                xhttp.onerror = function () {
                                    $$('connection error');
                                };
                                xhttp.send(form);
                            }
                        })(console.log);
                    </script>
					<?php
					break;
				case 'facebook':
					?>
                    <script>
                        (function ($$) {
                            form = new FormData();
                            xhttp = new XMLHttpRequest();
                            url = new URL(window.location.href);
                            request = new URL(`<?=$url?>${jnews_ajax_url}`);
                            request.searchParams.set('action', 'save_facebook_token');
                            xhttp.open("POST", request.toString(), true);
                            form.append('code', url.searchParams.get('code'));
                            form.append('redirect', url.origin + url.pathname);
                            form.append('nonce', '<?=wp_create_nonce( 'save_facebook_token' )?>');
                            xhttp.onload = function () {
                                if (this.status >= 200 && this.status < 400) {
                                    localStorage.setItem('jnews_facebook_token', 'reload');
                                } else {
                                    $$('error', xhttp.response);
                                }
                            };
                            xhttp.onerror = function () {
                                $$('connection error');
                            };
                            window.addEventListener('storage', function (e) {
                                if (e.key === 'jnews_facebook_token') {
                                    e.target.focus();
                                    window.close();
                                }
                            });
                            xhttp.send(form);
                        })(console.log);
                    </script>
					<?php
					break;
			}
			exit();
		}
	}

	public function add_rewrite_rule() {
		add_rewrite_rule( '^social-token/([^/]*)/?', 'index.php?social-token=$matches[1]', 'top' );
	}

	public function flush_rewrite_rules() {
		$this->add_rewrite_rule();

		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	public function save_facebook_token() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'save_facebook_token' ) ) {
			$fbid = get_theme_mod( 'jnews_facebook_client_id' );
			$fbsecret = get_theme_mod( 'jnews_facebook_client_secret' );
			get_theme_mod( 'jnews_facebook_client_id' );
			get_theme_mod( 'jnews_facebook_client_secret' );
			$url = sprintf( 'https://graph.facebook.com/v11.0/oauth/access_token?client_id=%s&client_secret=%s&code=%s&redirect_uri=%s',
				$fbid,
				$fbsecret,
				$_POST['code'],
				$_POST['redirect']
			);

			$response = wp_remote_get( $url, array(
				'timeout' => 10,
			) );
			if ( ! is_wp_error( $response ) && isset( $response['response'] ) && isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
				$json = json_decode( $response['body'], true );

				$url = sprintf( 'https://graph.facebook.com/debug_token?input_token=%s&access_token=%s',
					$json['access_token'],
					"{$fbid}|{$fbsecret}"
				);

				$response = wp_remote_get( $url, array(
					'timeout' => 10,
				) );

				$debug = json_decode( $response['body'], true );

				update_option( 'jnews_option[jnews_facebook]', [
					'token' => $json['access_token'],
					'expire' => $debug['data']['expires_at'] ? $debug['data']['expires_at'] + time() : false,
				] );
			}
		}
	}

	public function save_twitch_token() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'save_twitch_token' ) ) {
			update_option( 'jnews_option[jnews_twitch]', [
				'token'		=> $_POST['token'],
				'expire'	=> (int) $_POST['expire'] + time(),
				'user'		=> $_POST['user'],
			] );
		}
	}
}
