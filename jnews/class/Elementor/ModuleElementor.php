<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Elementor;

use Elementor\Plugin;
use Elementor\Element_Column;
use Elementor\Controls_Manager;
use JNews\Elementor\Normal\SocialIcon;
use JNews\Elementor\Normal\SocialCounter;
use JNews\Module\ModuleManager;
use JNews\Widget\Normal\RegisterNormalWidget;

/**
 * Class JNews VC Integration
 */
class ModuleElementor {

	/**
	 * @var ModuleElementor
	 */
	private static $instance;

	private $module_manager;

	/**
	 * @return ModuleElementor
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * ModuleVC constructor.
	 */
	private function __construct() {
		$this->setup_hook();
		$this->setup_post_type();
		$this->themeversion = wp_get_theme()->get( 'Version' );
	}

	protected function get_module_instance() {
		if ( ! $this->module_manager ) {
			$this->module_manager = ModuleManager::getInstance();
		}

		return $this->module_manager;
	}

	protected function setup_post_type() {
		add_post_type_support( 'footer', 'elementor' );
		add_post_type_support( 'custom-post-template', 'elementor' );
		add_post_type_support( 'custom-mega-menu', 'elementor' );
		add_post_type_support( 'archive-template', 'elementor' );
	}

	public function setup_hook() {
		// load script & style editor
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_style' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_script' ) );

		// load script & style frontend
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'frontend_script' ) );

		// register module, element, group & custom control
		if ( jnews_constant_version_compare( 'ELEMENTOR_VERSION', '3.5.0', '>=' ) ) {
			add_action( 'elementor/controls/register', array( $this, 'register_control' ) );
			add_action( 'elementor/widgets/register', array( $this, 'register_module' ) );
			add_action( 'elementor/widgets/register', array( $this, 'register_normal' ) );
		} else {
			add_action( 'elementor/controls/controls_registered', array( $this, 'register_control' ) );
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_module' ) );
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_normal' ) );
		}
		add_action( 'elementor/init', array( $this, 'register_group' ) );

		// register sticky sidebar option on column
		add_action( 'elementor/element/column/layout/before_section_end', array( $this, 'register_sticky_option' ) );
		add_action( 'elementor/element/column/section_advanced/before_section_end', array( $this, 'register_column_padding' ) );

		// register custom css option for single page / post
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			add_action( 'elementor/element/wp-page/document_settings/before_section_end', array( $this, 'register_custom_css_option' ) );
			add_action( 'elementor/element/wp-post/document_settings/before_section_end', array( $this, 'register_custom_css_option' ) );
			add_action( 'wp_head', array( $this, 'render_custom_css' ) );
		}

		// modify widget list on elementor
		add_filter( 'elementor/widgets/black_list', array( $this, 'modify_normal_widget_list' ) );

		// check width of section
		add_action( 'elementor/frontend/before_render', array( $this, 'register_width' ) );
		add_action( 'elementor/frontend/element/before_render', array( $this, 'register_width' ) );
		add_action( 'elementor/editor/element/before_raw_data', array( $this, 'register_width' ) );
		add_action( 'elementor/init', array( $this, 'register_element_handler' ) );

		// change elementor script
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'elementor_editor_script' ), 99 );

		add_action( 'wp_ajax_elementor_get_category', array( $this, 'elementor_get_category' ) );
		add_action( 'wp_ajax_elementor_get_author', array( $this, 'elementor_get_author' ) );
		add_action( 'wp_ajax_elementor_get_tag', array( $this, 'elementor_get_tag' ) );
	}

	public function elementor_get_tag() {
		if ( isset( $_REQUEST['string'] ) && ! empty( $_REQUEST['string'] ) ) {
			$value = sanitize_text_field( $_REQUEST['string'] );
		} else {
			return false;
		}

		$data   = array();
		$values = explode( ',', $value );

		foreach ( $values as $val ) {
			if ( ! empty( $val ) ) {
				$term   = get_term( $val, 'post_tag' );
				$data[] = array(
					'value' => $val,
					'text'  => $term->name,
				);
			}
		}

		wp_send_json( $data );
	}

	public function elementor_get_author() {
		if ( isset( $_REQUEST['string'] ) && ! empty( $_REQUEST['string'] ) ) {
			$value = $_REQUEST['string'];
		} else {
			return false;
		}

		$data   = array();
		$values = explode( ',', $value );

		foreach ( $values as $val ) {
			if ( ! empty( $val ) ) {
				$user   = get_userdata( $val );
				$data[] = array(
					'value' => $val,
					'text'  => $user->display_name,
				);
			}
		}

		wp_send_json( $data );
	}

	public function elementor_get_category() {
		if ( isset( $_REQUEST['string'] ) && ! empty( $_REQUEST['string'] ) ) {
			$value = $_REQUEST['string'];
		} else {
			return false;
		}

		$data   = array();
		$values = explode( ',', $value );

		foreach ( $values as $val ) {
			if ( ! empty( $val ) ) {
				$term   = get_term( $val, 'category' );
				$data[] = array(
					'value' => $val,
					'text'  => $term->name,
				);
			}
		}

		wp_send_json( $data );
	}

	public function elementor_editor_script() {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$script    = get_parent_theme_file_uri( 'assets/js' );
			$version   = (int) str_replace( '.', '', ELEMENTOR_VERSION );
			$available = array( 212, 216, 221, 224 );

			if ( in_array( $version, $available ) ) {
				wp_deregister_script( 'elementor-editor' );

				wp_register_script(
					'elementor-editor',
					$script . '/elementor/editor-' . $version . '.min.js',
					array(
						'wp-auth-check',
						'jquery-ui-sortable',
						'jquery-ui-resizable',
						'backbone-marionette',
						'backbone-radio',
						'perfect-scrollbar',
						'nprogress',
						'tipsy',
						'imagesloaded',
						'heartbeat',
						'jquery-elementor-select2',
						'flatpickr',
						'elementor-dialog',
						'ace',
						'ace-language-tools',
						'jquery-hover-intent',
					),
					ELEMENTOR_VERSION,
					true
				);
			}
		}
	}

	public function register_element_handler() {
		$experiments_manager = Plugin::$instance->experiments;
		if ( $experiments_manager->is_feature_active( 'container' ) ) {
			add_action( 'elementor/elements/elements_registered', array( $this, 'register_element' ) );
		} else {
			$this->register_element();
		}
	}

	public function register_element() {
		Plugin::$instance->elements_manager->register_element_type( new \JNews\Elementor\Element\Section() );
		Plugin::$instance->elements_manager->register_element_type( new \Elementor\Element_Column() );
	}

	private function get_column_width( $width ) {
		if ( $width < 34 ) {
			$column = 4;
		} elseif ( $width < 67 ) {
			$column = 8;
		} else {
			$column = 12;
		}

		return $column;
	}

	/**
	 * @param Element_Column $object
	 */
	public function register_width( $object ) {
		if ( $object instanceof Element_Column ) {
			$setting = $object->get_settings();
			$column  = $this->get_column_width( $setting['_column_size'] );
			ModuleManager::getInstance()->force_set_width( $column );
		}
	}

	public function modify_normal_widget_list( $new_widgets ) {
		$widgets  = array_keys( $GLOBALS['wp_widget_factory']->widgets );
		$excluded = array( 'Social_Widget', 'Social_Counter_Widget' );

		foreach ( $widgets as $widget ) {
			if ( in_array( $widget, $excluded ) ) {
				$new_widgets[] = $widget;
			}
		}

		/** Return black list array for filter */
		return (array) $new_widgets;
	}

	public function frontend_script() {
		$is_preview_mode = \Elementor\Plugin::$instance->preview->is_preview_mode();
		if ( $is_preview_mode ) {
			$asset_url = get_parent_theme_file_uri( 'assets/' );
			wp_enqueue_script( 'wp-mediaelement' );
			wp_enqueue_script( 'jscrollpane', $asset_url . 'js/jquery.jscrollpane.js', null, $this->themeversion, true );
			wp_enqueue_script( 'jnews-hero', $asset_url . 'js/jnewshero.js', array( 'tiny-slider-noconflict' ), $this->themeversion, null );
			wp_enqueue_script( 'jnews-overlayslider' );
			wp_enqueue_script( 'jnews-videoplaylist' );
			wp_enqueue_script( 'jnews-elementor-frontend', $asset_url . 'js/admin/elementor-frontend.js', array( 'jquery' ), $this->themeversion, null );

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_style( 'jnews-overlayslider' );
			wp_enqueue_style( 'jnews-videoplaylist' );
		}
	}

	public function editor_style() {
		wp_enqueue_style( 'jnews-admin', get_parent_theme_file_uri( '/assets/css/admin/admin-style.css' ), null, $this->themeversion );
		wp_enqueue_style( 'jnews-elementor-css', get_parent_theme_file_uri( '/assets/css/elementor-backend.css' ), null, $this->themeversion );
	}

	public function editor_script() {
		wp_enqueue_script( 'jquery-ui-spinner' );
		wp_enqueue_script( 'jnews-elementor-js', get_parent_theme_file_uri( '/assets/js/admin/elementor-backend.js' ), null, $this->themeversion, true );
		wp_localize_script( 'jnews-elementor-js', 'jnews_elementor', $this->localize_script() );
	}

	public function localize_script() {
		$option = array();

		$option['widgets'] = $this->populate_widget();

		return $option;
	}

	public function populate_widget() {
		// Module Widget
		$modules = $this->get_module_instance()->populate_module();
		$widgets = array();

		foreach ( $modules as $module ) {
			if ( $module['widget'] ) {
				$widgets[] = str_replace( 'jnews', 'jnews_module', strtolower( $module['name'] ) );
			}
		}

		// Normal Widget
		$normal_modules = RegisterNormalWidget::getInstance()->get_normal_widget();

		foreach ( $normal_modules as $module ) {
			$widgets[] = 'jnews_' . str_replace( '_widget', '', strtolower( $module ) );
		}

		return $widgets;
	}

	/**
	 * After widgets registered.
	 *
	 * Fires after Elementor widgets are registered.
	 * < 3.5.0 elementor/widgets/widgets_registered
	 * >= 3.5.0 elementor/widgets/register
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager The widgets manager.
	 */
	public function register_module( $widgets_manager ) {
		$elementor_deprecated = jnews_constant_version_compare( 'ELEMENTOR_VERSION', '3.5.0', '>=' );
		include get_parent_theme_file_path( 'class/Elementor/module-elementor.php' );
		do_action( 'jnews_module_elementor' );

		$modules = $this->get_module_instance()->populate_module();

		$exclude = array( 'social_icon_wrapper', 'social_icon_item', 'social_counter_wrapper', 'social_counter_item', 'widget' );

		foreach ( $modules as $module ) {
			if ( in_array( $module['type'], $exclude ) ) {
				continue;
			}

			$classname = '\\' . $module['name'] . '_Elementor';
			if ( $elementor_deprecated ) {
				$widgets_manager->register( new $classname() );
			} else {
				$widgets_manager->register_widget_type( new $classname() );
			}
		}
	}

	/**
	 * After widgets registered.
	 *
	 * Fires after Elementor widgets are registered.
	 * < 3.5.0 elementor/widgets/widgets_registered
	 * >= 3.5.0 elementor/widgets/register
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager The widgets manager.
	 */
	public function register_normal( $widgets_manager ) {
		$elementor_deprecated = jnews_constant_version_compare( 'ELEMENTOR_VERSION', '3.5.0', '>=' );
		if ( $elementor_deprecated ) {
			$widgets_manager->register( new SocialIcon() );
			$widgets_manager->register( new SocialCounter() );
		} else {
			$widgets_manager->register_widget_type( new SocialIcon() );
			$widgets_manager->register_widget_type( new SocialCounter() );
		}
	}

	/**
	 * Elementor init.
	 *
	 * Fires when Elementor components are initialized.
	 *
	 * After Elementor finished loading but before any headers are sent.
	 */
	public function register_group() {
		$groups = array(
			'jnews-module'   => esc_html__( 'JNews - Module', 'jnews' ),
			'jnews-hero'     => esc_html__( 'JNews - Hero', 'jnews' ),
			'jnews-slider'   => esc_html__( 'JNews - Slider', 'jnews' ),
			'jnews-element'  => esc_html__( 'JNews - Element', 'jnews' ),
			'jnews-carousel' => esc_html__( 'JNews - Carousel', 'jnews' ),
			'jnews-footer'   => esc_html__( 'JNews - Footer', 'jnews' ),
			'jnews-post'     => esc_html__( 'JNews - Post', 'jnews' ),
			'jnews-archive'  => esc_html__( 'JNews - Archive', 'jnews' ),

		);

		foreach ( $groups as $key => $value ) {
			\Elementor\Plugin::$instance->elements_manager->add_category( $key, array( 'title' => $value ), 1 );
		}
	}

	/**
	 * After controls registered.
	 *
	 * Fires after Elementor controls are registered.
	 * < 3.5.0 elementor/controls/controls_registered
	 * >= 3.5.0 elementor/controls/register
	 *
	 * @param \Elementor\Controls_Manager $controls_manager The controls manager.
	 */
	public function register_control( $controls_manager ) {
		$elementor_deprecated = jnews_constant_version_compare( 'ELEMENTOR_VERSION', '3.5.0', '>=' );
		$controls             = array(
			'dynamicselect' => 'JNews\Elementor\Control\Dynamicselect',
			'alert'         => 'JNews\Elementor\Control\Alert',
		);

		if ( $elementor_deprecated ) {
			foreach ( $controls as $classname ) {
				$controls_manager->register( new $classname() );
			}
		} else {
			foreach ( $controls as $type => $classname ) {
				$controls_manager->register_control( $type, new $classname() );
			}
		}
	}

	public function register_sticky_option( $element ) {
		$element->add_control(
			'sticky_sidebar',
			array(
				'label'        => esc_html__( 'Enable Sticky Sidebar', 'jnews' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'description'  => esc_html__( 'Set this column as sticky sidebar. Note: Sticky Sidebar will disabled when on the editor or you should refresh the editor to see the result. It will works fine on the frontend.', 'jnews' ),
				'prefix_class' => ' jeg_sticky_sidebar jeg_sidebar ',
			)
		);
	}

	public function register_custom_css_option( $element ) {
		if ( $element instanceof \Elementor\Core\DocumentTypes\Post ) {
			$element_post = $element->get_post();
			if ( $element_post && 'footer' !== $element_post->post_type ) {
				return;
			}
		}
		$element->add_control(
			'custom_css',
			array(
				'label'       => esc_html__( 'Custom CSS Setting', 'jnews' ),
				'type'        => \Elementor\Controls_Manager::CODE,
				'default'     => '',
				'description' => esc_html__( 'Enter custom CSS (Note: it will be outputted only on this particular page).', 'jnews' ),
			)
		);
	}

	public function render_custom_css( $post_id = null ) {
		if ( ! is_singular() ) {
			return;
		}

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( $post_id ) {
			$settings = get_post_meta( $post_id, '_elementor_page_settings', true );

			if ( ! empty( $settings['custom_css'] ) ) {
				echo '<style type="text/css" data-type="elementor_custom-css">';
				echo strip_tags( $settings['custom_css'] );
				echo '</style>';
			}
		}
	}

	public function register_column_padding( $element ) {
		$element->update_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'jnews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-element-populated' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
	}
}
