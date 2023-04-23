<?php
/*
	Plugin Name: JNews - Like
	Plugin URI: http://jegtheme.com/
	Description: JNews Like functionality
	Version: 11.0.0
	Author: Jegtheme
	Author URI: http://jegtheme.com
	License: GPL2
*/

defined( 'JNEWS_LIKE' ) or define( 'JNEWS_LIKE', 'jnews-like' );
defined( 'JNEWS_LIKE_VERSION' ) or define( 'JNEWS_LIKE_VERSION', '11.0.0' );
defined( 'JNEWS_LIKE_URL' ) or define( 'JNEWS_LIKE_URL', plugins_url( JNEWS_LIKE ) );
defined( 'JNEWS_LIKE_FILE' ) or define( 'JNEWS_LIKE_FILE', __FILE__ );
defined( 'JNEWS_LIKE_DIR' ) or define( 'JNEWS_LIKE_DIR', plugin_dir_path( __FILE__ ) );

require_once 'class.jnews-like.php';

/**
 * Get jnews option
 *
 * @param $setting
 * @param $default
 * @return mixed
 */
if ( ! function_exists( 'jnews_get_option' ) ) {
	function jnews_get_option( $setting, $default = null ) {
		$options = get_option( 'jnews_option', array() );
		$value   = $default;
		if ( isset( $options[ $setting ] ) ) {
			$value = $options[ $setting ];
		}
		return $value;
	}
}

/**
 * Like Option
 */
add_action( 'jeg_register_customizer_option', 'jnews_like_option' );

if ( ! function_exists( 'jnews_like_option' ) ) {
	function jnews_like_option() {
		require_once 'class.jnews-like-option.php';
		JNews_Like_Option::getInstance();
	}
}

add_filter( 'jeg_register_lazy_section', 'jnews_like_lazy_section' );

function jnews_like_lazy_section( $result ) {
	$result['jnews_like_section'][] = JNEWS_LIKE_DIR . 'like-option.php';
	return $result;
}

/**
 * Activation hook
 */
register_activation_hook( __FILE__, array( JNews_Like::getInstance(), 'activation_hook' ) );

/**
 * Render Like
 */
add_action( 'jnews_render_before_meta_right', 'jnews_like_element', 10, 2 );
add_action( 'jnews_render_meta_like', 'jnews_like_element', 10, 2 );

if ( ! function_exists( 'jnews_like_element' ) ) {
	function jnews_like_element( $post_id ) {
		JNews_Like::getInstance()->generate_element( $post_id );
	}
}

if ( ! function_exists( 'jnews_like_post_meta_element_option' ) ) {
	/**
	 * Add donation button to post meta element option
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	function jnews_like_post_meta_element_option( $options ) {
		foreach ( $options as $idx => $option ) {
			if ( 'meta_right' === $option['param_name'] || 'meta_left' === $option['param_name'] ) {
				$options[ $idx ]['value'][ esc_html__( 'Like Button', 'jnews-like' ) ] = 'like';
			}
		}
		return $options;
	}
}
add_filter( 'jnews_post_meta_element_options', 'jnews_like_post_meta_element_option' );

if ( ! function_exists( 'jnews_like_post_meta_element_render_meta' ) ) {
	/**
	 * Render post meta element
	 *
	 * @param string $data
	 * @param class  $class
	 * @param string $func
	 *
	 * @return string
	 */
	function jnews_like_post_meta_element_render_meta( $data, $class, $func ) {
		if ( ! method_exists( $class, $func ) && strpos( $func, 'render_like' ) !== false ) {
			ob_start();
			JNews_Like::getInstance()->generate_element( get_the_ID() );
			return ob_get_clean();
		}
		return $data;
	}
}
add_filter( 'jnews_post_meta_element_render_meta', 'jnews_like_post_meta_element_render_meta', 11, 3 );

if ( ! function_exists( 'jnews_like_post_meta_element_render_meta_back' ) ) {
	/**
	 * Render post meta element back
	 *
	 * @param string $data
	 * @param class  $class
	 * @param string $func
	 * @param array  $attr
	 *
	 * @return string
	 */
	function jnews_like_post_meta_element_render_meta_back( $data, $class, $func, $attr ) {
		if ( ! method_exists( $class, $func ) && strpos( $func, 'render_like' ) !== false ) {
			return '<div class="jeg_meta_like_container jeg_meta_like">
						<a class="like" href="#" data-id="472" data-type="like" data-message="">
							<i class="fa fa-thumbs-o-up"></i> <span>0</span>
						</a>
						<a class="dislike" href="#" data-id="472" data-type="dislike" data-message="">
							<i class="fa fa-thumbs-o-down fa-flip-horizontal"></i> <span>0</span>
						</a>
					</div>';
		}
		return $data;
	}
}
add_filter( 'jnews_post_meta_element_render_meta_back', 'jnews_like_post_meta_element_render_meta_back', 11, 4 );


/** Print Translation */

if ( ! function_exists( 'jnews_print_translation' ) ) {
	function jnews_print_translation( $string, $domain, $name ) {
		do_action( 'jnews_print_translation', $string, $domain, $name );
	}
}


if ( ! function_exists( 'jnews_print_main_translation' ) ) {
	add_action( 'jnews_print_translation', 'jnews_print_main_translation', 10, 2 );

	function jnews_print_main_translation( $string, $domain ) {
		call_user_func_array( 'esc_html_e', array( $string, $domain ) );
	}
}

/** Return Translation */

if ( ! function_exists( 'jnews_return_translation' ) ) {
	function jnews_return_translation( $string, $domain, $name, $escape = true ) {
		return apply_filters( 'jnews_return_translation', $string, $domain, $name, $escape );
	}
}

if ( ! function_exists( 'jnews_return_main_translation' ) ) {
	add_filter( 'jnews_return_translation', 'jnews_return_main_translation', 10, 4 );

	function jnews_return_main_translation( $string, $domain, $name, $escape = true ) {
		if ( $escape ) {
			return call_user_func_array( 'esc_html__', array( $string, $domain ) );
		} else {
			return call_user_func_array( '__', array( $string, $domain ) );
		}

	}
}


/**
 * Load Text Domain
 */

function jnews_like_load_textdomain() {
	 load_plugin_textdomain( JNEWS_LIKE, false, basename( __DIR__ ) . '/languages/' );
}

jnews_like_load_textdomain();
