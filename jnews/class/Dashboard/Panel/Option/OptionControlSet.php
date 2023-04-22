<?php
/**
 * Option Control Set
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel\Option
 */

namespace JNews\Dashboard\Panel\Option;

/**
 * Class OptionControlSet
 */
class OptionControlSet {


	const SAVE_SUCCESS = 1;

	const SAVE_NOCHANGES = 2;

	const SAVE_FAILED = 3;

	/**
	 * Menus
	 *
	 * @var array
	 */
	private $menus;

	/**
	 * Title
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Logo
	 *
	 * @var string
	 */
	private $logo;

	/**
	 * Layout
	 *
	 * @var string
	 */
	private $layout;

	/**
	 * Construction of OptionControlSet
	 */
	public function __construct() {
		$this->menus = array();
	}

	/**
	 * Get Option Set Title
	 *
	 * @return string Option set title
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Set Option Set title
	 *
	 * @param string $title Option set title.
	 *
	 * @return OptionControlSet
	 */
	public function set_title( $title ) {
		$this->title = $title;
		return $this;
	}

	/**
	 * Get Option Set Version
	 *
	 * @return string Option set version
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Set Option Set version
	 *
	 * @param string $version Option set version.
	 *
	 * @return OptionControlSet
	 */
	public function set_version( $version ) {
		$this->version = $version;
		return $this;
	}

	/**
	 * Set layout
	 *
	 * @return string layout
	 */
	public function get_layout() {
		return $this->layout;
	}

	/**
	 * Get layout
	 *
	 * @param string $layout layout.
	 *
	 * @return [type]
	 */
	public function set_layout( $layout ) {
		$this->layout = $layout;
		return $this;
	}

	/**
	 * Get logo
	 *
	 * @return string Logo URL
	 */
	public function get_logo() {
		return $this->logo;
	}

	/**
	 * Set logo
	 *
	 * @param string $logo Logo URL.
	 *
	 * @return OptionControlSet
	 */
	public function set_logo( $logo ) {
		$this->logo = $logo;
		return $this;
	}

	/**
	 * Add menu
	 *
	 * @param OptionControlGroupMenu $menu Object Menu.
	 */
	public function add_menu( $menu ) {
		$this->menus[] = $menu;
	}

	/**
	 * Getter of $menus
	 *
	 * @return array Collection of menus object
	 */
	public function get_menus() {
		return $this->menus;
	}

	/**
	 * Setter of $menus
	 *
	 * @param Array $menus Collection of menus object.
	 *
	 * @return OptionControlSet
	 */
	public function set_menus( $menus ) {
		$this->menus = $menus;
		return $this;
	}

	/**
	 * Get fields
	 *
	 * @param boolean $include_section Include Section.
	 *
	 * @return array
	 */
	public function get_fields( $include_section = false ) {
		$fields = array();

		foreach ( $this->menus as $menu ) {
			$submenus = $menu->get_menus();
			if ( ! empty( $submenus ) ) {
				foreach ( $submenus as $submenu ) {
					$fields = array_merge( $fields, \JNews\Dashboard\Panel\Panel::loop_controls( $submenu, $include_section ) );
				}
			} else {
				$fields = array_merge( $fields, \JNews\Dashboard\Panel\Panel::loop_controls( $menu, $include_section ) );
			}
		}
		return $fields;
	}

	/**
	 * Get field types
	 *
	 * @return array
	 */
	public function get_field_types() {
		$fields = $this->get_fields();
		$types  = array();
		foreach ( $fields as $field ) {
			$type = $field->get_type();
			if ( ! in_array( $type, $types, true ) ) {
				$types[] = $type;
			}
		}
		return $types;
	}

	/**
	 * Get field
	 *
	 * @param string $name Field name.
	 *
	 * @return array|null
	 */
	public function get_field( $name ) {
		$fields = $this->get_fields();
		if ( array_key_exists( $name, $fields ) ) {
			return $fields[ $name ];
		}
		return null;
	}

	/**
	 * Process Binding
	 */
	public function process_binding() {
		$fields = $this->get_fields();

		foreach ( $fields as $field ) {
			$bind = $field->get_binding();
			$val  = $field->get_value();
			if ( ! empty( $bind ) && is_null( $val ) ) {
				$bind   = explode( '|', $bind );
				$func   = $bind[0];
				$params = $bind[1];
				$params = preg_split( '/[\s,]+/', $params );
				$values = array();
				foreach ( $params as $param ) {
					if ( array_key_exists( $param, $fields ) ) {
						$values[] = $fields[ $param ]->get_value();
					}
				}
				$result = call_user_func_array( $func, $values );

				if ( \JNews\Dashboard\Panel\Panel::is_multiselectable( $field ) ) {
					$result = (array) $result;
				} else {
					if ( is_array( $result ) ) {
						$result = reset( $result );
					}
					$result = (string) $result;
				}
				$field->set_value( $result );
			}

			if ( \JNews\Dashboard\Panel\Panel::is_multiselectable( $field ) ) {
				$bind = $field->get_items_binding();
				if ( ! empty( $bind ) ) {
					$bind   = explode( '|', $bind );
					$func   = $bind[0];
					$params = $bind[1];
					$params = preg_split( '/[\s,]+/', $params );
					$values = array();
					foreach ( $params as $param ) {
						if ( array_key_exists( $param, $fields ) ) {
							$values[] = $fields[ $param ]->get_value();
						}
					}
					$items = call_user_func_array( $func, $values );
					if ( is_array( $items ) && ! empty( $items ) ) {
						$field->set_items( array() );
						$field->add_items_from_array( $items );
					}
				}
			}
		}
	}

