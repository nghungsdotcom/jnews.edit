<?php

$options = [];

$options[] = [
	'id'    => 'jnews_header_social_desktop',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Social - Desktop Header', 'jnews' ),
];

$options[] = [
	'id'        => "jnews_header_social_icon",
	'transport' => 'postMessage',
	'default'   => 'nobg',
	'type'      => 'jnews-select',
	'label'     => esc_html__( 'Social Icon Type', 'jnews' ),
	'multiple'  => 1,
	'choices'   => [
		'square'  => esc_attr__( 'Square', 'jnews' ),
		'rounded' => esc_attr__( 'Rounded', 'jnews' ),
		'circle'  => esc_attr__( 'Circle', 'jnews' ),
		'nobg'    => esc_attr__( 'No Background', 'jnews' ),
	],
	'output'    => [
		[
			'method'   => 'class-masking',
			'element'  => '.jeg_header .jeg_social_icon_block',
			'property' => [
				'square'  => 'square',
				'rounded' => 'rounded',
				'circle'  => 'circle',
				'nobg'    => 'nobg',
			],
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_social_icon_text_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Icon Color', 'jnews' ),
	'description' => esc_html__( 'Social icon text color.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_header .socials_widget > a > i.fa:before',
			'property' => 'color',
		],
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_header .socials_widget.nobg > a > span.jeg-icon svg',
			'property' => 'fill',
		],
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_header .socials_widget > a > span.jeg-icon svg',
			'property' => 'fill',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_social_icon_background',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Background Color', 'jnews' ),
	'description' => esc_html__( 'Social icon background.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_header .socials_widget > a > i.fa',
			'property' => 'background-color',
		],
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_header .socials_widget > a > span.jeg-icon',
			'property' => 'background-color',
		],
	],
];

$options[] = [
	'id'    => 'jnews_header_social_mobile_drawer',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Social - Mobile Drawer', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_header_drawer_social_icon_text_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Icon Color', 'jnews' ),
	'description' => esc_html__( 'Social icon text color.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_aside_item.socials_widget > a > i.fa:before',
			'property' => 'color',
		],
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_aside_item.socials_widget.nobg a span.jeg-icon svg',
			'property' => 'fill',
		],
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_aside_item.socials_widget a span.jeg-icon svg',
			'property' => 'fill',
		],
	],
];

return $options;