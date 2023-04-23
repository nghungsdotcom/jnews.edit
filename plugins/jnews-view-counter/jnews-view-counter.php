<?php
/*
	Plugin Name: JNews - View Counter
	Plugin URI: http://jegtheme.com/
	Description: Custom view counter for JNews
	Version: 11.0.0
	Author: Jegtheme
	Author URI: http://jegtheme.com
	Network: false
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'JNEWS_VIEW_COUNTER' ) or define( 'JNEWS_VIEW_COUNTER', 'jnews-view-counter' );
defined( 'JNEWS_VIEW_COUNTER_VERSION' ) or define( 'JNEWS_VIEW_COUNTER_VERSION', '11.0.0' );
defined( 'JNEWS_VIEW_COUNTER_FILE' ) or define( 'JNEWS_VIEW_COUNTER_FILE', __FILE__ );
defined( 'JNEWS_VIEW_COUNTER_URL' ) or define( 'JNEWS_VIEW_COUNTER_URL', plugins_url( JNEWS_VIEW_COUNTER ) );
defined( 'JNEWS_VIEW_COUNTER_DIR' ) or define( 'JNEWS_VIEW_COUNTER_DIR', plugin_dir_path( JNEWS_VIEW_COUNTER_FILE ) );
defined( 'JNEWS_VIEW_COUNTER_DB_DATA' ) or define( 'JNEWS_VIEW_COUNTER_DB_DATA', 'popularpostsdata' );
defined( 'JNEWS_VIEW_COUNTER_DB_SUMMARY' ) or define( 'JNEWS_VIEW_COUNTER_DB_SUMMARY', 'popularpostssummary' );

require_once JNEWS_VIEW_COUNTER_DIR . 'class/autoload.php';

/**
 * Initialise JNews View Counter
 *
 * @return JNEWS_VIEW_COUNTER\Init
 */
function JNews_View_Counter() {
	static $instance;

	// first call to instance() initializes the plugin
	if ( null === $instance || ! ( $instance instanceof JNEWS_VIEW_COUNTER\Init ) ) {
		$instance = JNEWS_VIEW_COUNTER\Init::instance();
	}

	return $instance;
}

JNews_View_Counter();
