<?php

$options = array();

$options[] = array(
    'id'            => 'jnews_query_option_header',
    'type'          => 'jnews-header',
    'label'         => esc_html__( 'Global Query Option Settings','jnews' ),
);

$options[] = array(
    'id'            => 'jnews_query_option_enable',
    'transport'     => 'postMessage',
    'default'       => true,
    'type'          => 'jnews-toggle',
    'label'         => esc_html__( 'Enable Cache Query', 'jnews' ),
    'description'   => esc_html__( 'Use cached query if available. Enabling this option might cause a newly published post to not be immediately visible.', 'jnews' )
);

$options[] = array(
    'id'            => 'jnews_category_option_header',
    'type'          => 'jnews-header',
    'label'         => esc_html__( 'Category Query Option Settings','jnews' ),
);

$options[] = array(
    'id'            => 'jnews_recursive_query_enable',
    'transport'     => 'postMessage',
    'default'       => true,
    'type'          => 'jnews-toggle',
    'label'         => esc_html__( 'Enable Recursive Category', 'jnews' ),
    'description'   => esc_html__( 'Enable recursive category in category related query.', 'jnews' )
);

$options = apply_filters( 'jnews_global_query_option', $options );

return $options;
