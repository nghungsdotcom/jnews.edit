<?php

$options = [];

$options[] = [
	'id'          => 'jnews_additional_font',
	'transport'   => 'postMessage',
	'type'        => 'jnews-repeater',
	'label'       => esc_html__( 'Upload Your Font', 'jnews' ),
	'description' => wp_kses( sprintf( __( "You can generate your font using
                                <a href='%s' target='_blank'>font squirrel generator</a>.
                                <br/>You will need to <strong>refresh your customizer</strong> to see your font on the font list.", "jnews" ), "http://www.fontsquirrel.com/tools/webfont-generator" ),
		wp_kses_allowed_html() ),
	'default'     => [],
	'row_label'   => [
		'type'  => 'text',
		'value' => esc_attr__( 'Custom font', 'jnews' ),
		'field' => false,
	],
	'fields'      => [
		'font_name'   => [
			'type'        => 'text',
			'label'       => esc_attr__( 'Font Name', 'jnews' ),
			'description' => esc_attr__( 'Please fill your font name. You can put same font name on font uploader in case you have several types of font (bold, italic or other).', 'jnews' ),
			'default'     => '',
		],
		'font_weight' => [
			'type'        => 'select',
			'label'       => esc_attr__( 'Font Weight', 'jnews' ),
			'description' => esc_attr__( 'Please choose this file\'s font weight.', 'jnews' ),
			'choices'     => [
				'100' => '100',
				'200' => '200',
				'300' => '300',
				'400' => '400',
				'500' => '500',
				'600' => '600',
				'700' => '700',
				'800' => '800',
				'900' => '900',
			],
			'default'     => '400',
		],
		'font_style'  => [
			'type'        => 'select',
			'label'       => esc_attr__( 'Font Style', 'jnews' ),
			'description' => esc_attr__( 'Please fill this file\'s font style.', 'jnews' ),
			'choices'     => [
				'italic' => esc_attr__( 'Italic', 'jnews' ),
				'normal' => esc_attr__( 'Regular', 'jnews' ),
			],
			'default'     => 'regular',
		],
		'eot'         => [
			'type'      => 'upload',
			'label'     => esc_attr__( 'EOT File', 'jnews' ),
			'default'   => '',
			'mime_type' => 'application/vnd.ms-fontobject',
		],
		'woff'        => [
			'type'      => 'upload',
			'label'     => esc_attr__( 'WOFF File', 'jnews' ),
			'default'   => '',
			'mime_type' => 'application/x-font-woff',
		],
		'ttf'         => [
			'type'      => 'upload',
			'label'     => esc_attr__( 'TTF File', 'jnews' ),
			'default'   => '',
			'mime_type' => 'application/x-font-truetype,application/x-font-ttf,application/octet-stream,font/ttf',
		],
		'svg'         => [
			'type'      => 'upload',
			'label'     => esc_attr__( 'SVG File', 'jnews' ),
			'default'   => '',
			'mime_type' => 'image/svg+xml',
		],
	],
];

return $options;