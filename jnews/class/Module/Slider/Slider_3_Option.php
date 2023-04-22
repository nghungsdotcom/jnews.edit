<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Slider;

class Slider_3_Option extends SliderOptionAbstract {

	protected $default_number = 5;

	public function compatible_column() {
		return [ 4, 8, 12 ];
	}

	public function get_module_name() {
		return esc_html__( 'JNews - Slider 3', 'jnews' );
	}

	public function excerpt_length() {
		return 15;
	}

	public function set_slider_option() {
		$this->options[] = [
			'type'        => 'slider',
			'param_name'  => 'number_item',
			'heading'     => esc_html__( 'Number of Column', 'jnews' ),
			'description' => esc_html__( 'Set number of carousel item column on each slide.', 'jnews' ),
			'min'         => 1,
			'max'         => 5,
			'step'        => 1,
			'std'         => 3,
		];
		$this->options[] = [
			'type'        => 'checkbox',
			'param_name'  => 'enable_autoplay',
			'heading'     => esc_html__( 'Enable Autoplay', 'jnews' ),
			'description' => esc_html__( 'Check this option to enable auto play.', 'jnews' ),
		];
		$this->options[] = [
			'type'        => 'slider',
			'param_name'  => 'autoplay_delay',
			'heading'     => esc_html__( 'Autoplay Delay', 'jnews' ),
			'description' => esc_html__( 'Set your autoplay delay (in millisecond).', 'jnews' ),
			'min'         => 1000,
			'max'         => 10000,
			'step'        => 500,
			'std'         => 3000,
			'dependency'  => [
				'element' => 'enable_autoplay',
				'value'   => 'true',
			],
		];
		$this->options[] = [
			'type'        => 'dropdown',
			'param_name'  => 'date_format',
			'heading'     => esc_html__( 'Choose Date Format', 'jnews' ),
			'description' => esc_html__( 'Choose which date format you want to use.', 'jnews' ),
			'std'         => 'default',
			'value'       => [
				esc_html__( 'Relative Date/Time Format (ago)', 'jnews' ) => 'ago',
				esc_html__( 'WordPress Default Format', 'jnews' ) => 'default',
				esc_html__( 'Custom Format', 'jnews' ) => 'custom',
			],
		];
		$this->options[] = [
			'type'        => 'textfield',
			'param_name'  => 'date_format_custom',
			'heading'     => esc_html__( 'Custom Date Format', 'jnews' ),
			'description' => wp_kses( sprintf( __( 'Please write custom date format for your module, for more detail about how to write date format, you can refer to this <a href="%s" target="_blank">link</a>.', 'jnews' ), 'https://codex.wordpress.org/Formatting_Date_and_Time' ), wp_kses_allowed_html() ),
			'std'         => 'Y/m/d',
			'dependency'  => [
				'element' => 'date_format',
				'value'   => [ 'custom' ],
			],
		];
		$this->options[] = [
			'type'        => 'checkbox',
			'param_name'  => 'force_normal_image_load',
			'heading'     => esc_html__( 'Use Normal Image Load', 'jnews' ),
			'description' => esc_html__( 'Force it to use normal load image and optimize Largest Contentful Paint (LCP) when using this element at the top of your site', 'jnews' ),
		];
	}
}
