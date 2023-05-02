<?php
/**
 * Liker
 * Helps you rate and like articles on a website and keep track of results.
 * Exclusively on https://1.envato.market/liker
 *
 * @encoding        UTF-8
 * @version         2.3.0
 * @copyright       (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Envato License https://1.envato.market/KYbje
 * @contributors    Nemirovskiy Vitaliy (nemirovskiyvitaliy@gmail.com), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Liker;

use Merkulove\Liker\Unity\Plugin;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

final class Compatibility {

	/**
	 * The one true RankMath.
	 *
     * @access private
	 * @var Compatibility
	 **/
	private static $instance;

	/**
	 * Sets up a new RankMathCompatibility instance.
	 *
	 * @access private
	 * @return void
	 **/
	private function __construct() {

		/** RankMath compatibility */
		if ( is_plugin_active( 'seo-by-rank-math/rank-math.php' ) ) {

			$this->rank_math_actions();

		}

	}

	/**
	 * Add actions.
	 *
	 * @access private
	 * @return void
	 **/
	private function rank_math_actions() {

		/** Add rating value variable */
		add_action( 'rank_math/vars/register_extra_replacements', function() {

			$liker_img = '<img src="' . Plugin::get_url() . 'images/logo-color.svg" style="width: 10px; margin-right: 5px;">';

			rank_math_register_var_replacement(
				'liker-rating',
				[
					'name' => $liker_img . esc_html__( 'Liker raring', 'liker' ),
					'description' => esc_html__( 'Page rating value from 0 to 5', 'liker' ),
					'variable' => 'liker-rating',
					'example' => $this->rankmath_liker_rating_callback(),
				],
				[ Compatibility::get_instance(), 'rankmath_liker_rating_callback' ]
			);

		} );

		/** Add rating count variable */
		add_action( 'rank_math/vars/register_extra_replacements', function() {

			$liker_img = '<img src="' . Plugin::get_url() . 'images/logo-color.svg" style="width: 10px; margin-right: 5px;">';

			rank_math_register_var_replacement(
				'liker-count',
				[
					'name' => $liker_img . esc_html__( 'Liker count', 'liker' ),
					'description' => esc_html__( 'Number of votes', 'liker' ),
					'variable' => 'liker-count',
					'example' => $this->rankmath_liker_count_callback(),
				],
				[ Compatibility::get_instance(), 'rankmath_liker_count_callback' ]
			);

		} );

	}

	/**
	 * Get rating value.
	 *
	 * @access private
	 * @return string
	 **/
	public function rankmath_liker_rating_callback(): string {

		$post_id = get_the_ID();
		if ( $post_id ) {

			/** Get likes values for current post. */
			$likes = Request::get_instance()->get_likes_data( $post_id );
			if ( count( $likes ) > 0 ) {

				$count = $likes[1] + $likes[2] + $likes[3];
				$rating = ( $likes[1] * 5 + $likes[3] ) / $count;
				$rating = round( $rating, 2 );

				return apply_filters( 'liker_rankmath_rating', $rating, $post_id );

			}

		}

		return apply_filters( 'liker_rankmath_rating', 0, $post_id );

	}

	/**
	 * Get rating count.
	 *
	 * @access private
	 * @return string
	 **/
	public function rankmath_liker_count_callback(): string {

		$post_id = get_the_ID();
		if ( $post_id ) {

			/** Get likes values for current post. */
			$likes = Request::get_instance()->get_likes_data( $post_id );
			if ( count( $likes ) > 0 ) {

				$count = $likes[1] + $likes[2] + $likes[3];
				return apply_filters( 'liker_rankmath_count', $count, $post_id );

			}

		}

		return apply_filters( 'liker_rankmath_count', 0, $post_id );

	}

	/**
	 * Main RankMathCompatibility Instance.
	 * Insures that only one instance of RankMathCompatibility exists in memory at any one time.
	 *
	 * @static
     * @return Compatibility
     * @access public
	 *
	 */
	public static function get_instance(): Compatibility {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
