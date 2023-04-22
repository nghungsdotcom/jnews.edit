<?php
/**
 * Panel
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel
 */

namespace JNews\Dashboard\Panel;

/**
 * Class used for managing option.
 */
class Panel {

	/**
	 * Stored Options.
	 *
	 * @var bool|PanelOption|PanelOptionVafpress
	 */
	private $options = false;

	/**
	 * Stored Template.
	 *
	 * @var array
	 */
	private $template = array();

	/**
	 * Panel type.
	 *
	 * @var string
	 */
	private $panel_type;

	/**
	 * Construct of class Panel
	 *
	 * @param array $configs Config Panel.
	 */
	public function __construct( $configs ) {
		if ( isset( $configs['type'] ) && 'vafpress' === $configs['type'] ) {
			if ( class_exists( '\VP_Option' ) && class_exists( '\JNews\Dashboard\Panel\PanelOptionVafpress' ) ) {
				$this->set_panel_type( $configs['type'] );
				$this->set_options( new PanelOptionVafpress( $configs ) );
			}
		} else {
			if ( class_exists( '\JNews\Dashboard\Panel\PanelOption' ) ) {
				$this->set_options( new PanelOption( $configs ) );
			}
		}
	}

	/**
	 * Set Options
	 *
	 * @param PanelOption|PanelOptionVafpress $options Panel Option.
	 */
	public function set_options( $options ) {
		$this->options = $options;
	}

	/**
	 * Get Options
	 *
	 * @return PanelOption|PanelOptionVafpress $options Panel Option.
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Get Template
	 *
	 * @return array $options template.
	 */
	public function get_template() {
		return $this->template;
	}

	/**
	 * Set Template
	 *
	 * @param array $template template option.
	 */
	public function set_template( $template ) {
		$this->template = $template;
	}

	/**
	 * Get panel type
	 *
	 * @return string.
	 */
	public function get_panel_type() {
		return $this->panel_type;
	}

	/**
	 * Set panel type
	 *
	 * @param string $panel_type Panel type.
	 */
	public function set_panel_type( $panel_type ) {
		$this->panel_type = $panel_type;
	}

	/**
	 * Panel Data
	 *
	 * @return array
	 */
	public function panel_data() {
		$options             = array();
		$method_exist        = $this->get_options() && method_exists( $this->get_options(), 'get_options_set' );
		$options['type']     = 'vafpress';
		$options['layout']   = 'fixed';
		$options['action']   = '';
		$options['logo']     = '';
		$options['menus']    = array();
		$options['title']    = '';
		$options['version']  = '';
		$options['dbValues'] = array();
		if ( $method_exist ) {
			if ( 'JNews\Dashboard\Panel\PanelOption' === get_class( $this->get_options() ) ) {
				$options['type'] = '';
			}
			$this->set_template( $this->get_options()->get_template() );
			$list_option         = $this->get_options()->get_options_set();
			$options['layout']   = $list_option->get_layout();
			$options['action']   = $this->get_options()->get_option_key();
			$options['logo']     = '';
			$options['menus']    = $this->get_all_menus( $list_option );
			$options['title']    = $list_option->get_title();
			$options['version']  = $list_option->get_version();
			$options['dbValues'] = $list_option->get_values();
			$options['nonce']    = wp_create_nonce( 'vafpress' );
		}

		return $options;
	}

	/**
	 * Retrieve Menus
	 *
	 * @return array
	 */
	/**
	 * Retrieve Menus
	 *
	 * @param array $menu_options Menu options.
	 * @param array $menu_template Original Menu from template.
	 *
	 * @return array
	 */
	private function retrieve_menus( $menu_options, $menu_template = array() ) {
		$menus = array();
		/**
		 * Initialization
		 *
		 * @var Option\OptionControlGroupMenu|\VP_Option_Control_Group_Menu $menu
		 */
		foreach ( $menu_options as $key => $menu ) {
			if ( ! empty( $menu->get_name() ) ) {
				$is_first  = reset( $menu_options ) === $menu;
				$item_menu = array(
					'name'     => $menu->get_name(),
					'title'    => $menu->get_title(),
					'icon'     => $menu->get_icon(),
					'current'  => $is_first,
					'controls' => $this->retrieve_controls( $menu->get_controls() ),
				);

				if ( isset( $menu_template[ $key ]['searchable'] ) ) {
					$item_menu['searchable'] = $menu_template[ $key ]['searchable'];
				}
				if ( isset( $menu_template[ $key ]['type'] ) ) {
					$item_menu['type'] = $menu_template[ $key ]['type'];
				}
				if ( $menu->get_menus() ) {
					$item_menu['menus'] = $this->retrieve_menus( $menu->get_menus(), $menu_template[ $key ]['menus'] );
				}
				$menus[] = $item_menu;
			}
		}
		return $menus;
	}

