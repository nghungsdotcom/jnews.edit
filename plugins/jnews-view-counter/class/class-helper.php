<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper
 */
class Helper {

	/**
	 * Localizes the Vanillajs datepicker.
	 *
	 * @global WP_Locale $wp_locale WordPress date and time locale object.
	 */
	public static function wp_localize_vanillajs_datepicker() {
		global $wp_locale;

		if ( ! wp_script_is( 'vanillajs-datepicker', 'enqueued' ) ) {
			return;
		}

		// Convert the PHP date format into jQuery UI's format.
		$datepicker_date_format = str_replace(
			array(
				'd',
				'j',
				'l',
				'z', // Day.
				'F',
				'M',
				'n',
				'm', // Month.
				'Y',
				'y', // Year.
			),
			array(
				'dd',
				'd',
				'DD',
				'o',
				'MM',
				'M',
				'm',
				'mm',
				'yy',
				'y',
			),
			get_option( 'date_format' )
		);

		$datepicker_defaults = wp_json_encode(
			array(
				'days'        => array_values( $wp_locale->weekday ),
				'daysShort'   => array_values( $wp_locale->weekday_abbrev ),
				'daysMin'     => array_values( $wp_locale->weekday_initial ),
				'months'      => array_values( $wp_locale->month ),
				'monthsShort' => array_values( $wp_locale->month_abbrev ),
				'today'       => __( 'Today' ),
				'clear'       => __( 'Clear' ),
				'weekStart'   => absint( get_option( 'start_of_week' ) ),
				'format'      => $datepicker_date_format,
				'titleFormat' => 'MM y',
			)
		);

		wp_add_inline_script( 'vanillajs-datepicker', "(function(){Datepicker.locales.en={$datepicker_defaults}})()" );
	}

	/**
	 * Returns server date.
	 *
	 * @return   string
	 */
	public static function curdate() {
		return current_time( 'Y-m-d', false );
	}

	/**
	 * Returns mysql datetime.
	 *
	 * @return   string
	 */
	public static function now() {
		return current_time( 'mysql' );
	}

	/**
	 * Returns site's timezone.
	 *
	 * @return  string
	 */
	public static function get_timezone() {
		$timezone_string = get_option( 'timezone_string' );

		if ( ! empty( $timezone_string ) ) {
			return $timezone_string;
		}

		$offset  = get_option( 'gmt_offset' );
		$sign    = $offset < 0 ? '-' : '+';
		$hours   = (int) $offset;
		$minutes = abs( ( $offset - (int) $offset ) * 60 );
		$offset  = sprintf( '%s%02d:%02d', $sign, abs( $hours ), $minutes );

		return $offset;
	}

	/**
	 * Checks for valid number.
	 *
	 * @param   int     number
	 * @return  bool
	 */
	public static function is_number( $number ) {
		return ! empty( $number ) && is_numeric( $number ) && ( intval( $number ) == floatval( $number ) );
	}

	/**
	 * Returns time.
	 *
	 * @return  string
	 */
	public static function microtime_float() {
		list($msec, $sec) = explode( ' ', microtime() );
		return (float) $msec + (float) $sec;
	}

