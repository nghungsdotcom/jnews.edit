<?php

$options = [];

$options[] = [
	'id'    => 'jnews_cpt_menu_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Custom Post Type', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_dashboard_post_template_disable',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Disable Post Template Dashboard', 'jnews' ),
	'description' => esc_html__( 'Disable post template dashboard for user role.', 'jnews' ),
];

$options[] = array(
	'id'              => 'jnews_dashboard_post_template_user_roles',
	'transport'       => 'postMessage',
	'type'            => 'jnews-select',
	'default'         => array(),
	'label'           => esc_html__( 'Restrict Post Template User Roles', 'jnews' ),
	'description'     => esc_html__( 'Restrict post template for the selected user roles.', 'jnews' ),
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
			'setting'  => 'jnews_dashboard_post_template_disable',
			'operator' => '===',
			'value'    => true,
		),
	),
);

$options[] = [
	'id'          => 'jnews_dashboard_footer_builder_disable',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Disable Footer Builder Dashboard', 'jnews' ),
	'description' => esc_html__( 'Disable footer builder dashboard for user role.', 'jnews' ),
];

$options[] = array(
	'id'              => 'jnews_dashboard_footer_builder_user_roles',
	'transport'       => 'postMessage',
	'type'            => 'jnews-select',
	'default'         => '',
	'label'           => esc_html__( 'Restrict Footer Builder User Roles', 'jnews' ),
	'description'     => esc_html__( 'Restrict footer builder for the selected user roles.', 'jnews' ),
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
			'setting'  => 'jnews_dashboard_footer_builder_disable',
			'operator' => '===',
			'value'    => true,
		),
	),
);

$options[] = [
	'id'          => 'jnews_dashboard_archive_template_disable',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Disable Archive Template Dashboard', 'jnews' ),
	'description' => esc_html__( 'Disable archive template dashboard for user role.', 'jnews' ),
];

$options[] = array(
	'id'              => 'jnews_dashboard_archive_template_user_roles',
	'transport'       => 'postMessage',
	'type'            => 'jnews-select',
	'default'         => '',
	'label'           => esc_html__( 'Restrict Archive Template User Roles', 'jnews' ),
	'description'     => esc_html__( 'Restrict archive template for the selected user roles.', 'jnews' ),
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
			'setting'  => 'jnews_dashboard_archive_template_disable',
			'operator' => '===',
			'value'    => true,
		),
	),
);

$options[] = [
	'id'          => 'jnews_dashboard_custom_menu_disable',
	'transport'   => 'postMessage',
	'default'     => false,
	'type'        => 'jnews-toggle',
	'label'       => esc_html__( 'Disable Custom Menu Dashboard', 'jnews' ),
	'description' => esc_html__( 'Disable custom menu dashboard for user role.', 'jnews' ),
];

$options[] = array(
	'id'              => 'jnews_dashboard_custom_menu_user_roles',
	'transport'       => 'postMessage',
	'type'            => 'jnews-select',
	'default'         => '',
	'label'           => esc_html__( 'Restrict Custom Menu User Roles', 'jnews' ),
	'description'     => esc_html__( 'Restrict custom menu for the selected user roles.', 'jnews' ),
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
			'setting'  => 'jnews_dashboard_custom_menu_disable',
			'operator' => '===',
			'value'    => true,
		),
	),
);

$options[] = [
	'id'    => 'jnews_cpt_header',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Custom Post Type Taxonomy', 'jnews' ),
];

$post_types = JNews\Util\Cache::get_exclude_post_type();

unset( $post_types['post'] );
unset( $post_types['page'] );

if ( ! empty( $post_types ) && is_array( $post_types ) ) {

	foreach ( $post_types as $key => $label ) {

		$options[] = [
			'id'          => 'jnews_enable_cpt_' . $key . ']',
			'transport'   => 'postMessage',
			'default'     => true,
			'type'        => 'jnews-toggle',
			'label'       => sprintf( esc_html__( 'Enable %s Post Type', 'jnews' ), $label ),
			'description' => sprintf( esc_html__( 'Enable %s post type and their custom taxonomy as content filter.', 'jnews' ), strtolower( $label ) ),
		];
	}

} else {
	$options[] = [
		'id'          => 'jnews_enable_post_type_alert',
		'type'        => 'jeg-alert',
		'default'     => 'info',
		'label'       => esc_html__( 'Notice', 'jnews' ),
		'description' => esc_html__( 'There\'s no custom post type found.', 'jnews' ),
	];
}

return $options;
