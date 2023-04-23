<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Activator
 */
class Activator {
	/**
	 * Data Table
	 *
	 * @return string
	 */
	public static function get_data_table() {
		global $wpdb;

		return $wpdb->prefix . JNEWS_VIEW_COUNTER_DB_DATA;
	}

	/**
	 * Summary Table
	 *
	 * @return string
	 */
	public static function get_summary_table() {
		global $wpdb;

		return $wpdb->prefix . JNEWS_VIEW_COUNTER_DB_SUMMARY;
	}

	/**
	 * Fired when the plugin is activated and check if plugin uses Network Activate.
	 *
	 * @param    bool $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 * @global   object  $wpdb
	 */
	public static function activate( $network_wide ) {
		register_uninstall_hook( JNEWS_VIEW_COUNTER_FILE, array( 'JNEWS_VIEW_COUNTER\Deactivator', 'deactivate' ) );

		global $wpdb;

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// run activation for each blog in the network
			if ( $network_wide ) {
				$original_blog_id = get_current_blog_id();
				$blogs_ids        = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

				foreach ( $blogs_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::plugin_activate();
				}

				// switch back to current blog
				switch_to_blog( $original_blog_id );

				return;
			}
		}

		self::plugin_activate();
	}

	public static function track_new_site() {
		self::plugin_activate();
	}

	private static function plugin_activate() {
		if ( wp_get_theme() == 'JNews' || wp_get_theme()->parent() == 'JNews' ) {
			$version = Helper::get_general_option( 'version' );

			if ( ! $version || version_compare( $version, JNEWS_VIEW_COUNTER_VERSION, '<' ) ) {
				self::do_db_tables();
			}
		}
	}

	public static function do_db_tables() {
		global $wpdb;
		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= "COLLATE {$wpdb->collate}";
		}

		$sql = '
        CREATE TABLE ' . self::get_data_table() . " (
            postid bigint(20) NOT NULL,
            day datetime NOT NULL,
            last_viewed datetime NOT NULL,
            pageviews bigint(20) DEFAULT 1,
            PRIMARY KEY (postid)
        ) {$charset_collate} ENGINE=InnoDB;
        CREATE TABLE " . self::get_summary_table() . " (
            ID bigint(20) NOT NULL AUTO_INCREMENT,
            postid bigint(20) NOT NULL,
            pageviews bigint(20) NOT NULL DEFAULT 1,
            view_date date NOT NULL,
            view_datetime datetime NOT NULL,
            PRIMARY KEY (ID),
            KEY postid (postid),
            KEY view_date (view_date),
            KEY view_datetime (view_datetime)
        ) {$charset_collate} ENGINE=InnoDB;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		Helper::update_general_option( 'version', JNEWS_VIEW_COUNTER_VERSION );
	}
}