	/**
	 * Merges two associative arrays recursively.
	 *
	 * @param   array   array1
	 * @param   array   array2
	 * @return  array
	 */
	public static function merge_array_r( array $array1, array $array2 ) {
		$merged = $array1;

		foreach ( $array2 as $key => &$value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = self::merge_array_r( $merged[ $key ], $value );
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * get_translate_id
	 *
	 * @param  int $post_id
	 * @return int
	 */
	public static function get_translate_id( $post_id ) {
		if ( function_exists( 'pll_get_post' ) ) {
			$result_id = pll_get_post( $post_id, pll_current_language() );

			if ( $result_id ) {
				$post_id = $result_id;
			}
		} elseif ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$post_id = icl_object_id( $post_id, 'post', true, ICL_LANGUAGE_CODE );
		}

		return $post_id;
	}

	/**
	 * arrange_index
	 *
	 * @param  array $result
	 * @param  array $result_ids
	 * @return array
	 */
	public static function arrange_index( $result, $result_ids, $pageviews = array() ) {
		$new_result = array();

		foreach ( $result_ids as $id ) {
			foreach ( $result as $post ) {
				if ( $id == $post->ID ) {
					if ( ! empty( $pageviews ) ) {
						if ( isset( $pageviews[ $id ] ) ) {
							$post->pageviews = $pageviews[ $id ];
						}
					}
					$new_result[] = $post;
					break;
				}
			}
		}

		return $new_result;
	}


	/**
	 * Get timestamp convertion.
	 *
	 * @param string $type
	 * @param int    $number
	 * @param int    $timestamp
	 * @return string
	 */
	public static function get_timestamp( $type, $number, $timestamp = true ) {
		$converter = array(
			'minutes' => 60,
			'hours'   => 3600,
			'days'    => 86400,
			'weeks'   => 604800,
			'months'  => 2592000,
			'years'   => 946080000,
		);

		return (int) ( ( $timestamp ? time() : 0 ) + $number * $converter[ $type ] );
	}

	/**
	 * Checks for valid date.
	 *
	 * @param   string $date
	 * @param   string $format
	 * @return  bool
	 */
	public static function is_valid_date( $date = null, $format = 'Y-m-d' ) {
		$d = \DateTime::createFromFormat( $format, $date );
		return $d && $d->format( $format ) === $date;
	}

	/**
	 * Returns an array of dates between two dates.
	 *
	 * @param   string $start_date
	 * @param   string $end_date
	 * @param   string $format
	 * @return  array|bool
	 */
	public static function get_date_range( $start_date = null, $end_date = null, $format = 'Y-m-d' ) {
		if ( self::is_valid_date( $start_date, $format ) && self::is_valid_date( $end_date, $format ) ) {
			$dates = array();

			$begin = new \DateTime( $start_date, new \DateTimeZone( self::get_timezone() ) );
			$end   = new \DateTime( $end_date, new \DateTimeZone( self::get_timezone() ) );

			if ( $begin < $end ) {
				while ( $begin <= $end ) {
					$dates[] = $begin->format( $format );
					$begin->modify( '+1 day' );
				}
			} else {
				while ( $begin >= $end ) {
					$dates[] = $begin->format( $format );
					$begin->modify( '-1 day' );
				}
			}

			return $dates;
		}

		return false;
	}

	/**
	 * Loads a template part into a template.
	 *
	 * @param  string $slug
	 * @param  string $name
	 */
	public static function get_template_part( $slug, $name = null ) {
		if ( function_exists( 'jnews_get_template_part' ) ) {
			jnews_get_template_part( $slug, $name, JNEWS_VIEW_COUNTER_DIR );
		}
	}

	/**
	 * Retrieve the name of the highest priority template file that exists.
	 *
	 * @param  string|array $template_names
	 * @param  boolean      $load
	 * @param  boolean      $require_once
	 * @return string
	 */
	public static function get_template_path( $template_names, $load = false, $require_once = true ) {
		if ( function_exists( 'jnews_get_template_path' ) ) {
			return jnews_get_template_path( $template_names, $load, $require_once, JNEWS_VIEW_COUNTER_DIR );
		}
	}

	/**
	 * Get view counter option
	 *
	 * @param  string $setting
	 * @param  mixed  $default
	 * @return mixed
	 */
	public static function get_view_counter_option( $setting, $default = false ) {
		$options = get_option( 'jnews_option', array() );
		$value   = $default;
		if ( isset( $options['view_counter'] ) && isset( $options['view_counter'][ $setting ] ) ) {
			$value = $options['view_counter'][ $setting ];
		}

		return apply_filters( "jnews_option_view_counter_{$setting}", $value );
	}

	/**
	 * Get view counter general option
	 *
	 * @param  string $setting
	 * @param  mixed  $default
	 * @return mixed
	 */
	public static function get_general_option( $setting, $default = false ) {
		$options = self::get_view_counter_option( 'general', JNews_View_Counter()->options['general'] );
		$value   = $default;
		if ( isset( $options[ $setting ] ) ) {
			$value = $options[ $setting ];
		}

		return apply_filters( "jnews_option_view_counter_general_{$setting}", $value );
	}

	/**
	 * Get view counter display option
	 *
	 * @param  string $setting
	 * @param  mixed  $default
	 * @return mixed
	 */
	public static function get_display_option( $setting, $default = false ) {
		$options = self::get_view_counter_option( 'display', JNews_View_Counter()->options['display'] );
		$value   = $default;
		if ( isset( $options[ $setting ] ) ) {
			$value = $options[ $setting ];
		}

		return apply_filters( "jnews_option_view_counter_display_{$setting}", $value );
	}

	/**
	 * Update view counter general option
	 *
	 * @param  string $setting
	 * @param  mixed  $value
	 * @return mixed
	 */
	public static function update_general_option( $setting, $value ) {
		$jnews_options       = get_option( 'jnews_option', array() );
		$options             = self::get_view_counter_option( 'general', JNews_View_Counter()->options['general'] );
		$options[ $setting ] = $value;

		if ( isset( $jnews_options['view_counter'] ) ) {
			if ( isset( $jnews_options['view_counter']['general'] ) ) {
				$jnews_options['view_counter']['general'][ $setting ] = $value;
			} else {
				$jnews_options['view_counter']['general'] = $options;
			}
		} else {
			$jnews_options['view_counter'] = array(
				'general' => $options,
			);
		}
		update_option( 'jnews_option', $jnews_options );
	}

	/**
	 * Update view counter display option
	 *
	 * @param  string $setting
	 * @param  mixed  $value
	 */
	public static function update_display_option( $setting, $value ) {
		$jnews_options       = get_option( 'jnews_option', array() );
		$options             = self::get_view_counter_option( 'display', JNews_View_Counter()->options['display'] );
		$options[ $setting ] = $value;

		if ( isset( $jnews_options['view_counter'] ) ) {
			if ( isset( $jnews_options['view_counter']['display'] ) ) {
				$jnews_options['view_counter']['display'][ $setting ] = $value;
			} else {
				$jnews_options['view_counter']['display'] = $options;
			}
		} else {
			$jnews_options['view_counter'] = array(
				'display' => $options,
			);
		}
		update_option( 'jnews_option', $jnews_options );
	}

	/**
	 * Update view counter display option
	 *
	 * @param  string $setting
	 * @param  mixed  $value
	 */
	public static function update_global_option( $setting, $value ) {
		$jnews_options = get_option( 'jnews_option', array() );
		$options       = array_merge( JNews_View_Counter()->options[ $setting ], $value );

		if ( isset( $jnews_options['view_counter'] ) ) {
			$jnews_options['view_counter'][ $setting ] = $options;
		} else {
			$jnews_options['view_counter'] = array(
				$setting => $options,
			);
		}
		update_option( 'jnews_option', $jnews_options );
	}

}
