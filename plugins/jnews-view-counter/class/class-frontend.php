<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Frontend
 *
 * @package JNEWS_VIEW_COUNTER
 */
class Frontend {

	/**
	 * Endpoint
	 *
	 * @var array
	 */
	private $endpoint;

	/**
	 * Frontend Construct.
	 */
	public function __construct() {
		$this->setup_hook();
	}

	/**
	 * Setup hook
	 */
	private function setup_hook() {
		add_action( 'init', array( $this, 'upgrade_check' ) );

		add_action( 'jnews_ajax_view_counter_chart', array( $this, 'view_counter_chart' ) );

		add_action( 'template_redirect', array( $this, 'set_post_id' ) );
		add_action( 'wp_head', array( $this, 'register_view_counter' ) );
	}

	public function view_counter_chart() {
		$response  = array(
			'status' => 'error',
		);
		$nonce     = isset( $_POST['data'] ) && isset( $_POST['data']['nonce'] ) ? sanitize_text_field( $_POST['data']['nonce'] ) : null;
		$author_id = get_current_user_id();

		if ( wp_verify_nonce( $nonce, 'jnews_view_counter_nonce' ) && $author_id ) {

			$valid_ranges = array( 'today', 'daily', 'last24hours', 'weekly', 'last7days', 'monthly', 'last30days', 'all', 'custom' );
			$time_units   = array( 'MINUTE', 'HOUR', 'DAY' );

			JNews_View_Counter()->options['config']['range']         = ( isset( $_POST['data'] ) && isset( $_POST['data']['range'] ) && in_array( $_POST['data']['range'], $valid_ranges ) ) ? sanitize_text_field( $_POST['data']['range'] ) : 'last7days';
			JNews_View_Counter()->options['config']['time_quantity'] = ( isset( $_POST['data'] ) && isset( $_POST['data']['time_quantity'] ) && filter_var( $_POST['data']['time_quantity'], FILTER_VALIDATE_INT ) ) ? sanitize_text_field( $_POST['data']['time_quantity'] ) : 24;
			JNews_View_Counter()->options['config']['time_unit']     = ( isset( $_POST['data'] ) && isset( $_POST['data']['time_unit'] ) && in_array( strtoupper( $_POST['data']['time_unit'] ), $time_units ) ) ? sanitize_text_field( $_POST['data']['time_unit'] ) : 'hour';

			Helper::update_global_option( 'config', JNews_View_Counter()->options['config'] );

			$response = array(
				'status' => 'ok',
				'data'   => json_decode(
					$this->get_chart_data( JNews_View_Counter()->options['config']['range'], JNews_View_Counter()->options['config']['time_unit'], JNews_View_Counter()->options['config']['time_quantity'], $author_id ),
					true
				),
			);
		}

		wp_send_json( $response );
	}

