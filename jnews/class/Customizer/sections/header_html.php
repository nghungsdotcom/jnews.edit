<?php

$options = [];

$options[] = [
	'id'              => 'jnews_header_html_mobile',
	'transport'       => 'postMessage',
	'sanitize'        => 'jnews_sanitize_by_pass',
	'default'         => '',
	'type'            => 'jnews-textarea',
	'label'           => esc_html__( 'Mobile HTML', 'jnews' ),
	'description'     => esc_html__( 'HTML / Shortcode element.', 'jnews' ),
	'partial_refresh' => [
		'jnews_header_html_mobile' => [
			'selector'        => '.jeg_navbar_mobile_wrapper',
			'render_callback' => function () {
				get_template_part( 'fragment/header/mobile-builder' );
			},
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_html_drawer',
	'transport'       => 'postMessage',
	'sanitize'        => 'jnews_sanitize_by_pass',
	'default'         => '',
	'type'            => 'jnews-textarea',
	'label'           => esc_html__( 'Drawer HTML', 'jnews' ),
	'description'     => esc_html__( 'HTML / Shortcode element.', 'jnews' ),
	'partial_refresh' => [
		'jnews_header_html_drawer' => [
			'selector'        => '.jeg_mobile_wrapper .jeg_nav_html',
			'render_callback' => function () {
				get_template_part( 'fragment/header/element/mobile/aside/html' );
			},
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_html_1',
	'transport'       => 'postMessage',
	'sanitize'        => 'jnews_sanitize_by_pass',
	'default'         => '',
	'type'            => 'jnews-textarea',
	'label'           => esc_html__( 'HTML Element 1', 'jnews' ),
	'description'     => esc_html__( 'HTML / Shortcode element.', 'jnews' ),
	'partial_refresh' => [
		'jnews_header_html_1' => [
			'selector'        => '.jeg_header_wrapper',
			'render_callback' => function () {
				get_template_part( 'fragment/header/desktop-builder' );
			},
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_html_2',
	'transport'       => 'postMessage',
	'sanitize'        => 'jnews_sanitize_by_pass',
	'default'         => '',
	'type'            => 'jnews-textarea',
	'label'           => esc_html__( 'HTML Element 2', 'jnews' ),
	'description'     => esc_html__( 'HTML / Shortcode element.', 'jnews' ),
	'partial_refresh' => [
		'jnews_header_html_2' => [
			'selector'        => '.jeg_header_wrapper',
			'render_callback' => function () {
				get_template_part( 'fragment/header/desktop-builder' );
			},
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_html_3',
	'transport'       => 'postMessage',
	'sanitize'        => 'jnews_sanitize_by_pass',
	'default'         => '',
	'type'            => 'jnews-textarea',
	'label'           => esc_html__( 'HTML Element 3', 'jnews' ),
	'description'     => esc_html__( 'HTML / Shortcode element.', 'jnews' ),
	'partial_refresh' => [
		'jnews_header_html_3' => [
			'selector'        => '.jeg_header_wrapper',
			'render_callback' => function () {
				get_template_part( 'fragment/header/desktop-builder' );
			},
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_html_4',
	'transport'       => 'postMessage',
	'sanitize'        => 'jnews_sanitize_by_pass',
	'default'         => '',
	'type'            => 'jnews-textarea',
	'label'           => esc_html__( 'HTML Element 4', 'jnews' ),
	'description'     => esc_html__( 'HTML / Shortcode element.', 'jnews' ),
	'partial_refresh' => [
		'jnews_header_html_4' => [
			'selector'        => '.jeg_header_wrapper',
			'render_callback' => function () {
				get_template_part( 'fragment/header/desktop-builder' );
			},
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_html_5',
	'transport'       => 'postMessage',
	'sanitize'        => 'jnews_sanitize_by_pass',
	'default'         => '',
	'type'            => 'jnews-textarea',
	'label'           => esc_html__( 'HTML Element 5', 'jnews' ),
	'description'     => esc_html__( 'HTML / Shortcode element.', 'jnews' ),
	'partial_refresh' => [
		'jnews_header_html_5' => [
			'selector'        => '.jeg_header_wrapper',
			'render_callback' => function () {
				get_template_part( 'fragment/header/desktop-builder' );
			},
		],
	],
];

return $options;