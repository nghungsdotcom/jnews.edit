<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Asset;

/**
 * Class JNews Load Assets
 */
class GoogleAnalytics extends AssetAbstract {

	/**
	 * Hold info about the uploads directory.
	 *
	 * @var array
	 */
	protected $uploads_dir;

	/**
	 * Name of the base directory to hold local JS file.
	 *
	 * @var string
	 */
	protected $local_base_dir_name = 'jnews';

	/**
	 * Name of sub directory to hold local JS file.
	 *
	 * @var string
	 */
	protected $local_gtag_dir_name = 'gtag';

	/**
	 * Name of the temp local JS file.
	 *
	 * @var string
	 */
	protected $local_gtag_temp_file_name = 'latest-gtag.js';

	/**
	 * Name of the local JS file.
	 *
	 * @var string
	 */
	protected $local_gtag_file_name = 'gtag.js';

	/**
	 * @var GoogleAnalytics
	 */
	private static $instance;

	/**
	 * @return GoogleAnalytics
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Construct of GoogleAnalytics
	 */
	public function __construct() {
		// Change gtag file src from remote to local.
		add_filter( 'jnews_google_analytics_gtag_src', array( $this, 'add_local_gtag_js' ) );
		// Run plugin init on admin_init.
		add_action( 'admin_init', array( $this, 'init' ) );
		// Run the Cron Job action to fetch the content from remote URL.
		add_action( 'jnews_fetch_remote_gtag_js', array( $this, 'get_remote_gtag_js_content' ) );
		// Run this hook when user wants to manually fetch gtag.js code from Google Servers upon a button click from Settings/Advanced.
		add_action( 'wp_ajax_jnews_get_local_gtag_js_from_remote', array( $this, 'get_local_gtag_js_from_remote' ) );
	}

	/**
	 * Initialize the functionality to add local gtag.js.
	 *
	 * @return void
	 */
	public function init() {

		if ( $this->passed_security_checks() ) {
			// Add & Run Cron Job.
			$this->schedule_fetch_remote_gtag_js();

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

		if ( ! $this->enable_local_gtag_js() ) {
			$this->error = __( 'Please check your analytics data', 'jnews' );
			return false;
		}

		if ( ! $this->is_uploads_dir_writeable() ) {
			$this->error = __( 'Uploads directory does not exists or is not writeable', 'jnews' );
			return false;
		}

		if ( ! $this->create_local_dir() ) {
			$this->error = __( 'Error: Unable to create custom directory jnews/gtag inside uploads directory.', 'jnews' );
			return false;
		}

		return true;
	}

	/**
	 * Add a Cron Job to run every 24 hours.
	 */
	public function schedule_fetch_remote_gtag_js() {
		// Make sure this event hasn't been scheduled.
		if ( ! wp_next_scheduled( 'jnews_fetch_remote_gtag_js' ) ) {
			// Schedule the event to run daily (once).
			wp_schedule_event( time(), 'daily', 'jnews_fetch_remote_gtag_js' );
		}
	}

	/**
	 * Return google gtag url with UA Code.
	 *
	 * @return string
	 */
	private function google_gtag_url() {
		$analytics_code = get_theme_mod( 'jnews_google_analytics_code', '' );
		return 'https://www.googletagmanager.com/gtag/js?id=' . $analytics_code;
	}

	/**
	 * Fetch latest gtag.js code from remote upon Ajax Request.
	 *
	 * @uses hook: wp_ajax_jnews_get_local_gtag_js_from_remote
	 *
	 * @return void
	 */
	public function get_local_gtag_js_from_remote() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! $this->passed_security_checks() ) {

			$result['result'] = false;
			$result['error']  = $this->error;

			wp_send_json( $result );
		}

		$error = esc_html__( 'We encountered an issue grabbing the latest version of the gtag.js file from Google servers. This is usually a temporary issue so please try again later. The current file was not replaced so tracking should not be affected.', 'jnews-performance' );

		$result['result'] = false;
		$result['error']  = $error;

		$response = $this->get_remote_gtag_js_file_content( $this->google_gtag_url(), 'ajax' );