	/**
	 * Returns an array of dates.
	 *
	 * @return  array|bool
	 */
	public function get_dates( $range = 'last7days', $time_unit = 'HOUR', $time_quantity = 24 ) {
		$valid_ranges = array( 'today', 'daily', 'last24hours', 'weekly', 'last7days', 'monthly', 'last30days', 'all', 'custom' );
		$range        = in_array( $range, $valid_ranges ) ? $range : 'last7days';
		$now          = new \DateTime( Helper::now(), new \DateTimeZone( Helper::get_timezone() ) );

		// Determine time range
		switch ( $range ) {
			case 'last24hours':
			case 'daily':
				$end_date   = $now->format( 'Y-m-d H:i:s' );
				$start_date = $now->modify( '-1 day' )->format( 'Y-m-d H:i:s' );
				break;

			case 'today':
				$start_date = $now->format( 'Y-m-d' ) . ' 00:00:00';
				$end_date   = $now->format( 'Y-m-d' ) . ' 23:59:59';
				break;

			case 'last7days':
			case 'weekly':
				$end_date   = $now->format( 'Y-m-d' ) . ' 23:59:59';
				$start_date = $now->modify( '-6 day' )->format( 'Y-m-d' ) . ' 00:00:00';
				break;

			case 'last30days':
			case 'monthly':
				$end_date   = $now->format( 'Y-m-d' ) . ' 23:59:59';
				$start_date = $now->modify( '-29 day' )->format( 'Y-m-d' ) . ' 00:00:00';
				break;

			case 'custom':
				$end_date = $now->format( 'Y-m-d H:i:s' );

				if ( Helper::is_number( $time_quantity ) && $time_quantity >= 1 ) {
					$end_date  = $now->format( 'Y-m-d H:i:s' );
					$time_unit = strtoupper( $time_unit );

					if ( 'MINUTE' === $time_unit ) {
						$start_date = $now->sub( new \DateInterval( 'PT' . ( 60 * $time_quantity ) . 'S' ) )->format( 'Y-m-d H:i:s' );
					} elseif ( 'HOUR' === $time_unit ) {
						$start_date = $now->sub( new \DateInterval( 'PT' . ( ( 60 * $time_quantity ) - 1 ) . 'M59S' ) )->format( 'Y-m-d H:i:s' );
					} else {
						$end_date   = $now->format( 'Y-m-d' ) . ' 23:59:59';
						$start_date = $now->sub( new \DateInterval( 'P' . ( $time_quantity - 1 ) . 'D' ) )->format( 'Y-m-d' ) . ' 00:00:00';
					}
				} else { // fallback to last 24 hours.
					$start_date = $now->modify( '-1 day' )->format( 'Y-m-d H:i:s' );
				}

				// Check if custom date range has been requested.
				$dates = null;

				if ( isset( $_POST['data'] ) && isset( $_POST['data']['dates'] ) ) {
					$dates = explode( ' ~ ', $_POST['data']['dates'] );

					if ( ! is_array( $dates ) || empty( $dates ) || ! Helper::is_valid_date( $dates[0] ) ) {
						$dates = null;
					} else {
						if ( ! isset( $dates[1] ) || ! Helper::is_valid_date( $dates[1] ) ) {
							$dates[1] = $dates[0];
						}

						$start_date = $dates[0] . ' 00:00:00';
						$end_date   = $dates[1] . ' 23:59:59';
					}
				}

				break;

			default:
				$end_date   = $now->format( 'Y-m-d' ) . ' 23:59:59';
				$start_date = $now->modify( '-6 day' )->format( 'Y-m-d' ) . ' 00:00:00';
				break;
		}

		return array( $start_date, $end_date );
	}

	/**
	 * Returns an array of dates with views/comments count.
	 *
	 * @param   string $start_date
	 * @param   string $end_date
	 * @param   string $item
	 * @return  array
	 */
	public function get_range_item_count( $start_date, $end_date, $item = 'views', $author = false ) {
		global $wpdb;

		$args = array_map( 'trim', explode( ',', JNews_View_Counter()->options['config']['post_type'] ) );
		if ( empty( $args ) ) {
			$args = array( 'post' );
		}

		$post_type_placeholders = array_fill( 0, count( $args ), '%s' );
		$freshness              = false;
		if ( $freshness ) {
			$args[] = $start_date;
		}
		if ( $author ) {
			$args[] = $author;
		}

		// Append dates to arguments list
		array_unshift( $args, $start_date, $end_date );

		if ( $item == 'comments' ) {
			$query = $wpdb->prepare(
				"SELECT DATE(`c`.`comment_date_gmt`) AS `c_date`, COUNT(*) AS `comments` 
                FROM `{$wpdb->comments}` c INNER JOIN `{$wpdb->posts}` p ON `c`.`comment_post_ID` = `p`.`ID`
                WHERE (`c`.`comment_date_gmt` BETWEEN %s AND %s) AND `c`.`comment_approved` = '1' AND `p`.`post_type` IN (" . implode( ', ', $post_type_placeholders ) . ") AND `p`.`post_status` = 'publish' AND `p`.`post_password` = '' 
                " . ( $freshness ? ' AND `p`.`post_date` >= %s' : '' ) . ( $author ? ' AND `p`.`post_author` = %s' : '' ) . '
                GROUP BY `c_date` ORDER BY `c_date` DESC;',
				$args
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT `v`.`view_date`, SUM(`v`.`pageviews`) AS `pageviews` 
                FROM `{$wpdb->prefix}popularpostssummary` v INNER JOIN `{$wpdb->posts}` p ON `v`.`postid` = `p`.`ID`
                WHERE (`v`.`view_datetime` BETWEEN %s AND %s) AND `p`.`post_type` IN (" . implode( ', ', $post_type_placeholders ) . ") AND `p`.`post_status` = 'publish' AND `p`.`post_password` = '' 
                " . ( $freshness ? ' AND `p`.`post_date` >= %s' : '' ) . ( $author ? ' AND `p`.`post_author` = %s' : '' ) . '
                GROUP BY `v`.`view_date` ORDER BY `v`.`view_date` DESC;',
				$args
			);
		}

		return $wpdb->get_results( $query, OBJECT_K );
	}

