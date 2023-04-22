<?php

$options = array();

$options[] = array(
	'id'          => 'jnews_google_analytics_switch',
	'transport'   => 'refresh',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Use Google Analytics 4', 'jnews' ),
	'description' => esc_html__( 'Enable to use Google Analytics 4 instead of Universal Analytics', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_google_analytics_code',
	'transport'   => 'refresh',
	'default'     => '',
	'type'        => 'jnews-text',
	'label'       => esc_html__( 'Google Analytics Code', 'jnews' ),
	'description' => esc_html__( 'Insert your google analytics ID right here (e.g UA-XXXXX-Y for Universal Analytics or G-XXXXX for Google Analytics 4).', 'jnews' ),
);

if ( function_exists( 'JNews_AMP' ) ) {
	$options[] = array(
		'id'              => 'jnews_google_analytics_amp_code',
		'transport'       => 'refresh',
		'default'         => '',
		'type'            => 'jnews-text',
		'label'           => esc_html__( 'Google Analytics Code for AMP', 'jnews' ),
		'description'     => sprintf(
			__( 'Unfortunately, AMP doesn\'t support Google Analytics 4 yet. Please %1$sconnect Universal Analytics with Google Analytics 4%2$s and insert Universal Analytics ID right here (e.g UA-XXXXX-Y).', 'jnews' ),
			'<a target="_blank" href="https://support.google.com/analytics/answer/10312255?hl=en">',
			'</a>'
		),
		'active_callback' => array(
			array(
				'setting'  => 'jnews_google_analytics_switch',
				'operator' => '==',
				'value'    => true,
			),
		),
	);
}

$options[] = array(
	'id'          => 'jnews_google_analytics_local',
	'transport'   => 'refresh',
	'default'     => '',
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Load Analytics Locally', 'jnews' ),
	'description' => esc_html__( 'Load Google Analytics script code locally for better page speed performance.', 'jnews' ),
);

$last_update = jnews_get_option( 'local_gtag_file_modified_at' );
$options[]   = array(
	'id'              => 'jnews_google_analytics_gtag_local',
	'transport'       => 'refresh',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Localize gtag.js', 'jnews' ),
	'description'     => sprintf(
		__( 'Click %1$shere%2$s to fetch latest gtag.js code from Google\'s Server and update the local file.%3$s%4$sFile Last Update: %5$s%6$s%7$s', 'jnews' ),
		'<a class="jnews_google_analytics_local refresh" href="#">',
		'</a>',
		'</br>',
		'<strong>',
		'<span class="jnews_google_analytics_local last_update">' . ( is_int( $last_update ) ? date_i18n( 'm-d-Y H:i A', $last_update, true ) : '-' ) . '</span>',
		'</strong>',
		'<span class="jnews-spinner spinner"></span>'
	),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_google_analytics_switch',
			'operator' => '==',
			'value'    => true,
		),
		array(
			'setting'  => 'jnews_google_analytics_local',
			'operator' => '==',
			'value'    => true,
		),
	),
);

return $options;
