<?php

$options = [];

$boxed_enabled = [
	'setting'  => 'jnews_boxed_layout',
	'operator' => '==',
	'value'    => true,
];

$options[] = [
	'id'    => 'jnews_boxed_layout_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Box Layout', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_boxed_layout',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Boxed Layout', 'jnews' ),
	'description' => esc_html__( 'By enabling boxed layout, you can use background image.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'add-class',
			'element'  => 'body',
			'property' => 'jeg_boxed',
		],
	],
];

$options[] = [
	'id'              => 'jnews_background_color',
	'transport'       => 'postMessage',
	'default'         => '#ffffff',
	'type'            => 'jnews-color',
	'label'           => esc_html__( 'Background Color', 'jnews' ),
	'description'     => esc_html__( 'Set website background color.', 'jnews' ),
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => 'body.jnews',
			'property' => 'background-color',
		],
	],
	'active_callback' => [ $boxed_enabled ],
];

$options[] = [
	'id'              => 'jnews_background_image',
	'transport'       => 'postMessage',
	'default'         => '',
	'type'            => 'jnews-image',
	'label'           => esc_html__( 'Background Image', 'jnews' ),
	'description'     => esc_html__( 'Upload your background image.', 'jnews' ),
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => 'body.jnews',
			'property' => 'background-image',
			'prefix'   => 'url("',
			'suffix'   => '")',
		],
	],
	'active_callback' => [ $boxed_enabled ],
];

$options[] = [
	'id'              => 'jnews_background_repeat',
	'transport'       => 'postMessage',
	'default'         => '',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Background Repeat', 'jnews' ),
	'description'     => esc_html__( 'Set background image repeat.', 'jnews' ),
	'choices'         => [
		''          => '',
		'repeat-x'  => 'Repeat Horizontal',
		'repeat-y'  => 'Repeat Vertical',
		'repeat'    => 'Repeat Image',
		'no-repeat' => 'No Repeat',
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => 'body.jnews',
			'property' => 'background-repeat',
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_background_image',
			'operator' => '!=',
			'value'    => '',
		],
		$boxed_enabled,
	],
];

$options[] = [
	'id'              => 'jnews_background_position',
	'transport'       => 'postMessage',
	'default'         => '',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Background Position', 'jnews' ),
	'description'     => esc_html__( 'Set background image position.', 'jnews' ),
	'choices'         => [
		''              => '',
		'left left'     => 'Left Left',
		'left center'   => 'Left Center',
		'left right'    => 'Left Right',
		'center left'   => 'Center Left',
		'center center' => 'Center Center',
		'center right'  => 'Center Right',
		'right left'    => 'Right Left',
		'right center'  => 'Right Center',
		'right right'   => 'Right Right',
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => 'body.jnews',
			'property' => 'background-position',
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_background_image',
			'operator' => '!=',
			'value'    => '',
		],
		$boxed_enabled,
	],
];

$options[] = [
	'id'              => 'jnews_background_fixed',
	'transport'       => 'postMessage',
	'default'         => '',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Attachment Background', 'jnews' ),
	'description'     => esc_html__( 'Set background image attachment.', 'jnews' ),
	'choices'         => [
		''       => '',
		'fixed'  => 'Fixed',
		'scroll' => 'Scroll',
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => 'body.jnews',
			'property' => 'background-attachment',
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_background_image',
			'operator' => '!=',
			'value'    => '',
		],
		$boxed_enabled,
	],
];

$options[] = [
	'id'              => 'jnews_background_size',
	'transport'       => 'postMessage',
	'default'         => 'inherit',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Background Size', 'jnews' ),
	'description'     => esc_html__( 'Set background image size.', 'jnews' ),
	'choices'         => [
		''        => '',
		'cover'   => 'Cover',
		'contain' => 'Contain',
		'initial' => 'Initial',
		'inherit' => 'Inherit',
	],
	'output'          => [
		[
			'method'   => 'inject-style',
			'element'  => 'body.jnews',
			'property' => 'background-size',
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_background_image',
			'operator' => '!=',
			'value'    => '',
		],
		$boxed_enabled,
	],
];

