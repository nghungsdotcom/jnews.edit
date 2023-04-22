<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Dashboard;

use JNews\Template;
use JNews\Util\ValidateLicense;

/**
 * Class SystemDashboard
 *
 * @package JNews\Dashboard
 */
class SystemDashboard {

	/**
	 * @var SystemDashboard
	 */
	private static $instance;

	/**
	 * @var Template
	 */
	private $template;

	/**
	 * @var bool
	 */
	private $detail = false;

	/**
	 * @var int
	 */
	private $status = 3;

	/**
	 * @var string
	 */
	private $package = 'JNews';

	/**
	 * Instance of SystemDashboard
	 *
	 * @return SystemDashboard
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * SystemDashboard constructor.
	 */
	public function __construct( $template = '' ) {
		$revert_dashboard = apply_filters( 'jnews_revert_dashboard', false );
		if ( $revert_dashboard ) {
			$this->set_template( $template );
			$this->register_hooks();
		}
	}

	/**
	 * Register Hooks
	 */
	public function register_hooks() {
		add_action( 'jnews_system_status_theme_info', array( &$this, 'theme_info' ), null, 1 );
		add_action( 'jnews_system_status_wordpress_environment', array( &$this, 'wordpress_environment' ), null, 1 );
		add_action( 'jnews_system_status_server_environment', array( &$this, 'server_environment' ), null, 1 );
		add_action( 'jnews_system_status_plugin', array( &$this, 'active_plugin' ), null, 1 );
	}

	/**
	 * @param $template
	 */
	public function set_template( $template ) {
		$this->template = $template;
	}

	/**
	 * @param $size
	 *
	 * @return false|int|string
	 */
	public function let_to_num( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}

	/**
	 * @param $code
	 *
	 * @return string
	 */
	public function status_flag( $code ) {
		$string = '';
		switch ( $code ) {
			case 'green':
				$string = esc_html__( 'Everything is Good', 'jnews' );
				break;
			case 'yellow':
				$string = esc_html__( 'This setting may not affect your website entirely, but it will cause some of the features not working as expected.', 'jnews' );
				break;
			case 'red':
				$string = esc_html__( 'You will need to fix this setting to make themes & plugin work as expected.', 'jnews' );
				break;
		}
		return "<div class='tooltip flag-item flag-{$code}' title='{$string}'></div>";
	}

	/**
	 * @param $options
	 * @param $html
	 */
	public function status_render( $options, $html ) {
		if ( $html ) {
			foreach ( $options as $option ) {
				$option['self'] = $this;
				if ( 'status' === $option['type'] ) {
					$this->template->render( 'system-status-help', $option, true );
				} elseif ( 'flag' === $option['type'] ) {
					$this->template->render( 'system-status-flag', $option, true );
				}
			}
		} else {
			foreach ( $options as $option ) {
				$option['self'] = $this;
				$this->template->render( 'system-status-text', $option, true );
			}
		}
	}
	/**
	 * Data active plugin
	 *
	 * @param bool $html Check if content HTML.
	 *
	 * @return array
	 */
	public function data_active_plugin( $html = false ) {
		$active_plugin = array();

		$plugins = array_merge(
			array_flip( (array) get_option( 'active_plugins', array() ) ),
			(array) get_site_option( 'active_sitewide_plugins', array() )
		);

		if ( $plugins = array_intersect_key( get_plugins(), $plugins ) ) {
			foreach ( $plugins as $plugin ) {
				$item                = array();
				$item['uri']         = isset( $plugin['PluginURI'] ) ? esc_url( $plugin['PluginURI'] ) : '#';
				$item['name']        = isset( $plugin['Name'] ) ? $plugin['Name'] : esc_html__( 'unknown', 'jnews' );
				$item['author_uri']  = isset( $plugin['AuthorURI'] ) ? esc_url( $plugin['AuthorURI'] ) : '#';
				$item['author_name'] = isset( $plugin['Author'] ) ? $plugin['Author'] : esc_html__( 'unknown', 'jnews' );
				$item['version']     = isset( $plugin['Version'] ) ? $plugin['Version'] : esc_html__( 'unknown', 'jnews' );

				if ( $html ) {
					$content = esc_html__( 'by', 'jnews' ) . ' ' . "<a href='{$item['author_uri']}'>" . $item['author_name'] . '</a>' . ' - ' . $item['version'];
				} else {
					$content = esc_html__( 'by', 'jnews' );
				}

				$active_plugin[] = array(
					'type'            => 'status',
					'title'           => $item['name'],
					'content'         => $content,
					'link'            => $item['author_uri'],
					'link_text'       => $item['author_name'],
					'additional_text' => $item['version'],
				);
			}
		}

		return $active_plugin;
	}

