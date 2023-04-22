<?php

$options = array();

$options[] = array(
    'id'            => 'jnews_global_wpblocks_header',
    'type'          => 'jnews-header',
    'label'         => esc_html__( 'Global WP Blocks Settings','jnews' ),
);

$options[] = [
	'id'              => 'jnews_global_wpblocks_margin_top',
	'transport'       => 'postMessage',
	'default'         => 0,
	'type'            => 'jnews-slider',
	'label'           => esc_html__( 'WP Blocks Margin Top', 'jnews' ),
	'description'     => esc_html__( 'Set Margin Top of WP Block', 'jnews' ),
	'choices'         => [
		'min'  => '0',
		'max'  => '100',
		'step' => '1',
	],
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.content-inner > [class^="wp-block-"]:not(h1,h2,h3,h4,h5,h6)',
			'property' => 'margin-top',
			'units'    => 'px',
		],
	],
];

$options[] = [
	'id'              => 'jnews_global_wp_blocks_margin_bottom',
	'transport'       => 'postMessage',
	'default'         => 0,
	'type'            => 'jnews-slider',
	'label'           => esc_html__( 'WP Blocks Margin Bottom', 'jnews' ),
	'description'     => esc_html__( 'Set Margin Bottom of WP Block', 'jnews' ),
	'choices'         => [
		'min'  => '0',
		'max'  => '100',
		'step' => '1',
	],
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.content-inner > [class^="wp-block-"]:not(h1,h2,h3,h4,h5,h6)',
			'property' => 'margin-bottom',
			'units'    => 'px',
		],
	],
];

$options = apply_filters( 'jnews_global_wpblocks_option', $options );

return $options;
