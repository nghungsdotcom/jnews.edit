<?php

$options = array();

$last_update = jnews_get_option( 'google_fonts_list_modified_at' );
$options[]   = array(
	'id'          => 'jnews_google_fonts_enable_update',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Automatic Update Google Fonts', 'jnews' ),
	'description' => sprintf( __( 'Enable this option to automatically update Google Fonts. You will need Google Fonts Developer API, You can get the key %1$s here %2$s', 'jnews' ), '<a href="https://developers.google.com/fonts/docs/developer_api#identifying_your_application_to_google" target="_blank">', '</a>' ),
);

$options[] = array(
	'id'              => 'jnews_google_fonts_api_key',
	'transport'       => 'refresh',
	'default'         => '',
	'type'            => 'jnews-text',
	'label'           => esc_html__( 'Google Fonts Developer API Key', 'jnews' ),
	'description'     => sprintf(
		__( 'Click %1$shere%2$s to fetch latest Google Fonts from Google\'s Server and update the local file.%3$s%4$sFile Last Update: %5$s%6$s%7$s', 'jnews' ),
		'<a class="jnews_google_fonts_update refresh" href="#">',
		'</a>',
		'</br>',
		'<strong>',
		'<span class="jnews_google_fonts_update last_update">' . ( is_int( $last_update ) ? date_i18n( 'm-d-Y H:i A', $last_update, true ) : '-' ) . '</span>',
		'</strong>',
		'<span class="jnews-spinner spinner"></span>'
	),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_google_fonts_enable_update',
			'operator' => '==',
			'value'    => true,
		),
	),
);

return $options;
