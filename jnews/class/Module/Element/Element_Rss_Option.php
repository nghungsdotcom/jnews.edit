<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Element;

use JNews\Module\ModuleOptionAbstract;

class Element_Rss_Option extends ModuleOptionAbstract {

	public function get_category() {
		return esc_html__( 'JNews - Element', 'jnews' );
	}

	public function compatible_column() {
		return array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 );
	}

	public function get_module_name() {
		 return esc_html__( 'JNews - RSS Block', 'jnews' );
	}

	public function set_options() {
		 $this->set_header_option();
		$this->set_general_option();
		$this->set_style_option();
	}

	public function set_header_option() {
		$this->options[] = array(
			'type'        => 'iconpicker',
			'param_name'  => 'header_icon',
			'heading'     => esc_html__( 'Header Icon', 'jnews' ),
			'description' => esc_html__( 'Choose icon for this block icon.', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
			'std'         => '',
			'settings'    => array(
				'emptyIcon'    => true,
				'iconsPerPage' => 100,
			),
		);
		$this->options[] = array(
			'type'        => 'textfield',
			'param_name'  => 'first_title',
			'holder'      => 'span',
			'heading'     => esc_html__( 'Title', 'jnews' ),
			'description' => esc_html__( 'Main title of Module Block.', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
		);
		$this->options[] = array(
			'type'        => 'textfield',
			'param_name'  => 'second_title',
			'holder'      => 'span',
			'heading'     => esc_html__( 'Second Title', 'jnews' ),
			'description' => esc_html__( 'Secondary title of Module Block.', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
		);
		$this->options[] = array(
			'type'        => 'textfield',
			'param_name'  => 'url',
			'heading'     => esc_html__( 'Title URL', 'jnews' ),
			'description' => esc_html__( 'Insert URL of heading title.', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
		);
		$this->options[] = array(
			'type'        => 'radioimage',
			'param_name'  => 'header_type',
			'std'         => 'heading_6',
			'value'       => array(
				JNEWS_THEME_URL . '/assets/img/admin/heading-1.png' => 'heading_1',
				JNEWS_THEME_URL . '/assets/img/admin/heading-2.png' => 'heading_2',
				JNEWS_THEME_URL . '/assets/img/admin/heading-3.png' => 'heading_3',
				JNEWS_THEME_URL . '/assets/img/admin/heading-4.png' => 'heading_4',
				JNEWS_THEME_URL . '/assets/img/admin/heading-5.png' => 'heading_5',
				JNEWS_THEME_URL . '/assets/img/admin/heading-6.png' => 'heading_6',
				JNEWS_THEME_URL . '/assets/img/admin/heading-7.png' => 'heading_7',
				JNEWS_THEME_URL . '/assets/img/admin/heading-8.png' => 'heading_8',
				JNEWS_THEME_URL . '/assets/img/admin/heading-9.png' => 'heading_9',
			),
			'heading'     => esc_html__( 'Header Type', 'jnews' ),
			'description' => esc_html__( 'Choose which header type fit with your content design.', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
		);
		$this->options[] = array(
			'type'        => 'colorpicker',
			'param_name'  => 'header_background',
			'heading'     => esc_html__( 'Header Background', 'jnews' ),
			'description' => esc_html__( 'This option may not work for all of heading type.', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
			'dependency'  => array(
				'element' => 'header_type',
				'value'   => array(
					'heading_1',
					'heading_2',
					'heading_3',
					'heading_4',
					'heading_5',
				),
			),
		);
		$this->options[] = array(
			'type'        => 'colorpicker',
			'param_name'  => 'header_secondary_background',
			'heading'     => esc_html__( 'Header Secondary Background', 'jnews' ),
			'description' => esc_html__( 'change secondary background', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
			'dependency'  => array(
				'element' => 'header_type',
				'value'   => array( 'heading_2' ),
			),
		);
		$this->options[] = array(
			'type'        => 'colorpicker',
			'param_name'  => 'header_text_color',
			'heading'     => esc_html__( 'Header Text Color', 'jnews' ),
			'description' => esc_html__( 'Change color of your header text', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
		);
		$this->options[] = array(
			'type'        => 'colorpicker',
			'param_name'  => 'header_line_color',
			'heading'     => esc_html__( 'Header line Color', 'jnews' ),
			'description' => esc_html__( 'Change line color of your header', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
			'dependency'  => array(
				'element' => 'header_type',
				'value'   => array( 'heading_1', 'heading_5', 'heading_6', 'heading_9' ),
			),
		);
		$this->options[] = array(
			'type'        => 'colorpicker',
			'param_name'  => 'header_accent_color',
			'heading'     => esc_html__( 'Header Accent', 'jnews' ),
			'description' => esc_html__( 'Change Accent of your header', 'jnews' ),
			'group'       => esc_html__( 'Header', 'jnews' ),
			'dependency'  => array(
				'element' => 'header_type',
				'value'   => array( 'heading_6', 'heading_7' ),
			),
		);
	}

	public function set_general_option() {
		$content_layout = apply_filters(
			'jnews_get_content_layout_block_option',
			array(
				JNEWS_THEME_URL . '/assets/img/admin/content-3.png'  => '3',
			)
		);

		$this->options[] = array(
			'type'        => 'textfield',
			'param_name'  => 'feed_url',
			'std'         => '',
			'value'       => array( esc_html__( 'Insert URL Feed', 'jnews' ) => '' ),
			'heading'     => esc_html__( 'Feed URL', 'jnews' ),
			'description' => wp_kses( __( 'Insert Feed URL to be rendered.', 'jnews' ), wp_kses_allowed_html() ),
		);

		$this->options[] = array(
			'type'        => 'radioimage',
			'param_name'  => 'block_type',
			'std'         => '3',
			'value'       => $content_layout,
			'heading'     => esc_html__( 'Block Type', 'jnews' ),
			'description' => esc_html__( 'Choose which block type that fit your content design.', 'jnews' ),
		);

		$this->options[] = array(
			'type'        => 'checkbox',
			'param_name'  => 'thumbnail',
			'heading'     => esc_html__( 'Enable Thumbnail', 'jnews' ),
			'value'       => array( esc_html__( 'Enable thumbnail for this block', 'jnews' ) => 'yes' ),
			'description' => esc_html__( 'Thumbnail would increase loading time. Please use this setting with precaution.', 'jnews' ),
			'dependency'  => array(
				'element' => 'block_type',
				'value'   => array( '3', '4', '5', '6', '7', '9', '10', '14', '18', '22', '23', '25', '26', '27', '39' ),
			),
		);

		$this->options[] = array(
			'type'        => 'checkbox',
			'param_name'  => 'fallback',
			'heading'     => esc_html__( 'Enable Image Fallback', 'jnews' ),
			'value'       => array( esc_html__( 'Enable image fallback.', 'jnews' ) => 'yes' ),
			'description' => esc_html__( 'Use fallback image incase there is no thumbnail information.', 'jnews' ),
			'dependency'  => array(
				'element' => 'thumbnail',
				'value'   => 'yes',
			),
		);

		$this->options[] = array(
			'type'        => 'attach_image',
			'param_name'  => 'fallimage',
			'heading'     => esc_html__( 'Image', 'jnews' ),
			'description' => esc_html__( 'Use fallback image incase there is no thumbnail information.', 'jnews' ),
			'dependency'  => array(
				'element' => 'fallback',
				'value'   => 'yes',
			),
		);

		$this->options[] = array(
			'type'        => 'slider',
			'param_name'  => 'number_post',
			'heading'     => esc_html__( 'Number of post', 'jnews' ),
			'description' => esc_html__( 'Set number of post for this block.', 'jnews' ),
			'min'         => 1,
			'max'         => 100,
			'step'        => 1,
			'std'         => 5,
		);

		$this->options[] = array(
			'type'       => 'checkbox',
			'param_name' => 'boxed',
			'heading'    => esc_html__( 'Enable Boxed', 'jnews' ),
			'value'      => array( esc_html__( 'This option will turn the module into boxed.', 'jnews' ) => 'yes' ),
			'dependency' => array(
				'element' => 'block_type',
				'value'   => array( '3', '4', '5', '6', '7', '9', '10', '14', '18', '22', '23', '25', '26', '27', '39' ),
			),
		);

		$this->options[] = array(
			'type'        => 'checkbox',
			'param_name'  => 'boxed_shadow',
			'heading'     => esc_html__( 'Enable Shadow', 'jnews' ),
			'description' => esc_html__( 'Enable excerpt ellipsis', 'jnews' ),
			'dependency'  => array(
				'element' => 'boxed',
				'value'   => 'yes',
			),
		);

		$this->options[] = array(
			'type'        => 'slider',
			'param_name'  => 'excerpt_length',
			'heading'     => esc_html__( 'Excerpt Length', 'jnews' ),
			'description' => esc_html__( 'Set word length of excerpt on post block.', 'jnews' ),
			'min'         => 0,
			'max'         => 200,
			'step'        => 1,
			'std'         => 20,
			'dependency'  => array(
				'element' => 'block_type',
				'value'   => array( '3', '4', '5', '6', '7', '10', '12', '23', '25', '26', '27', '32', '33', '35', '36', '39' ),
			),
		);

		$this->options[] = array(
			'type'        => 'textfield',
			'param_name'  => 'excerpt_ellipsis',
			'heading'     => esc_html__( 'Excerpt Ellipsis', 'jnews' ),
			'description' => esc_html__( 'Define excerpt ellipsis', 'jnews' ),
			'std'         => '...',
			'dependency'  => array(
				'element' => 'block_type',
				'value'   => array( '3', '4', '5', '6', '7', '10', '12', '23', '25', '26', '27', '32', '33', '35', '36', '38' ),
			),
		);

		$this->options[] = array(
			'type'        => 'dropdown',
			'param_name'  => 'date_format',
			'heading'     => esc_html__( 'Content Date Format', 'jnews' ),
			'description' => esc_html__( 'Choose which date format you want to use.', 'jnews' ),
			'std'         => 'default',
			'value'       => array(
				esc_html__( 'Relative Date/Time Format (ago)', 'jnews' ) => 'ago',
				esc_html__( 'WordPress Default Format', 'jnews' ) => 'default',
				esc_html__( 'Custom Format', 'jnews' ) => 'custom',
			),
			'dependency'  => array(
				'element' => 'block_type',
				'value'   => array( '3', '4', '5', '6', '7', '10', '12', '23', '25', '26', '27', '32', '33', '35', '36', '37', '38', '39' ),
			),
		);

		$this->options[] = array(
			'type'        => 'textfield',
			'param_name'  => 'date_format_custom',
			'heading'     => esc_html__( 'Custom Date Format', 'jnews' ),
			'description' => wp_kses( sprintf( __( 'Please write custom date format for your module, for more detail about how to write date format, you can refer to this <a href="%s" target="_blank">link</a>.', 'jnews' ), 'https://codex.wordpress.org/Formatting_Date_and_Time' ), wp_kses_allowed_html() ),
			'std'         => 'Y/m/d',
			'dependency'  => array(
				'element' => 'date_format',
				'value'   => array( 'custom' ),
			),
		);

		$this->options = apply_filters( 'jnews_custom_block_option', $this->options );
	}

	public function set_typography_option( $instance ) {

		$instance->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'        => 'title_typography',
				'label'       => esc_html__( 'Title Typography', 'jnews' ),
				'description' => esc_html__( 'Set typography for post title', 'jnews' ),
				'selector'    => '{{WRAPPER}} .jeg_post_title > a',
			)
		);

		$instance->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'        => 'meta_typography',
				'label'       => esc_html__( 'Meta Typography', 'jnews' ),
				'description' => esc_html__( 'Set typography for post meta', 'jnews' ),
				'selector'    => '{{WRAPPER}} .jeg_post_meta, {{WRAPPER}} .jeg_post_meta .fa, {{WRAPPER}}.jeg_postblock .jeg_subcat_list > li > a:hover, {{WRAPPER}} .jeg_pl_md_card .jeg_post_category a, {{WRAPPER}}.jeg_postblock .jeg_subcat_list > li > a.current, {{WRAPPER}} .jeg_pl_md_5 .jeg_post_meta, {{WRAPPER}} .jeg_pl_md_5 .jeg_post_meta .fa, {{WRAPPER}} .jeg_post_category a',
			)
		);

		$instance->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'        => 'content_typography',
				'label'       => esc_html__( 'Post Content Typography', 'jnews' ),
				'description' => esc_html__( 'Set typography for post content', 'jnews' ),
				'selector'    => '{{WRAPPER}} .jeg_post_excerpt, {{WRAPPER}} .jeg_readmore',
			)
		);
	}
}
