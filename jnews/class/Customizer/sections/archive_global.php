<?php

$options = [];

$all_sidebar = apply_filters( 'jnews_get_sidebar_widget', null );

global $jnews_get_all_custom_archive_template; 

$content_layout = apply_filters( 'jnews_get_content_layout_customizer', [
	'3'  => '',
	'4'  => '',
	'5'  => '',
	'6'  => '',
	'7'  => '',
	'9'  => '',
	'10' => '',
	'11' => '',
	'12' => '',
	'14' => '',
	'15' => '',
	'18' => '',
	'22' => '',
	'23' => '',
	'25' => '',
	'26' => '',
	'27' => '',
	'32' => '',
	'33' => '',
	'34' => '',
	'35' => '',
	'36' => '',
	'37' => '',
	'38' => '',
	'39' => '',
] );

// sidebar section
$options[] = [
	'id'    => 'jnews_archive_sidebar_section',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Archive Sidebar', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_archive_page_layout',
	'transport'   => 'postMessage',
	'default'     => 'right-sidebar',
	'type'        => 'jnews-radio-image',
	'label'       => esc_html__( 'Page Layout', 'jnews' ),
	'description' => esc_html__( 'Choose your page layout.', 'jnews' ),
	'choices'     => [
		'right-sidebar'        => '',
		'left-sidebar'         => '',
		'right-sidebar-narrow' => '',
		'left-sidebar-narrow'  => '',
		'double-sidebar'       => '',
		'double-right-sidebar' => '',
		'no-sidebar'           => '',
		'custom-template'      => '',
	],
	'postvar'     => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_custom_template_id',
	'transport'       => 'postMessage',
	'default'         => '',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Archive Page Template List', 'jnews' ),
	'description'     => wp_kses( sprintf( __( 'Create custom archive page template from <a href="%s" target="_blank">here</a>', 'jnews' ), get_admin_url() . 'edit.php?post_type=archive-template' ), wp_kses_allowed_html() ),
	'choices'         => $jnews_get_all_custom_archive_template,
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '==',
			'value'    => 'custom-template',
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_custom_template_number_post',
	'transport'       => 'postMessage',
	'default'         => 10,
	'type'            => 'jnews-text',
	'label'           => esc_html__( 'Number of Post', 'jnews' ),
	'description'     => esc_html__( 'Set the number of post per page on archive page.', 'jnews' ),
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '==',
			'value'    => 'custom-template',
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_sidebar',
	'transport'       => 'postMessage',
	'default'         => 'default-sidebar',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Archive Sidebar', 'jnews' ),
	'description'     => wp_kses( __( "Choose your archive sidebar. If you need another sidebar, you can create from <strong>WordPress Admin</strong> &raquo; <strong>Appearance</strong> &raquo; <strong>Widget</strong>.", 'jnews' ), wp_kses_allowed_html() ),
	'multiple'        => 1,
	'choices'         => $all_sidebar,
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => true,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'no-sidebar',
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_second_sidebar',
	'transport'       => 'postMessage',
	'default'         => 'default-sidebar',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Second Archive Sidebar', 'jnews' ),
	'description'     => wp_kses( __( "Choose your second sidebar for archive page. If you need another sidebar, you can create from <strong>WordPress Admin</strong> &raquo; <strong>Appearance</strong> &raquo; <strong>Widget</strong>.", 'jnews' ), wp_kses_allowed_html() ),
	'multiple'        => 1,
	'choices'         => $all_sidebar,
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => true,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => 'in',
			'value'    => [ 'double-sidebar', 'double-right-sidebar' ],
		],
	],
];


$options[] = [
	'id'              => 'jnews_archive_sticky_sidebar',
	'transport'       => 'postMessage',
	'default'         => true,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Archive Sticky Sidebar', 'jnews' ),
	'description'     => esc_html__( 'Enable sticky sidebar.', 'jnews' ),
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => true,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'no-sidebar',
		],
	],
];

