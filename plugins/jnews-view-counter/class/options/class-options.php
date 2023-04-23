<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Options
 *
 * @package JNEWS_VIEW_COUNTER
 */
class Options {
	/**
	 * Customizer
	 *
	 * @var \Jeg\Customizer\Customizer|boolean
	 */
	private $customizer;

	/**
	 * Options Construct.
	 */
	public function __construct() {
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_control_css' ) );
		add_action( 'jeg_register_customizer_option', array( $this, 'customizer_option' ) );
		add_filter( 'jeg_register_lazy_section', array( $this, 'lazy_section' ), 99 );
	}

	/**
	 * Load additional customizer style
	 */
	public function customize_control_css() {
		wp_enqueue_style( 'jnews-view-counter-additional-customizer', JNEWS_VIEW_COUNTER_URL . '/assets/css/admin/additional-customizer.css', null, JNEWS_VIEW_COUNTER_VERSION );
	}

	/**
	 * Customizer
	 *
	 * @return \Jeg\Customizer\Customizer|boolean
	 */
	private function customizer() {
		if ( ! isset( $this->customizer ) && ! $this->customizer ) {
			if ( function_exists( 'jnews_customzier' ) ) {
				$this->customizer = jnews_customizer();
			} elseif ( class_exists( '\Jeg\Customizer\Customizer' ) ) {
				$this->customizer = \Jeg\Customizer\Customizer::get_instance();
			} else {
				$this->customizer = false;
			}
		}
		return $this->customizer;
	}

	/**
	 * Register new customizer option
	 */
	public function customizer_option() {
		if ( $this->customizer() ) {
			$this->set_section();
		}
	}

	/**
	 * Set new section panel
	 */
	private function set_section() {
		$section = array(
			'id'         => 'jnews_view_counter',
			'title'      => esc_html__( 'JNews : View Counter Setting', 'jnews-view-counter' ),
			'panel'      => null,
			'priority'   => 192,
			'type'       => 'jnews-lazy-section',
			'dependency' => array(),
		);
		$this->customizer()->add_section( $section );
	}

	/**
	 * Register new section and their respective file
	 *
	 * @param $result
	 *
	 * @return Jeg\Customizer\Customizer::get_lazy_section_files
	 */
	public function lazy_section( $result ) {
		$result['jnews_view_counter'][] = JNEWS_VIEW_COUNTER_DIR . 'class/options/sections/view-counter-customizer.php';

		return $result;
	}
}