	/**
	 * Process dependencies
	 */
	public function process_dependencies() {
		$fields = $this->get_fields( true );

		foreach ( $fields as $field ) {
			$dependency = $field->get_dependency();
			if ( ! empty( $dependency ) ) {
				$dependency = explode( '|', $dependency );
				$func       = $dependency[0];
				$params     = $dependency[1];
				$params     = preg_split( '/[\s,]+/', $params );
				$values     = array();
				foreach ( $params as $param ) {
					if ( array_key_exists( $param, $fields ) ) {
						$values[] = $fields[ $param ]->get_value();
					}
				}
				$result = call_user_func_array( $func, $values );
				if ( ! $result ) {
					$field->add_container_extra_classes( 'jnews-panel-dep-inactive' );
					$field->is_hidden( true );
				}
			}
		}
	}


	/**
	 * Normalize values
	 *
	 * @param array $opt_arr Option array.
	 *
	 * @return array
	 */
	public function normalize_values( $opt_arr ) {
		$fields            = $this->get_fields();
		$force_first_value = apply_filters( 'jnews_vp_multiple_value_force_first_value', false );

		foreach ( $opt_arr as $key => $value ) {
			if ( array_key_exists( $key, $fields ) ) {
				$is_multi = \JNews\Dashboard\Panel\Panel::is_multiselectable( $fields[ $key ] );
				if ( $is_multi && ! is_array( $value ) ) {
					$opt_arr[ $key ] = array( $value );
				}
				if ( ! $is_multi && is_array( $value ) ) {
					$opt_arr[ $key ] = $force_first_value ? $value[0] : '';
				}
			}
		}
		return $opt_arr;
	}

	/**
	 * Get Defaults
	 *
	 * @return array
	 */
	public function get_defaults() {
		$defaults = array();
		$fields   = $this->get_fields();
		foreach ( $fields as $field ) {
			$defaults[ $field->get_name() ] = $field->get_default();
		}
		return $defaults;
	}

	/**
	 * Get Values
	 *
	 * @return array
	 */
	public function get_values() {
		$values = array();
		$fields = $this->get_fields();
		foreach ( $fields as $field ) {
			$values[ $field->get_name() ] = $field->get_value();
		}
		return $values;
	}

	/**
	 * Setup
	 *
	 * @param array $options Options.
	 */
	public function setup( $options ) {
		// Populate option to fields' values.
		$this->populate_values( $options );

		// Process binding.
		$this->process_binding();

		// Process dependencies.
		$this->process_dependencies();
	}

	/**
	 * Save
	 *
	 * @param string $option_key Option key.
	 *
	 * @return array
	 */
	public function save( $option_key ) {
		$opt = $this->get_values();

		do_action( 'jnews_panel_option_set_before_save', $opt );

		if ( \JNews\Dashboard\Panel\Panel::update_panel_option( $option_key, $opt ) ) {
			$result['status']  = true;
			$result['code']    = self::SAVE_SUCCESS;
			$result['message'] = __( 'Saving successful', 'jnews' );
			$curr_opt          = \JNews\Dashboard\Panel\Panel::get_panel_option( $option_key, $opt );
		} else {
			$curr_opt = \JNews\Dashboard\Panel\Panel::get_panel_option( $option_key, $opt );
			$changed  = $opt !== $curr_opt;
			if ( $changed ) {
				$result['status']  = false;
				$result['code']    = self::SAVE_FAILED;
				$result['message'] = __( 'Saving failed', 'jnews' );
			} else {
				$result['status']  = true;
				$result['code']    = self::SAVE_NOCHANGES;
				$result['message'] = __( 'No changes made', 'jnews' );
			}
		}

		do_action( 'jnews_panel_option_set_after_save', $curr_opt, $result['status'], $option_key );

		return $result;
	}

	/**
	 * Populate Values
	 *
	 * @param array $opt Options.
	 * @param bool  $force_update Force update.
	 */
	public function populate_values( $opt, $force_update = false ) {
		$fields = $this->get_fields();
		foreach ( $fields as $field ) {
			$is_multi = \JNews\Dashboard\Panel\Panel::is_multiselectable( $field );
			if ( array_key_exists( $field->get_name(), $opt ) ) {
				if ( $is_multi && is_array( $opt[ $field->get_name() ] ) ) {
					$field->set_value( $opt[ $field->get_name() ] );
				}
				if ( ! $is_multi && ! is_array( $opt[ $field->get_name() ] ) ) {
					$field->set_value( $opt[ $field->get_name() ] );
				}
			} else {
				if ( $force_update ) {
					if ( $is_multi ) {
						$field->set_value( array() );
					} else {
						$field->set_value( '' );
					}
				}
			}
		}
	}

}
