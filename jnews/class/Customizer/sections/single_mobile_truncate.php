<?php

$options = [];

$options[] = [
	'id'          => 'jnews_mobile_truncate',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable mobile truncate', 'jnews' ),
	'description' => esc_html__( 'turn this option on to enable mobile truncate', 'jnews' ),
	'postvar'     => [
		[
			'redirect' => 'single_post_tag',
			'refresh'  => false,
		],
	],
];

$options[] = [
	'id'              => 'jnews_mobile_truncate_btn_bg',
	'transport'       => 'postMessage',
	'default'         => '',
	'type'            => 'jnews-color',
	'disable_color'   => true,
	'label'           => esc_html__( 'Mobile button background', 'jnews' ),
	'description'     => esc_html__( 'Mobile button background colors', 'jnews' ),
	'choices'         => [
		'alpha' => true,
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_mobile_truncate',
			'operator' => '==',
			'value'    => true,
		],
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => '.mobile-truncate .truncate-read-more span, .author-truncate .truncate-read-more span',
			'property' => 'background',
		],
	],
];

$options[] = [
	'id'              => 'jnews_mobile_truncate_btn_color',
	'transport'       => 'postMessage',
	'default'         => '',
	'type'            => 'jnews-color',
	'disable_color'   => true,
	'label'           => esc_html__( 'Mobile button color', 'jnews' ),
	'description'     => esc_html__( 'Mobile button text color', 'jnews' ),
	'choices'         => [
		'alpha' => true,
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_mobile_truncate',
			'operator' => '==',
			'value'    => true,
		],
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => '.mobile-truncate .truncate-read-more span, .author-truncate .truncate-read-more span',
			'property' => 'color',
		],
	],
];

return $options;