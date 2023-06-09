<?php

$options = [];

// Field
$options[] = [
	'id'    => 'jnews_header_stickybar_setting',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Sticky Bar Setting', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_header_menu_follow',
	'transport'   => 'refresh',
	'default'     => 'scroll',
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Menu Following Mode', 'jnews' ),
	'description' => esc_html__( 'Choose your navbar menu style.', 'jnews' ),
	'multiple'    => 1,
	'choices'     => [
		'fixed'  => esc_attr__( 'Always Follow', 'jnews' ),
		'scroll' => esc_attr__( 'Follow when Scroll Up', 'jnews' ),
		'pinned' => esc_attr__( 'Show when Scroll', 'jnews' ),
		'normal' => esc_attr__( 'No follow', 'jnews' ),
	],
];

$options[] = [
	'id'          => 'jnews_header_sticky_width',
	'transport'   => 'postMessage',
	'default'     => 'normal',
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Header Sticky Width', 'jnews' ),
	'description' => esc_html__( 'Choose header container width.', 'jnews' ),
	'multiple'    => 1,
	'choices'     => [
		'normal' => esc_attr__( 'Normal', 'jnews' ),
		'full'   => esc_attr__( 'Fullwidth', 'jnews' ),
	],
	'output'      => [
		[
			'method'   => 'class-masking',
			'element'  => '.jeg_header_sticky .jeg_header',
			'property' => [
				'normal' => 'normal',
				'full'   => 'full',
			],
		],
	],
];

$options[] = [
	'id'        => 'jnews_header_stickybar_height',
	'transport' => 'refresh',
	'default'   => 50,
	'type'      => 'jnews-slider',
	'label'     => esc_html__( 'Sticky Bar Height', 'jnews' ),
	'choices'   => [
		'min'  => '30',
		'max'  => '150',
		'step' => '1',
	],
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_stickybar.jeg_navbar,.jeg_navbar .jeg_nav_icon',
			'property' => 'height',
			'units'    => 'px',
		],
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_stickybar.jeg_navbar,
                                        .jeg_stickybar .jeg_main_menu:not(.jeg_menu_style_1) > li > a,
                                        .jeg_stickybar .jeg_menu_style_1 > li,
                                        .jeg_stickybar .jeg_menu:not(.jeg_main_menu) > li > a',
			'property' => 'line-height',
			'units'    => 'px',
		],
	],
];


$options[] = [
	'id'          => 'jnews_header_stickybar_boxed',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Boxed Navbar', 'jnews' ),
	'description' => esc_html__( 'Enable this option and convert nav bar into boxed.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'add-class',
			'element'  => '.jeg_header_sticky .jeg_navbar_wrapper',
			'property' => 'jeg_navbar_boxed',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_fitwidth',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Fit Width Navbar', 'jnews' ),
	'description' => esc_html__( 'Enable this option and nav bar will have fit width effect.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'add-class',
			'element'  => '.jeg_header_sticky .jeg_navbar_wrapper',
			'property' => 'jeg_navbar_fitwidth',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_border',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Navbar Border', 'jnews' ),
	'description' => esc_html__( 'Enable this option and nav bar will have border around it.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'add-class',
			'element'  => '.jeg_header_sticky .jeg_navbar_wrapper',
			'property' => 'jeg_navbar_menuborder',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_shadow',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Navbar Shadow', 'jnews' ),
	'description' => esc_html__( 'Enable this option and nav bar will have shadow around it.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'add-class',
			'element'  => '.jeg_header_sticky .jeg_navbar_wrapper',
			'property' => 'jeg_navbar_shadow',
		],
	],
];

$options[] = [
	'id'    => 'jnews_header_stickybar_style',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Sticky Bar Style', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_header_stickybar_scheme',
	'transport'   => 'postMessage',
	'default'     => 'jeg_navbar_normal',
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Menu Scheme', 'jnews' ),
	'description' => esc_html__( 'Choose your menu scheme.', 'jnews' ),
	'multiple'    => 1,
	'choices'     => [
		'jeg_navbar_normal' => esc_attr__( 'Normal Style (Light)', 'jnews' ),
		'jeg_navbar_dark'   => esc_attr__( 'Dark Style', 'jnews' ),
	],
	'output'      => [
		[
			'method'   => 'class-masking',
			'element'  => '.jeg_header_sticky .jeg_navbar_wrapper',
			'property' => [
				'jeg_navbar_normal' => 'jeg_navbar_normal',
				'jeg_navbar_dark'   => 'jeg_navbar_dark',
			],
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_background_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Sticky Bar Background Color', 'jnews' ),
	'description' => esc_html__( 'Set sticky bar background color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_header_sticky .jeg_navbar_wrapper:not(.jeg_navbar_boxed),
                                        .jeg_header_sticky .jeg_navbar_boxed .jeg_nav_row",
			'property' => 'background',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_enable_gradient',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Gradient', 'jnews' ),
	'description' => esc_html__( 'Enable sticky bar gradient', 'jnews' ),
];

$options[] = [
	'id'              => 'jnews_header_stickybar_gradient',
	'transport'       => 'postMessage',
	'default'         => [
		'degree'        => 90,
		'beginlocation' => 0,
		'endlocation'   => 100,
		'begincolor'    => "#dd3333",
		'endcolor'      => "#8224e3",
	],
	'type'            => 'jnews-gradient',
	'label'           => esc_html__( 'Gradient Color', 'jnews' ),
	'description'     => esc_html__( 'Sticky bar gradient color.', 'jnews' ),
	'output'          => [
		[
			'method'  => 'gradient',
			'element'  => ".jeg_header_sticky .jeg_navbar_wrapper:not(.jeg_navbar_boxed),
                                        .jeg_header_sticky .jeg_navbar_boxed .jeg_nav_row",
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_header_stickybar_enable_gradient',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_border_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Border Color', 'jnews' ),
	'description' => esc_html__( 'Sticky bar bottom color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_header_sticky .jeg_navbar_menuborder .jeg_main_menu > li:not(:last-child),
                                        .jeg_header_sticky .jeg_navbar_menuborder .jeg_nav_item, .jeg_navbar_boxed .jeg_nav_row,
                                        .jeg_header_sticky .jeg_navbar_menuborder:not(.jeg_navbar_boxed) .jeg_nav_left .jeg_nav_item:first-child",
			'property' => 'border-color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_text_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Default Text color', 'jnews' ),
	'description' => esc_html__( 'Sticky bar text color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_stickybar, .jeg_stickybar.dark",
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_link_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Default Link Color', 'jnews' ),
	'description' => esc_html__( 'Set sticky bar link color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_stickybar a, .jeg_stickybar.dark a",
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'        => 'jnews_header_stickybar_border_bottom_height',
	'transport' => 'postMessage',
	'default'   => 1,
	'type'      => 'jnews-slider',
	'label'     => esc_html__( 'Border Bottom Height', 'jnews' ),
	'choices'   => [
		'min'  => '0',
		'max'  => '20',
		'step' => '1',
	],
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_stickybar, .jeg_stickybar.dark',
			'property' => 'border-bottom-width',
			'units'    => 'px',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_stickybar_border_bottom_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Border Bottom Color', 'jnews' ),
	'description' => esc_html__( 'Set sticky Bar border bottom color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_stickybar, .jeg_stickybar.dark,
                                        .jeg_stickybar.jeg_navbar_boxed .jeg_nav_row',
			'property' => 'border-bottom-color',
		],
	],
];

return $options;