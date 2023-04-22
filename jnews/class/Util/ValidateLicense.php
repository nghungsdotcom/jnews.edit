<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Util;

/**
 * Class ValidateLicense
 *
 * @package JNews\Util
 */
class ValidateLicense {

	/**
	 * @var array
	 *
	 * Contain array of menu slug
	 */
	private $menu;

	/**
	 * @var string
	 */
	private $update = 'JNews';

	/**
	 * @var string
	 */
	private static $optionname = 'jnews_license';

	/**
	 * @var string
	 */
	private $version_url = 'https://updates.jnews.io/';

	/**
	 * @var ValidateLicense
	 */
	private static $instance;

	/**
	 * @return ValidateLicense
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	private function __construct() {
		$this->setup_hook();
		$this->menu = apply_filters( 'jnews_get_admin_slug', '' );
	}

	public function setup_hook() {
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		add_action( 'admin_init', array( $this, 'validate_license' ) );
		add_action( 'admin_init', array( $this, 'schedule_update_themes' ) );

		add_filter( 'jnews_check_is_license_validated', array( $this, 'is_license_validated' ) );

		/**
		 * Action for schedule event
		 *
		 * @see \JNews\Init::update_themes()
		 */

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'transient_update_themes' ) );
		add_filter( 'pre_set_transient_update_themes', array( $this, 'transient_update_themes' ) );
		add_action( 'upgrader_package_options', array( $this, 'maybe_deferred_download' ), 99 );

		add_action( 'wp_ajax_dismiss_license_notice', array( $this, 'dismiss_license_notice' ) );
		add_action( 'wp_ajax_nopriv_dismiss_license_notice', array( $this, 'dismiss_license_notice' ) );

		add_action( 'wp_ajax_dismiss_update_notice', array( $this, 'dismiss_update_notice' ) );
		add_action( 'wp_ajax_nopriv_dismiss_update_notice', array( $this, 'dismiss_update_notice' ) );
	}

	/**
	 * Register custom schedule event for themes update checker
	 */
	public function schedule_update_themes() {
		if ( is_multisite() && ! is_main_site() ) {
			if ( ! wp_next_scheduled( 'jnews_update_themes' ) ) {
				wp_schedule_event( time(), 'daily', 'jnews_update_themes' );
			}
		}
	}

	public function dismiss_license_notice() {
		if ( current_user_can( 'manage_options' ) ) {
			update_option( 'jnews_dismiss_license_notice', true );
		}
	}

	public function dismiss_update_notice() {
		if ( $this->is_license_validated() ) {
			$new_version = $this->get_latest_version();
			if ( $new_version ) {
				$dismiss_version = get_option( 'jnews_dismiss_update_notice', false );
				$dismiss_version = $dismiss_version && $dismiss_version !== 1 ? $dismiss_version : $new_version;
				update_option( 'jnews_dismiss_update_notice', $dismiss_version );
			}
		}
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public function maybe_deferred_download( $options ) {
		$package = $options['package'];
		if ( false !== strrpos( $package, 'deferred_download' ) && false !== strrpos( $package, 'item_id' ) ) {
			parse_str( wp_parse_url( $package, PHP_URL_QUERY ), $vars );
			if ( $vars['item_id'] ) {
				$options['package'] = $this->get_download_url( $vars['item_id'] );
			}
		}
		return $options;
	}

	/**
	 * @param $transient
	 *
	 * @return mixed
	 */
	public function transient_update_themes( $transient ) {
		if ( isset( $transient->checked ) ) {
			$slug    = get_template();
			$theme   = wp_get_theme( $slug );
			$license = jnews_get_license();

			if ( ! empty( $license ) && isset( $license['purchase_code'] ) && isset( $license['refresh'] ) && ( ! isset( $license['item'] ) ) ) {
				$new_version = $this->get_latest_version( 'new' );
			} else {
				$new_version = $this->get_latest_version();
			}

			if ( version_compare( $theme->get( 'Version' ), $new_version, '<' ) ) {
				$package = $this->deferred_download_url( JNEWS_THEME_ID );

				$transient->response[ JNEWS_THEME_TEXTDOMAIN ] = array(
					'theme'       => JNEWS_THEME_TEXTDOMAIN,
					'new_version' => $new_version,
					'url'         => 'https://support.jegtheme.com/theme/jnews/',
					'package'     => $package,
				);
			}
		}

		return $transient;
	}

	/**
	 * Get the latest theme version
	 *
	 * @param string $license_type
	 *
	 * @return string|boolean
	 */
	public function get_latest_version( $license_type = 'old' ) {
		if ( 'old' === $license_type ) {
			$request = wp_remote_get( $this->version_url, array( 'timeout' => 20 ) );
			return ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ? $request['body'] : false;
		} else {
			$option = get_option( jnews_get_license_optionname() );
			$args   = array(
				'method'    => 'POST',
				'sslverify' => false,
				'body'      => build_query(
					array(
						'domain'  => home_url(),
						'code'    => isset( $option['purchase_code'] ) ? $option['purchase_code'] : '',
						'item_id' => JNEWS_THEME_ID,
					)
				),
			);

			$response = wp_remote_post( jnews_get_license_server_rest_url( 'getVersion' ), $args );
			$response = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( $response ) {
				if ( ! $response['valid'] ) {
					jnews_reset_license();
				}

				return $response['version'];
			}

			return false;
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function deferred_download_url( $id ) {
		$args = array(
			'deferred_download' => true,
			'item_id'           => $id,
		);
		return add_query_arg( $args, esc_url( $this->license_url() ) );
	}

	/**
	 * @return mixed
	 */
	public function license_url() {
		static $url;
		$adminslug = apply_filters( 'jnews_get_admin_slug', '' );

		if ( ! isset( $url ) ) {
			$parent = JNEWS_THEME_TEXTDOMAIN;
			if ( false === strpos( $parent, '.php' ) ) {
				$parent = 'admin.php';
			}
			$url = add_query_arg(
				array(
					'page' => urlencode( $adminslug['dashboard'] ),
				),
				self_admin_url( $parent )
			);
		}

		return $url;
	}

	/**
	 * @param null $token
	 *
	 * @return bool|null
	 */
	public function get_token( $token = null ) {
		if ( null === $token || empty( $token ) ) {
			if ( $this->is_license_validated() ) {
				$option = get_option( self::$optionname );
				$args   = array(
					'method'    => 'POST',
					'sslverify' => false,
					'body'      => build_query(
						array(
							'domain' => home_url(),
							'code'   => isset( $option['purchase_code'] ) ? $option['purchase_code'] : '',
						)
					),
				);

				$response = wp_remote_post( jnews_get_license_server_rest_url( 'getToken' ), $args );
				$response = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( $response ) {
					$token = $response;
				}
			} else {
				return false;
			}
		}

		return $token;
	}

	/**
	 * @param $id
	 * @param null $token
	 *
	 * @return bool
	 */
	public function get_download_url( $id, $token = null ) {
		$token = $this->get_token( $token );

		if ( $token ) {
			$url      = 'https://api.envato.com/v2/market/buyer/download?item_id=' . $id . '&shorten_url=true';
			$response = $this->request( $url, $token, array() );
			return ! is_wp_error( $response ) ? $response['wordpress_theme'] : false;
		}

		return false;
	}

	/**
	 * @param $url
	 * @param $token
	 * @param $args
	 *
	 * @return mixed|\WP_Error
	 */
	public function request( $url, $token, $args ) {
		$defaults = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'User-Agent'    => 'JNews WordPress Themes',
			),
			'timeout' => 20,
		);

		$args  = wp_parse_args( $args, $defaults );
		$token = trim( str_replace( 'Bearer', '', $args['headers']['Authorization'] ) );

		if ( empty( $token ) ) {
			return new \WP_Error( 'api_token_error', esc_html__( 'An API token is required.', 'jnews' ) );
		}

		// Make an API request.
		$response = wp_remote_get( esc_url_raw( $url ), $args );

		// Check the response code.
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			return new \WP_Error( $response_code, $response_message );
		} elseif ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, esc_html__( 'An unknown API error occurred.', 'jnews' ) );
		}

		$return = json_decode( wp_remote_retrieve_body( $response ), true );

		return null === $return ? new \WP_Error( 'api_error', esc_html__( 'An unknown API error occurred.', 'jnews' ) ) : $return;

	}

	/**
	 * Theme's license submit handler
	 */
	public function validate_license() {
		if ( isset( $_GET['action'] ) && 'validate-license' === sanitize_key( $_GET['action'] ) ) {
			if ( ! isset( $_GET['purchase_code'] ) || ! isset( $_GET['refresh_token'] ) || ! isset( $_GET['access_token'] ) ) {
				return;
			}

			update_option(
				jnews_get_license_optionname(),
				array(
					'validated'     => true,
					'refresh'       => $_GET['refresh_token'],
					'purchase_code' => $_GET['purchase_code'],
				)
			);
		}
	}

	public function admin_notices() {
		if ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) === $this->menu['dashboard'] ) {
			// do nothing
		} else {
			$license = jnews_get_license();
			if ( ! $this->is_license_validated() ) {
				$this->print_validate_notice();
			}

			if ( $this->is_license_validated() && ( ! isset( $license['purchase_code'] ) ) ) {
				$this->print_validate_notice( 'migrate' );
			}

			$slug      = get_template();
			$transient = get_site_transient( 'update_themes' );

			if ( $transient && isset( $transient->response[ JNEWS_THEME_TEXTDOMAIN ] ) ) {
				$theme = wp_get_theme( $slug );

				if ( version_compare( $theme->get( 'Version' ), $transient->response[ JNEWS_THEME_TEXTDOMAIN ]['new_version'], '<' ) ) {
					$dismiss_version = get_option( 'jnews_dismiss_update_notice', false );
					$dismiss_version = $dismiss_version && $dismiss_version !== 1 ? $dismiss_version : $theme->get( 'Version' );
					$url             = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( JNEWS_THEME_TEXTDOMAIN ) ), 'upgrade-theme_' . JNEWS_THEME_TEXTDOMAIN );

					if ( version_compare( $dismiss_version, $transient->response[ JNEWS_THEME_TEXTDOMAIN ]['new_version'], '<' ) ) {
						if ( JNEWS_THEME_TEXTDOMAIN === $slug ) {
							$this->print_update_notice( $url );
						} else {
							$update_notice = __( 'There is a new version of JNews available! You\'re using custom JNews Theme and you need to manual update.</br>Please ask support on our forum for more information. Update your theme to get new features and bug fixes.', 'jnews' );

							$this->print_update_notice( $url, $update_notice );
						}
					}
				}
			}
		}
	}

	/**
	 * @param $url
	 * @param string $update_notice
	 */
	public function print_update_notice( $url, $update_notice = '' ) {
		if ( empty( $update_notice ) ) {
			$update_notice = esc_html__( 'There is a new version of JNews available! Update your theme to get new features and bug fixes.', 'jnews' );
		}
		// phpcs:disable WordPress.WP.I18n.UnorderedPlaceholdersText
		?>
		<div class="notice jnews-notice-update">
			<p>
				<?php
				$dismiss_button = $this->is_license_validated() ? '<a class="dismiss-button update" href="#">Dismiss Update</a>' : '';
				printf(
					wp_kses(
						__(
							'<span class="jnews-notice-heading">New Update Available!</span>
                        <span style="display: block;">%s</span>
                        <span class="jnews-notice-button">
							<a href="%s">Update Now</a>
							%s
                        </span>',
							'jnews'
						),
						array(
							'strong' => array(),
							'span'   => array(
								'style' => true,
								'class' => true,
							),
							'a'      => array(
								'href'  => true,
								'class' => true,
							),
						)
					),
					$update_notice,
					esc_url( $url ),
					$dismiss_button
				);
				?>
			</p>
		</div>
		<?php
		// phpcs:enable WordPress.WP.I18n.UnorderedPlaceholdersText
	}

	/**
	 * @return bool
	 */
	public static function is_license_validated() {
		$option = get_option( self::$optionname );
		return $option ? $option['validated'] : false;
	}

	/**
	 * Check Validate Notice
	 */
	public static function check_validate_notice_length() {
		ob_start();
		\JNews\Util\ValidateLicense::getInstance()->print_validate_notice();
		$content = ob_get_clean();
		return strlen( $content );
	}

	public function print_validate_failed() {
		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Failed to validate license, please check if required access is granted when token created, also please check to make sure if your account already bought the item', 'jnews' ); ?></p>
		</div>
		<?php
	}

	public function print_validate_success() {
		?>
		<div class="notice notice-success">
			<p><?php esc_html_e( 'Thank you, your license has been validated.', 'jnews' ); ?></p>
		</div>
		<?php
	}

	public function print_validate_notice( $type = 'activate' ) {
		// phpcs:disable WordPress.WP.I18n.UnorderedPlaceholdersText
		if ( ! function_exists( 'menu_page_url' ) ) {
			/**
			 * Uncaught Error: Call to undefined function JNews\Util\menu_page_url()
			 * when call it from Rest API
			 */
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$home_url            = home_url();
		$jnews_dashboard_url = menu_page_url( 'jnews', false );
		$callback            = str_replace( $home_url, '', $jnews_dashboard_url );
		$url                 = add_query_arg(
			array(
				'siteurl'  => $home_url,
				'callback' => $callback,
				'item_id'  => JNEWS_THEME_ID,
			),
			JEGTHEME_SERVER . '/activate/'
		);

		$heading_text = esc_html__( 'Activate License', 'jnews' );
		$button_text  = esc_html__( 'Activate Now', 'jnews' );
		$message      = sprintf(
			wp_kses(
				__( 'Please activate your copy of JNews to receive theme updates, premium support service and full benefit of this theme.', 'jnews' ),
				array(
					'strong' => array(),
				)
			)
		);
		if ( 'migrate' === $type ) {
			$heading_text = esc_html__( 'Migrate License', 'jnews' );
			$button_text  = esc_html__( 'Migrate Now', 'jnews' );
			$message      = sprintf(
				wp_kses(
					__( 'Please migrate your current license to new license system of JNews.', 'jnews' ),
					array(
						'strong' => array(),
					)
				)
			);
		}
		?>
		<div class="notice notice-error license-notice">
			<p>
				<span class="jnews-notice-icon-wrapper">
					<i class="jnews-notice-icon-warning-svg"></i>
				</span>
				<span class="jnews-notice-content-wrapper">
					<span class="jnews-notice-message-wrapper">
						<span class="jnews-notice-heading"><?php echo jnews_sanitize_output( $heading_text ); ?></span>
						<span style="display: block;"><?php echo jnews_sanitize_output( $message ); ?></span>
					</span>
					<span class="jnews-notice-action">
						<a href="<?php echo esc_url( $url ); ?>"><?php echo jnews_sanitize_output( $button_text ); ?></a>
					</span>
				</span>
			</p>
		</div>
		<?php
		// phpcs:enable WordPress.WP.I18n.UnorderedPlaceholdersText
	}


	/**
	 * Dashboard license config
	 *
	 * @return array
	 */
	public function jnews_dashboard_config() {
		$license_data        = array();
		$home_url            = home_url();
		$jnews_dashboard_url = menu_page_url( 'jnews-new-dashboard', false );
		$callback            = str_replace( $home_url, '', $jnews_dashboard_url );
		$is_validated        = apply_filters( 'jnews_check_is_license_validated', false );
		$is_migration        = false;
		$license_url         = add_query_arg(
			array(
				'siteurl'  => $home_url,
				'callback' => $callback,
				'item_id'  => JNEWS_THEME_ID,
			),
			JEGTHEME_SERVER . '/activate/'
		);

		$license_data['validated']     = $is_validated;
		$license_data['url']           = $license_url;
		$license_data['purchase_code'] = '';
		if ( $is_validated ) {
			$license = jnews_get_license();
			if ( ! empty( $license ) ) {
				if ( ( ! isset( $license['purchase_code'] ) ) && ( ! isset( $license['refresh'] ) ) && isset( $license['item'] ) ) {
					$is_migration = true;
				} else {
					$license_data['purchase_code'] = $license['purchase_code'];
				}
			}
		}
		$license_data['migration'] = $is_migration;

		return $license_data;
	}
}