// content type
$options[] = [
	'id'              => 'jnews_archive_content_section',
	'type'            => 'jnews-header',
	'label'           => esc_html__( 'Archive Content', 'jnews' ),
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content',
	'transport'       => 'postMessage',
	'default'         => '3',
	'type'            => 'jnews-radio-image',
	'label'           => esc_html__( 'Archive Content Layout', 'jnews' ),
	'description'     => esc_html__( 'Choose your archive content layout.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => $content_layout,
	'partial_refresh' => [
		'jnews_archive_content' => [
			'selector'        => '.jnews_archive_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SingleArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_boxed',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Boxed', 'jnews' ),
	'description'     => esc_html__( 'This option will turn the module into boxed.', 'jnews' ),
	'partial_refresh' => [
		'jnews_archive_boxed' => [
			'selector'        => '.jnews_archive_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SingleArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content',
			'operator' => 'in',
			'value'    => [ '3', '4', '5', '6', '7', '9', '10', '14', '18', '22', '23', '25', '26', '27', '39' ],
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_boxed_shadow',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Shadow', 'jnews' ),
	'description'     => esc_html__( 'Enable shadow on the module template.', 'jnews' ),
	'partial_refresh' => [
		'jnews_archive_boxed_shadow' => [
			'selector'        => '.jnews_archive_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SingleArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content',
			'operator' => 'in',
			'value'    => [ '3', '4', '5', '6', '7', '9', '10', '14', '18', '22', '23', '25', '26', '27', '39' ],
		],
		[
			'setting'  => 'jnews_archive_boxed',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_box_shadow',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Shadow', 'jnews' ),
	'description'     => esc_html__( 'Enable shadow on the module template.', 'jnews' ),
	'partial_refresh' => [
		'jnews_archive_box_shadow' => [
			'selector'        => '.jnews_archive_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SingleArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content',
			'operator' => 'in',
			'value'    => [ '37', '35', '33', '36', '32', '38' ],
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_excerpt',
	'transport'       => 'postMessage',
	'default'         => 20,
	'type'            => 'jnews-number',
	'label'           => esc_html__( 'Excerpt Length', 'jnews' ),
	'description'     => esc_html__( 'Set word length of excerpt on post.', 'jnews' ),
	'choices'         => [
		'min'  => '0',
		'max'  => '200',
		'step' => '1',
	],
	'partial_refresh' => [
		'jnews_archive_content_excerpt' => [
			'selector'        => '.jnews_archive_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SingleArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_date',
	'transport'       => 'postMessage',
	'default'         => 'default',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Content Date Format', 'jnews' ),
	'description'     => esc_html__( 'Choose which date format you want to use for archive content.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => [
		'ago'     => esc_attr__( 'Relative Date/Time Format (ago)', 'jnews' ),
		'default' => esc_attr__( 'WordPress Default Format', 'jnews' ),
		'custom'  => esc_attr__( 'Custom Format', 'jnews' ),
	],
	'partial_refresh' => [
		'jnews_archive_content_date' => [
			'selector'        => '.jnews_archive_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SingleArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_date_custom',
	'transport'       => 'postMessage',
	'default'         => 'Y/m/d',
	'type'            => 'jnews-text',
	'label'           => esc_html__( 'Custom Date Format for Content', 'jnews' ),
	'description'     => wp_kses( sprintf( __( "Please set custom date format for post content. For more detail about this format, please refer to
                                <a href='%s' target='_blank'>Developer Codex</a>.", "jnews" ), "https://developer.wordpress.org/reference/functions/current_time/" ),
		wp_kses_allowed_html() ),
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'partial_refresh' => [
		'jnews_archive_content_date_custom' => [
			'selector'        => '.jnews_archive_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SingleArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content_date',
			'operator' => '==',
			'value'    => 'custom',
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_pagination',
	'transport'       => 'postMessage',
	'default'         => 'nav_1',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Choose Pagination Mode', 'jnews' ),
	'description'     => esc_html__( 'Choose which pagination mode that fit with your archive content.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => [
		'nav_1'      => esc_attr__( 'Normal - Navigation 1', 'jnews' ),
		'nav_2'      => esc_attr__( 'Normal - Navigation 2', 'jnews' ),
		'nav_3'      => esc_attr__( 'Normal - Navigation 3', 'jnews' ),
		'nextprev'   => esc_attr__( 'Ajax - Next Prev', 'jnews' ),
		'loadmore'   => esc_attr__( 'Ajax - Load More', 'jnews' ),
		'scrollload' => esc_attr__( 'Ajax - Auto Scroll Load', 'jnews' ),
	],
	'output'          => [
		[
			'method'   => 'class-masking',
			'element'  => '.jeg_navigation.jeg_pagination',
			'property' => [
				'nav_1'      => 'jeg_pagenav_1',
				'nav_2'      => 'jeg_pagenav_2',
				'nav_3'      => 'jeg_pagenav_3',
				'nextprev'   => 'jeg_pagenav_nextprev',
				'loadmore'   => 'jeg_pagenav_loadmore',
				'scrollload' => 'jeg_pagenav_scrollload',
			],
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
	],
];

$options[] = [
	'id'          => 'jnews_index_block_notice',
	'type'        => 'jnews-alert',
	'default'     => 'info',
	'label'       => esc_html__( 'Notice', 'jnews' ),
	'description' => wp_kses( __(
		'<ul>
                    <li>Ajax pagination will not work on <strong>Taxonomy Archive</strong></li>
                </ul>',
		'jnews' ), wp_kses_allowed_html() ),
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_content_pagination',
			'operator' => 'in',
			'value'    => [ 'nextprev', 'loadmore', 'scrollload' ],
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_pagination_limit',
	'transport'       => 'postMessage',
	'default'         => 0,
	'type'            => 'jnews-number',
	'label'           => esc_html__( 'Auto Load Limit', 'jnews' ),
	'description'     => esc_html__( 'Limit of auto load when scrolling, set to zero to always load until end of content.', 'jnews' ),
	'choices'         => [
		'min'  => 0,
		'max'  => 9999,
		'step' => 1,
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content_pagination',
			'operator' => '==',
			'value'    => 'scrollload',
		],
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_pagination_align',
	'transport'       => 'postMessage',
	'default'         => 'center',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Pagination Align', 'jnews' ),
	'description'     => esc_html__( 'Choose pagination alignment.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => [
		'left'   => esc_attr__( 'Left', 'jnews' ),
		'center' => esc_attr__( 'Center', 'jnews' ),
	],
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content_pagination',
			'operator' => 'in',
			'value'    => [ 'nav_1', 'nav_2', 'nav_3' ],
		],
	],
	'output'          => [
		[
			'method'   => 'class-masking',
			'element'  => '.jeg_navigation.jeg_pagination',
			'property' => [
				'left'   => 'jeg_alignleft',
				'center' => 'jeg_aligncenter',
			],
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_pagination_show_navtext',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Show Navigation Text', 'jnews' ),
	'description'     => esc_html__( 'Show navigation text (next, prev).', 'jnews' ),
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content_pagination',
			'operator' => 'in',
			'value'    => [ 'nav_1', 'nav_2', 'nav_3'],
		],
	],
	'output'          => [
		[
			'method'   => 'remove-class',
			'element'  => '.jeg_navigation.jeg_pagination ',
			'property' => 'no_navtext',
		],
	],
];

$options[] = [
	'id'              => 'jnews_archive_content_pagination_show_pageinfo',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Show Page Info', 'jnews' ),
	'description'     => esc_html__( 'Show page info text (Page x of y).', 'jnews' ),
	'postvar'         => [
		[
			'redirect' => 'archive_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_archive_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_archive_content_pagination',
			'operator' => 'in',
			'value'    => [ 'nav_1', 'nav_2', 'nav_3' ],
		],
	],
	'output'          => [
		[
			'method'   => 'remove-class',
			'element'  => '.jeg_navigation.jeg_pagination ',
			'property' => 'no_pageinfo',
		],
	],
];

return apply_filters( 'jnews_custom_customizer_option', $options, 'jnews_archive_', null );
