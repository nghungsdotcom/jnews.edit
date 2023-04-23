<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Deactivator
 */
class Deactivator {

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @global  object  wpbd
	 * @param   bool    network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public static function deactivate( $network_wide ) {
		global $wpdb;

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// Run deactivation for each blog in the network
			if ( $network_wide ) {
				$original_blog_id = get_current_blog_id();
				$blogs_ids        = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

				foreach ( $blogs_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::plugin_deactivate();
				}

				// Switch back to current blog
				switch_to_blog( $original_blog_id );

				return;
			}
		}

		self::plugin_deactivate();
	}

	/**
	 * On plugin deactivation, disables the shortcode and removes the scheduled task.
	 */
	private static function plugin_deactivate() {
		Helper::update_general_option( 'version', false );
		wp_clear_scheduled_hook( 'jnews_view_counter_cache_event' );
	}
}
