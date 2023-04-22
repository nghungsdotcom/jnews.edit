<?php

$options = [];

$options[] = [
	'id'              => 'jnews_search_only_post',
	'transport'       => 'postMessage',
	'default'         => true,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Only Search Post', 'jnews' ),
	'description'     => esc_html__( 'WordPress default search will also look for your single page, enable this feature to only search post type.', 'jnews' ),
	'partial_refresh' => [
		'jnews_search_only_post' => [
			'selector'        => '.jnews_search_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SearchArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => false,
		],
	],
];

return $options;