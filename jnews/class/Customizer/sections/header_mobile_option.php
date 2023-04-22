<?php

$menus   = \JNews\Util\Cache::get_menu();
$menus   = array_combine( wp_list_pluck( $menus, 'slug' ), wp_list_pluck( $menus, 'name' ) );
$options = array();

$options[] = [
	'id'          => 'jnews_mobile_menu_follow',
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
	'id'    => 'jnews_header_mobile_midbar_setting',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Mobile Header - Middle Bar', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_header_mobile_midbar_scheme',
	'transport'   => 'postMessage',
	'default'     => 'dark',
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Middle Bar Scheme', 'jnews' ),
	'description' => esc_html__( 'Choose your menu scheme.', 'jnews' ),
	'multiple'    => 1,
	'choices'     => [
		'normal' => esc_attr__( 'Normal Style (Light)', 'jnews' ),
		'dark'   => esc_attr__( 'Dark Style', 'jnews' ),
	],
	'output'      => [
		[
			'method'   => 'class-masking',
			'element'  => '.jeg_mobile_bottombar',
			'property' => [
				'normal' => 'normal',
				'dark'   => 'dark',
			],
		],
	],
];

$options[] = [
	'id'        => 'jnews_header_mobile_midbar_height',
	'transport' => 'postMessage',
	'default'   => 60,
	'type'      => 'jnews-slider',
	'label'     => esc_html__( 'Middle Bar Height', 'jnews' ),
	'choices'   => [
		'min'  => '30',
		'max'  => '150',
		'step' => '1',
	],
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_mobile_bottombar',
			'property' => 'height',
			'units'    => 'px',
		],
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_mobile_bottombar',
			'property' => 'line-height',
			'units'    => 'px',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_mobile_midbar_background_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Background Color', 'jnews' ),
	'description' => esc_html__( 'Set background color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_mobile_midbar, .jeg_mobile_midbar.dark",
			'property' => 'background',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_mobile_midbar_enable_gradient',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Gradient', 'jnews' ),
	'description' => esc_html__( 'Enable mobile bar gradient', 'jnews' ),
];

$options[] = [
	'id'              => 'jnews_header_mobile_midbar_gradient_color',
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
	'description'     => esc_html__( 'Mobile middle bar gradient color.', 'jnews' ),
	'output'          => [
		[
			'method'  => 'gradient',
			'element' => ".jeg_mobile_midbar, .jeg_mobile_midbar.dark",
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_header_mobile_midbar_enable_gradient',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_mobile_midbar_text_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Default Text color', 'jnews' ),
	'description' => esc_html__( 'Top bar text color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_mobile_midbar, .jeg_mobile_midbar.dark",
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_mobile_midbar_link_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Default Link color', 'jnews' ),
	'description' => esc_html__( 'Middle bar link color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => ".jeg_mobile_midbar a, .jeg_mobile_midbar.dark a",
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'        => 'jnews_header_mobile_midbar_border_top_height',
	'transport' => 'postMessage',
	'default'   => 0,
	'type'      => 'jnews-slider',
	'label'     => esc_html__( 'Bottom Bar Border Height', 'jnews' ),
	'choices'   => [
		'min'  => '0',
		'max'  => '20',
		'step' => '1',
	],
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_mobile_midbar, .jeg_mobile_midbar.dark',
			'property' => 'border-top-width',
			'units'    => 'px',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_mobile_midbar_border_top_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Mobile Top Bar Border - Top Color', 'jnews' ),
	'description' => esc_html__( 'Mobile top border color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_mobile_midbar, .jeg_mobile_midbar.dark',
			'property' => 'border-top-color',
		],
	],
];

$options[] = [
	'id'    => 'jnews_header_mobile_menu_below_setting',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Mobile Header - Below Header Menu', 'jnews' ),
];

$options[] = [
	'id'              => 'jnews_header_mobile_menu_below_enable',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Below Header Menu', 'jnews' ),
    'description'     => esc_html__( 'Enable scrolling mobile menu. The menu is draggable horizontally.', 'jnews' ),
];

$options[] = [
	'id'              => 'jnews_header_mobile_menu_below',
	'transport'       => 'refresh',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Below Header Menu', 'jnews' ),
	'description'     => esc_html__( 'Choose menu that shown on the below mobile header.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => $menus,
	'active_callback' => [
		[
			'setting'  => 'jnews_header_mobile_menu_below_enable',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_mobile_menu_style',
	'transport'       => 'refresh',
	'default'         => 'style_1',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Menu Style', 'jnews' ),
	'description'     => esc_html__( 'Choose your navbar menu style.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => [
		'style_1' => esc_attr__( 'Style 1', 'jnews' ),
		'style_2' => esc_attr__( 'Style 2', 'jnews' ),
		'style_3' => esc_attr__( 'Style 3', 'jnews' ),
		'style_4' => esc_attr__( 'Style 4', 'jnews' ),
		'style_5' => esc_attr__( 'Style 5', 'jnews' ),
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_header_mobile_menu_below_enable',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_mobile_menu_background_color',
	'transport'       => 'refresh',
	'default'         => '',
	'type'            => 'jnews-color',
	'label'           => esc_html__( 'Background Color', 'jnews' ),
	'description'     => esc_html__( 'Mobile below header menu background color.', 'jnews' ),
	'choices'         => [
		'alpha' => true,
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_navbar_mobile_menu',
			'property' => 'background',
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_header_mobile_menu_below_enable',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_header_mobile_menu_text_color',
	'transport'       => 'refresh',
	'default'         => '',
	'type'            => 'jnews-color',
	'label'           => esc_html__( 'Text Color', 'jnews' ),
	'description'     => esc_html__( 'Mobile below header menu text color.', 'jnews' ),
	'choices'         => [
		'alpha' => true,
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_navbar_mobile_menu li a',
			'property' => 'color',
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_header_mobile_menu_below_enable',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'        => 'jnews_header_mobile_menu_border_top_height',
	'transport' => 'postMessage',
	'default'   => 0,
	'type'      => 'jnews-slider',
	'label'     => esc_html__( 'Border Top Height', 'jnews' ),
	'choices'   => [
		'min'  => '0',
		'max'  => '20',
		'step' => '1',
	],
	'output'    => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_navbar_mobile_menu',
			'property' => 'border-top-width',
			'units'    => 'px',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_mobile_menu_border_top_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Border Top Color', 'jnews' ),
	'description' => esc_html__( 'Mobile header menu border top color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_navbar_mobile_menu',
			'property' => 'border-top-color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_mobile_menu_gradient_enable',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Gradient', 'jnews' ),
	'description' => esc_html__( 'Enable below header menu gradient', 'jnews' ),
];

$options[] = [
	'id'              => 'jnews_header_mobile_menu_gradient_color',
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
	'description'     => esc_html__( 'Mobile below header menu gradient color.', 'jnews' ),
	'output'          => [
		[
			'method'  => 'gradient',
			'element' => '.jeg_navbar_mobile_menu .container, .jnews-dark-mode .jeg_navbar_mobile_menu .container',
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_header_mobile_menu_gradient_enable',
			'operator' => '==',
			'value'    => true,
		],
	],
];

return $options;