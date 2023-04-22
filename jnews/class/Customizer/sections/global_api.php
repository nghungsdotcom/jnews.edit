<?php

$twitch_token       = get_option( 'jnews_option[jnews_twitch]', array() );
$twitch_label       = esc_html__( 'Connect Twitch Account', 'jnews' );
$twitch_description = sprintf( __( 'Connect your Twitch account by clicking this <a class="%1$s" href="%2$s" target="_blank">link</a> and refer to next page URL. Remember to set <strong>%3$s</strong> as the callback URL.', 'jnews' ), 'jnews_twitch_access_token twitch', get_admin_url() . 'widgets.php', home_url( '/social-token/twitch/' ) );
if ( is_array( $twitch_token ) && ! empty( $twitch_token ) && time() < $twitch_token['expire'] ) {
	$twitch_label       = sprintf( __( 'Connected as %s', 'jnews' ), $twitch_token['user'] );
	$twitch_description = sprintf( __( 'This token is valid until %1$s. Connect another account by clicking this <a class="%2$s" href="%3$s" target="_blank">link</a>.', 'jnews' ), date( 'F d, Y H:i:s', (int) $twitch_token['expire'] ), 'jnews_twitch_access_token twitch', get_admin_url() . 'widgets.php' );
}

$facebook_token       = get_option( 'jnews_option[jnews_facebook]', array() );
$facebook_label       = esc_html__( 'Connect Facebook Account', 'jnews' );
$facebook_description = sprintf( __( 'Connect your Facebook account by clicking this <a class="%1$s" href="%2$s" target="_blank">link</a> and refer to next page URL. Remember to set <strong>%3$s</strong> as the <strong>Valid OAuth Redirect URIs</strong>.', 'jnews' ), 'jnews_facebook_access_token facebook', get_admin_url() . 'widgets.php', home_url( '/social-token/facebook/' ) );
if ( is_array( $facebook_token ) && ! empty( $facebook_token ) && ( ! $facebook_token['expire'] || time() < $facebook_token['expire'] ) ) {
	$facebook_label       = sprintf( __( 'Account Connected', 'jnews' ) );
	$facebook_description = sprintf( __( 'This token is valid until %1$s. Connect another account by clicking this <a class="%2$s" href="%3$s" target="_blank">link</a>.', 'jnews' ), $facebook_token['expire'] ? date( 'F d, Y H:i:s', (int) $facebook_token['expire'] ) : 'indefinitely', 'jnews_facebook_access_token facebook', get_admin_url() . 'widgets.php' );
}

$options = array();

/** START YouTube Data API */
$options[] = array(
	'id'      => 'jnews_youtube_data_api_section',
	'type'    => 'jnews-header',
	'section' => 'jnews_youtube_data_api_section',
	'label'   => esc_html__( 'YouTube Data API', 'jnews' ),
);
$options[] = array(
	'id'          => 'jnews_youtube_api',
	'section'     => 'jnews_youtube_data_api_section',
	'transport'   => 'refresh',
	'default'     => '',
	'type'        => 'jnews-text',
	'label'       => esc_html__( 'Youtube API', 'jnews' ),
	'description' => sprintf(
		__( 'Insert your youtube API right here. For more information, <a href="%s">please go here</a>', 'jnews' ),
		'https://developers.google.com/youtube/v3/getting-started'
	),
);

$options[] = array(
	'id'          => 'jnews_youtube_playlist_cache',
	'section'     => 'jnews_youtube_data_api_section',
	'transport'   => 'postMessage',
	'default'     => '1',
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Youtube Playlist Cache Expired', 'jnews' ),
	'description' => esc_html__( 'Choose the expired time of Youtube playlist video data cache.', 'jnews' ),
	'choices'     => array(
		'1'  => esc_html__( '1 Hour', 'jnews' ),
		'2'  => esc_html__( '2 Hour', 'jnews' ),
		'3'  => esc_html__( '3 Hour', 'jnews' ),
		'no' => esc_html__( 'Disable Cache', 'jnews' ),
	),
);
/** END YouTube Data API */

/** START reCAPTCHA API */
$options[] = array(
	'id'      => 'jnews_recaptcha_api_section',
	'type'    => 'jnews-header',
	'section' => 'jnews_recaptcha_api_section',
	'label'   => esc_html__( 'reCAPTCHA API', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_enable_recaptcha_new',
	'section'     => 'jnews_recaptcha_api_section',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Enable Recaptcha', 'jnews' ),
	'description' => esc_html__( 'Enable this feature to use recaptcha feature.', 'jnews' ),
);

$options[] = array(
	'id'              => 'jnews_recaptcha_site_key',
	'section'         => 'jnews_recaptcha_api_section',
	'transport'       => 'refresh',
	'default'         => '',
	'type'            => 'jnews-text',
	'label'           => esc_html__( 'Google Recaptcha Site Key', 'jnews' ),
	'description'     => sprintf(
		__( 'Create your recaptcha site key, <a href="%s">please go here</a>', 'jnews' ),
		'https://www.google.com/recaptcha/admin'
	),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_enable_recaptcha_new',
			'operator' => '==',
			'value'    => true,
		),
	),
);

