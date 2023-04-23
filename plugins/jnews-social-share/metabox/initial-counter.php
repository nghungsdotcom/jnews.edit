<?php
/**
 * @see \JNews\Util\ValidateLicense::is_license_validated
 * @since 8.0.0
 */
if ( function_exists( 'jnews_is_active' ) && jnews_is_active()->is_license_validated() ) {
	return array(
		'id'       => 'jnews_override_counter',
		'types'    => array( 'post' ),
		'title'    => 'JNews : Override Fake Counter',
		'priority' => 'high',
		'template' => array(

			array(
				'type'        => 'toggle',
				'name'        => 'override_view_counter',
				'label'       => esc_html__( 'Override View Counter Setting', 'jnews-social-share' ),
				'description' => esc_html__( 'enable this option to override view counter setting', 'jnews-social-share' ),
			),

			array(
				'type'            => 'textbox',
				'name'            => 'view_counter_number',
				'label'           => esc_html__( 'Total View Counter', 'jnews-social-share' ),
				'description'     => esc_html__( 'please insert number of view counter', 'jnews-social-share' ),
				'default'         => 0,
				'active_callback' => array(
					array(
						'field'    => 'override_view_counter',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			array(
				'type'        => 'toggle',
				'name'        => 'override_share_counter',
				'label'       => esc_html__( 'Override Share Counter Setting', 'jnews-social-share' ),
				'description' => esc_html__( 'enable this option to override Share counter setting', 'jnews-social-share' ),
			),

			array(
				'type'            => 'textbox',
				'name'            => 'share_counter_number',
				'label'           => esc_html__( 'Total Share Counter', 'jnews-social-share' ),
				'description'     => esc_html__( 'please insert number of share counter', 'jnews-social-share' ),
				'default'         => 0,
				'active_callback' => array(
					array(
						'field'    => 'override_share_counter',
						'operator' => '==',
						'value'    => true,
					),
				),
			),


			array(
				'type'        => 'toggle',
				'name'        => 'override_like_counter',
				'label'       => esc_html__( 'Override Like Counter Setting', 'jnews-social-share' ),
				'description' => esc_html__( 'enable this option to override Like counter setting', 'jnews-social-share' ),
			),

			array(
				'type'            => 'textbox',
				'name'            => 'like_counter_number',
				'label'           => esc_html__( 'Total Like Counter', 'jnews-social-share' ),
				'description'     => esc_html__( 'please insert number of like counter', 'jnews-social-share' ),
				'default'         => 0,
				'active_callback' => array(
					array(
						'field'    => 'override_like_counter',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			array(
				'type'        => 'toggle',
				'name'        => 'override_dislike_counter',
				'label'       => esc_html__( 'Override Dislike Counter Setting', 'jnews-social-share' ),
				'description' => esc_html__( 'enable this option to override Dislike counter setting', 'jnews-social-share' ),
			),

			array(
				'type'            => 'textbox',
				'name'            => 'dislike_counter_number',
				'label'           => esc_html__( 'Total Dislike Counter', 'jnews-social-share' ),
				'description'     => esc_html__( 'please insert number of dislike counter', 'jnews-social-share' ),
				'default'         => 0,
				'active_callback' => array(
					array(
						'field'    => 'override_dislike_counter',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

		),
	);
} else {
	return array(
		'id'       => 'jnews_override_counter',
		'types'    => array( 'post' ),
		'title'    => 'JNews : Override Fake Counter',
		'priority' => 'high',
		'template' => array(
			array(
				'type'        => 'notebox',
				'name'        => 'activate_license',
				'status'      => 'error',
				'label'       => esc_html__( 'Activate License', 'jnews-social-share' ),
				'description' => sprintf(
					wp_kses(
						__(
							'<span style="display: block;">Please activate your copy of JNews to unlock this feature. Click button bellow to activate:</span>
						<span class="jnews-notice-button">
							<a href="%s" class="button-primary jnews_customizer_activate_license">Activate Now</a>
						</span>',
							'jnews-social-share'
						),
						array(
							'strong' => array(),
							'span'   => array(
								'style' => true,
								'class' => true,
							),
							'a'      => array(
								'href'  => true,
								'class' => true,
							),
						)
					),
					get_admin_url()
				),
			),
		),
	);
}
