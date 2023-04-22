<?php

$options = [];

$options[] = [
	'id'          => 'jnews_header_language_text_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Text Color', 'jnews' ),
	'description' => esc_html__( 'Language text color.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_lang_switcher, .jeg_lang_switcher span',
			'property' => 'color',
		],
	],
];

$options[] = [
	'id'          => 'jnews_header_language_background_color',
	'transport'   => 'postMessage',
	'default'     => '',
	'type'        => 'jnews-color',
	'label'       => esc_html__( 'Background Color', 'jnews' ),
	'description' => esc_html__( 'Language background color.', 'jnews' ),
	'output'      => [
		[
			'method'   => 'inject-style',
			'element'  => '.jeg_lang_switcher',
			'property' => 'background',
		],
	],
];

return $options;