	/**
	 * Render active plugin
	 *
	 * @param bool $html Check if content HTML.
	 */
	public function active_plugin( $html = true ) {
		$active_plugin = $this->data_active_plugin( $html );
		$this->status_render( $active_plugin, $html );
	}

	/**
	 * Data server environment
	 *
	 * @param bool $html Check if content HTML.
	 *
	 * @return array
	 */
	public function data_server_environment( $html = false ) {
		$server = array();

		$server[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Server Info', 'jnews' ),
			'tooltip' => esc_html__( 'Information about the web server that is currently hosting your site', 'jnews' ),
			'content' => jnews_server_info(),
		);

		if ( function_exists( 'phpversion' ) ) {
			$php_version = PHP_VERSION;

			if ( version_compare( $php_version, '7.4', '<' ) ) {

				$content = '<mark class="error">' . sprintf( __( '%1$s - We recommend a minimum PHP version of 7.4. See: %2$s', 'jnews' ), esc_html( $php_version ), esc_html( $php_version ) ) . '</mark>';

				if ( ! $html ) {
					$content = sprintf( __( '%1$s - We recommend a minimum PHP version of 7.4.', 'jnews' ), esc_html( $php_version ) );
				}

				$server[] = array(
					'mark'    => 'error',
					'type'    => 'flag',
					'title'   => esc_html__( 'PHP Version', 'jnews' ),
					'flag'    => 'red',
					'content' => $content,
				);
			} else {
				$content = '<mark class="yes">' . esc_html( $php_version ) . '</mark>';

				if ( ! $html ) {
					$content = esc_html( $php_version );
				}

				$server[] = array(
					'mark'    => 'yes',
					'type'    => 'flag',
					'title'   => esc_html__( 'PHP Version', 'jnews' ),
					'flag'    => 'green',
					'content' => $content,
				);
			}
		} else {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP Version', 'jnews' ),
				'flag'    => 'red',
				'content' => esc_html__( "Couldn't determine PHP version because phpversion() doesn't exist", 'jnews' ),
			);
		}

		if ( function_exists( 'ini_get' ) ) {

			$server[] = array(
				'type'    => 'status',
				'title'   => esc_html__( 'PHP Post Max Size', 'jnews' ),
				'tooltip' => esc_html__( 'The largest filesize that can be contained in one post', 'jnews' ),
				'content' => size_format( $this->let_to_num( ini_get( 'post_max_size' ) ) ),
			);

			$maxtime      = ini_get( 'max_execution_time' );
			$maxtimelimit = 3000;

			if ( $maxtime >= $maxtimelimit ) {
				$server[] = array(
					'type'    => 'flag',
					'title'   => esc_html__( 'PHP Time Limit', 'jnews' ),
					'flag'    => 'green',
					'content' => $maxtime,
				);
			} else {
				$server[] = array(
					'type'    => 'flag',
					'title'   => esc_html__( 'PHP Time Limit', 'jnews' ),
					'flag'    => 'yellow',
					'content' => $maxtime,
					'small'   => sprintf( esc_html__( 'max_execution_time should be bigger than %s, otherwise import process may not finished as expected', 'jnews' ), $maxtimelimit ),
					'mark'    => 'error',
				);
			}

			$maxinput      = ini_get( 'max_input_vars' );
			$maxinputlimit = 2000;

			if ( $maxinput >= $maxinputlimit ) {
				$server[] = array(
					'type'    => 'flag',
					'title'   => esc_html__( 'PHP Max Input Vars', 'jnews' ),
					'flag'    => 'green',
					'content' => $maxinput,
				);
			} else {
				$server[] = array(
					'type'    => 'flag',
					'title'   => esc_html__( 'PHP Max Input Vars', 'jnews' ),
					'flag'    => 'yellow',
					'content' => $maxinput,
					'small'   => sprintf( esc_html__( 'max_input_vars should be bigger than %s, otherwise you may not able to save setting on option panel', 'jnews' ), $maxinputlimit ),
					'mark'    => 'error',
				);
			}

			$server[] = array(
				'type'    => 'status',
				'title'   => esc_html__( 'SUHOSIN Installed', 'jnews' ),
				'tooltip' => esc_html__( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself. If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'jnews' ),
				'content' => extension_loaded( 'suhosin' ) ? '&#10004;' : '&ndash;',
			);
		}

