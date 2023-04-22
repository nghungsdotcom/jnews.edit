<?php

$options = [];

$all_sidebar = apply_filters( 'jnews_get_sidebar_widget', null );

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
	'id'    => 'jnews_search_sidebar_section',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Search Page Layout', 'jnews' ),
];

$options[] = [
	'id'          => 'jnews_search_page_layout',
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
	],
	'postvar'     => [
		[
			'redirect' => 'search_tag',
			'refresh'  => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_sidebar',
	'transport'       => 'postMessage',
	'default'         => 'default-sidebar',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Search Sidebar', 'jnews' ),
	'description'     => wp_kses( __( "Choose your search sidebar. If you need another sidebar, you can create from <strong>WordPress Admin</strong> &raquo; <strong>Appearance</strong> &raquo; <strong>Widget</strong>.", 'jnews' ), wp_kses_allowed_html() ),
	'multiple'        => 1,
	'choices'         => $all_sidebar,
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => true,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_search_page_layout',
			'operator' => '!=',
			'value'    => 'no-sidebar',
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_second_sidebar',
	'transport'       => 'postMessage',
	'default'         => 'default-sidebar',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Second Search Sidebar', 'jnews' ),
	'description'     => wp_kses( __( "Choose your second sidebar for search page. If you need another sidebar, you can create from <strong>WordPress Admin</strong> &raquo; <strong>Appearance</strong> &raquo; <strong>Widget</strong>.", 'jnews' ), wp_kses_allowed_html() ),
	'multiple'        => 1,
	'choices'         => $all_sidebar,
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => true,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_search_page_layout',
			'operator' => 'in',
			'value'    => [ 'double-sidebar', 'double-right-sidebar' ],
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_sticky_sidebar',
	'transport'       => 'postMessage',
	'default'         => true,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Search Sticky Sidebar', 'jnews' ),
	'description'     => esc_html__( 'Enable sticky sidebar on search result page.', 'jnews' ),
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => true,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_search_page_layout',
			'operator' => '!=',
			'value'    => 'no-sidebar',
		],
	],
];


// content type
$options[] = [
	'id'    => 'jnews_search_content_section',
	'type'  => 'jnews-header',
	'label' => esc_html__( 'Search Content', 'jnews' ),
];

