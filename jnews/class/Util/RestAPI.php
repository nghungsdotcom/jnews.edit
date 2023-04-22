<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Util;

use JNews\Dashboard\AdminDashboard;
use JNews\Dashboard\SystemDashboard;
use JNews\Util\Api\Importer;
use JNews\Util\Api\Plugin;

/**
 * Rest API
 */
class RestAPI {

	/**
	 * Endpoint Path
	 *
	 * @var string
	 */
	const ENDPOINT = 'jnews/v1';

	/**
	 * Class instance
	 *
	 * @var Api
	 */
	private static $instance;

	/**
	 * Return class instance
	 *
	 * @return Api
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Class constructor
	 */
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'prepare_assets' ) );
	}

	/**
	 * Prepare assets.
	 */
	public function prepare_assets() {
		$theme = wp_get_theme();
		wp_register_script( 'jnews-hash', apply_filters( 'jnews_get_asset_uri', get_parent_theme_file_uri( 'assets/' ) ) . 'js/admin/jnews.hash.min.js', array(), $theme->get( 'Version' ) );
		wp_register_script( 'jnews-essential-local', apply_filters( 'jnews_get_asset_uri', get_parent_theme_file_uri( 'assets/' ) ) . 'js/admin/jnews-essential.local.js', array( 'jquery', 'jnews-hash', 'wp-api-fetch', 'wp-util' ), $theme->get( 'Version' ) );
		$ls_data             = jnews_get_license();
		$home_url            = home_url();
		$jnews_dashboard_url = menu_page_url( 'jnews', false );
		$callback            = str_replace( $home_url, '', $jnews_dashboard_url );
		$domain              = jnews_get_domain( $home_url );
		$server_url          = add_query_arg(
			array(
				'siteurl'  => $home_url,
				'callback' => $callback,
				'item_id'  => JNEWS_THEME_ID,
			),
			JEGTHEME_SERVER . '/activate/'
		);

		$ls_var = array(
			'domain'     => $domain,
			'url'        => get_site_url(),
			'restUrl'    => get_rest_url(),
			'api'        => JEGTHEME_SERVER,
			'activation' => $server_url,
			'nonce'      => wp_create_nonce( 'wp_rest' ),
		);

		if ( $ls_data && isset( $ls_data['purchase_code'] ) ) {
			$ls_var['license'] = $ls_data['purchase_code'];
		}

		wp_localize_script(
			'jnews-essential-local',
			'jnewsEssential',
			$ls_var
		);
	}

	/**
	 * Register API
	 *
	 * @return void
	 */
	public function register_routes() {
		// Config
		register_rest_route(
			self::ENDPOINT,
			'getDashboardConfig',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_dashboard_config' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);

		// Panel.
		register_rest_route(
			self::ENDPOINT,
			'savePanelOptions',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_panel_options' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);
		register_rest_route(
			self::ENDPOINT,
			'restorePanelOptions',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'restore_panel_options' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);
		register_rest_route(
			self::ENDPOINT,
			'importPanelOptions',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'import_panel_options' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);
		register_rest_route(
			self::ENDPOINT,
			'exportPanelOptions',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'export_panel_options' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);

		// Plugin.
		register_rest_route(
			self::ENDPOINT,
			'getPlugins',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_plugins' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			self::ENDPOINT,
			'validatePlugin',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'validate_plugin' ),
				'permission_callback' => array( $this, 'permission_install_plugin' ),
			)
		);
		register_rest_route(
			self::ENDPOINT,
			'checkPluginRemote',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'check_plugin_remote' ),
				'permission_callback' => array( $this, 'permission_install_plugin' ),
			)
		);
		register_rest_route(
			self::ENDPOINT,
			'managePlugin',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'manage_plugin' ),
				'permission_callback' => array( $this, 'permission_install_plugin' ),
			)
		);

		// Lincese.
		register_rest_route(
			self::ENDPOINT,
			'resetLicense',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'reset_license' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);

		register_rest_route(
			self::ENDPOINT,
			'getValidateNoticeLength',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_validate_notice_length' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);

		// Import Demo.
		register_rest_route(
			self::ENDPOINT,
			'manageDemo',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'manage_demo' ),
				'permission_callback' => array( $this, 'permission_manage_options' ),
			)
		);

	}

	/**
	 * Check permission manage options
	 *
	 * @return bool
	 */
	public function permission_manage_options() {
		return function_exists( 'jnews_permission_manage_options' ) ? jnews_permission_manage_options() : current_user_can( 'manage_options' );
	}

	/**
	 * Get Dashboard Config
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return array
	 */
	public function get_dashboard_config( $request ) {
		$config = sanitize_key( $request->get_param( 'config' ) );
		$nonce  = sanitize_key( $request->get_param( 'nonce' ) );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) || empty( $config ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		if ( 'system' === $config ) {
			$system = SystemDashboard::get_instance();
			return $this->response_success( $system->jnews_dashboard_config() );
		} elseif ( 'all' === $config ) {
			return $this->response_success( AdminDashboard::jnews_dashboard() );
		}
	}

	/**
	 * Save Panel Options
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return \WP_REST_Response|array
	 */
	public function save_panel_options( $request ) {
		$action      = sanitize_key( $request->get_param( 'action' ) );
		$nonce       = sanitize_key( $request->get_param( 'nonce' ) );
		$panel_nonce = sanitize_key( $request->get_param( 'panelNonce' ) );
		$options     = $request->get_param( 'options' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) || empty( $panel_nonce ) || empty( $options ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		return $this->response_success( apply_filters( 'jnews_panel_request_save', $options, $action, $panel_nonce ) );
	}

	/**
	 * Restore Panel Options
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return \WP_REST_Response|array
	 */
	public function restore_panel_options( $request ) {
		$action      = sanitize_key( $request->get_param( 'action' ) );
		$nonce       = sanitize_key( $request->get_param( 'nonce' ) );
		$panel_nonce = sanitize_key( $request->get_param( 'panelNonce' ) );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) || empty( $panel_nonce ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		return $this->response_success( apply_filters( 'jnews_panel_request_restore', $action, $panel_nonce ) );
	}

	/**
	 * Import Panel Options
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return \WP_REST_Response|array
	 */
	public function import_panel_options( $request ) {
		$action      = sanitize_key( $request->get_param( 'action' ) );
		$nonce       = sanitize_key( $request->get_param( 'nonce' ) );
		$panel_nonce = sanitize_key( $request->get_param( 'panelNonce' ) );
		$options     = $request->get_param( 'options' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) || empty( $panel_nonce ) || empty( $options ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		return $this->response_success( apply_filters( 'jnews_panel_request_import_option', $options, $action, $panel_nonce ) );
	}

	/**
	 * Export Panel Options
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return \WP_REST_Response|array
	 */
	public function export_panel_options( $request ) {
		$action      = sanitize_key( $request->get_param( 'action' ) );
		$nonce       = sanitize_key( $request->get_param( 'nonce' ) );
		$panel_nonce = sanitize_key( $request->get_param( 'panelNonce' ) );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) || empty( $panel_nonce ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		return $this->response_success( apply_filters( 'jnews_panel_request_export_option', $action, $panel_nonce ) );
	}

	/**
	 * Reset license handler
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 */
	public function reset_license( $request ) {
		$purchase_code = $request->get_param( 'code' );
		if ( ! empty( $purchase_code ) ) {
			jnews_reset_license();
		}
	}

	/**
	 * Get Validate Notice Length
	 *
	 * @return int
	 */
	public function get_validate_notice_length() {
		return \JNews\Util\ValidateLicense::check_validate_notice_length();
	}

	/**
	 * Get Plugins
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return array
	 */
	public function get_plugins( $request ) {
		$plugins       = Plugin::get_plugin_list();
		$plugin_groups = Plugin::get_plugin_group();

		return $this->response_success(
			array(
				'plugins' => $plugins,
				'groups'  => $plugin_groups,
			)
		);
	}

	/**
	 * Validate plugin
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return \WP_Error|array
	 */
	public function validate_plugin( $request ) {
		$plugins = $request->get_param( 'plugins' );
		$nonce   = sanitize_key( $request->get_param( 'nonce' ) );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		return $this->response_success( Plugin::validate_plugin( $plugins ) );
	}

	/**
	 * Check plugin remote
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return \WP_Error|array
	 */
	public function check_plugin_remote( $request ) {
		$slug   = $request->get_param( 'slug' );
		$source = $request->get_param( 'source' );
		$nonce  = sanitize_key( $request->get_param( 'nonce' ) );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		$result = '';
		if ( ! empty( $source ) ) {
			if ( file_exists( JNEWS_THEME_DIR_PLUGIN . $source ) ) {
				$result = 'bundle';
			} else {
				$result = 'server';
			}
		} else {
			if ( is_wp_error( Plugin::retrieve_plugin_source( $slug ) ) ) {
				$result = 'server';
			} else {
				$result = 'remote';
			}
		}

		return $this->response_success( $result );
	}

	/**
	 * Manage give plugin
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return \WP_Error|array
	 */
	public function manage_plugin( $request ) {
		$from          = sanitize_key( $request->get_param( 'from' ) );
		$nonce         = sanitize_key( $request->get_param( 'nonce' ) );
		$doing         = sanitize_key( $request->get_param( 'doing' ) );
		$plugin        = $request->get_param( 'plugin' );
		$plugin_source = isset( $plugin['source'] ) ? $plugin['source'] : false;
		$plugin        = array_map( 'sanitize_text_field', $plugin );
		if ( $plugin_source ) {
			$plugin['source'] = $plugin_source;
		}
		if ( ! wp_verify_nonce( $nonce, 'wp_rest', false ) ) {
			return $this->response_error( esc_html__( 'You are not allowed to perform this action.', 'jnews' ) );
		}
		if ( isset( $plugin['refresh'] ) && '1' === $plugin['refresh'] ) {
			$plugin['refresh'] = true;
		}
		return $this->response_success( Plugin::manage_plugin( $plugin, $this, $doing, $from ) );
	}

	/**
	 * Manage import demo
	 *
	 * @param \WP_REST_Request $request Core class used to implement a REST request object.
	 *
	 * @return boolean|array
	 */
	public function manage_demo( $request ) {
		$id       = sanitize_text_field( $request->get_param( 'id' ) );
		$action   = sanitize_text_field( $request->get_param( 'action' ) );
		$step     = sanitize_text_field( $request->get_param( 'step' ) );
		$option   = $request->get_param( 'option' );
		$demo     = $request->get_param( 'demo' );
		$data     = $request->get_param( 'data' );
		$importer = new Importer( $id, $action, $step, $option, $data, $demo );
		$result   = $importer->prepare_import();

		return $this->response_success( $result ? $result : true );
	}

	/**
	 * Check user permissions
	 *
	 * @return boolean
	 */
	public function permission_install_plugin() {
		return current_user_can( 'install_plugins' );
	}

	/**
	 * Return success response
	 *
	 * @param array $args
	 *
	 * @return \WP_REST_Response
	 */
	public function response_success( $args ) {
		return new \WP_REST_Response( $args, 200 );
	}

	/**
	 * Return error response
	 *
	 * @param string $message Error message
	 *
	 * @return \WP_REST_Response
	 */
	public function response_error( $message ) {
		return new \WP_REST_Response(
			array(
				'message' => $message,
			),
			500
		);
	}
}
