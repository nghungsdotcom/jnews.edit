<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Asset;

/**
 * Class JNews Load Assets
 */
class GoogleFonts extends AssetAbstract {

	/**
	 * @var GoogleFonts
	 */
	private static $instance;

	/**
	 * @return GoogleFonts
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Construct of GoogleFonts
	 */
	public function __construct() {

		if ( $this->enable_google_fonts_update() ) {
			// Change Google Fonts from static file to dynamic options.
			add_filter( 'jeg_fonts_google_fonts_index', array( $this, 'add_google_fonts_index_list' ) );
			add_filter( 'jeg_fonts_google_fonts', array( $this, 'add_google_fonts_list' ) );
			// Run plugin init on admin_init.
			add_action( 'admin_init', array( $this, 'init' ) );
			// Run the Cron Job action to fetch the content from remote URL.
			add_action( 'jnews_update_google_fonts_list', array( $this, 'get_google_fonts_list' ) );
			// Run this hook when user wants to manually fetch Google Fonts list Google Servers upon a button click from Settings/Advanced.
			add_action( 'wp_ajax_jnews_get_dynamic_google_fonts_from_remote', array( $this, 'get_dynamic_google_fonts_from_remote' ) );
		}
	}

	/**
	 * Check if user wants to update Google Fonts list.
	 *
	 * @return bool
	 */
	private function enable_google_fonts_update() {
		$fonts_api = get_theme_mod( 'jnews_google_fonts_api_key', '' );
		return get_theme_mod( 'jnews_google_fonts_enable_update', false ) && ! empty( $fonts_api );
	}

	/**
	 * Initialize the functionality to add dynamic Google Fonts.
	 *
	 * @return void
	 */
	public function init() {

		if ( $this->passed_security_checks() ) {
			// Add & Run Cron Job.
			$this->schedule_update_google_fonts_list();

			$this->report_last_modified_difference();
		} else {
			$this->disable_cron();
		}
	}


	/**
	 * Add some validations before proceeding to creating an actual file.
	 *
	 * @return bool
	 */
	public function passed_security_checks() {

		if ( ! $this->enable_google_fonts_update() ) {
			$this->error = __( 'Please check your Google Fonts API Key', 'jnews' );
			return false;
		}

		return true;
	}

	/**
	 * Add a Cron Job to run every 24 hours.
	 */
	public function schedule_update_google_fonts_list() {
		// Make sure this event hasn't been scheduled.
		if ( ! wp_next_scheduled( 'jnews_update_google_fonts_list' ) ) {
			// Schedule the event to run daily (once).
			wp_schedule_event( time(), 'daily', 'jnews_update_google_fonts_list' );
		}
	}

	/**
	 * Return Google Font API url with API Key.
	 *
	 * @return string
	 */
	private function google_fonts_list_url() {
		$google_fonts_api = get_theme_mod( 'jnews_google_fonts_api_key', '' );
		return 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $google_fonts_api;
	}

