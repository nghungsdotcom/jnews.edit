<?php

$options = [];

$elements = [1, 2, 3, 'mobile', 'drawer'];

foreach ( $elements as $index => $i ) {
	$partial_refresh = [
		'selector'        => '.jeg_button_' . $i,
		'render_callback' => function () use ( $i ) {
			jnews_create_button( $i );
		},
	];

	$options[] = [
		'id'    => 'jnews_header_button_' . $i,
		'type'  => 'jnews-header',
		'label' => esc_html__( 'Button', 'jnews' ) . ' ' . $i,
	];

	$options[] = [
		'id'              => 'jnews_header_button_' . $i . '_text',
		'transport'       => 'postMessage',
		'default'         => 'Your text',
		'type'            => 'jnews-text',
		'label'           => esc_html__( 'Button Text', 'jnews' ),
		'partial_refresh' => [
			'jnews_header_button_' . $i . '_text' => $partial_refresh,
		],
	];

	$options[] = [
		'id'              => 'jnews_header_button_' . $i . '_icon',
		'transport'       => 'postMessage',
		'default'         => 'fa fa-envelope',
		'type'            => 'jnews-text',
		'label'           => esc_html__( 'Font Icon Class', 'jnews' ),
		'partial_refresh' => [
			'jnews_header_button_' . $i . '_icon' => $partial_refresh,
		],
	];

	$options[] = [
		'id'              => 'jnews_header_button_' . $i . '_type',
		'transport'       => 'postMessage',
		'default'         => 'url',
		'type'            => 'jnews-select',
		'label'           => esc_html__( 'Button Link Type', 'jnews' ),
		'choices'         => [
			'url'    => esc_attr__( 'URL', 'jnews' ),
			'submit' => esc_attr__( 'Frontend Submit', 'jnews-frontend-submit' ),
			'upload' => esc_attr__( 'Frontend Video Submit', 'jnews-video' ),
		],
		'partial_refresh' => [
			'jnews_header_button_' . $i . '_icon' => $partial_refresh,
		],
	];

	$options[] = [
		'id'              => 'jnews_header_button_' . $i . '_link',
		'transport'       => 'postMessage',
		'default'         => '#',
		'type'            => 'jnews-text',
		'label'           => esc_html__( 'Button Link', 'jnews' ),
		'partial_refresh' => [
			'jnews_header_button_' . $i . '_link' => $partial_refresh,
		],
		'active_callback' => [
			[
				'setting'  => 'jnews_header_button_' . $i . '_type',
				'operator' => '==',
				'value'    => 'url',
			],
		],
	];

	$options[] = [
		'id'        => 'jnews_header_button_' . $i . '_nofollow',
		'transport' => 'postMessage',
		'default'   => false,
		'type'      => 'jnews-toggle',
		'label'     => esc_html__( 'Enable Nofollow', 'jnews' ),
	];

	$options[] = [
		'id'              => 'jnews_header_button_' . $i . '_target',
		'transport'       => 'postMessage',
		'default'         => '_blank',
		'type'            => 'jnews-select',
		'label'           => esc_html__( 'Link Target', 'jnews' ),
		'choices'         => [
			'_blank'  => esc_attr__( 'Blank', 'jnews' ),
			'_self'   => esc_attr__( 'Self', 'jnews' ),
			'_parent' => esc_attr__( 'Parent', 'jnews' ),
		],
		'partial_refresh' => [
			'jnews_header_button_' . $i . '_target' => $partial_refresh,
		],
	];

	$options[] = [
		'id'              => 'jnews_header_button_' . $i . '_form',
		'transport'       => 'postMessage',
		'default'         => 'default',
		'type'            => 'jnews-radio-buttonset',
		'label'           => esc_html__( 'Button Style', 'jnews' ),
		'description'     => esc_html__( 'Choose button style.', 'jnews' ),
		'choices'         => [
			'default' => esc_attr__( 'Default', 'jnews' ),
			'round'   => esc_attr__( 'Round', 'jnews' ),
			'outline' => esc_attr__( 'Outline', 'jnews' ),
		],
		'partial_refresh' => [
			'jnews_header_button_' . $i . '_form' => $partial_refresh,
		],
	];

	$options[] = [
		'id'          => 'jnews_header_button_' . $i . '_background_color',
		'transport'   => 'postMessage',
		'default'     => '',
		'type'        => 'jnews-color',
		'label'       => esc_html__( 'Background Color', 'jnews' ),
		'description' => esc_html__( 'Background color.', 'jnews' ),
		'output'      => [
			[
				'method'   => 'inject-style',
				'element'  => '.jeg_button_' . $i . ' .btn',
				'property' => 'background',
			],
		],
	];

	$options[] = [
		'id'          => 'jnews_header_button_' . $i . '_background_hover_color',
		'transport'   => 'postMessage',
		'default'     => '',
		'type'        => 'jnews-color',
		'label'       => esc_html__( 'Background Hover Color', 'jnews' ),
		'description' => esc_html__( 'Background hover color.', 'jnews' ),
		'output'      => [
			[
				'method'   => 'inject-style',
				'element'  => '.jeg_button_' . $i . ' .btn:hover',
				'property' => 'background',
			],
		],
	];

	$options[] = [
		'id'          => 'jnews_header_button_' . $i . '_text_color',
		'transport'   => 'postMessage',
		'default'     => '',
		'type'        => 'jnews-color',
		'label'       => esc_html__( 'Text Color', 'jnews' ),
		'description' => esc_html__( 'Text color.', 'jnews' ),
		'output'      => [
			[
				'method'   => 'inject-style',
				'element'  => '.jeg_button_' . $i . ' .btn',
				'property' => 'color',
			],
		],
	];

	$options[] = [
		'id'          => 'jnews_header_button_' . $i . '_border_color',
		'transport'   => 'postMessage',
		'default'     => '',
		'type'        => 'jnews-color',
		'label'       => esc_html__( 'Border Color', 'jnews' ),
		'description' => esc_html__( 'Button border color.', 'jnews' ),
		'output'      => [
			[
				'method'   => 'inject-style',
				'element'  => '.jeg_button_' . $i . ' .btn',
				'property' => 'border-color',
			],
		],
	];
}

return $options;