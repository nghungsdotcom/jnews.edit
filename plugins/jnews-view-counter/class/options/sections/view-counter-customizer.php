<?php

$options = array();

$options[] = array(
	'id'    => 'jnews_view_counter_log_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Data Setting', 'jnews-view-counter' ),
);

$options[] = array(
	'id'          => 'jnews_option[view_counter][general][log][limit]',
	'option_type' => 'option',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Limit Data', 'jnews-view-counter' ),
	'description' => esc_html__( 'Limit the view count data to reduce the database space usage. Total view count would be preserved however, the detailed view count information would be lost.', 'jnews-view-counter' ),
);

$options[] = array(
	'id'              => 'jnews_option[view_counter][general][log][expires_after]',
	'option_type'     => 'option',
	'transport'       => 'postMessage',
	'default'         => 180,
	'type'            => 'jnews-number',
	'label'           => esc_html__( 'Data Expiration', 'jnews-view-counter' ),
	'description'     => esc_html__( 'Data older than the specified time frame ( days ) will be automatically discarded.', 'jnews-view-counter' ),
	'choices'         => array(
		'min'  => '0',
		'max'  => '9999',
		'step' => '1',
	),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_option[view_counter][general][log][limit]',
			'operator' => '==',
			'value'    => true,
		),
	),
);

$options[] = array(
	'id'    => 'jnews_view_counter_general_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'View Setting', 'jnews-view-counter' ),
);

$options[] = array(
	'id'          => 'jnews_option[view_counter][general][time_between_counts][number]',
	'option_type' => 'option',
	'transport'   => 'postMessage',
	'default'     => 0,
	'type'        => 'jnews-number',
	'label'       => esc_html__( 'Count Interval', 'jnews-view-counter' ),
	'description' => esc_html__( 'Enter time length', 'jnews-view-counter' ),
	'choices'     => array(
		'min'  => '0',
		'max'  => '9999',
		'step' => '1',
	),
);

$options[] = array(
	'id'          => 'jnews_option[view_counter][general][time_between_counts][type]',
	'option_type' => 'option',
	'transport'   => 'postMessage',
	'default'     => 'hours',
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Unit', 'jnews-view-counter' ),
	'description' => esc_html__( 'Enter time Unit', 'jnews-view-counter' ),
	'multiple'    => 1,
	'choices'     => array(
		'minutes' => __( 'minutes', 'jnews-view-counter' ),
		'hours'   => __( 'hours', 'jnews-view-counter' ),
		'days'    => __( 'days', 'jnews-view-counter' ),
		'weeks'   => __( 'weeks', 'jnews-view-counter' ),
		'months'  => __( 'months', 'jnews-view-counter' ),
		'years'   => __( 'years', 'jnews-view-counter' ),
	),
);

$options[] = array(
	'id'          => 'jnews_option[view_counter][general][strict_counts]',
	'option_type' => 'option',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Strict Count', 'jnews-view-counter' ),
	'description' => esc_html__( 'Prevents counting the same visitor view within the View Count Interval that uses private browsing or clearing cookies', 'jnews-view-counter' ),
);

$options[] = array(
	'id'          => 'jnews_option[view_counter][general][exclude][groups]',
	'option_type' => 'option',
	'transport'   => 'postMessage',
	'default'     => array( 'robots' ),
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Exclude Visitors', 'jnews-view-counter' ),
	'description' => esc_html__( 'Ignores view from the selected visitor type', 'jnews-view-counter' ),
	'multiple'    => 100,
	'choices'     => array(
		'robots' => __( 'Robots', 'jnews-view-counter' ),
		'users'  => __( 'Logged in users', 'jnews-view-counter' ),
		'guest'  => __( 'Guest', 'jnews-view-counter' ),
		'roles'  => __( 'Selected user roles', 'jnews-view-counter' ),
	),
);

$options[] = array(
	'id'              => 'jnews_option[view_counter][general][exclude][roles]',
	'option_type'     => 'option',
	'transport'       => 'postMessage',
	'type'            => 'jnews-select',
	'default'         => '',
	'label'           => esc_html__( 'Exclude Roles', 'jnews-view-counter' ),
	'description'     => esc_html__( 'Ignores view from the selected user roles. Requires `Selected user roles` to be included in `Exclude Visitors`.', 'jnews-view-counter' ),
	'multiple'        => 100,
	'choices'         => call_user_func(
		function() {
			global $wp_roles;

			$roles = array();

			foreach ( apply_filters( 'editable_roles', $wp_roles->roles ) as $role => $details ) {
				$roles[ $role ] = translate_user_role( $details['name'] );
			}

			asort( $roles, SORT_STRING );

			return $roles;
		}
	),
	'active_callback' => array(
		array(
			'setting'  => 'jnews_option[view_counter][general][exclude][groups]',
			'operator' => 'contains',
			'value'    => 'roles',
		),
	),
);

return $options;