	/**
	 * Fetch latest Google Fonts list code from remote upon Ajax Request.
	 *
	 * @uses hook: wp_ajax_jnews_get_dynamic_google_fonts_from_remote
	 *
	 * @return void
	 */
	public function get_dynamic_google_fonts_from_remote() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $this->passed_security_checks() ) {

			$result['result'] = false;
			$result['error']  = $this->error;

			wp_send_json( $result );
		}

		$error = esc_html__( 'We encountered an issue grabbing the latest version of Google Fonts list from Google servers. This is usually a temporary issue so please try again later.', 'jnews-performance' );

		$result['result'] = false;
		$result['error']  = $error;

		$response = $this->get_remote_google_fonts_list( $this->google_fonts_list_url(), 'ajax' );

		if ( true === $response ) {
			$last_update       = jnews_get_option( 'google_fonts_list_modified_at' );
			$result['result']  = true;
			$result['success'] = is_int( $last_update ) ? date_i18n( 'm-d-Y H:i A', $last_update, true ) : '-';
		} else {
			if ( is_bool( $response ) && false === $response ) {
				$result['result'] = false;
				$result['error']  = $error;
			} else {
				$result['result'] = false;
				$result['error']  = $response;
			}
		}

		wp_send_json( $result );
	}

	/**
	 * Get the content from remote URL for Google Fonts.
	 *
	 * @param string $url          Remote Google Fonts URL.
	 * @param string $request_type If it is a Cron or an Ajax request. Defaults to cron.
	 *
	 * @return mixed
	 */
	public function get_remote_google_fonts_list( $url, $request_type = 'cron' ) {

		$response = wp_remote_get( $url );

		if ( ! is_wp_error( $response ) ) {

			$response_body = wp_remote_retrieve_body( $response );

			if ( strlen( $response_body ) > 0 ) {
				$response_body          = json_decode( $response_body, true );
				$hashed_remote_response = hash( 'sha512', wp_json_encode( $response_body ) );
				if ( ! $this->google_fonts_list_exists() ) {
					return $this->create_file( $response_body, $hashed_remote_response );
				} else {
					return $this->update_file( $response_body, $hashed_remote_response );
				}
			}
		} else {
			if ( 'ajax' === $request_type ) {
				return 'WP Error: ' . $response->get_error_message();
			}
		}

		return false;
	}

	/**
	 * Run this function to fetch Google Fonts list from Google Fonts API and
	 * create a dynamic options.
	 *
	 * @return void
	 */
	public function get_google_fonts_list() {
		// Fetch Google Fonts list from remote URL and create dynamic options.
		$result = $this->get_remote_google_fonts_list( $this->google_fonts_list_url() );
	}

	/**
	 * Do cleanup and remove Cron form WP.
	 */
	protected static function disable_cron() {
		if ( has_action( 'jnews_update_google_fonts_list' ) ) {
			wp_clear_scheduled_hook( 'jnews_update_google_fonts_list' );
		}
	}

	/**
	 * Check if the has not been updated since 72 hours. If that is the case
	 * then add the error/warning to admin notices.
	 *
	 * @return void
	 */
	public function report_last_modified_difference() {

		if ( ! $this->google_fonts_list_exists() ) {
			jnews_update_option( 'google_fonts_list_modified_at', false );
			return;
		}

		$last_modified = jnews_get_option( 'google_fonts_list_modified_at' );

		$settings_url = admin_url( '#' );

		if ( ( time() - $last_modified ) > 3 * DAY_IN_SECONDS ) {
			$this->error = sprintf(
				__( 'Hey, we noticed Google Fonts list has not been updated in more than %1$s, please try a %2$smanual fetch%3$s or contact support if you need help.', 'jnews' ),
				human_time_diff( $last_modified ),
				sprintf( '<a href="%1$s">', esc_url( $settings_url ) ),
				'</a>'
			);
		}
	}

	/**
	 * Check if dynamic options Google Fonts list exists.
	 *
	 * @return bool
	 */
	protected function google_fonts_list_exists() {
		$fonts_index = jnews_get_option( 'google_fonts_index_list', array() );
		$fonts       = jnews_get_option( 'google_fonts_list', array() );
		return ! empty( $fonts ) && ! empty( $fonts_index );
	}

	/**
	 * Change Google Fonts index from static file to dynamic options.
	 *
	 * @param array $google_font_index Font index for Google Fonts.
	 *
	 * @return string
	 */
	public function add_google_fonts_index_list( $google_font_index ) {
		if ( $this->enable_google_fonts_update() ) {
			$fonts_index = jnews_get_option( 'google_fonts_index_list', array() );
			if ( ! empty( $fonts_index ) ) {
				return $fonts_index;
			}
		}

		// Return original font index if anything goes wrong.
		return $google_font_index;
	}

	/**
	 * Change Google Fonts list from static file to dynamic options.
	 *
	 * @param array $google_fonts_list Google Fonts list.
	 *
	 * @return string
	 */
	public function add_google_fonts_list( $google_fonts_list ) {
		if ( $this->enable_google_fonts_update() ) {
			$fonts = jnews_get_option( 'google_fonts_list', array() );
			if ( ! empty( $fonts ) ) {
				$google_fonts = array();
				if ( is_array( $fonts ) ) {
					foreach ( $fonts['items'] as $font ) {
						$google_fonts[ $font['family'] ] = array(
							'label'    => $font['family'],
							'variants' => $font['variants'],
							'subsets'  => $font['subsets'],
							'category' => $font['category'],
							'type'     => 'google',
						);
					}
				}
				return $google_fonts;
			}
		}

		// Return original font list if anything goes wrong.
		return $google_fonts_list;
	}


	/**
	 * Update last modified time of the file to site options.
	 *
	 * @return void
	 */
	protected function update_file_modified_time_option() {
		$current_time = current_time( 'timestamp' );
		jnews_update_option( 'google_fonts_list_modified_at', $current_time );
		do_action( 'jnews_after_update_settings', 'google_fonts_list_modified_at', $current_time );
	}

	/**
	 * Delete dynamic options Google Fonts list.
	 *
	 * @return bool
	 */
	public function delete_google_fonts_list_options() {

		if ( $this->google_fonts_list_exists() ) {
			$fonts_index = jnews_update_option( 'google_fonts_index_list', array() );
			$fonts       = jnews_update_option( 'google_fonts_list', array() );
			return $fonts && $fonts_index;
		}

		return true;
	}

	/**
	 * Create fonts index.
	 *
	 * @param array $fonts Google Fonts list.
	 *
	 * @return array
	 */
	protected function create_fonts_index( $fonts ) {
		$fonts_index = array();
		if ( is_array( $fonts ) && ! empty( $fonts ) ) {
			$fonts_items = isset( $fonts['items'] ) ? $fonts['items'] : array();

			foreach ( $fonts_items as $font ) {
				$fonts_index[] = $font['family'];
			}
		}

		return $fonts_index;
	}

	/**
	 * Create dynamic options Google Fonts list if not created.
	 *
	 * @param array  $response_body          Content fetched from Google.
	 * @param string $hashed_remote_response Hashed Content fetched from Google.
	 */
	protected function create_file( $response_body, $hashed_remote_response ) {
		if ( $response_body && is_array( $response_body ) && ! empty( $response_body ) ) {
			jnews_update_option( 'google_fonts_list', $response_body );
			$temp_fonts_index = $this->create_fonts_index( $response_body );
			if ( ! empty( $temp_fonts_index ) ) {
				jnews_update_option( 'google_fonts_index_list', $temp_fonts_index );
				// Hash content fetched.
				$hashed_fonts_index_remote_response = hash( 'sha512', wp_json_encode( $temp_fonts_index ) );
				// Get fonts from options.
				$fonts       = jnews_get_option( 'google_fonts_list', array() );
				$fonts_index = jnews_get_option( 'google_fonts_index_list', array() );
				// Hash fonts from options.
				$hashed_options             = hash( 'sha512', wp_json_encode( $fonts ) );
				$hashed_fonts_index_options = hash( 'sha512', wp_json_encode( $fonts_index ) );
				// Validate change.
				$validate_change_fonts       = $hashed_options === $hashed_remote_response;
				$validate_change_fonts_index = $hashed_fonts_index_options === $hashed_fonts_index_remote_response;
				if ( $validate_change_fonts && $validate_change_fonts_index ) {
					$this->update_file_modified_time_option();
					return true;
				}
			}
		}

		$this->delete_google_fonts_list_options();
		return false;
	}

	/**
	 * Update dynamic options Google Fonts list if already created.
	 *
	 * @param array  $response_body          Content fetched from Google.
	 * @param string $hashed_remote_response Hashed Content fetched from Google.
	 */
	protected function update_file( $response_body, $hashed_remote_response ) {

		if ( $response_body && is_array( $response_body ) && ! empty( $response_body ) ) {
			$fonts_index       = $this->create_fonts_index( $response_body );
			$hashed_temp_fonts = hash( 'sha512', wp_json_encode( $response_body ) );
			if ( ! empty( $fonts_index ) ) {
				// Validate change.
				$validate_change_fonts = $hashed_temp_fonts === $hashed_remote_response;
				if ( $validate_change_fonts ) {
					jnews_update_option( 'google_fonts_list', $response_body );
					jnews_update_option( 'google_fonts_index_list', $fonts_index );

					$this->update_file_modified_time_option();
					return true;
				} else {
					return false;
				}
			}
		}

		return false;
	}
}
