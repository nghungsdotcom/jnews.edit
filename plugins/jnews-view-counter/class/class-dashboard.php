<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Dashboard
 *
 * @package JNEWS_VIEW_COUNTER
 */
class Dashboard {
	/**
	 * Dashboard Construct.
	 */
	public function __construct() {
		// Delete old data on demand
		if ( JNews_View_Counter()->options['general']['log']['limit'] ) {
			if ( ! wp_next_scheduled( 'jnews_view_counter_cache_event' ) ) {
				$midnight = strtotime( 'midnight' ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) + DAY_IN_SECONDS;
				wp_schedule_event( $midnight, 'daily', 'jnews_view_counter_cache_event' );
			}
		} else {
			// Remove the scheduled event if exists
			if ( $timestamp = wp_next_scheduled( 'jnews_view_counter_cache_event' ) ) {
				wp_unschedule_event( $timestamp, 'jnews_view_counter_cache_event' );
			}
		}
		$this->setup_hook();
	}

	private function setup_hook() {
		// Register new column
		add_action( 'admin_init', array( $this, 'register_new_column' ) );
		// Purge post data on post/page deletion
		add_action( 'admin_init', array( $this, 'purge_post_data' ) );
		// Purge old data on demand
		add_action( 'jnews_view_counter_cache_event', array( $this, 'purge_data' ) );
	}

	/**
	 * Purges old post data from summary table.
	 *
	 * @global  object  $wpdb
	 */
	public function purge_data() {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->prefix}popularpostssummary WHERE view_date < DATE_SUB('" . Helper::curdate() . "', INTERVAL " . JNews_View_Counter()->options['general']['log']['expires_after'] . ' DAY);' );
	}

	/**
	 * Purges post from data/summary tables.
	 */
	public function purge_post_data() {
		if ( current_user_can( 'delete_posts' ) ) {
			add_action( 'delete_post', array( $this, 'purge_post' ) );
		}
	}

	/**
	 * Purges post from data/summary tables.
	 *
	 * @global   object   $wpdb
	 */
	public function purge_post( $post_ID ) {
		global $wpdb;

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT postid FROM {$wpdb->prefix}popularpostsdata WHERE postid = %d", $post_ID ) ) ) {
			// Delete from data table
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}popularpostsdata WHERE postid = %d;", $post_ID ) );
			// Delete from summary table
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}popularpostssummary WHERE postid = %d;", $post_ID ) );
		}
	}

	/**
	 * Add column Views
	 *
	 * @param  array $columns
	 * @return array
	 */
	public function add_new_column( $columns ) {
		$offset = 0;

		if ( isset( $columns['date'] ) ) {
			$offset++;
		}

		if ( isset( $columns['comments'] ) ) {
			$offset++;
		}

		$column_title = esc_html__( 'Views', 'jnews-view-counter' );

		if ( $offset > 0 ) {
			$date = array_slice( $columns, -$offset, $offset, true );

			foreach ( $date as $column => $name ) {
				unset( $columns[ $column ] );
			}

			$columns['view_count'] = $column_title;

			foreach ( $date as $column => $name ) {
				$columns[ $column ] = $name;
			}
		} else {
			$columns['view_count'] = $column_title;
		}

		return $columns;
	}

	/**
	 * Add content to Views column
	 *
	 * @param  string $column_name
	 * @param  int    $post_id
	 * @return mixed
	 */
	public function add_new_column_content( $column_name, $post_id ) {
		if ( 'view_count' === $column_name ) {
			$views = jnews_get_views( $post_id );
			echo ( esc_attr( $views ) );
		}
	}

	/**
	 * Register sortable post views column.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function register_sortable_custom_column( $columns ) {
		$columns['view_count'] = 'view_count';

		return $columns;
	}

	/**
	 * Register post views column for specific post types
	 */
	public function register_new_column() {
		$post_types = array_map( 'trim', explode( ',', JNews_View_Counter()->options['config']['post_type'] ) );
		if ( empty( $post_types ) ) {
			$post_types = array( 'post' );
		}

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				// actions
				add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'add_new_column_content' ), 10, 2 );

				// filters
				add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_new_column' ) );
				add_filter( 'manage_edit-' . $post_type . '_sortable_columns', array( $this, 'register_sortable_custom_column' ) );
			}
		}
	}
}
