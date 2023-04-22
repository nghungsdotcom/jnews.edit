<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Util;

/**
 * Updater
 */
class Updater {

	/**
	 * Class instance
	 *
	 * @var Updater
	 */
	private static $instance;

	/**
	 * Return class instance
	 *
	 * @return Updater
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
		add_action( 'admin_init', array( $this, 'schedule_update_plugins' ) );
		add_action( 'jnews_update_plugins', array( $this, 'update_plugins' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'transient_update_plugins' ) );
		add_filter( 'pre_set_transient_update_plugins', array( $this, 'transient_update_plugins' ) );
	}

	/**
	 * Register custom schedule event for plugins update checker
	 */
	public function schedule_update_plugins() {
		if ( ! wp_next_scheduled( 'jnews_update_plugins' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'jnews_update_plugins' );
		}
	}

	/**
	 * Provide custom schedule update status for themes
	 */
	public function update_plugins() {
		$transient = get_site_transient( 'update_plugins' );
		$transient = $this->transient_update_plugins( $transient );

		set_site_transient( 'update_plugins', $transient );
	}

	/**
	 * Provide update status for plugins
	 *
	 * @param mixed $transient
	 *
	 * @return mixed
	 */
	public function transient_update_plugins( $transient ) {
		if ( ! is_object( $transient ) ) {
			$transient = new \stdClass();
		}
		if ( ! function_exists( 'get_plugins' ) ) {
			/**
			 * Uncaught Error: Call to undefined function JNews\Util\wp_clean_plugins_cache()
			 * when call it from Rest API
			 */
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins     = \JNews\Util\Api\Plugin::get_bundle_plugin_list();
		$all_plugins = get_plugins();

		foreach ( $plugins as $slug => $plugin ) {
			$activated_plugin   = isset( $all_plugins[ $plugin['file'] ] ) && version_compare( $all_plugins[ $plugin['file'] ]['Version'], $plugin['version'], '<' );
			$deactivated_plugin = false;
			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin['file'] ) ) {
				$plugin_from_file   = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin['file'] );
				$deactivated_plugin = version_compare( $plugin_from_file['Version'], $plugin['version'], '<' );
			}
			if ( $activated_plugin || $deactivated_plugin ) {
				$package      = '';
				$license_data = jnews_get_license();

				if ( $license_data ) {
					$package = add_query_arg(
						array(
							'domain'        => home_url(),
							'purchase_code' => $license_data['purchase_code'],
							'name'          => isset( $plugin['source'] ) ? $plugin['source'] : $slug,
							'type'          => 'plugin',
						),
						JNEWS_THEME_SERVER . '/wp-json/jnews-server/v1/getJNewsData'
					);
				}

				$transient->response[ $plugin['file'] ] = (object) array(
					'id'          => $slug,
					'slug'        => $slug,
					'new_version' => $plugin['version'],
					'package'     => $package,
					'url'         => '',
				);
				unset( $transient->no_update[ $plugin['file'] ] );
			}
		}

		return $transient;
	}
}
