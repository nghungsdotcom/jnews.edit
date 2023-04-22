<?php

$options = [];

$options[] = [
	'id'          => 'jnews_share_library',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Share Library', 'jnews' ),
	'description' => esc_html__( 'Share image library across all users', 'jnews' ),
];

return $options;