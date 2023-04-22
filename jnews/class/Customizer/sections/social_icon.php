<?php

$options = [];

$options[] = [
	'id'          => 'jnews_social_icon_notice',
	'type'        => 'jnews-alert',
	'default'     => 'info',
	'label'       => esc_html__( 'Info', 'jnews' ),
	'description' => wp_kses( __(
		'<ul>
                    <li>This social icon will show on header & footer of your website. </li>
                    <li>Also will be used if you install JNews - Meta Header & JNews - JSON LD plugin</li>
                </ul>',
		'jnews' ), wp_kses_allowed_html() ),
];

$options[] = [
	'id'              => 'jnews_social_icon',
	'transport'       => 'postMessage',
	'type'            => 'jnews-repeater',
	'label'           => esc_html__( 'Add Social Icon', 'jnews' ),
	'description'     => esc_html__( 'Add icon for each of your social account.', 'jnews' ),
	'default'         => [
		[
			'social_icon' => 'facebook',
			'social_url'  => 'https://www.facebook.com/jegtheme/',
		],
		[
			'social_icon' => 'twitter',
			'social_url'  => 'https://twitter.com/jegtheme',
		],
	],
	'row_label'       => [
		'type'  => 'text',
		'value' => esc_attr__( 'Social Icon', 'jnews' ),
		'field' => false,
	],
	'fields'          => [
		'social_icon' => [
			'type'    => 'select',
			'label'   => esc_attr__( 'Social Icon', 'jnews' ),
			'default' => '',
			'choices' => [
				''              => esc_attr__( 'Choose Icon', 'jnews' ),
				'facebook'      => esc_attr__( 'Facebook', 'jnews' ),
				'twitter'       => esc_attr__( 'Twitter', 'jnews' ),
				'linkedin'      => esc_attr__( 'Linkedin', 'jnews' ),
				'pinterest'     => esc_attr__( 'Pinterest', 'jnews' ),
				'behance'       => esc_attr__( 'Behance', 'jnews' ),
				'github'        => esc_attr__( 'Github', 'jnews' ),
				'flickr'        => esc_attr__( 'Flickr', 'jnews' ),
				'tumblr'        => esc_attr__( 'Tumblr', 'jnews' ),
				'telegram'      => esc_attr__( 'Telegram', 'jnews' ),
				'dribbble'      => esc_attr__( 'Dribbble', 'jnews' ),
				'soundcloud'    => esc_attr__( 'Soundcloud', 'jnews' ),
				'stumbleupon'   => esc_attr__( 'StumbleUpon', 'jnews' ),
				'instagram'     => esc_attr__( 'Instagram', 'jnews' ),
				'vimeo'         => esc_attr__( 'Vimeo', 'jnews' ),
				'youtube'       => esc_attr__( 'Youtube', 'jnews' ),
				'twitch'        => esc_attr__( 'Twitch', 'jnews' ),
				'vk'            => esc_attr__( 'Vk', 'jnews' ),
				'reddit'        => esc_attr__( 'Reddit', 'jnews' ),
				'weibo'         => esc_attr__( 'Weibo', 'jnews' ),
				'wechat'        => esc_attr__( 'WeChat', 'jnews' ),
				'rss'           => esc_attr__( 'RSS', 'jnews' ),
				'line'          => esc_attr__( 'Line', 'jnews' ),
				'discord'       => esc_attr__( 'Discord', 'jnews' ),
				'odnoklassniki' => esc_attr__( 'Odnoklassniki', 'jnews' ),
				'tiktok'        => esc_attr__( 'TikTok', 'jnews' ),
				'snapchat'      => esc_attr__( 'Snapchat', 'jnews' ),
				'whatsapp'      => esc_attr__( 'Whatsapp', 'jnews' ),
			],
		],
		'social_url'  => [
			'type'    => 'text',
			'label'   => esc_attr__( 'Social URL', 'jnews' ),
			'default' => '',
		],
	],
	'partial_refresh' => [
		'social_icon'             => [
			'selector'        => '.jeg_top_socials',
			'render_callback' => function () {
				return jnews_generate_social_icon( false );
			},
		],
		'social_icon2'            => [
			'selector'        => '.jeg_social_icon_block',
			'render_callback' => function () {
				return jnews_generate_social_icon_block( false );
			},
		],
		'social_icon3'            => [
			'selector'        => '.jeg_new_social_icon_block',
			'render_callback' => function () {
				return jnews_generate_social_icon_block( false, true );
			},
		],
		'social_icon_mobile_menu' => [
			'selector'        => '.jeg_mobile_socials',
			'render_callback' => function () {
				return jnews_generate_social_icon( false );
			},
		],
	],
];

return $options;