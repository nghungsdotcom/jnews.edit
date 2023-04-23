<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Template
 *
 * @package JNEWS_VIEW_COUNTER
 */
class Template {

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
		$this->setup_endpoint();
		add_action( 'template_include', array( $this, 'load_assets' ) );
		add_action( 'jnews_ajax_view_counter_stats_template', array( $this, 'view_counter_stats_template' ) );
		add_action( 'jnews_account_right_content', array( $this, 'get_right_content' ) );
		add_filter( 'jnews_account_page_endpoint', array( $this, 'account_page_endpoint' ) );
	}

	public function view_counter_stats_template() {
		$response  = array(
			'status' => 'error',
		);
		$nonce     = isset( $_POST['data'] ) && isset( $_POST['data']['nonce'] ) ? sanitize_text_field( $_POST['data']['nonce'] ) : null;
		$author_id = get_current_user_id();
		if ( wp_verify_nonce( $nonce, 'jnews_view_counter_nonce' ) && $author_id ) {
			$template           = isset( $_POST['data'] ) && isset( $_POST['data']['template'] ) ? sanitize_text_field( $_POST['data']['template'] ) : 'most-viewed';
			$response['status'] = 'ok';
			$args               = $this->get_popular_items( $author_id );
			$templates          = array(
				'template/post-stats-' . $template . '.php',
			);
			ob_start();
			jeg_locate_template( Helper::get_template_path( $templates, false, false ), true, $args );
			$result           = ob_get_clean();
			$response['data'] = $result;
		}
		wp_send_json( $response );
	}

	/**
	 * Fetches most viewed/commented/trending posts via AJAX.
	 *
	 * @param int $author_id
	 *
	 * @return array
	 */
	public function get_popular_items( $author_id = null ) {
		$response = array(
			'status' => 'error',
		);
		$items    = isset( $_POST['data'] ) && isset( $_POST['data']['items'] ) ? sanitize_text_field( $_POST['data']['items'] ) : null;
		$nonce    = isset( $_POST['data'] ) && isset( $_POST['data']['nonce'] ) ? sanitize_text_field( $_POST['data']['nonce'] ) : null;

		if ( wp_verify_nonce( $nonce, 'jnews_view_counter_nonce' ) ) {
			$args = array(
				'range'         => JNews_View_Counter()->options['config']['range'],
				'time_quantity' => JNews_View_Counter()->options['config']['time_quantity'],
				'time_unit'     => JNews_View_Counter()->options['config']['time_unit'],
				'post_type'     => JNews_View_Counter()->options['config']['post_type'],
				'freshness'     => JNews_View_Counter()->options['config']['freshness'],
				'limit'         => JNews_View_Counter()->options['config']['limit'],
				'stats_tag'     => array(
					'date' => array(
						'active' => 1,
					),
				),
				'author'        => $author_id,
			);

			if ( 'most-commented' == $items ) {
				$args['order_by']                   = 'comments';
				$args['stats_tag']['comment_count'] = 1;
				$args['stats_tag']['views']         = 0;
			} elseif ( 'trending-now' == $items ) {
				$args['range']                      = 'custom';
				$args['time_quantity']              = 1;
				$args['time_unit']                  = 'HOUR';
				$args['stats_tag']['comment_count'] = 1;
				$args['stats_tag']['views']         = 1;
			} else {
				$args['stats_tag']['comment_count'] = 0;
				$args['stats_tag']['views']         = 1;
			}

			if ( 'trending-now' != $items ) {

				add_filter(
					'jnews_view_counter_query_join',
					function( $join, $options ) use ( $items ) {
						global $wpdb;
						$dates = null;

						if ( isset( $_POST['data'] ) && isset( $_POST['data']['dates'] ) ) {
							$dates = explode( ' ~ ', $_POST['data']['dates'] );

							if (
							! is_array( $dates )
							|| empty( $dates )
							|| ! Helper::is_valid_date( $dates[0] )
							) {
								$dates = null;
							} else {
								if (
								! isset( $dates[1] )
								|| ! Helper::is_valid_date( $dates[1] )
								) {
									$dates[1] = $dates[0];
								}

								$start_date = $dates[0];
								$end_date   = $dates[1];
							}
						}

						if ( $dates ) {
							if ( 'most-commented' == $items ) {
								return "INNER JOIN (SELECT comment_post_ID, COUNT(comment_post_ID) AS comment_count, comment_date_gmt FROM `{$wpdb->comments}` WHERE comment_date_gmt BETWEEN '{$dates[0]} 00:00:00' AND '{$dates[1]} 23:59:59' AND comment_approved = '1' GROUP BY comment_post_ID) c ON p.ID = c.comment_post_ID";
							}

							return "INNER JOIN (SELECT SUM(pageviews) AS pageviews, view_date, postid FROM `{$wpdb->prefix}popularpostssummary` WHERE view_datetime BETWEEN '{$dates[0]} 00:00:00' AND '{$dates[1]} 23:59:59' GROUP BY postid) v ON p.ID = v.postid";
						}

						$now = Helper::now();

						// Determine time range
						switch ( $options['range'] ) {
							case 'last24hours':
							case 'daily':
								$interval = '24 HOUR';
								break;

							case 'today':
								$hours    = date( 'H', strtotime( $now ) );
								$minutes  = $hours * 60 + (int) date( 'i', strtotime( $now ) );
								$interval = "{$minutes} MINUTE";
								break;

							case 'last7days':
							case 'weekly':
								$interval = '6 DAY';
								break;

							case 'last30days':
							case 'monthly':
								$interval = '29 DAY';
								break;

							case 'custom':
								$time_units = array( 'MINUTE', 'HOUR', 'DAY' );
								$interval   = '24 HOUR';

								// Valid time unit
								if (
								isset( $options['time_unit'] )
								&& in_array( strtoupper( $options['time_unit'] ), $time_units )
								&& isset( $options['time_quantity'] )
								&& filter_var( $options['time_quantity'], FILTER_VALIDATE_INT )
								&& $options['time_quantity'] > 0
								) {
									$interval = "{$options['time_quantity']} " . strtoupper( $options['time_unit'] );
								}

								break;

							default:
								$interval = '1 DAY';
								break;
						}

						if ( 'most-commented' == $items ) {
							return "INNER JOIN (SELECT comment_post_ID, COUNT(comment_post_ID) AS comment_count, comment_date_gmt FROM `{$wpdb->comments}` WHERE comment_date_gmt > DATE_SUB('{$now}', INTERVAL {$interval}) AND comment_approved = '1' GROUP BY comment_post_ID) c ON p.ID = c.comment_post_ID";
						}

						return "INNER JOIN (SELECT SUM(pageviews) AS pageviews, view_date, postid FROM `{$wpdb->prefix}popularpostssummary` WHERE view_datetime > DATE_SUB('{$now}', INTERVAL {$interval}) GROUP BY postid) v ON p.ID = v.postid";
					},
					1,
					2
				);

			}
			$popular_items = jnews_view_counter_query( $args );
			if ( 'trending-now' != $items ) {
				remove_all_filters( 'jnews_view_counter_query_join', 1 );
			}
			$response['status'] = 'ok';
			$response['data']   = $popular_items;
		}

		return $response;
	}

	/**
	 * Setup endpoint
	 */
	private function setup_endpoint() {
		$endpoint = array(
			'post_view_stats' => array(
				'title' => esc_html__( 'Post View Stats', 'jnews-view-counter' ),
				'label' => 'post_view_stats',
				'slug'  => 'post-view-stats',
			),
		);

		$this->endpoint = apply_filters( 'jnews_view_counter_endpoint', $endpoint );
	}

	/**
	 * Get the right content
	 */
	public function get_right_content() {
		global $wp;

		if ( is_user_logged_in() ) {
			if ( isset( $wp->query_vars['account'] ) && ! empty( $wp->query_vars['account'] ) ) {
				foreach ( $this->endpoint as $key => $value ) {
					$query_vars = explode( '/', $wp->query_vars['account'] );

					if ( $query_vars[0] === $value['slug'] ) {
						$this->render_template();
					}
				}
			}
		}
	}

	/**
	 * Add Bookmark Endpoint
	 *
	 * @param object $endpoint Global Endpoint.
	 * @return object
	 */
	public function account_page_endpoint( $endpoint ) {

		if ( isset( $this->endpoint ) && ! empty( $this->endpoint ) ) {
			if ( is_array( $endpoint ) && ! empty( $endpoint ) && isset( $endpoint['change_password'] ) ) {
				$position     = array_search( 'change_password', array_keys( $endpoint ) ) + 1;
				$first_slice  = array_slice( $endpoint, 0, $position, true );
				$second_slice = array_slice( $endpoint, $position, count( $endpoint ) - 1, true );
				$endpoint     = $first_slice + $this->endpoint + $second_slice;
			} else {
				$endpoint = array_merge( $endpoint, $this->endpoint );
			}
		}

		return $endpoint;
	}

	/**
	 * Load plugin assest
	 *
	 * @param  string $template
	 * @return string
	 */
	public function load_assets( $template ) {
		global $wp;
		if ( is_user_logged_in() && ! is_admin() ) {
			if ( isset( $wp->query_vars['account'] ) && ! empty( $wp->query_vars['account'] ) ) {
				foreach ( $this->endpoint as $key => $value ) {
					$query_vars = explode( '/', $wp->query_vars['account'] );

					if ( $query_vars[0] === $value['slug'] ) {
						add_action( 'wp_enqueue_scripts', array( $this, 'load_script' ) );
						add_action( 'wp_enqueue_scripts', array( $this, 'load_style' ), 98 );
					}
				}
			}
		}
		return $template;
	}

	public function load_script() {
		// vendor
		wp_register_script( 'chartjs-moment', JNEWS_VIEW_COUNTER_URL . '/assets/js/vendor/chartjs/chartjs-adapter-moment.min.js', array( 'chartjs', 'moment' ), '1.0.0', true );
		wp_register_script( 'chartjs', JNEWS_VIEW_COUNTER_URL . '/assets/js/vendor/chartjs/chart.min.js', array(), '3.4.1', true );
		wp_register_script( 'vanillajs-datepicker', JNEWS_VIEW_COUNTER_URL . '/assets/js/vendor/vanillajs-datepicker/datepicker-full.min.js', array(), '1.1.4', true );

		wp_register_script( 'jnews-view-counter-chart', JNEWS_VIEW_COUNTER_URL . '/assets/js/chart.js', array( 'chartjs-moment', 'vanillajs-datepicker' ), JNEWS_VIEW_COUNTER_VERSION, true );
		wp_enqueue_script( 'jnews-view-counter', JNEWS_VIEW_COUNTER_URL . '/assets/js/plugin.js', array( 'jnews-view-counter-chart' ), JNEWS_VIEW_COUNTER_VERSION, true );
		wp_localize_script( 'jnews-view-counter', 'jvcoption', $this->localize_script() );
	}

	public function load_style() {
		// vendor
		wp_enqueue_style( 'vanillajs-datepicker', JNEWS_VIEW_COUNTER_URL . '/assets/css/vendor/vanillajs-datepicker/datepicker.min.css', array(), '1.1.4' );

		wp_register_style( 'jnews-view-counter', JNEWS_VIEW_COUNTER_URL . '/assets/css/plugin.css', array(), JNEWS_VIEW_COUNTER_VERSION );
		wp_enqueue_style( 'jnews-view-counter-dark', JNEWS_VIEW_COUNTER_URL . '/assets/css/darkmode.css', array( 'jnews-view-counter' ), JNEWS_VIEW_COUNTER_VERSION );
	}

	public function localize_script() {
		$option          = array();
		$option['nonce'] = wp_create_nonce( 'jnews_view_counter_nonce' );
		return $option;
	}

	/**
	 * Render post stats
	 */
	private function render_template() {
		Helper::get_template_part( 'template/post-stats' );
	}
}
