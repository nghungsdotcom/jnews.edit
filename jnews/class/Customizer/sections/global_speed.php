<?php

$options = array();

$options[] = array(
	'id'    => 'jnews_speed_optimization_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'General Settings', 'jnews' ),
);
$options[] = array(
	'id'          => 'jnews_enable_preload_page',
	'transport'   => 'refresh',
	'default'     => true,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Preload Page', 'jnews' ),
	'description' => esc_html__( 'Allows to speeds up page load times by preloading pages that are likely to be visited next.', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_load_necessary_asset',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Optimize Assets', 'jnews' ),
	'description' => esc_html__( 'Only load necessary assets based on the current page.', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_empty_base64',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Base64 Image', 'jnews' ),
	'description' => esc_html__( 'Use a base64 image for the empty image when using the lazy load image option.', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_ajax_megamenu',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Ajax Mega Menu', 'jnews' ),
	'description' => esc_html__( 'Use ajax load for the mega menu category.', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_disable_image_srcset',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Disable Image Srcset', 'jnews' ),
	'description' => esc_html__( 'Disable srcset on the image attribute.', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_enable_global_mediaelement',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Global MediaElement', 'jnews' ),
	'description' => esc_html__( 'Enable this option if there are video or audio player issues', 'jnews' ),
);

if ( function_exists( 'autoptimize' ) ) {
	$options[] = array(
		'id'    => 'jnews_speed_optimization_autoptimize_header',
		'type'  => 'jnews-header',
		'label' => esc_html__( 'Autoptimize Settings', 'jnews' ),
	);
	$options[] = array(
		'id'          => 'jnews_speed_optimization_autoptimize_alert',
		'type'        => 'jnews-alert',
		'default'     => 'info',
		'label'       => esc_html__( 'Autoptimize Settings', 'jnews' ),
		'description' => wp_kses( __( 'Autoptimize settings will only work when <strong>Optimize JavaScript Code</strong> and <strong>Aggregate JS-files</strong> Enable, You can enable it from <strong>Dashboard</strong> &raquo; <strong>Settings</strong> &raquo; <strong>Autoptimize</string> &raquo; <strong>JS, CSS & HTML</strong>.', 'jnews' ), wp_kses_allowed_html() ),
	);
	$options[] = array(
		'id'          => 'jnews_extreme_autoptimize_script_loader',
		'transport'   => 'refresh',
		'default'     => false,
		'type'        => 'jnews-toggle',
		'label'       => esc_html__( 'Enable Optimized Script Loader', 'jnews' ),
		'description' => esc_html__( 'Enable this option for Autoptimize plugin to load only required scripts and delay scripts for a while. Please note that this option can break your site if any third-party plugins cannot be optimized.', 'jnews' ),
	);
	$options[] = array(
		'id'          => 'jnews_enable_async_javascript',
		'transport'   => 'refresh',
		'default'     => false,
		'type'        => 'jnews-toggle',
		'label'       => esc_html__( 'Enable Async Javascript', 'jnews' ),
		'description' => esc_html__( 'Allow Autoptimize script to be asynced or deferred to improve performance', 'jnews' ),
	);
	$options[] = array(
		'id'              => 'jnews_async_javascript_method',
		'transport'       => 'refresh',
		'default'         => 'async',
		'type'            => 'jnews-select',
		'label'           => esc_html__( 'Async Javascript Method', 'jnews' ),
		'description'     => esc_html__( 'Please select the method (async or defer) that you wish to use', 'jnews' ),
		'multiple'        => 1,
		'choices'         => array(
			'async' => esc_attr__( 'Async', 'jnews' ),
			'defer' => esc_attr__( 'Defer', 'jnews' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'jnews_enable_async_javascript',
				'operator' => '==',
				'value'    => true,
			),
		),
	);
}

$options[] = array(
	'id'    => 'jnews_speed_optimization_font_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Font Settings', 'jnews' ),
);
$options[] = array(
	'id'          => 'jnews_enable_font_preloading',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Font Preloading', 'jnews' ),
	'description' => esc_html__( 'Remove any flash of unstyled text/icon ( FOUT ).', 'jnews' ),
);

if ( function_exists( 'vc_asset_url' ) ) {
	$options[] = array(
		'id'              => 'jnews_enable_font_preloading_vc',
		'transport'       => 'refresh',
		'default'         => false,
		'type'            => 'jnews-toggle',
		'label'           => esc_html__( 'Enable Font Preloading for WPBakery Page Builder', 'jnews' ),
		'description'     => esc_html__( 'Remove any flash of unstyled text/icon ( FOUT ) on WPBakery Page Builder.', 'jnews' ),
		'active_callback' => array(
			array(
				'setting'  => 'jnews_enable_font_preloading',
				'operator' => '==',
				'value'    => true,
			),
		),
	);
}

if ( defined( 'ELEMENTOR_VERSION' ) ) {
	$options[] = array(
		'id'              => 'jnews_enable_font_preloading_elementor',
		'transport'       => 'refresh',
		'default'         => false,
		'type'            => 'jnews-toggle',
		'label'           => esc_html__( 'Enable Font Preloading for Elementor', 'jnews' ),
		'description'     => esc_html__( 'Remove any flash of unstyled text/icon ( FOUT ) on Elementor.', 'jnews' ),
		'active_callback' => array(
			array(
				'setting'  => 'jnews_enable_font_preloading',
				'operator' => '==',
				'value'    => true,
			),
		),
	);
}

return $options;
