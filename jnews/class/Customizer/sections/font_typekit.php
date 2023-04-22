<?php

$options = [];

$options[] = [
	'id'          => 'jnews_type_kit_id',
	'transport'   => 'refresh',
	'default'     => '',
	'type'        => 'jnews-text',
	'label'       => esc_html__( 'Type Kit ID', 'jnews' ),
	'description' => esc_html__( 'Please input your type kit ID here.', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_type_kit',
	'transport'   => 'postMessage',
	'type'        => 'jnews-repeater',
	'label'       => esc_html__( 'Name your type kit font', 'jnews' ),
	'description' => esc_html__( 'Please provide your type kit name for your theme.', 'jnews' ),
	'default'     => [],
	'row_label'   => [
		'type'  => 'text',
		'value' => esc_attr__( 'Type kit font', 'jnews' ),
		'field' => false,
	],
	'fields'      => [
		'font_name' => [
			'type'    => 'text',
			'label'   => esc_attr__( 'Font Name', 'jnews' ),
			'default' => '',
		],
	],
];

return $options;