$options[] = [
	'id'    => 'jnews_boxed_container_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Box Container', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_boxed_container',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Boxed Container', 'jnews' ),
	'description' => esc_html__( 'This option will affect some element on themes for example single post, archive, widget, etc', 'jnews' ),
	'output'      => [
		[
			'method'   => 'add-class',
			'element'  => 'body',
			'property' => 'jeg_boxed_container',
		],
	],
];

$options[] = [
	'id'          => 'jnews_boxed_container_shadow',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Boxed Container Shadow', 'jnews' ),
	'description' => esc_html__( 'This option will affect some element on themes for example single post, archive, widget, etc', 'jnews' ),
	'output'      => [
		[
			'method'   => 'add-class',
			'element'  => 'body',
			'property' => 'jeg_boxed_container_shadow',
		],
	],
];


$options[] = [
	'id'    => 'jnews_container_background_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Container Background', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_container_background',
	'transport'   => 'postMessage',
	'default'     => '#ffffff',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Container Background Color', 'jnews' ),
	'description' => esc_html__( 'Inside container background color.', 'jnews' ),
	'choices'     => [
		'alpha' => true,
	],
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_container, .jeg_content, .jeg_boxed .jeg_main .jeg_container, .jeg_autoload_separator',
			'property' => 'background-color',
		],
	],
];


$options[] = [
	'id'    => 'jnews_laptop_container_section',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Laptop Container Width', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_override_container_width',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Override Container Width', 'jnews' ),
	'description' => esc_html__( 'override container & content width for laptop or desktop', 'jnews' ),
];


$options[] = [
	'id'              => 'jnews_content_laptop',
	'transport'       => 'postMessage',
	'default'         => '1170',
	'type'            => 'jnews-range-slider',
	'label'           => esc_html__( '[ Laptop ] Content Width ', 'jnews' ),
	'description'     => esc_html__( 'Content width on Laptop ( width less than 1440px )', 'jnews' ),
	'choices'         => [
		'min'  => '1170',
		'max'  => '1370',
		'step' => '1',
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_override_container_width',
			'operator' => '==',
			'value'    => true,
		],
	],
	'output'          => [
		[
			'mediaquery' => '@media only screen and (min-width : 1200px)',
			'method'     => 'inject-style',
			'element'    => '.container, .jeg_vc_content > .vc_row, .jeg_vc_content > .wpb-content-wrapper > .vc_row, .jeg_vc_content > .vc_element > .vc_row, .jeg_vc_content > .wpb-content-wrapper > .vc_element > .vc_row, .jeg_vc_content > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper, .jeg_vc_content > .wpb-content-wrapper > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper, .jeg_vc_content > .vc_element > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper, .jeg_vc_content > .wpb-content-wrapper > .vc_element > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper',
			'property'   => 'max-width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1200px)',
			'method'     => 'inject-style',
			'element'    => '.elementor-section.elementor-section-boxed > .elementor-container',
			'property'   => 'max-width',
			'units'      => 'px',
		],
	],
];

$options[] = [
	'id'              => 'jnews_container_laptop',
	'transport'       => 'postMessage',
	'default'         => '1230',
	'type'            => 'jnews-range-slider',
	'label'           => esc_html__( '[ Laptop ] Boxed Container', 'jnews' ),
	'description'     => esc_html__( 'Boxed container width on Laptop ( width less than 1440px )', 'jnews' ),
	'choices'         => [
		'min'  => '1230',
		'max'  => '1370',
		'step' => '1',
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_override_container_width',
			'operator' => '==',
			'value'    => true,
		],
		$boxed_enabled,
	],
	'output'          => [
		[
			'mediaquery' => '@media only screen and (min-width : 1200px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_boxed:not(.jeg_sidecontent) .jeg_viewport, .jeg_boxed.jeg_sidecontent .jeg_container, .jeg_boxed .jeg_container .jeg_navbar_wrapper.jeg_sticky_nav',
			'property'   => 'max-width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1200px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_single_fullwidth.jeg_boxed .content-inner > .alignfull',
			'property'   => 'width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1200px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_single_fullwidth.jeg_boxed .content-inner > .alignfull',
			'property'   => 'max-width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1200px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_single_fullwidth.jeg_boxed .content-inner > .alignfull',
			'property'   => 'margin-left',
			'prefix'     => 'calc(-1 * (',
			'suffix'     => ' - 100%) / 2)',
			'units'      => 'px',
		],
	],
];