	/**
	 * Fetches chart data.
	 *
	 * @return  string
	 */
	public function get_chart_data( $range = 'last7days', $time_unit = 'HOUR', $time_quantity = 24, $author = false ) {
		$dates         = $this->get_dates( $range, $time_unit, $time_quantity );
		$start_date    = $dates[0];
		$end_date      = $dates[ count( $dates ) - 1 ];
		$date_range    = Helper::get_date_range( $start_date, $end_date, 'Y-m-d H:i:s' );
		$views_data    = $this->get_range_item_count( $start_date, $end_date, 'views', $author );
		$views         = array();
		$comments_data = $this->get_range_item_count( $start_date, $end_date, 'comments', $author );
		$comments      = array();

		if ( 'today' != $range ) {
			foreach ( $date_range as $date ) {
				$key        = date( 'Y-m-d', strtotime( $date ) );
				$views[]    = ( ! isset( $views_data[ $key ] ) ) ? 0 : $views_data[ $key ]->pageviews;
				$comments[] = ( ! isset( $comments_data[ $key ] ) ) ? 0 : $comments_data[ $key ]->comments;
			}
		} else {
			$key        = date( 'Y-m-d', strtotime( $dates[0] ) );
			$views[]    = ( ! isset( $views_data[ $key ] ) ) ? 0 : $views_data[ $key ]->pageviews;
			$comments[] = ( ! isset( $comments_data[ $key ] ) ) ? 0 : $comments_data[ $key ]->comments;
		}

		if ( $start_date != $end_date ) {
			$label_date_range = date_i18n( 'M, D d', strtotime( $start_date ) ) . ' &mdash; ' . date_i18n( 'M, D d', strtotime( $end_date ) );
		} else {
			$label_date_range = date_i18n( 'M, D d', strtotime( $start_date ) );
		}

		$total_views    = array_sum( $views );
		$total_comments = array_sum( $comments );

		$label_summary = sprintf( _n( '%s view', '%s views', $total_views, 'jnews-view-counter' ), '<strong>' . number_format_i18n( $total_views ) . '</strong>' ) . '<br style="display: none;" /> / ' . sprintf( _n( '%s comment', '%s comments', $total_comments, 'jnews-view-counter' ), '<strong>' . number_format_i18n( $total_comments ) . '</strong>' );

		// Format labels
		if ( 'today' != $range ) {
			$date_range = array_map(
				function( $d ) {
					return date_i18n( 'Y-m-d', strtotime( $d ) );
				},
				$date_range
			);
		} else {
			$date_range = array( date_i18n( 'Y-m-d', strtotime( $date_range[0] ) ) );
			$comments   = array( array_sum( $comments ) );
			$views      = array( array_sum( $views ) );
		}
		$show_views_label = false;
		$views            = array_map(
			function( $v, $k ) use ( $date_range, &$show_views_label ) {
				if ( ! $show_views_label && (int) $v > 0 ) {
					$show_views_label = true;
				}
				$v = array(
					'x' => date_i18n( 'Y-m-d', strtotime( $date_range[ $k ] ) ),
					'y' => $v,
				);
				return $v;
			},
			$views,
			array_keys( $views )
		);

		$response = array(
			'backgroundColor' => '#FAFAFA',
			'color'           => get_theme_mod( 'jnews_accent_color', '#f70d28' ),
			'totals'          => array(
				'label_summary'    => $label_summary,
				'label_date_range' => $label_date_range,
			),
			'labels'          => $date_range,
			'datasets'        => array(
				// array(
				// 'label' => __( 'Comments', 'jnews-view-counter' ),
				// 'data'  => $comments,
				// ),
				array(
					'label' => __( 'Views', 'jnews-view-counter' ),
					'data'  => $views,
				),
			),
			'x'               => array(
				'type' => 'time',
				'time' => array(
					'unit'           => 'day',
					'displayFormats' => array(
						'day' => 'DD[\n]MMM[\n]YYYY',
					),
				),
			),
			'y'               => array(
				'display' => false,
			),
		);
		if ( $show_views_label && apply_filters( 'jnews_view_counter_show_views_label_chart', false ) ) {
			$response['y']['display'] = true;
		}
		if ( count( $date_range ) > 23 ) {
			$response['x']['time']['unit']                   = 'week';
			$response['x']['time']['displayFormats']['week'] = 'DD[\n]MMM[\n]YYYY';
			$divide_week                                     = array_chunk( $date_range, 7 );
			if ( count( $divide_week ) > 12 ) {
				$response['x']['time']['unit']                    = 'month';
				$response['x']['time']['displayFormats']['month'] = 'DD[\n]MMM[\n]YYYY';
				$divide_month                                     = array_chunk( $date_range, 30 );
				if ( count( $divide_month ) > 12 ) {
					$response['x']['time']['unit']                      = 'quarter';
					$response['x']['time']['displayFormats']['quarter'] = 'DD[\n]MMM[\n]YYYY';
					$divide_quarter                                     = array_chunk( $date_range, 90 );
					if ( count( $divide_quarter ) > 12 ) {
						$response['x']['time']['unit']                   = 'year';
						$response['x']['time']['displayFormats']['year'] = 'DD[\n]MMM[\n]YYYY';
					}
				}
			}
		}

		return wp_json_encode( $response );
	}