	/**
	 * Retrieve Controls
	 *
	 * @param array $control_options Control option.
	 *
	 * @return array
	 */
	private function retrieve_controls( $control_options ) {
		$controls = array();
		/**
		 * Initialization
		 *
		 * @var Option\OptionControlGroupSection|\VP_Option_Control_Group_Section|Option\OptionControlField|\VP_Control_Field $control
		 */
		foreach ( $control_options as $control ) {
			if ( 'vafpress' === $this->get_panel_type() ) {
				$type = strtolower( substr( get_class( $control ), strrpos( get_class( $control ), '_' ) + 1 ) );
			} else {
				$type = $control->get_type();
			}

			if ( 'section' !== $type ) {
				$item_control = array(
					'type'            => $type,
					'name'            => $control->get_name(),
					'label'           => $control->get_label(),
					'description'     => $control->get_description(),
					'active_callback' => json_decode( $control->get_active_callback() ),
					'default'         => $control->get_default(),
					'value'           => $control->get_value(),
				);
				if ( 'notebox' === $type ) {
					$item_control['status'] = $control->get_status();
				}
				$controls[] = $item_control;
			} else {
				$item_control = array(
					'type'        => $type,
					'name'        => $control->get_name(),
					'title'       => $control->get_title(),
					'description' => $control->get_description(),
					'dependency'  => $control->get_dependency(),
					'is_hidden'   => $control->is_hidden(),
					'fields'      => $this->retrieve_fields( $control->get_fields() ),
				);
				$controls[]   = $item_control;
			}
		}
		return $controls;
	}

	/**
	 * Retrieve fields
	 *
	 * @param mixed $field_options Field options.
	 *
	 * @return array
	 */
	private function retrieve_fields( $field_options ) {
		$fields = array();
		$fields = $this->retrieve_controls( $field_options );
		return $fields;
	}

	/**
	 * Get all menus
	 *
	 * @param Option\OptionControlSet|\VP_Option_Control_Set $list_option List Option.
	 *
	 * @return array
	 */
	public function get_all_menus( $list_option ) {
		$menus = array();
		$menus = array_merge( $menus, $this->retrieve_menus( $list_option->get_menus(), $this->get_template()['menus'] ) );
		return $menus;
	}