		if ( true === $response ) {
			$last_update       = jnews_get_option( 'local_gtag_file_modified_at' );
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
	 * Get the content from remote URL for gtag.
	 *
	 * @param string $url          Remote gtag URL.
	 * @param string $request_type If it is a Cron or an Ajax request. Defaults to cron.
	 *
	 * @return mixed
	 */
	public function get_remote_gtag_js_file_content( $url, $request_type = 'cron' ) {

		$response = wp_remote_get( $url );

		if ( ! is_wp_error( $response ) ) {

			$response_body = wp_remote_retrieve_body( $response );

			$hashed_remote_response = hash( 'sha512', $response_body );

			if ( strlen( $response_body ) > 0 ) {

				if ( ! $this->local_js_file_exists() ) {
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
	 * Create local JS File.
	 *
	 * @param string $type Temp or Original File. Defaults to original.
	 *
	 * @return bool
	 */
	protected function create_local_js_file( $type = 'original' ) {

		if ( ! $this->create_local_dir() ) {
			return false;
		}

		$file = $this->get_local_file( 'path', $type );

		if ( ! $this->filesystem()->is_dir( $file ) && $handle = fopen( $file, 'w+' ) ) {
			fclose( $handle );
			return true;
		}

		return false;
	}

	/**
	 * Run this function to fetch content from remote gtag URL and
	 * create a local JS file.
	 *
	 * @return void
	 */
	public function get_remote_gtag_js_content() {
		// Fetch content from remote URL and create the file.
		$result = $this->get_remote_gtag_js_file_content( $this->google_gtag_url() );
	}

	/**
	 * Do cleanup and remove Cron form WP.
	 */
	protected static function disable_cron() {
		if ( has_action( 'jnews_fetch_remote_gtag_js' ) ) {
			wp_clear_scheduled_hook( 'jnews_fetch_remote_gtag_js' );
		}
	}

	/**
	 * Check if the has not been updated since 72 hours. If that is the case
	 * then add the error/warning to admin notices.
	 *
	 * @return void
	 */
	public function report_last_modified_difference() {

		if ( ! $this->local_js_file_exists() ) {
			jnews_update_option( 'local_gtag_file_modified_at', false );
			return;
		}

		$last_modified = jnews_get_option( 'local_gtag_file_modified_at' );

		$settings_url = admin_url( '#' );

		if ( ( time() - $last_modified ) > 3 * DAY_IN_SECONDS ) {
			$this->error = sprintf(
				__( 'Hey, we noticed the local gtag file has not been updated in more than %1$s, please try a %2$smanual fetch%3$s or contact support if you need help.', 'jnews' ),
				human_time_diff( $last_modified ),
				sprintf( '<a href="%1$s">', esc_url( $settings_url ) ),
				'</a>'
			);
		}
	}

	/**
	 * Check if local JS file exists.
	 *
	 * @param string $type Temp or Original File. Defaults to original.
	 *
	 * @return bool
	 */
	protected function local_js_file_exists( $type = 'original' ) {
		return $this->filesystem()->exists( $this->get_local_file( 'path', $type ) );
	}

	/**
	 * Check if 'uploads' directory is writeable by the server.
	 *
	 * @return bool
	 */
	protected function is_uploads_dir_writeable() {
		if ( $this->filesystem()->exists( $this->uploads_dir()['basedir'] ) && $this->filesystem()->is_writable( $this->uploads_dir['basedir'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Create local directory.
	 *
	 * @var string $type Wether to create a original directory or a temp
	 *                   directory.
	 *
	 * @return bool
	 */
	protected function create_local_dir() {
		if ( wp_mkdir_p( $this->get_local_file_dir_path() ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if user wants to include the local gtag.js file.
	 *
	 * @return bool
	 */
	protected function enable_local_gtag_js() {
		$analytics_code = get_theme_mod( 'jnews_google_analytics_code', '' );
		return get_theme_mod( 'jnews_google_analytics_switch', false ) && get_theme_mod( 'jnews_google_analytics_local', false ) && get_theme_mod( 'jnews_google_analytics_gtag_local', false ) && stristr( $analytics_code, 'G-' );
	}

	/**
	 * Get path to local file. This only includes the path to the directory and not the file.
	 *
	 * @return string
	 */
	protected function get_local_file_dir_path() {
		$uploads_dir = $this->uploads_dir();
		return wp_normalize_path( $uploads_dir['basedir'] . '/' . $this->local_base_dir_name . '/' . $this->local_gtag_dir_name );
	}

	/**
	 * Get URL to local file. This only includes the URL to the directory and not the file.
	 *
	 * @return string
	 */
	protected function get_local_dir_url() {
		$uploads_dir = $this->uploads_dir();
		$baseurl     = $uploads_dir['baseurl'];
		$baseurl     = str_replace( 'http://', '//', $baseurl );
		$baseurl     = str_replace( 'https://', '//', $baseurl );

		return esc_url( $baseurl . '/' . $this->local_base_dir_name . '/' . $this->local_gtag_dir_name );
	}

	/**
	 * Get Path or URL to local file.
	 *
	 * @param string $mode What to fetch path or the URL to the file. Defaults to 'path'.
	 * @param string $type Temp or Original File. Defaults to original.
	 *
	 * @return string
	 */
	protected function get_local_file( $mode = 'path', $type = 'original' ) {
		if ( 'path' === $mode ) {
			if ( 'temp' === $type ) {
				return wp_normalize_path( $this->get_local_file_dir_path() . '/' . $this->local_gtag_temp_file_name );
			} else {
				return wp_normalize_path( $this->get_local_file_dir_path() . '/' . $this->local_gtag_file_name );
			}
		}

		return esc_url( $this->get_local_dir_url() . '/' . $this->local_gtag_file_name );
	}

	/**
	 * Check if local JS file is readable by the server user.
	 *
	 * @return bool
	 */
	protected function is_file_readable() {
		return $this->filesystem()->exists( $this->get_local_file() ) && $this->filesystem()->is_readable( $this->get_local_file() );
	}

	/**
	 * Get size of the local JS file.
	 *
	 * @return int
	 */
	protected function get_local_file_size() {
		return intval( $this->filesystem()->size( $this->get_local_file() ) );
	}

	/**
	 * Update file src from remote to local JS file URL.
	 *
	 * @param string $url Remote URL for gtag.
	 *
	 * @return string
	 */
	public function add_local_gtag_js( $url ) {
		if ( $this->enable_local_gtag_js() ) {
			if ( $this->is_file_readable() && $this->get_local_file_size() > 0 ) {
				return $this->get_local_file( 'url' );
			}
		}

		// Return original URL if anything goes wrong.
		return esc_url( $url );
	}

	/**
	 * Check if WP Filesystem is available via $wp_filesystem.
	 *
	 * @return object
	 */
	public function filesystem() {
		global $wp_filesystem;

		if ( is_null( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * Get uploads directory info.
	 *
	 * @return mixed
	 */
	public function uploads_dir() {
		if ( ! isset( $this->uploads_dir ) ) {
			$this->uploads_dir = wp_upload_dir();
		}

		return $this->uploads_dir;
	}


	/**
	 * Update last modified time of the file to site options.
	 *
	 * @return void
	 */
	protected function update_file_modified_time_option() {
		$current_time = current_time( 'timestamp' );
		jnews_update_option( 'local_gtag_file_modified_at', $current_time );
		do_action( 'jnews_after_update_settings', 'local_gtag_file_modified_at', $current_time );
	}

	/**
	 * Delete the local JS File.
	 *
	 * @param string $type Temp or Original File. Defaults to original.
	 *
	 * @return bool
	 */
	public function delete_local_gtag_js_file( $type = 'original' ) {

		if ( $this->local_js_file_exists( $type ) ) {

			return $this->filesystem()->delete( $this->get_local_file( 'path', $type ), false, 'f' );
		}

		return true;
	}

	/**
	 * Create local gtag.js file if not created.
	 *
	 * @param string $response_body          Content fetched from Google.
	 * @param string $hashed_remote_response Hashed Content fetched from Google.
	 */
	protected function create_file( $response_body, $hashed_remote_response ) {

		$local_file = $this->create_local_js_file();

		if ( $local_file ) {

			if ( $this->is_file_readable() ) {

				if ( $this->filesystem()->put_contents( $this->get_local_file(), $response_body ) ) {

					$file_contents = $this->filesystem()->get_contents( $this->get_local_file() );

					$hashed_local_file = hash( 'sha512', $file_contents );

					if ( $hashed_local_file === $hashed_remote_response ) {
						$this->update_file_modified_time_option();
						return true;
					}
				}
			}
		}

		$this->delete_local_gtag_js_file();
		return false;
	}

	/**
	 * Update local gtag.js file if already created.
	 *
	 * @param string $response_body          Content fetched from Google.
	 * @param string $hashed_remote_response Hashed Content fetched from Google.
	 */
	protected function update_file( $response_body, $hashed_remote_response ) {

		$temp_file = $this->create_local_js_file( 'temp' );

		if ( $temp_file ) {

			$temp_file = $this->get_local_file( 'path', 'temp' );

			$this->filesystem()->put_contents( $temp_file, $response_body );

			$temp_file_contents     = $this->filesystem()->get_contents( $temp_file );
			$hashed_local_temp_file = hash( 'sha512', $temp_file_contents );

			if ( $hashed_local_temp_file === $hashed_remote_response ) {

				$this->filesystem()->put_contents( $this->get_local_file(), $response_body );

				$this->update_file_modified_time_option();

				$this->delete_local_gtag_js_file( 'temp' );
				return true;
			} else {
				$this->delete_local_gtag_js_file( 'temp' );
				return false;
			}
		}

		return false;
	}
}
