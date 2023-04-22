<?php
/**
 * Importer
 *
 * @author Jegtheme
 * @package JNews\Util\Api
 */

namespace JNews\Util\Api;

use JNews\Customizer\CustomizeSetting;
use JNews\Widget\EditWidgetArea;

/**
 * Class used for managing import demo.
 */
class Importer {

	/**
	 * Class instance
	 *
	 * @var Importer
	 */
	private static $instance;

	/**
	 * Importer Option
	 *
	 * Example:
	 * array(
	 *  'content' => import_content keys ( news | sport | etc )
	 *  'style' => import_style keys ( news | sport | etc )
	 * )
	 *
	 * @var string
	 */
	public static $option = 'jnews_import';

	/**
	 * Importer Content
	 *
	 * Example:
	 * array(
	 *   'image'           =>
	 *   array(
	 *       'logo' => 237,
	 *   ),
	 *   'taxonomy'        =>
	 *   array(
	 *       'category' =>
	 *       array(
	 *           'news' => 54,
	 *       ),
	 *       'post_tag' =>
	 *       array(
	 *           'united-stated' => 74,
	 *       ),
	 *   ),
	 *   'taxonomy_image'  =>
	 *   array(
	 *       'category' =>
	 *       array(),
	 *       'post_tag' =>
	 *       array(),
	 *   ),
	 *   'post'            =>
	 *   array(
	 *       'lady-gaga-pulled-off-one-of-the-best-halftime-shows-ever' => 278,
	 *       'home-1' => 279,
	 *   ),
	 *   'menu_location'   =>
	 *   array(
	 *       'main-navigation' => 85,
	 *   ),
	 *   'menu'            =>
	 *   array(
	 *       'home' => 284,
	 *   ),
	 *   'widget_position' =>
	 *   array(
	 *       'Home 3 - Loop',
	 *   ),
	 * );
	 *
	 * @var string
	 */
	public static $option_content = 'jnews_import_content';

	/**
	 * Backup content
	 *
	 * Example:
	 * array(
	 *  'widget'     =>
	 *  array(
	 *      'default-sidebar' =>
	 *      array(
	 *          'block-2' =>
	 *          array(
	 *              'content' => '<!-- wp:search /-->',
	 *          ),
	 *      ),
	 *  ),
	 *  'customizer' =>
	 *  array(
	 *      'template' => 'jnews',
	 *      'mods'     =>
	 *      array(
	 *          'nav_menu_locations' => array(),
	 *      ),
	 *  ),
	 *  'elementor_setting' =>
	 *  array(
	 *      'elementor_setting_data'
	 *  ),
	 * )
	 *
	 * @var string
	 */
	public static $backup_option = 'jnews_import_backup';

	/**
	 * Construct of Importer
	 *
	 * @param string $id Demo slug.
	 * @param string $type Type doing import ( install | uninstall ).
	 * @param string $step Import step.
	 * @param array  $import_option Import option.
	 * @param mixed  $data Data config for each step.
	 * @param array  $config Demo config for each step.
	 */
	public function __construct( $id, $type, $step, $import_option, $data, $config ) {
		$this->id            = $id;
		$this->type          = $type;
		$this->step          = $step;
		$this->import_option = $import_option;
		$this->data          = $data;
		$this->config        = $config;
	}

	/**
	 * Prepare import
	 *
	 * @return mixed
	 */
	public function prepare_import() {
		if ( 'check-step' === $this->step ) {
			return $this->import_step();
		} elseif ( 'backup' === $this->step ) {
			return $this->backup_content();
		} elseif ( 'plugin' === $this->step ) {
			$plugins = array(
				array(
					'file'    => 'vafpress-post-formats-ui-develop/vafpress-post-formats-ui-develop.php',
					'slug'    => 'vafpress-post-formats-ui-develop',
					'source'  => 'vafpress-post-formats-ui-develop.zip',
					'version' => '',
				),
				array(
					'file'    => 'jnews-essential/jnews-essential.php',
					'slug'    => 'jnews-essential',
					'source'  => 'jnews-essential.zip',
					'version' => '',
				),
			);
			if ( $this->import_option['include_content'] ) {
				if ( 'vc-content' === $this->import_option['builder_content'] ) {
					$plugins[] = array(
						'file'    => 'js_composer/js_composer.php',
						'slug'    => 'js_composer',
						'source'  => 'js_composer.zip',
						'version' => '',
					);
				} else {
					$plugins[] = array(
						'file'    => 'elementor/elementor.php',
						'slug'    => 'elementor',
						'source'  => '',
						'version' => '',
					);
				}
			}
			return array(
				'data' => $plugins,
			);
		} elseif ( 'uninstall' === $this->step ) {
			return $this->uninstall_content();
		} elseif ( 'restore' === $this->step ) {
			return $this->restore_content();
		} else {
			return $this->import_process();
		}
	}