	/**
	 * Combine array with the same $left to single array item
	 * from
	 * array( [0] => array( "name" => "a", "value" => "1" ),
	 *        [1] => array( "name" => "a", "value" => "2" ),
	 *        [0] => array( "name" => "b", "value" => "3" ))
	 * to
	 * array( "a" => array( "1", "2" ),
	 *        "b" => 3)
	 *
	 * @param  array $array Array to unite.
	 * @param  mixed $left  Left side array key.
	 * @param  mixed $right Right side array key.
	 * @return array        United Array
	 */
	public static function unite( $array, $left, $right ) {
		$result = array();
		if ( is_array( $array ) ) {
			foreach ( $array as $item ) {
				if ( is_array( $item ) ) {
					if ( isset( $result[ $item[ $left ] ] ) ) {
						if ( is_array( $result[ $item[ $left ] ] ) ) {
							$result[ $item[ $left ] ][] = $item[ $right ];
						} else {
							$result[ $item[ $left ] ] = array( $result[ $item[ $left ] ], $item[ $right ] );
						}
					} else {
						$result[ $item[ $left ] ] = $item[ $right ];
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Loop contorls
	 *
	 * @param OptionControlGroupMenu $menu Menu Group.
	 * @param mixed                  $include_section Include section.
	 *
	 * @return array
	 */
	public static function loop_controls( $menu, $include_section ) {
		$fields = array();
		/**
		 * Initialization
		 *
		 * @var Option\OptionControlGroupSection|\VP_Option_Control_Group_Section $control
		 */
		foreach ( $menu->get_controls() as $control ) {
			if ( ! empty( $control->get_name() ) ) {
				if ( get_class( $control ) === 'JNews\Dashboard\Panel\Option\OptionControlGroupSection' ) {
					if ( $include_section ) {
						$fields[ $control->get_name() ] = $control;
					}
					/**
					 * Initialization
					 *
					 * @var Option\OptionControlField|\VP_Control_Field $field
					 */
					foreach ( $control->get_fields() as $field ) {
						if ( ! empty( $field->get_name() ) ) {
							if ( get_class( $field ) !== 'JNews\Dashboard\Panel\Option\OptionControlFieldImpexp' ) {
								$fields[ $field->get_name() ] = $field;
							}
						}
					}
				} else {
					if ( get_class( $control ) !== 'JNews\Dashboard\Panel\Option\OptionControlFieldImpexp' ) {
						$fields[ $control->get_name() ] = $control;
					}
				}
			}
		}
		return $fields;
	}


	/**
	 * Is Multiselectable
	 *
	 * @param Option\OptionControlField $object Option Control Field.
	 *
	 * @return boolean
	 */
	public static function is_multiselectable( $object ) {
		if ( is_object( $object ) ) {
			return $object->get_multiselect();
		} elseif ( is_string( $object ) ) {
			/**
			 * Check if field return string
			 */
			return false;
		} else {
			return false;
		}
		return false;
	}

	/**
	 * Get panel option
	 *
	 * @param  string $key Option key.
	 * @param  mixed  $default Option Default Value.
	 *
	 * @return mixed
	 */
	public static function get_panel_option( $key, $default = false, $type = '' ) {
		if ( 'vafpress' === $type ) {
			$value = get_option( $key, $default );
		} else {
			$jnews_options = get_option( 'jnews_option', array() );
			if ( isset( $jnews_options[ $key ] ) && isset( $jnews_options[ $key ] ) ) {
				$value = $jnews_options[ $key ];
			} else {
				$jnews_options[ $key ] = $default;
				$value                 = $jnews_options[ $key ];
			}
		}

		return apply_filters( "jnews_option_panel_{$key}", $value );
	}

	/**
	 * Update panel option
	 *
	 * @param  string $key Option key.
	 * @param  mixed  $value Option Default Value.
	 *
	 * @return boolean
	 */
	public static function update_panel_option( $key, $value ) {
		$jnews_options         = get_option( 'jnews_option', array() );
		$options               = $value;
		$jnews_options[ $key ] = $options;
		return update_option( 'jnews_option', $jnews_options );
	}

	/**
	 * JNews Panel Option
	 *
	 * @param string     $key Key options.
	 * @param null|mixed $default Default value option.
	 *
	 * @return mixed
	 */
	public static function jnews_panel_option( $key, $default = null ) {
		$panel_options = PanelOption::get_pool();

		if ( empty( $panel_options ) ) {
			return apply_filters( 'jnews_panel_option', $default );
		}

		$keys = apply_filters( 'jnews_panel_get_option_key', explode( '.', $key ) );
		$temp = null;

		foreach ( $keys as $idx => $key ) {
			if ( 0 === $idx ) {
				if ( array_key_exists( $key, $panel_options ) ) {
					$temp = $panel_options[ $key ];
					$temp = $temp->get_options();
				} else {
					return apply_filters( 'jnews_panel_option', $default );
				}
			} else {
				if ( is_array( $temp ) && array_key_exists( $key, $temp ) ) {
					$temp = $temp[ $key ];
				} else {
					return apply_filters( 'jnews_panel_option', $default );
				}
			}
		}
		return apply_filters( 'jnews_panel_option', $temp );
	}

}