$options[] = [
	'id'              => 'jnews_search_content',
	'transport'       => 'postMessage',
	'default'         => '3',
	'type'            => 'jnews-radio-image',
	'label'           => esc_html__( 'Search Content Layout', 'jnews' ),
	'description'     => esc_html__( 'Choose your search content layout.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => $content_layout,
	'partial_refresh' => [
		'jnews_search_content' => [
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

$options[] = [
	'id'              => 'jnews_search_boxed',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Boxed', 'jnews' ),
	'description'     => esc_html__( 'This option will turn the module into boxed.', 'jnews' ),
	'partial_refresh' => [
		'jnews_search_boxed' => [
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
	'active_callback' => [
		[
			'setting'  => 'jnews_search_content',
			'operator' => 'in',
			'value'    => [ '3', '4', '5', '6', '7', '9', '10', '14', '18', '22', '23', '25', '26', '27', '39' ],
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_boxed_shadow',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Shadow', 'jnews' ),
	'description'     => esc_html__( 'Enable shadow on the module template.', 'jnews' ),
	'partial_refresh' => [
		'jnews_search_boxed_shadow' => [
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
	'active_callback' => [
		[
			'setting'  => 'jnews_search_content',
			'operator' => 'in',
			'value'    => [ '3', '4', '5', '6', '7', '9', '10', '14', '18', '22', '23', '25', '26', '27', '39' ],
		],
		[
			'setting'  => 'jnews_search_boxed',
			'operator' => '==',
			'value'    => true,
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_box_shadow',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Enable Shadow', 'jnews' ),
	'description'     => esc_html__( 'Enable shadow on the module template.', 'jnews' ),
	'partial_refresh' => [
		'jnews_search_box_shadow' => [
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
	'active_callback' => [
		[
			'setting'  => 'jnews_search_content',
			'operator' => 'in',
			'value'    => [ '37', '35', '33', '36', '32', '38' ],
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_content_excerpt',
	'transport'       => 'postMessage',
	'default'         => 20,
	'type'            => 'jnews-number',
	'label'           => esc_html__( 'Excerpt Length', 'jnews' ),
	'description'     => esc_html__( 'Set the word length of excerpt on post.', 'jnews' ),
	'choices'         => [
		'min'  => '0',
		'max'  => '200',
		'step' => '1',
	],
	'partial_refresh' => [
		'jnews_search_content_excerpt' => [
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

$options[] = [
	'id'              => 'jnews_search_content_date',
	'transport'       => 'postMessage',
	'default'         => 'default',
	'type'            => 'jnews-select',
	'label'           => esc_html__( 'Content Date Format', 'jnews' ),
	'description'     => esc_html__( 'Choose which date format you want to use for search for content.', 'jnews' ),
	'multiple'        => 1,
	'choices'         => [
		'ago'     => esc_attr__( 'Relative Date/Time Format (ago)', 'jnews' ),
		'default' => esc_attr__( 'WordPress Default Format', 'jnews' ),
		'custom'  => esc_attr__( 'Custom Format', 'jnews' ),
	],
	'partial_refresh' => [
		'jnews_search_content_date' => [
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

$options[] = [
	'id'              => 'jnews_search_content_date_custom',
	'transport'       => 'postMessage',
	'default'         => 'Y/m/d',
	'type'            => 'jnews-text',
	'label'           => esc_html__( 'Custom Date Format for Content', 'jnews' ),
	'description'     => wp_kses( sprintf( __( "Please set custom date format for post content. For more detail about this format, please refer to
								<a href='%s' target='_blank'>Developer Codex</a>.", "jnews" ), "https://developer.wordpress.org/reference/functions/current_time/" ),
		wp_kses_allowed_html() ),
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => false,
		],
	],
	'partial_refresh' => [
		'jnews_search_content_date_custom' => [
			'selector'        => '.jnews_search_content_wrapper',
			'render_callback' => function () {
				$single = new \JNews\Archive\SearchArchive();
				echo jnews_sanitize_output( $single->render_content() );
			},
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_search_content_date',
			'operator' => '==',
			'value'    => 'custom',
		],
	],
];

$options[] = [
	'id'          => 'jnews_search_content_pagination',
	'transport'   => 'postMessage',
	'default'     => 'nav_1',
	'type'        => 'jnews-select',
	'label'       => esc_html__( 'Choose Pagination Mode', 'jnews' ),
	'description' => esc_html__( 'Choose which pagination mode that fit with your block.', 'jnews' ),
	'multiple'    => 1,
	'choices'     => [
		'nav_1'      => esc_attr__( 'Normal - Navigation 1', 'jnews' ),
		'nav_2'      => esc_attr__( 'Normal - Navigation 2', 'jnews' ),
		'nav_3'      => esc_attr__( 'Normal - Navigation 3', 'jnews' ),
		'nextprev'   => esc_attr__( 'Ajax - Next Prev', 'jnews' ),
		'loadmore'   => esc_attr__( 'Ajax - Load More', 'jnews' ),
		'scrollload' => esc_attr__( 'Ajax - Auto Scroll Load', 'jnews' ),
	],
	'output'      => [
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
	'postvar'     => [
		[
			'redirect' => 'search_tag',
			'refresh'  => false,
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_content_pagination_limit',
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
			'setting'  => 'jnews_search_page_layout',
			'operator' => '!=',
			'value'    => 'custom-template',
		],
		[
			'setting'  => 'jnews_search_content_pagination',
			'operator' => '==',
			'value'    => 'scrollload',
		],
	],
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => false,
		],
	],
];

$options[] = [
	'id'              => 'jnews_search_content_pagination_align',
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
			'redirect' => 'search_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_search_content_pagination',
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
	'id'              => 'jnews_search_content_pagination_show_navtext',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Show Navigation Text', 'jnews' ),
	'description'     => esc_html__( 'Show navigation text (next, prev).', 'jnews' ),
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_search_content_pagination',
			'operator' => 'in',
			'value'    => [ 'nav_1', 'nav_2', 'nav_3' ],
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
	'id'              => 'jnews_search_content_pagination_show_pageinfo',
	'transport'       => 'postMessage',
	'default'         => false,
	'type'            => 'jnews-toggle',
	'label'           => esc_html__( 'Show Page Info', 'jnews' ),
	'description'     => esc_html__( 'Show page info text (Page x of y).', 'jnews' ),
	'postvar'         => [
		[
			'redirect' => 'search_tag',
			'refresh'  => false,
		],
	],
	'active_callback' => [
		[
			'setting'  => 'jnews_search_content_pagination',
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

return apply_filters( 'jnews_custom_customizer_option', $options, 'jnews_search_', null );