$options[] = array(
	'id'              => 'jnews_recaptcha_secret_key',
	'section'         => 'jnews_recaptcha_api_section',
	'transport'       => 'refresh',
	'default'         => '',
	'type'            => 'jnews-text',
	'label'           => esc_html__( 'Google Recaptcha Secret Key', 'jnews' ),
	'description'     => sprintf(
		__( 'Create your recaptcha site key, <a href="%s">please go here</a>', 'jnews' ),
		'https://www.google.com/recaptcha/admin'
	),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_enable_recaptcha_new',
			'operator' => '==',
			'value'    => true,
		),
	),
);

$options[] = array(
	'id'              => 'jnews_enable_recaptcha',
	'section'         => 'jnews_recaptcha_api_section',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Recaptcha for login popup', 'jnews' ),
	'description'     => esc_html__( 'Enable this feature to use recaptcha on login popup section.', 'jnews' ),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_enable_recaptcha_new',
			'operator' => '==',
			'value'    => true,
		),
	),
);

$options[] = array(
	'id'              => 'jnews_enable_recaptcha_comment',
	'section'         => 'jnews_recaptcha_api_section',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Recaptcha for comment', 'jnews' ),
	'description'     => esc_html__( 'Enable this feature to use recaptcha on comment section.', 'jnews' ),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_enable_recaptcha_new',
			'operator' => '==',
			'value'    => true,
		),
	),
);
/** END reCAPTCHA API */

/** START twitch API */
$options[] = array(
	'id'      => 'jnews_twitch_api_section',
	'type'    => 'jnews-header',
	'section' => 'jnews_twitch_api_section',
	'label'   => esc_html__( 'Twitch API', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_twitch_alert',
	'type'        => 'jnews-alert',
	'default'     => 'info',
	'section'     => 'jnews_twitch_api_section',
	'label'       => $twitch_label,
	'description' => $twitch_description,
);

$options[] = array(
	'id'          => 'jnews_twitch_client_id',
	'transport'   => 'refresh',
	'default'     => '',
	'section'     => 'jnews_twitch_api_section',
	'type'        => 'jnews-text',
	'label'       => esc_html__( 'Client ID', 'jnews' ),
	'description' => sprintf(
		__( 'Insert your Twitch Client ID right here. For more information, <a href="%s">please go here</a>', 'jnews' ),
		'https://dev.twitch.tv/dashboard/apps/create'
	),
);

$options[] = array(
	'id'          => 'jnews_twitch_client_secret',
	'transport'   => 'refresh',
	'default'     => '',
	'section'     => 'jnews_twitch_api_section',
	'type'        => 'jnews-text',
	'label'       => esc_html__( 'Client Secret', 'jnews' ),
	'description' => sprintf(
		__( 'Insert your Twitch Client Secret right here. For more information, <a href="%s">please go here</a>', 'jnews' ),
		'https://dev.twitch.tv/dashboard/apps/create'
	),
);
/** END twitch API */

/** Start Facebook API */
$options[] = array(
	'id'      => 'jnews_facebook_api_section',
	'type'    => 'jnews-header',
	'section' => 'jnews_facebook_api_section',
	'label'   => esc_html__( 'Facebook API', 'jnews' ),
);

$options[] = array(
	'id'          => 'jnews_facebook_alert',
	'type'        => 'jnews-alert',
	'default'     => 'info',
	'section'     => 'jnews_facebook_api_section',
	'label'       => $facebook_label,
	'description' => $facebook_description,
);

$options[] = array(
	'id'          => 'jnews_facebook_client_id',
	'transport'   => 'refresh',
	'default'     => '',
	'section'     => 'jnews_facebook_api_section',
	'type'        => 'jnews-text',
	'label'       => esc_html__( 'APP ID', 'jnews' ),
	'description' => sprintf(
		__( 'Insert your Facebook APP ID right here. For more information, <a href="%s">please go here</a>', 'jnews' ),
		'https://developers.facebook.com/apps'
	),
);

$options[] = array(
	'id'          => 'jnews_facebook_client_secret',
	'transport'   => 'refresh',
	'default'     => '',
	'section'     => 'jnews_facebook_api_section',
	'type'        => 'jnews-text',
	'label'       => esc_html__( 'APP Secret', 'jnews' ),
	'description' => sprintf(
		__( 'Insert your Facebook APP Secret right here. For more information, <a href="%s">please go here</a>', 'jnews' ),
		'https://developers.facebook.com/apps'
	),
);
/** END Facebook API */

return $options;
