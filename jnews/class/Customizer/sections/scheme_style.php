<?php

$options = array();


$options[] = array(
	'id'          => 'jnews_scheme_style_notice',
	'type'        => 'jnews-alert',
	'default'     => 'info',
	'label'       => esc_html__( 'Notice', 'jnews' ),
	'description' => wp_kses(
		__(
			'<ul>
                    <li>This option will create a new file as a scheme style</li>
                    <li>Every time you import a demo the options will be overridden using the scheme styles from the demo</li>
                </ul>',
			'jnews'
		),
		wp_kses_allowed_html()
	),
);

$options[] = array(
	'id'          => 'jnews_scheme_style',
	'transport'   => 'refresh',
	'type'        => 'jnews-code-editor',
	'code_type'   => 'text/css',
	'input_attrs' => array(
		'aria-describedby' => 'editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4',
	),
);

return $options;
