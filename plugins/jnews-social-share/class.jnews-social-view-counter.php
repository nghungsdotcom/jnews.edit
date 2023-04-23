<?php
/**
 * @author : Jegtheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Native View Counter & Handle chosen view counter
 */

/**
 * Class JNews_Social_View_Counter
 */
class JNews_Social_View_Counter {

	/**
	 * @var JNews_Social_View_Counter
	 */
	private static $instance;

	/**
	 * @var views_cache
	 */
	private $views_cache;

	/**
	 * @return JNews_Social_View_Counter
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	private function __construct() {
		// how many view
		add_filter( 'jnews_get_total_view', array( $this, 'get_total_view' ), null, 3 );
	}

	public function original_id( $post_id ) {
		if ( function_exists( 'pll_get_post' ) ) {

			$result_id = pll_get_post( $post_id, pll_default_language() );

			if ( $result_id ) {
				$post_id = $result_id;
			}
		}

		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			global $sitepress;
			$post_id = icl_object_id( $post_id, 'post', true, $sitepress->get_default_language() );
		}

		return $post_id;
	}

	public function get_cache_name( $post_id, $range ) {
		return 'total_view_' . $post_id . '_' . $range;
	}

	public function get_total_view( $total, $post_id, $range = 'all' ) {
		$post_id    = $this->original_id( $post_id );
		$script     = $this->get_view_script();
		$cache_name = $this->get_cache_name( $post_id, $range );
		if ( ! isset( $this->views_cache[ $cache_name ] ) ) {
			if ( $script === 'jnews' && function_exists( 'jnews_get_views' ) ) {
				$total = jnews_get_views( $post_id, $range, false );
			} elseif ( $script === 'jetpack' && function_exists( 'stats_get_csv' ) ) {
				$parameter = array(
					'post_id' => $post_id,
					'days'    => $this->range_to_days( $range ),
				);
				$data      = stats_get_csv( 'postviews', $parameter );
				$data      = $data[0];
				$total     = $data['views'];
			}

			$this->views_cache[ $cache_name ] = $total;
		}
		return $this->views_cache[ $cache_name ];
	}

	public function range_to_days( $range ) {
		switch ( $range ) {
			case 'daily':
				$days = 1;
				break;
			case 'weekly':
				$days = 7;
				break;
			case 'monthly':
				$days = 30;
				break;
			default:
				$days = -1;
				break;
		}

		return $days;
	}

	/**
	 * @return mixed|void
	 */
	public function get_view_script() {
		return jnews_get_option( 'single_view_option', 'jnews' );
	}
}