	/**
	 * Step of Import
	 *
	 * @return array
	 */
	public function import_step() {
		$option = get_option( self::$option );

		if ( 'import' === $this->type ) {
			if ( $this->import_option['include_content'] ) {
				$steps = array(
					array(
						'id'   => 'image',
						'text' => esc_html__( 'Importing Images', 'jnews' ),
					),
					array(
						'id'   => 'taxonomy',
						'text' => esc_html__( 'Importing Taxonomy', 'jnews' ),
					),
					array(
						'id'   => 'post',
						'text' => esc_html__( 'Importing Post', 'jnews' ),
					),
					array(
						'id'   => 'menu_location',
						'text' => esc_html__( 'Importing Menu', 'jnews' ),
					),
					array(
						'id'   => 'menu',
						'text' => esc_html__( 'Importing Menu', 'jnews' ),
					),
					array(
						'id'   => 'widget',
						'text' => esc_html__( 'Importing Widget', 'jnews' ),
					),
					array(
						'id'   => 'customizer',
						'text' => esc_html__( 'Importing Customizer', 'jnews' ),
					),
				);
				if ( 'elementor-content' === $this->import_option['builder_content'] ) {
					$steps[] = array(
						'id'   => 'elementor_setting',
						'text' => esc_html__( 'Importing Elementor Global Setting', 'jnews' ),
					);
				}
			} else {
				$steps = array(
					array(
						'id'   => 'style_only',
						'text' => esc_html__( 'Importing Style', 'jnews' ),
					),
				);
			}

			if ( $this->import_option['install_plugin'] ) {
				$step =
				array_unshift(
					$steps,
					array(
						'id'   => 'plugin',
						'text' => esc_html__( 'Installing Required Plugins', 'jnews' ),
					),
					array(
						'id'   => 'related-plugin',
						'text' => esc_html__( 'Installing Related Plugins', 'jnews' ),
					)
				);
			}

			// do we need to uninstall the content first?
			if ( $this->import_option['include_content'] ) {
				array_unshift(
					$steps,
					array(
						'id'   => 'uninstall',
						'text' => esc_html__( 'Uninstall Demo', 'jnews' ),
						'item' => array( 'style', 'widget', 'menu', 'post', 'taxonomy', 'image', 'finish' ),
					)
				);
			}

			// do we need to backup the content first?
			if ( ! $option ) {
				array_unshift(
					$steps,
					array(
						'id'   => 'backup',
						'text' => esc_html__( 'Backup', 'jnews' ),
					)
				);
			}

			// we need to prepare data first
			array_unshift(
				$steps,
				array(
					'id'   => 'prepare',
					'text' => esc_html__( 'Preparing Data', 'jnews' ),
				)
			);
		} else {
			$steps = array(
				array(
					'id'   => 'uninstall',
					'text' => esc_html__( 'Uninstall', 'jnews' ),
					'item' => array( 'style', 'widget', 'menu', 'post', 'taxonomy', 'image', 'finish' ),
				),
				array(
					'id'   => 'restore',
					'text' => esc_html__( 'Restore Data', 'jnews' ),
				),
			);
		}

		return array(
			'data' => $steps,
		);
	}

	/**
	 * Import Process
	 *
	 * @return mixed
	 */
	public function import_process() {
		$content_flag = array( 'image', 'taxonomy', 'post', 'menu_location', 'menu', 'widget' );
		$style_flag   = array( 'customizer', 'style_only' );
		$result       = false;

		// Import Only Style.
		if ( 'style_only' === $this->step ) {
			$this->do_import_style_only();
			$result = true;
		} else {
			$result = $this->do_import();
		}

		// Import Content & Flag as Content.
		if ( in_array( $this->step, $content_flag, true ) ) {
			$this->save_import_option( 'content', $this->id );
		}

		// Import Style & Customizer, Flag as style.
		if ( in_array( $this->step, $style_flag, true ) ) {
			$this->save_import_option( 'style', $this->id );
		}

		return $result;
	}

	/**
	 * Do actual import style only process
	 */
	public function do_import_style_only() {
		if ( is_array( $this->config['customizer'] ) && ! empty( $this->config['customizer'] ) ) {
			$theme_mod = get_theme_mods();
			remove_theme_mods();

			$customizer = $this->config['customizer'];
			$customizer = wp_json_encode( $customizer );
			$customizer = $this->text_filter( $customizer );
			$customizer = json_decode( $customizer, true );

			$customizer['mods'] = $this->alter_mod_default( $theme_mod, $customizer['mods'] );
			unset( $customizer['options'] );

			$this->import_theme_mod( $customizer );
		}
		if ( 'elementor_setting' === $this->step && isset( $this->config['elementor_setting'] ) ) {
			$this->do_import_elementor_setting( $this->content['elementor_setting'] );
		}
	}

	/**
	 * Alter mod default
	 *
	 * @param array $current all theme modifications.
	 * @param array $data New scheme theme.
	 *
	 * @return array
	 */
	public function alter_mod_default( $current, $data ) {
		$overwrites = array(
			'nav_menu_locations',
			'jnews_social_icon',
			'jnews_header_logo',
			'jnews_header_logo_retina',
			'jnews_sticky_menu_logo',
			'jnews_sticky_menu_logo_retina',
			'jnews_mobile_logo',
			'jnews_mobile_logo_retina',
			'jnews_background_image',
			'jnews_social_icon',
		);

		foreach ( $overwrites as $option ) {
			$data[ $option ] = isset( $current[ $option ] ) ? $current[ $option ] : null;
		}

		return $data;
	}

	/**
	 * Backup
	 *
	 * @return boolean
	 */
	public function backup_content() {
		$this->do_backup();

		return true;
	}

	/**
	 * Backup Content
	 */
	public function do_backup() {
		$backup = array();

		// Backup widget.
		$backup['widget'] = $this->export_widget();

		// Backup style.
		$backup['customizer'] = $this->export_style();

		// Backup elementor settings.
		$backup['elementor_setting'] = $this->export_elementor_setting();
		if ( empty( $backup['elementor_setting'] ) ) {
			unset( $backup['elementor_setting'] );
		}

		// Save option.
		update_option( self::$backup_option, $backup );
	}

	/**
	 * Export Widget
	 *
	 * @return array
	 */
	public function export_widget() {
		$available_widgets = $this->available_widgets();

		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$instances = get_option( 'widget_' . $widget_data['id_base'] );

			if ( ! empty( $instances ) ) {
				foreach ( $instances as $instance_id => $instance_data ) {
					if ( is_numeric( $instance_id ) ) {
						$unique_instance_id                      = $widget_data['id_base'] . '-' . $instance_id;
						$widget_instances[ $unique_instance_id ] = $instance_data;
					}
				}
			}
		}

		$sidebars_widgets          = get_option( 'sidebars_widgets' );
		$sidebars_widget_instances = array();

		foreach ( $sidebars_widgets as $sidebar_id => $widget_ids ) {

			if ( 'wp_inactive_widgets' == $sidebar_id ) {
				continue;
			}

			if ( ! is_array( $widget_ids ) || empty( $widget_ids ) ) {
				continue;
			}

			foreach ( $widget_ids as $widget_id ) {
				if ( isset( $widget_instances[ $widget_id ] ) ) {
					$sidebars_widget_instances[ $sidebar_id ][ $widget_id ] = $widget_instances[ $widget_id ];
				}
			}
		}