	/**
	 * Set post ID
	 */
	public function set_post_id() {
		$trackable  = array();
		$post_types = array_map( 'trim', explode( ',', JNews_View_Counter()->options['config']['post_type'] ) );
		if ( empty( $post_types ) ) {
			$post_types = array( 'post' );
		}

		foreach ( $post_types as $post_type ) {
			$trackable[] = $post_type;
		}

		$trackable = apply_filters( 'jnews_trackable_post_types', $trackable );

		if ( is_singular( $trackable ) && ! is_front_page() && ! is_preview() && ! is_trackback() && ! is_feed() && ! is_robots() ) {
			global $post;
			$this->post_id = ( is_object( $post ) ) ? $post->ID : 0;
		} else {
			$this->post_id = 0;
		}
	}

	/**
	 * Register View Counter
	 */
	public function register_view_counter() {
		$post_types = array_map( 'trim', explode( ',', JNews_View_Counter()->options['config']['post_type'] ) );
		if ( empty( $post_types ) ) {
			$post_types = array( 'post' );
		}

			// whether to count this post type or not
		if ( empty( $post_types ) || ! is_singular( $post_types ) || 0 === $this->post_id ) {
			return;
		}
		do_action( 'jnews_push_first_load_action', 'view_counter' );
	}

	public function upgrade_check() {
		$this->upgrade_site();
	}

	private function upgrade_site() {
		// Get version
		$old_version = get_option( 'jnews_view_counter_ver' );
		if ( $old_version ) {
			Helper::update_general_option( 'version', $old_version );
			delete_option( 'jnews_view_counter_ver' );
			delete_option( 'jnews_view_counter_update' );
		}
		$version = Helper::get_general_option( 'version' );
		$upgrade = false;

		if ( ! $version || version_compare( $version, JNEWS_VIEW_COUNTER_VERSION, '<' ) ) {
			$upgrade = true;
		}

		if ( $upgrade ) {
			$this->upgrade();
		}
	}