		// WP Remote Get
		$response = @wp_remote_get( 'http://api.wordpress.org/plugins/update-check/1.1/' );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$wp_remote_get       = true;
			$wp_remote_get_error = '';
			$wp_remote_get_flag  = 'green';
		} else {
			$wp_remote_get       = false;
			$wp_remote_get_error = $response->get_error_code() . ' - ' . $response->get_error_message();
			$wp_remote_get_flag  = 'yellow';
		}

		$server[] = array(
			'type'    => 'flag',
			'title'   => esc_html__( 'WP Remote Get', 'jnews' ),
			'flag'    => $wp_remote_get_flag,
			'tooltip' => esc_html__( 'Some features of JNews need WP remote to be installed properly. Including demo importer and validated license.', 'jnews' ),
			'content' => $wp_remote_get ? '&#10004;' : $wp_remote_get_error,
		);

		/** check if GD or Imagick installed */
		$imagick_installed = extension_loaded( 'imagick' );

		if ( $imagick_installed ) {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP Image library installed ', 'jnews' ),
				'flag'    => 'green',
				'content' => '&#10004;',
			);
		} else {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP Image library installed ', 'jnews' ),
				'flag'    => 'red',
				'content' => esc_html__( 'Please install PHP image Magic library ', 'jnews' ),
			);
		}

		$gd_installed       = extension_loaded( 'gd' ) && function_exists( 'gd_info' );
		$fileinfo_installed = extension_loaded( 'fileinfo' ) && ( function_exists( 'finfo_open' ) || function_exists( 'mime_content_type' ) );

		if ( $gd_installed ) {
			$gd_support = array();
			foreach ( gd_info() as $key => $value ) {
				$gd_support[ $key ] = $value;
			}
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP GD library installed ', 'jnews' ),
				'flag'    => 'green',
				'content' => '&#10004;',
			);
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP GD WebP supported', 'jnews' ),
				'flag'    => $gd_support['WebP Support'] && $imagick_installed ? 'green' : 'red',
				'content' => $gd_support['WebP Support'] && $imagick_installed ? '&#10004;' : esc_html__( 'Please install Image Magic & GD Library', 'jfp' ),
			);
		} else {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP GD library installed ', 'jnews' ),
				'flag'    => 'red',
				'content' => esc_html__( 'Please install PHP Image Magic & GD library', 'jnews' ),
			);
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP GD WebP supported', 'jnews' ),
				'flag'    => 'red',
				'content' => esc_html__( 'Please install Image Magic & GD Library', 'jfp' ),
			);
		}

		if ( $fileinfo_installed ) {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP fileinfo library installed ', 'jnews' ),
				'flag'    => 'green',
				'content' => '&#10004;',
			);
		} else {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP fileinfo library installed ', 'jnews' ),
				'flag'    => 'red',
				'content' => esc_html__( 'Please install PHP fileinfo library (fileinfo package)', 'jnews' ),
			);
		}

		/** check if CURL Installed */

		$curl_installed = extension_loaded( 'curl' ) && function_exists( 'curl_version' );

		if ( $curl_installed ) {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'CURL Installed ', 'jnews' ),
				'flag'    => 'green',
				'content' => '&#10004;',
			);
		} else {
			$server[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'CURL Installed ', 'jnews' ),
				'flag'    => 'yellow',
				'content' => esc_html__( 'Please install CURL PHP library', 'jnews' ),
			);
		}

		return $server;
	}

	/**
	 * Render server environment
	 *
	 * @param bool $html Check if content HTML.
	 */
	public function server_environment( $html = true ) {
		$server = $this->data_server_environment( $html );
		$this->status_render( $server, $html );
	}

	/**
	 * Data status flag
	 *
	 * @param string $code Color flag.
	 *
	 * @return string
	 */
	public function data_system_status_flag( $code ) {
		$string = '';
		switch ( $code ) {
			case 'green':
				$string = esc_html__( 'Everything is Good', 'jnews' );
				break;
			case 'yellow':
				$string = esc_html__( 'This setting may not affect your website entirely, but it will cause some of the features not working as expected.', 'jnews' );
				break;
			case 'red':
				$string = esc_html__( 'You will need to fix this setting to make themes & plugin work as expected.', 'jnews' );
				break;
		}
		return $string;
	}

	/**
	 * Print status flag
	 *
	 * @param $code
	 * @return string
	 */
	public function system_status_flag( $code ) {
		$string = $this->data_system_status_flag( $code );
		return "<div class='tooltip flag-item flag-{$code}' title='{$string}'></div>";
	}

	/**
	 * Data WordPress environment
	 *
	 * @param bool $html Check if content HTML.
	 *
	 * @return array
	 */
	public function data_wordpress_environment( $html = false ) {
		$wpenvironment = array();

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Home URL', 'jnews' ),
			'tooltip' => esc_html__( 'The URL of your site\'s homepage', 'jnews' ),
			'content' => esc_url( home_url( '/' ) ),
		);

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Site URL', 'jnews' ),
			'tooltip' => esc_html__( 'The root URL of your site', 'jnews' ),
			'content' => esc_url( site_url() ),
		);

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Login URL', 'jnews' ),
			'tooltip' => esc_html__( 'your website login url', 'jnews' ),
			'content' => esc_url( wp_login_url() ),
		);

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'WP Version', 'jnews' ),
			'tooltip' => esc_html__( 'The version of WordPress installed on your site', 'jnews' ),
			'content' => get_bloginfo( 'version', 'display' ),
		);

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'WP Multisite', 'jnews' ),
			'tooltip' => esc_html__( 'Whether or not you have WordPress Multisite enabled', 'jnews' ),
			'content' => is_multisite() ? '&#10004;' : '&ndash;',
		);

		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			$wpenvironment[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'WP Debug Mode', 'jnews' ),
				'flag'    => 'yellow',
				'content' => esc_html__( 'Enabled', 'jnews' ),
				'small'   => esc_html__( 'Only enable WP DEBUG if you are on development server, once on production server, you will need to disable WP Debug', 'jnews' ),
			);
		} else {
			$wpenvironment[] = array(
				'type'    => 'flag',
				'title'   => esc_html__( 'WP Debug Mode', 'jnews' ),
				'flag'    => 'green',
				'content' => esc_html__( 'Disabled', 'jnews' ),
				'small'   => esc_html__( 'Only enable WP DEBUG if you are on development server, once on production server, you will need to disable WP Debug', 'jnews' ),
			);
		}

		$memory         = $this->let_to_num( WP_MEMORY_LIMIT );
		$minmemorylimit = 134217728;

		if ( function_exists( 'memory_get_usage' ) ) {
			$system_memory = $this->let_to_num( @ini_get( 'memory_limit' ) );

			if ( $system_memory >= $minmemorylimit ) {
				$content = '<mark class="yes">' . size_format( $system_memory ) . '</mark>';
				if ( ! $html ) {
					$content = size_format( $system_memory );
				}
				$color = 'green';
			} else {
				$content = '<mark class="error">' . sprintf( __( '%1$s - We recommend setting memory to at least 128MB. See: %2$s', 'jnews' ), size_format( $system_memory ), '<a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . esc_html__( 'Increasing memory allocated to PHP', 'jnews' ) . '</a>' ) . '</mark>';
				if ( ! $html ) {
					$content = sprintf( __( '%s - We recommend setting memory to at least 128MB. See: ', 'jnews' ), size_format( $system_memory ) );
				}
				$color = 'yellow';
			}

			$temp_data = array(
				'mark'    => $system_memory >= $minmemorylimit ? 'yes' : 'error',
				'type'    => 'flag',
				'title'   => esc_html__( 'PHP Memory Limit', 'jnews' ),
				'flag'    => $color,
				'content' => $content,
			);

			if ( ! ( $system_memory >= $minmemorylimit ) ) {
				$temp_data['link']      = 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP';
				$temp_data['link_text'] = esc_html__( 'Increasing memory allocated to PHP', 'jnews' );
			}

			$wpenvironment[] = $temp_data;
		}

		if ( $memory >= $minmemorylimit ) {
			$content = '<mark class="yes">' . size_format( $memory ) . '</mark>';
			if ( ! $html ) {
				$content = size_format( $memory );
			}
			$color = 'green';
		} else {
			$content = '<mark class="error">' . sprintf( __( '%1$s - We recommend setting memory to at least 128MB. See: %2$s', 'jnews' ), size_format( $memory ), '<a href="http://support.jegtheme.com/documentation/system-status/#memory-limit" target="_blank">' . esc_html__( 'Increasing the WordPress Memory Limit', 'jnews' ) . '</a>' ) . '</mark>';
			if ( ! $html ) {
				$content = sprintf( __( '%s - We recommend setting memory to at least 128MB. See: ', 'jnews' ), size_format( $memory ) );
			}
			$color = 'yellow';
		}

		$temp_data = array(
			'mark'    => $memory >= $minmemorylimit ? 'yes' : 'error',
			'type'    => 'flag',
			'title'   => esc_html__( 'WP Memory Limit', 'jnews' ),
			'flag'    => $color,
			'content' => $content,
		);

		if ( ! ( $memory >= $minmemorylimit ) ) {
			$temp_data['link']      = 'http://support.jegtheme.com/documentation/system-status/#memory-limit';
			$temp_data['link_text'] = esc_html__( 'Increasing the WordPress Memory Limit', 'jnews' );
		}

		$wpenvironment[] = $temp_data;

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'WP Language', 'jnews' ),
			'flag'    => 'green',
			'content' => get_locale(),
			'tooltip' => esc_html__( 'Default Language of your WordPress Installation', 'jnews' ),
		);

		$wp_upload_dir = wp_upload_dir();

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'WP Upload Directory', 'jnews' ),
			'flag'    => 'green',
			'content' => wp_is_writable( $wp_upload_dir['basedir'] ) ? '&#10004;' : '&ndash;',
			'tooltip' => esc_html__( 'Determine if upload directory is writable', 'jnews' ),
		);

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Number of Category', 'jnews' ),
			'flag'    => 'green',
			'content' => wp_count_terms( 'category' ),
			'tooltip' => esc_html__( 'The current number of post category on your site', 'jnews' ),
		);

		$wpenvironment[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Number of Tag', 'jnews' ),
			'flag'    => 'green',
			'content' => wp_count_terms( 'post_tag' ),
			'tooltip' => esc_html__( 'The current number of post tag on your site', 'jnews' ),
		);

		return $wpenvironment;
	}

	/**
	 * Render WordPress environment
	 *
	 * @param bool $html Check if content HTML.
	 */
	public function wordpress_environment( $html = true ) {
		$wpenvironment = $this->data_wordpress_environment( $html );
		$this->status_render( $wpenvironment, $html );
	}

	/**
	 * Data theme info
	 *
	 * @return array
	 */
	public function data_theme_info() {
		$themeinfo     = array();
		$theme         = wp_get_theme();
		$theme_license = ValidateLicense::getInstance()->is_license_validated();

		// Theme name.
		$themeinfo[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Themes Name', 'jnews' ),
			'tooltip' => esc_html__( 'Themes currently installed & activated', 'jnews' ),
			'content' => $theme->get( 'Name' ),
		);

		// Theme version.
		$themeinfo[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Themes Version', 'jnews' ),
			'tooltip' => esc_html__( 'Current theme version', 'jnews' ),
			'content' => $theme->get( 'Version' ),
		);

		// Theme parent.
		if ( is_child_theme() ) {
			$themeinfo[] = array(
				'type'    => 'status',
				'title'   => esc_html__( 'Themes Parent', 'jnews' ),
				'tooltip' => esc_html__( 'Current parent theme version', 'jnews' ),
				'content' => wp_get_theme( 'jnews' )->get( 'Version' ),
			);
		}

		// Theme license.
		$themeinfo[] = array(
			'type'    => 'status',
			'title'   => esc_html__( 'Themes License', 'jnews' ),
			'tooltip' => esc_html__( 'Theme license registration', 'jnews' ),
			'content' => $theme_license ? '&#10004;' : '&ndash;',
		);

		if ( $this->detail ) {
			// Theme license.
			$themeinfo[] = array(
				'type'    => 'status',
				'title'   => esc_html__( 'License Code', 'jnews' ) . ' ' . $this->status,
				'tooltip' => esc_html__( 'Theme License Code', 'jnews' ),
				'content' => ValidateLicense::getInstance()->get_token(),
			);
		}

		return $themeinfo;
	}

	/**
	 * Render theme info
	 *
	 * @param bool $html Check if content HTML.
	 */
	public function theme_info( $html = true ) {
		$themeinfo = $this->data_theme_info();
		$this->status_render( $themeinfo, $html );
	}

	public function backend_status( $detail = false ) {
		$this->detail = $detail;
		?>

			<pre>### THEME INFO ###

		<?php do_action( 'jnews_system_status_theme_info', false ); ?>


### WordPress Environment ###

		<?php do_action( 'jnews_system_status_wordpress_environment', false ); ?>


### Server Environment ###

		<?php do_action( 'jnews_system_status_server_environment', false ); ?>


### Active Plugins ###

		<?php do_action( 'jnews_system_status_plugin', false ); ?>

### End ###</pre>

			<?php
			exit;
	}

	/**
	 * Register tooltip flag
	 *
	 * @param array $options Data.
	 *
	 * @return array
	 */
	public function register_tooltip_flag( $options ) {
		foreach ( $options as &$data ) {
			if ( 'flag' === $data['type'] ) {
				$data['tooltip'] = $this->data_system_status_flag( $data['flag'] );
			}
		}
		return $options;
	}

	/**
	 * Dashboard import config
	 *
	 * @return array
	 */
	public function jnews_dashboard_config() {
		if ( ! function_exists( 'wp_clean_plugins_cache' ) ) {
			/**
			 * Uncaught Error: Call to undefined function JNews\Util\wp_clean_plugins_cache()
			 * when call it from Rest API
			 */
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return array(
			'theme_info'            => array(
				'title' => esc_html__( 'Themes Info', 'jnews' ),
				'data'  => $this->register_tooltip_flag( $this->data_theme_info() ),
			),
			'wordpress_environment' => array(
				'title' => esc_html__( 'WordPress Environment', 'jnews' ),
				'data'  => $this->register_tooltip_flag( $this->data_wordpress_environment() ),
			),
			'server_environment'    => array(
				'title' => esc_html__( 'Server Environment', 'jnews' ),
				'data'  => $this->register_tooltip_flag( $this->data_server_environment() ),
			),
			'active_plugin'         => array(
				'title' => esc_html__( 'Active Plugins', 'jnews' ),
				'data'  => $this->register_tooltip_flag( $this->data_active_plugin() ),
			),
			'end'                   => array(
				'title' => esc_html__( 'END', 'jnews' ),
				'data'  => array(),
			),
		);
	}

	/**
	 * START Revert Dashboard
	 */
	public function system_status() {
		$this->template->render( 'system-status', null, true );
	}
	/**
	 * END Revert Dashboard
	 */
}