		return $sidebars_widget_instances;
	}

	/**
	 * Export Style
	 *
	 * @return array
	 */
	public function export_style() {
		return array(
			'template' => get_template(),
			'mods'     => get_theme_mods(),
		);
	}

	/**
	 * Export Elementor Setting
	 *
	 * @return array
	 */
	public function export_elementor_setting() {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			if ( \Elementor\Plugin::instance()->kits_manager ) {
				return \Elementor\Plugin::instance()->kits_manager->get_active_kit()->get_export_data();
			}
		}
		return array();
	}

	/**
	 * Restore Content
	 *
	 * @return boolean
	 */
	public function restore_content() {
		$this->do_restore();

		return true;
	}

	/**
	 * Restore backup content
	 */
	public function do_restore() {
		$option = get_option( self::$backup_option );

		// Restore widget.
		$this->import_widget( $option['widget'] );

		// Restore style.
		$this->import_theme_mod( $option['customizer'] );

		// Restore elementor setting.
		if ( isset( $option['elementor_setting'] ) ) {
			$this->import_elementor_setting( $option['elementor_setting'] );
		}
	}

	/**
	 * Uninstall content
	 * Note: Uninstall content will use custom config data instead config from demo
	 *
	 * @return boolean
	 */
	public function uninstall_content() {
		$option = get_option( self::$option );

		if ( $option && ! empty( $option ) ) {
			$this->do_uninstall_single( $this->data );
		}
		return true;
	}

	/**
	 * Do actual uninstall
	 *
	 * @param string $step Uninstall step.
	 */
	public function do_uninstall_single( $step ) {
		$option_name = self::$option_content;
		$option      = get_option( $option_name );

		switch ( $step ) {
			case 'style':
				$this->uninstall_style();
				break;
			case 'widget':
				$this->reset_widget_content();
				if ( isset( $option['widget_position'] ) ) {
					$this->uninstall_widget_position( $option['widget_position'] );
				}
				break;
			case 'menu':
				if ( isset( $option['menu'] ) ) {
					$this->uninstall_menu( $option['menu'] );
				}
				if ( isset( $option['menu_location'] ) ) {
					$this->uninstall_menu_location( $option['menu_location'] );
				}
				break;
			case 'post':
				if ( isset( $option['post'] ) ) {
					$this->uninstall_post( $option['post'] );
				}
				break;
			case 'taxonomy':
				if ( isset( $option['taxonomy'] ) ) {
					$this->uninstall_taxonomy( $option['taxonomy'] );
				}
				if ( isset( $option['taxonomy_image'] ) ) {
					$this->uninstall_taxonomy_image( $option['taxonomy_image'] );
				}
				break;
			case 'image':
				if ( isset( $option['image'] ) ) {
					$this->uninstall_image( $option['image'] );
				}
				break;
			case 'finish':
				delete_option( $option_name );
				delete_option( self::$option );
				break;
		}
	}

	/**
	 * Uninstall menu
	 *
	 * @param array $menus Menus.
	 */
	public function uninstall_menu( $menus ) {
		foreach ( $menus as $menu ) {
			wp_delete_post( $menu, true );
		}
	}

	/**
	 * Uninstall menu location
	 *
	 * @param array $menu_location Menu location.
	 */
	public function uninstall_menu_location( $menu_location ) {
		foreach ( $menu_location as $location ) {
			wp_delete_term( $location, 'nav_menu' );
		}
	}

	/**
	 * Uninstall widget position
	 *
	 * @param array $widget_position Widget position.
	 */
	public function uninstall_widget_position( $widget_position ) {
		$widget_list = get_option( EditWidgetArea::$widget_list );

		foreach ( $widget_list as $key => $widget ) {
			if ( in_array( $widget, $widget_position, true ) ) {
				unset( $widget_list[ $key ] );
			}
		}

		update_option( EditWidgetArea::$widget_list, $widget_list );
	}

	/**
	 * Uninstall post
	 *
	 * @param array $posts Posts.
	 */
	public function uninstall_post( $posts ) {
		foreach ( $posts as $post ) {
			wp_delete_post( $post, true );
		}
	}

	/**
	 * Uninstall taxonomy
	 *
	 * @param array $taxonomies Taxonomies.
	 */
	public function uninstall_taxonomy( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy => $values ) {
			foreach ( $values as $key => $value ) {
				wp_delete_term( $value, $taxonomy );
			}
		}
	}

	/**
	 * Uninstall taxonomy image
	 *
	 * @param array $taxonomies_image Taxonomies image.
	 */
	public function uninstall_taxonomy_image( $taxonomies_image ) {
		foreach ( $taxonomies_image as $taxonomy => $values ) {
			$option_name         = 'jnews_' . $taxonomy . '_term_image';
			$taxonomy_image_list = get_option( $option_name );
			if ( ! empty( $taxonomy_image_list ) ) {
				foreach ( $taxonomy_image_list as $taxonomy_id => $image_id ) {
					if ( array_key_exists( $taxonomy_id, $values ) ) {
						unset( $taxonomy_image_list[ $taxonomy_id ] );
					}
				}
				$this->save_taxonomy_image( $option_name, null, null, $taxonomy_image_list );
			}
			if ( empty( $taxonomy_image_list ) ) {
				delete_option( $option_name );
			}
		}
	}

	/**
	 * Uninstall image
	 *
	 * @param array $images Images.
	 */
	public function uninstall_image( $images ) {
		foreach ( $images as $image ) {
			wp_delete_attachment( $image, true );
		}
	}

	/**
	 * Uninstall theme mod
	 */
	public function uninstall_style() {
		delete_option( 'theme_mods_jnews' );
	}

	/**
	 * Do actual import process
	 *
	 * @return mixed
	 */
	public function do_import() {
		// no timeout
		set_time_limit( 0 );
		$result = false;

		if ( 'image' === $this->step && isset( $this->config['image'] ) ) {
			$images = $this->do_import_image();
			if ( ! empty( $images ) ) {
				$result = $images;
				$this->save_option( 'image', $this->config['image'] );
			}
		}
		if ( 'taxonomy' === $this->step && isset( $this->config['taxonomy'] ) ) {
			$taxonomies = $this->do_import_taxonomy();
			if ( ! empty( $taxonomies ) ) {
				$result = $taxonomies;
				$this->save_option( 'taxonomy', $this->config['taxonomy'] );
				$this->save_option( 'taxonomy_image', $this->config['taxonomy_image'] );
			}
		}
		if ( 'post' === $this->step && isset( $this->config['post'] ) ) {
			$posts = $this->do_import_post();
			if ( ! empty( $posts ) ) {
				$result = $posts;
				$this->save_option( 'post', $this->config['post'] );
			}
		}
		if ( 'menu_location' === $this->step && isset( $this->config['menu_location'] ) ) {
			$menu_locations = $this->do_import_menu_location();
			if ( ! empty( $menu_locations ) ) {
				$result = $menu_locations;
				$this->save_option( 'menu_location', $this->config['menu_location'] );
			}
		}
		if ( 'menu' === $this->step && isset( $this->config['menu'] ) ) {
			$menus = $this->do_import_menu();
			if ( ! empty( $menus ) ) {
				$result = $menus;
				$this->save_option( 'menu', $this->config['menu'] );
			}
		}

		if ( 'widget' === $this->step && isset( $this->config['widget'] ) ) {
			if ( isset( $this->config['widget_position'] ) ) {
				$this->do_import_widget_position();
				$this->save_option( 'widget_position', $this->config['widget_position'] );
			}
			$this->do_import_widget();
			$result = true;
		}

		if ( 'customizer' === $this->step && isset( $this->config['customizer'] ) ) {
			$this->do_import_style();
			$result = true;
		}

		if ( 'elementor_setting' === $this->step && isset( $this->config['elementor_setting'] ) && 'elementor-content' === $this->import_option['builder_content'] ) {
			$this->do_import_elementor_setting();
			$result = true;
		}

		return $result;
	}

	/**
	 * Import menu location
	 *
	 * @return array
	 */
	public function do_import_menu_location() {
		$menu_locations = array();
		foreach ( $this->config['menu_location'] as $key => $menu ) {
			$menu_exists = wp_get_nav_menu_object( $menu['title'] );

			if ( $menu_exists ) {
				wp_delete_nav_menu( $menu['title'] );
			}

			// create menu
			$menu_locations[ $key ] = wp_create_nav_menu( $menu['title'] );

			// assign menu to location
			if ( isset( $menu['location'] ) ) {
				$location                      = get_theme_mod( 'nav_menu_locations' );
				$location[ $menu['location'] ] = $menu_locations[ $key ];
				set_theme_mod( 'nav_menu_locations', $location );
			}
		}
		$this->config['menu_location'] = $menu_locations;
		return array(
			'data' => $menu_locations,
		);
	}

	/**
	 * Import menu
	 *
	 * @return array
	 */
	public function do_import_menu() {
		foreach ( $this->config['menu'] as $key => $menu ) {
			// convert every tag on menu item data
			foreach ( $menu['menu-item-data'] as $item_key => $data ) {
				$menu['menu-item-data'][ $item_key ] = $this->text_filter( $data );
			}

			$this->config['menu'][ $key ] = wp_update_nav_menu_item( $this->config['menu_location'][ $menu['location'] ], 0, $menu['menu-item-data'] );

			// set metabox
			if ( isset( $menu['metabox'] ) && ! empty( $menu['metabox'] ) ) {
				$this->update_metabox( $this->config['menu'][ $key ], $menu['metabox'] );
			}
		}
		return array(
			'data' => $this->config['menu'],
		);
	}

	/**
	 * Import widget position
	 */
	public function do_import_widget_position() {
		$widget_positions = $this->config['widget_position'];

		$current_location = get_option( EditWidgetArea::$widget_list );

		if ( is_array( $current_location ) ) {
			$new_location = array_merge( $current_location, $widget_positions );
		} else {
			$new_location = $widget_positions;
		}

		foreach ( $new_location as $widget ) {
			register_sidebar(
				array(
					'id'            => sanitize_title( $widget ),
					'name'          => $widget,
					'before_widget' => '<div class="widget %2$s" id="%1$s">',
					'before_title'  => '',
					'after_title'   => '',
					'after_widget'  => '</div>',
				)
			);
		}

		update_option( EditWidgetArea::$widget_list, $new_location );
	}

	/**
	 * Import widget
	 */
	public function do_import_widget() {
		$this->reset_widget_content();
		if ( is_array( $this->config['widget'] ) && ! empty( $this->config['widget'] ) ) {
			$widgets = $this->config['widget'];
			$widgets = wp_json_encode( $widgets );
			$widgets = $this->text_filter( $widgets );
			$widgets = json_decode( $widgets, true );
			$this->import_widget( $widgets );
		}
	}

	/**
	 * Import customizer style
	 */
	public function do_import_style() {
		if ( is_array( $this->config['customizer'] ) && ! empty( $this->config['customizer'] ) ) {
			$customizer = $this->config['customizer'];
			$customizer = wp_json_encode( $customizer );
			$customizer = $this->text_filter( $customizer );
			$customizer = json_decode( $customizer, true );
			$this->import_theme_mod( $customizer );
		}
	}

	/**
	 * Import elementor setting
	 */
	public function do_import_elementor_setting() {
		if ( is_array( $this->config['elementor_setting'] ) && ! empty( $this->config['elementor_setting'] ) ) {
			$elementor_setting = $this->config['elementor_setting'];
			$elementor_setting = wp_json_encode( $elementor_setting );
			$elementor_setting = $this->text_filter( $elementor_setting );
			$elementor_setting = json_decode( $elementor_setting, true );
			$this->import_elementor_setting( $elementor_setting );
		}
	}

	/**
	 * Do real elementor setting import
	 *
	 * @param array $data Elementor setting data.
	 */
	public function import_elementor_setting( $data ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			if ( \Elementor\Plugin::instance()->kits_manager ) {
				$kit = \Elementor\Plugin::instance()->kits_manager->get_active_kit();

				$old_settings = $kit->get_meta( '_elementor_page_settings' );

				if ( ! $old_settings ) {
					$old_settings = array();
				}

				$new_settings = $data;

				$new_settings = isset( $new_settings['settings'] ) ? $new_settings['settings'] : array();

				if ( $old_settings ) {
					$new_settings['custom_colors'] = array_merge( isset( $old_settings['custom_colors'] ) ? $old_settings['custom_colors'] : array(), isset( $new_settings['custom_colors'] ) ? $new_settings['custom_colors'] : array() );

					$new_settings['custom_typography'] = array_merge( isset( $old_settings['custom_typography'] ) ? $old_settings['custom_typography'] : array(), isset( $new_settings['custom_typography'] ) ? $new_settings['custom_typography'] : array() );
				}

				$new_settings = array_replace_recursive( $old_settings, $new_settings );

				$kit->save( array( 'settings' => $new_settings ) );
			}
		}
	}

	/**
	 * Do real style import
	 *
	 * @param array $data Customizer data.
	 */
	public function import_theme_mod( $data ) {
		global $wp_customize;

		if ( isset( $data['options'] ) ) {
			foreach ( $data['options'] as $option_key => $option_value ) {
				$option = new CustomizeSetting(
					$wp_customize,
					$option_key,
					array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
					)
				);

				$option->import( $option_value );
			}
		}

		// Call the customize_save action.
		do_action( 'customize_save', $wp_customize );

		if ( isset( $data['mods'] ) && ! empty( $data['mods'] ) ) {
			// Loop through the mods.
			foreach ( $data['mods'] as $key => $val ) {

				// Call the customize_save_ dynamic action.
				do_action( 'customize_save_' . $key, $wp_customize );

				// Save the mod.
				set_theme_mod( $key, $val );
			}
		}

		// Call the customize_save_after action.
		do_action( 'customize_save_after', $wp_customize );

		if ( isset( $data['wp_css'] ) ) {
			wp_update_custom_css_post( $data['wp_css'], array() );
		}
	}

	/**
	 * Do actual widget import
	 *
	 * @param array $data Widget data
	 */
	public function import_widget( $data ) {
		// available widget
		global $wp_registered_sidebars;
		$available_widgets = $this->available_widgets();

		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
		}

		foreach ( $data as $sidebar_id => $widgets ) {

			// Skip inactive widgets (should not be in export file)
			if ( 'wp_inactive_widgets' == $sidebar_id ) {
				continue;
			}

			// Check if sidebar is available on this site
			// Otherwise add widgets to inactive, and say so
			if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
				$use_sidebar_id = $sidebar_id;
			} else {
				$use_sidebar_id = 'wp_inactive_widgets';
			}

			// Result for sidebar
			$results[ $sidebar_id ]['name']    = ! empty( $wp_registered_sidebars[ $sidebar_id ]['name'] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
			$results[ $sidebar_id ]['widgets'] = array();

			// Loop widgets
			foreach ( $widgets as $widget_instance_id => $widget ) {

				$fail = false;

				// Get id_base (remove -# from end) and instance ID number
				$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );

				// Does site support this widget?
				if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
					$fail = true;
				}

				// Convert multidimensional objects to multidimensional arrays
				$widget = json_decode( json_encode( $widget ), true );

				// convert all tag on widget content
				foreach ( $widget as $key => $value ) {
					$widget[ $key ] = $this->text_filter( $value );
				}

				// Does widget with identical settings already exist in same sidebar?
				if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {

					// Get existing widgets in this sidebar
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets  = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array(); // check Inactive if that's where will go

					// Loop widgets with ID base
					$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {
						// Is widget in same sidebar and has identical settings?
						if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
							$fail = true;
							break;
						}
					}
				}

				// No failure
				if ( ! $fail ) {
					// Add widget instance
					$single_widget_instances   = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
					$single_widget_instances   = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
					$single_widget_instances[] = $widget; // add it

					// Get the key it was given
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number                             = 1;
						$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget
					update_option( 'widget_' . $id_base, $single_widget_instances );

					// Assign widget instance to sidebar
					$sidebars_widgets                      = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
					$new_instance_id                       = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
					$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id; // add new instance to sidebar
					update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data
				}
			}
		}
	}

	/**
	 * Empty widget content
	 */
	public function reset_widget_content() {
		$sidebar_options = get_option( 'sidebars_widgets' );

		foreach ( $sidebar_options as $sidebar_name => $sidebar_value ) {
			if ( is_array( $sidebar_value ) ) {
				unset( $sidebar_options[ $sidebar_name ] );
				$sidebar_options[ $sidebar_name ] = array();
			}
		}

		update_option( 'sidebars_widgets', $sidebar_options );
	}

	/**
	 * @return array
	 *
	 * return all available registred widget
	 */
	public function available_widgets() {
		global $wp_registered_widget_controls;
		$widget_controls   = $wp_registered_widget_controls;
		$available_widgets = array();

		foreach ( $widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
				$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
				$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
			}
		}

		return $available_widgets;
	}

	/**
	 * Handle Import Taxonomy
	 *
	 * @return array
	 */
	public function do_import_taxonomy() {
		$taxonomies       = array();
		$taxonomies_image = array();
		foreach ( $this->config['taxonomy'] as $taxonomy => $items ) {
			$taxonomies[ $taxonomy ]       = array();
			$taxonomies_image[ $taxonomy ] = array();

			foreach ( $items as $slug => $tax ) {
				$currentterm = get_term_by( 'slug', $slug, $taxonomy );

				if ( ! $currentterm ) {
					$tax_args = array(
						'slug' => $slug,
					);

					if ( isset( $tax['parent'] ) ) {
						$tax_args['parent'] = $taxonomies[ $taxonomy ][ $tax['parent'] ];
					}
					if ( isset( $tax['description'] ) ) {
						$tax_args['description'] = $tax['description'];
					}

					$term = wp_insert_term( $tax['title'], $taxonomy, $tax_args );

					if ( $term instanceof \WP_Error ) {
						$taxonomies[ $taxonomy ][ $slug ] = $term->error_data['term_exists'];
					} else {
						$taxonomies[ $taxonomy ][ $slug ] = $term['term_id'];
					}
				} else {
					$taxonomies[ $taxonomy ][ $slug ] = $currentterm->term_id;
				}
				if ( isset( $tax['term_image'] ) ) {
					$taxonomies_image[ $taxonomy ][ $taxonomies[ $taxonomy ][ $slug ] ] = $this->text_filter( $tax['term_image'] );
				}
			}
		}
		$this->config['taxonomy']       = $taxonomies;
		$this->config['taxonomy_image'] = $taxonomies_image;
		if ( ! empty( $this->config['taxonomy_image'] ) ) {
			foreach ( $this->config['taxonomy_image'] as $taxonomy => $values ) {
				foreach ( $values as $taxonomy_id => $image_id ) {
					$this->save_taxonomy_image( 'jnews_' . $taxonomy . '_term_image', $taxonomy_id, $this->text_filter( $image_id ), null );
				}
			}
		}
		return array(
			'data' => array(
				'taxonomy'       => $taxonomies,
				'taxonomy_image' => $taxonomies_image,
			),
		);
	}

	/**
	 * Save taxonomy image to databases
	 *
	 * @param string $taxonomy_image_option Taxonomy image option.
	 * @param int    $taxonomy_id Taxonomy ID.
	 * @param string $image_id Image ID.
	 * @param null   $value Value.
	 */
	public function save_taxonomy_image( $taxonomy_image_option, $taxonomy_id, $image_id, $value ) {
		$option_name = $taxonomy_image_option;
		if ( ! isset( $value ) ) {
			$option                 = get_option( $option_name );
			$option[ $taxonomy_id ] = $image_id;
		} else {
			$option = $value;
		}

		update_option( $option_name, $option );
	}

	/**
	 * Temporary support webp.
	 *
	 * @param array $mime_types Mime types keyed by the file extension regex corresponding to those types.
	 *
	 * @return array
	 */
	public function support_webp_mime_type( $mime_types ) {
		$mime_types['webp'] = 'image/webp';

		return $mime_types;
	}

	/**
	 * Handle Import Image
	 * Note: import image will use custom config data instead config from demo
	 *
	 * @return array
	 */
	public function do_import_image() {
		$images = array();
		add_filter( 'upload_mimes', array( $this, 'support_webp_mime_type' ) );
		foreach ( $this->data as $key => $image ) {
			$result = $this->handle_file( $image );
			if ( $result ) {
				$images[ $key ]                = $result;
				$this->config['image'][ $key ] = $result;
			}
		}
		remove_filter( 'upload_mimes', array( $this, 'support_webp_mime_type' ) );
		return array(
			'data' => $images,
		);
	}

	/**
	 * Handle Import file, and return File ID when process complete
	 *
	 * @param string $url url of image.
	 *
	 * @return int|null
	 */
	public function handle_file( $url ) {
		$file_name = basename( $url );
		$upload    = wp_upload_bits( $file_name, null, '' );
		$this->fetch_file( $url, $upload, true );

		if ( $upload['file'] ) {
			$file_loc  = $upload['file'];
			$file_name = basename( $upload['file'] );
			$file_type = wp_check_filetype( $file_name );

			$attachment = array(
				'post_mime_type' => $file_type['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
				require_once ABSPATH . '/wp-admin/includes/image.php';
			}

			$attach_id     = wp_insert_attachment( $attachment, $file_loc );
			$process_image = true;

			if ( 'image/webp' === $file_type['type'] ) {
				$process_image = false;
			}
			if ( $process_image ) {
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file_loc );
				wp_update_attachment_metadata( $attach_id, $attach_data );
			}

			return $attach_id;
		} else {
			return null;
		}
	}

	/**
	 * Download file and save to file system
	 *
	 * @param string  $url A url.
	 * @param array   $upload {
	 *  Information about the newly-uploaded file.
	 *      @type string       $file  Filename of the newly-uploaded file.
	 *      @type string       $url   URL of the uploaded file.
	 *      @type string       $type  File type.
	 *      @type string|false $error Error message, if there has been an error.
	 *  }
	 * @param boolean $retry Optional. Retry if failed.
	 *
	 * @return array|bool
	 */
	public function fetch_file( $url, $upload, $retry = false ) {
		$http     = new \WP_Http();
		$response = $http->get( $url );

		if ( is_wp_error( $response ) ) {
			if ( $retry ) {
				return $this->fetch_file( $url, $upload['file'] );
			}
			return false;
		}

		$headers             = wp_remote_retrieve_headers( $response );
		$headers['response'] = wp_remote_retrieve_response_code( $response );

		if ( false == $upload['file'] ) {
			return $headers;
		}

		// GET request - write it to the supplied filename
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();
		}
		$wp_filesystem->put_contents( $upload['file'], wp_remote_retrieve_body( $response ), FS_CHMOD_FILE );

		return $headers;
	}

	/**
	 * Handle import post
	 * Note: import post will use custom config data instead config from demo
	 */
	public function do_import_post() {

		foreach ( $this->data as $slug => $item ) {
			$this->data[ $slug ]           = $this->import_post( $slug, $item, 0, $this->import_option['builder_content'] );
			$this->config['post'][ $slug ] = $this->data[ $slug ];
		}

		return array(
			'data' => $this->data,
		);
	}

	/**
	 * Import post
	 *
	 * @param string $slug Post slug.
	 * @param array  $item Post data.
	 * @param int    $index Data index.
	 * @param string $builder Post Builder.
	 *
	 * @return int|WP_Error
	 */
	public function import_post( $slug, $item, $index = 0, $builder = 'vc-content' ) {
		$check_post = get_page_by_title( $item['title'], null, $item['post_type'] );

		$post_date = $this->post_date( $slug );

		$post_array = array(
			'post_title'    => wp_kses(
				$item['title'],
				array(
					'em'     => array(),
					'strong' => array(),
				)
			),
			'post_name'     => $slug,
			'post_type'     => $item['post_type'],
			'post_status'   => 'publish',
			'post_date'     => $post_date,
			'post_date_gmt' => $post_date,
		);

		if ( isset( $item['parent'] ) ) {
			$post_array['post_parent'] = $this->text_filter( $item['parent'] );
		}

		if ( ( 'footer' === $item['post_type'] || 'page' === $item['post_type'] ) && 'default' != ( isset( $item['metabox']['_wp_page_template'] ) && $item['metabox']['_wp_page_template'] ) ) {
			if ( 'vc-content' === $builder ) {
				unset( $item['metabox']['_elementor_page_settings'] );
				unset( $item['metabox']['_elementor_edit_mode'] );
				unset( $item['metabox']['_elementor_data'] );

				$post_array['post_content'] = $this->compile_content( $item['content'] );
			} else {

				unset( $item['metabox']['_wpb_shortcodes_custom_css'] );
				unset( $item['metabox']['_wpb_post_custom_css'] );

				unset( $post_array['post_content'] );
			}
		} else {
			if ( is_array( $item['content'] ) ) {
				$post_data                           = $this->compile_post_content_json( $item['content'] );
				$post_array['post_content']          = $post_data['post_content'];
				$post_array['post_content_filtered'] = $post_data['post_content_filtered'];
			} else {
				$post_array['post_content'] = $this->compile_content( $item['content'] );
			}
		}

		// insert or update post
		if ( ! $check_post ) {
			$post_id = wp_insert_post( $post_array );
		} else {
			$post_id = $check_post->ID;
			wp_update_post( $post_array );
		}

		// set post featured image
		if ( isset( $item['featured_image'] ) ) {
			set_post_thumbnail( $post_id, $this->config['image'][ $item['featured_image'] ] );
		}

		// set taxonomy
		if ( isset( $item['taxonomy'] ) && ! empty( $item['taxonomy'] ) ) {
			foreach ( $item['taxonomy'] as $taxonomy => $taxstring ) {
				$tax_array = array();
				$taxs      = explode( ',', $taxstring );

				if ( count( $taxs ) > 1 ) {
					$taxs = array_map( 'trim', explode( ',', $taxstring ) );

					foreach ( $taxs as $tax ) {
						$tax_array[] = $this->config['taxonomy'][ $taxonomy ][ $tax ];
					}

					wp_set_object_terms( $post_id, $tax_array, $taxonomy );

				} else {

					wp_set_object_terms( $post_id, $taxstring, $taxonomy );

				}
			}
		}

		// set metabox
		if ( isset( $item['metabox'] ) && ! empty( $item['metabox'] ) ) {
			$this->update_metabox( $post_id, $item['metabox'] );
		}

		return $post_id;
	}

	/**
	 * Post date
	 *
	 * @param  string $slug Post slug.
	 *
	 * @return string
	 */
	public function post_date( $slug ) {
		$index = 0;
		foreach ( $this->config['post'] as $key => $post ) {
			if ( $slug === $key ) {
				break;
			}
			$index ++;
		}

		$now       = strtotime( '-1 months' );
		$interval  = $index * DAY_IN_SECONDS;
		$post_date = gmdate( 'Y-m-d H:i:s', ( $now - $interval ) );

		return $post_date;
	}

	/**
	 * Compile post content JSON
	 *
	 * @param array $content Array Content.
	 *
	 * @return array
	 */
	public function compile_post_content_json( $content ) {
		$data                          = wp_parse_args(
			$content ? $content : array(),
			array(
				'post_content'          => '',
				'post_content_filtered' => '',
			)
		);
		$data['post_content']          = $this->text_filter( $data['post_content'] );
		$data['post_content_filtered'] = $this->text_filter( $data['post_content_filtered'] );
		return $data;
	}

	/**
	 * Print file with content.
	 *
	 * @param string $file Post content name.
	 *
	 * @return string
	 *
	 * print file with content
	 */
	public function compile_content( $file ) {
		// $text = $this->load_file_content( $this->import_path . '/post/' . $file );
		$text = $file;

		return $this->text_filter( $text );
	}

	/**
	 * Update metabox
	 *
	 * @param int   $post_id Post ID.
	 * @param array $metabox List metabox.
	 */
	public function update_metabox( $post_id, $metabox ) {
		foreach ( $metabox as $metakey => $metavalue ) {
			$metavalue = $this->recursive_filter_text( $metavalue );

			if ( '_elementor_data' === $metakey && ! empty( $metavalue ) ) {
				$metavalue = $this->compile_content( $metavalue );
			}

			update_post_meta( $post_id, $metakey, $metavalue );
		}
	}

	/**
	 * Recursive filter text
	 *
	 * @param string|array $contents Content.
	 *
	 * @return string|array
	 */
	public function recursive_filter_text( $contents ) {
		if ( is_array( $contents ) ) {
			foreach ( $contents as $key => $value ) {
				$contents[ $key ] = $this->recursive_filter_text( $value );
			}
		} else {
			return $this->text_filter( $contents );
		}

		return $contents;
	}

	/**
	 * Save content to databases
	 *
	 * @param string $type String of option type.
	 * @param mixed  $content content of option.
	 */
	public static function save_option( $type, $content ) {
		$option_name     = self::$option_content;
		$option          = get_option( $option_name );
		$option[ $type ] = $content;

		update_option( $option_name, $option );
	}

	/**
	 * Save import to databases
	 *
	 * @param string $type String of option type.
	 * @param mixed  $value Value of option.
	 */
	public function save_import_option( $type, $value ) {
		$option          = get_option( self::$option );
		$option[ $type ] = $value;

		update_option( self::$option, $option );
	}

	/**
	 * Load file in this path, and return text that contain inside the file it self
	 *
	 * @param string $filepath File path.
	 *
	 * @return string
	 */
	public function load_file_content( $filepath ) {
		ob_start();
		include $filepath;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Replace text with defined content
	 *
	 * @param string $text A Text.
	 *
	 * @return string|null
	 */
	public function text_filter( $text ) {
		$result = preg_replace_callback( '/(\{{.*?\}})/', array( $this, 'trim_convert_tag' ), $text );

		return $result;
	}

	/**
	 * Trim convert tag searched text
	 *
	 * @param array $content Searched text.
	 *
	 * @return null|string
	 */
	public function trim_convert_tag( $content ) {
		return $this->convert_tag( trim( $content[1], '{}' ) );
	}

	/**
	 * Convert every string with tag to corespondent tag
	 *
	 * @param string $string Searched tag text
	 *
	 * @return null|string
	 */
	public function convert_tag( $string ) {
		$tag = explode( ':', $string );

		if ( count( $tag ) > 1 ) {
			switch ( $tag[0] ) {
				case 'image':
					$result = $this->image_tag( $tag );
					break;
				case 'category':
					$result = $this->category_tag( $tag );
					break;
				case 'taxonomy':
					$result = $this->taxonomy_tag( $tag );
					break;
				case 'post':
					$result = $this->post_tag( $tag );
					break;
				case 'url':
					$result = $this->url_tag( $tag );
					break;
				case 'menu':
					$result = $this->menu_tag( $tag );
					break;
				case 'menu_location':
					$result = $this->menu_location_tag( $tag );
					break;
				default:
					$result = $string;
					break;
			}
		} else {
			$result = $string;
		}

		return apply_filters( 'jnews_convert_tag', $result, $tag );
	}

	/**
	 * Convert image tag
	 * ex:
	 *  1. get ID of image : image:news01:id
	 *  2. get URL of image by size : image:news01:url:thumbnail
	 *  3. Retrieve the URL for an attachment : image:attach:src
	 *
	 * @param array $tag Splitted string.
	 *
	 * @return null
	 */
	public function image_tag( $tag ) {
		if ( isset( $this->config['image'][ $tag[1] ] ) ) {
			$image_id = $this->config['image'][ $tag[1] ];
			$to       = $tag[2];

			if ( 'id' === $to ) {
				return $image_id;
			} elseif ( 'url' === $to ) {
				$result = wp_get_attachment_image_src( $image_id, $tag[3] );

				return $result[0];
			} else {
				$result = wp_get_attachment_url( $image_id );

				return $result;
			}
		}

		return null;
	}

	/**
	 * Convert category tag
	 * ex:
	 *  1. get ID of category : category:first-category-slug:id
	 *  2. get URL of category : category:first-category-slug:url
	 *
	 * @param array $tag Splitted string.
	 *
	 * @return null|string
	 */
	public function category_tag( $tag ) {
		if ( isset( $this->config['taxonomy']['category'] ) && isset( $this->config['taxonomy']['category'][ $tag[1] ] ) ) {
			$category_id = $this->config['taxonomy']['category'][ $tag[1] ];
			if ( ! is_array( $category_id ) ) {
				$to = $tag[2];

				if ( 'id' === $to ) {
					return $category_id;
				} elseif ( 'url' === $to ) {
					return get_category_link( $this->config['taxonomy']['category'][ $tag[1] ] );
				}
			}
		}

		return null;
	}

	/**
	 * Convert taxonomy tag
	 * ex:
	 *  1. get ID of taxonomy : taxonomy:post_tag:first-tag:id
	 *  2. get URL of taxonomy : taxonomy:post_tag:first-tag:url
	 *
	 * @param array $tag Splitted string.
	 *
	 * @return null|string
	 */
	public function taxonomy_tag( $tag ) {
		$taxonomy = $tag[1];

		if ( isset( $this->config['taxonomy'][ $taxonomy ] ) && $this->config['taxonomy'][ $taxonomy ][ $tag[2] ] ) {
			$taxonomy_id = $this->config['taxonomy'][ $taxonomy ][ $tag[2] ];
			if ( ! is_array( $taxonomy_id ) ) {
				$to = $tag[3];

				if ( 'id' === $to ) {
					return $taxonomy_id;
				} elseif ( 'url' === $to ) {
					return get_term_link( $taxonomy_id . $taxonomy );
				}
			}
		}

		return null;
	}

	/**
	 * Convert post tag
	 * ex:
	 *  1. get ID of post : post:first-content-slug:id
	 *  2. get URL of post : post:first-content-slug:url
	 *
	 * @param array $tag Splitted string.
	 *
	 * @return null|string
	 */
	public function post_tag( $tag ) {
		if ( isset( $this->config['post'][ $tag[1] ] ) ) {
			$post_id = $this->config['post'][ $tag[1] ];
			if ( ! is_array( $post_id ) ) {
				$to = $tag[2];

				if ( 'id' === $to ) {
					return $post_id;
				} elseif ( 'url' === $to ) {
					return get_permalink( $post_id );
				}
			}
		}

		return null;
	}

	/**
	 * Convert url tag
	 * ex:
	 *  1. get home url : url:home
	 *  2. get specific endpoint : url:home:favorite
	 *
	 * @param array $tag Splitted string.
	 *
	 * @return string|void
	 */
	public function url_tag( $tag ) {
		if ( isset( $tag[1] ) ) {
			$content = $tag[1];
			$to      = isset( $tag[2] ) ? $tag[2] : '';

			switch ( $content ) {
				case 'domain':
					$urlparts = parse_url( home_url( '/' ) );
					$domain   = $urlparts['host'];
					return $domain;
					break;
				case 'home':
					if ( ! empty( $to ) ) {
						return home_url( '/' . $to, 'relative' );
					} else {
						return home_url( '/', 'relative' );
					}
					break;
				case 'email':
					$current_user = wp_get_current_user();
					return $current_user->user_email;
					break;
			}
		}
		return null;
	}

	/**
	 * Convert menu tag
	 * ex:
	 *  1. get ID of post : menu:first-menu:id
	 *
	 * @param array $tag Splitted string.
	 *
	 * @return null|string
	 */
	public function menu_tag( $tag ) {
		if ( isset( $this->config['menu'][ $tag[1] ] ) ) {
			$menu_id = $this->config['menu'][ $tag[1] ];
			if ( ! is_array( $menu_id ) ) {
				$to = $tag[2];

				if ( 'id' === $to ) {
					return $menu_id;
				}
			}
		}

		return null;
	}

	/**
	 * convert menu location tag
	 * ex:
	 *  1. get ID of post : menu_location:main-navigation:id
	 *
	 * @param array $tag Splitted string.
	 *
	 * @return null|string
	 */
	public function menu_location_tag( $tag ) {
		if ( isset( $this->config['menu_location'][ $tag[1] ] ) ) {
			$menu_id = $this->config['menu_location'][ $tag[1] ];
			if ( ! is_array( $menu_id ) ) {
				$to = $tag[2];

				if ( 'id' === $to ) {
					return $menu_id;
				}
			}
		}

		return null;
	}
}