$options[] = [
	'id'              => 'jnews_content_desktop',
	'transport'       => 'postMessage',
	'default'         => '1170',
	'type'            => 'jnews-range-slider',
	'label'           => esc_html__( '[ Desktop ] Content Width', 'jnews' ),
	'description'     => esc_html__( 'Content width on Desktop ( width more than 1440px )', 'jnews' ),
	'choices'         => [
		'min'  => '1170',
		'max'  => '1400',
		'step' => '1',
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_override_container_width',
			'operator' => '==',
			'value'    => true,
		],
	],
	'output'          => [
		[
			'mediaquery' => '@media only screen and (min-width : 1441px)',
			'method'     => 'inject-style',
			'element'    => '.container, .jeg_vc_content > .vc_row, .jeg_vc_content > .wpb-content-wrapper > .vc_row, .jeg_vc_content > .vc_element > .vc_row, .jeg_vc_content > .wpb-content-wrapper > .vc_element > .vc_row, .jeg_vc_content > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper, .jeg_vc_content > .wpb-content-wrapper > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper, .jeg_vc_content > .vc_element > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper, .jeg_vc_content > .wpb-content-wrapper > .vc_element > .vc_row[data-vc-full-width="true"]:not([data-vc-stretch-content="true"]) > .jeg-vc-wrapper',
			'property'   => 'max-width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1441px)',
			'method'     => 'inject-style',
			'element'    => '.elementor-section.elementor-section-boxed > .elementor-container',
			'property'   => 'max-width',
			'units'      => 'px',
		],
	],
];

$options[] = [
	'id'              => 'jnews_container_desktop',
	'transport'       => 'postMessage',
	'default'         => '1230',
	'type'            => 'jnews-range-slider',
	'label'           => esc_html__( '[ Desktop ] Boxed Container', 'jnews' ),
	'description'     => esc_html__( 'Boxed container width on Desktop ( width more than 1440px )', 'jnews' ),
	'choices'         => [
		'min'  => '1230',
		'max'  => '1600',
		'step' => '1',
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_override_container_width',
			'operator' => '==',
			'value'    => true,
		],
		$boxed_enabled,
	],
	'output'          => [
		[
			'mediaquery' => '@media only screen and (min-width : 1441px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_boxed:not(.jeg_sidecontent) .jeg_viewport, .jeg_boxed.jeg_sidecontent .jeg_container, .jeg_boxed .jeg_container .jeg_navbar_wrapper.jeg_sticky_nav, .jeg_boxed:not(.jeg_sidecontent) .jeg_container .jeg_navbar_wrapper.jeg_sticky_nav',
			'property'   => 'max-width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1441px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_single_fullwidth.jeg_boxed .content-inner > .alignfull',
			'property'   => 'width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1441px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_single_fullwidth.jeg_boxed .content-inner > .alignfull',
			'property'   => 'max-width',
			'units'      => 'px',
		],
		[
			'mediaquery' => '@media only screen and (min-width : 1441px)',
			'method'     => 'inject-style',
			'element'    => '.jeg_single_fullwidth.jeg_boxed .content-inner > .alignfull',
			'property'   => 'margin-left',
			'prefix'     => 'calc(-1 * (',
			'suffix'     => ' - 100%) / 2)',
			'units'      => 'px',
		],
	],
];

return $options;