	/**
	 * On plugin upgrade, performs a number of actions: update database tables structures (if needed),
	 * run the setup wizard (if needed), and some other checks.
	 *
	 * @global  object  $wpdb
	 */
	private function upgrade() {
		$now = Helper::now();

		// Keep the upgrade process from running too many times
		if ( $update = Helper::get_general_option( 'update' ) ) {
			$from_time             = strtotime( $update );
			$to_time               = strtotime( $now );
			$difference_in_minutes = round( abs( $to_time - $from_time ) / 60, 2 );

			// Upgrade flag is still valid, abort
			if ( $difference_in_minutes <= 15 ) {
				return;
			}
			// Upgrade flag expired, delete it and continue
			Helper::update_general_option( 'update', false );
		}

		global $wpdb;

		// Upgrade flag
		Helper::update_general_option( 'update', $now );

		// Set table name
		$prefix = $wpdb->prefix . 'popularposts';

		// Update data table structure and indexes
		$data_fields = $wpdb->get_results( "SHOW FIELDS FROM {$prefix}data;" );

		foreach ( $data_fields as $column ) {
			if ( 'day' === $column->Field ) {
				$wpdb->query( "ALTER TABLE {$prefix}data ALTER COLUMN day DROP DEFAULT;" );
			}

			if ( 'last_viewed' === $column->Field ) {
				$wpdb->query( "ALTER TABLE {$prefix}data ALTER COLUMN last_viewed DROP DEFAULT;" );
			}
		}

		// Update summary table structure and indexes
		$summary_fields = $wpdb->get_results( "SHOW FIELDS FROM {$prefix}summary;" );

		foreach ( $summary_fields as $column ) {
			if ( 'last_viewed' === $column->Field ) {
				$wpdb->query( "ALTER TABLE {$prefix}summary CHANGE last_viewed view_datetime datetime NOT NULL, ADD KEY view_datetime (view_datetime);" );
			}

			if ( 'view_date' === $column->Field ) {
				$wpdb->query( "ALTER TABLE {$prefix}summary ALTER COLUMN view_date DROP DEFAULT;" );
			}

			if ( 'view_datetime' === $column->Field ) {
				$wpdb->query( "ALTER TABLE {$prefix}summary ALTER COLUMN view_datetime DROP DEFAULT;" );
			}
		}

		$summary_indexes = $wpdb->get_results( "SHOW INDEX FROM {$prefix}summary;" );

		foreach ( $summary_indexes as $index ) {
			if ( 'ID_date' === $index->Key_name ) {
				$wpdb->query( "ALTER TABLE {$prefix}summary DROP INDEX ID_date;" );
			}

			if ( 'last_viewed' === $index->Key_name ) {
				$wpdb->query( "ALTER TABLE {$prefix}summary DROP INDEX last_viewed;" );
			}
		}

		// Validate the structure of the tables, create missing tables / fields if necessary
		Activator::track_new_site();

		// Check storage engine
		$storage_engine_data = $wpdb->get_var( "SELECT `ENGINE` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA`='{$wpdb->dbname}' AND `TABLE_NAME`='{$prefix}data';" );

		if ( 'InnoDB' !== $storage_engine_data ) {
			$wpdb->query( "ALTER TABLE {$prefix}data ENGINE=InnoDB;" );
		}

		$storage_engine_summary = $wpdb->get_var( "SELECT `ENGINE` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA`='{$wpdb->dbname}' AND `TABLE_NAME`='{$prefix}summary';" );

		if ( 'InnoDB' !== $storage_engine_summary ) {
			$wpdb->query( "ALTER TABLE {$prefix}summary ENGINE=InnoDB;" );
		}

		// Update version
		Helper::update_general_option( 'version', JNEWS_VIEW_COUNTER_VERSION );
		// Remove upgrade flag
		Helper::update_general_option( 'update', false );
	}
}
