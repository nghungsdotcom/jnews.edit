<?php
/**
 * Option Control Field
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel\Option
 */

namespace JNews\Dashboard\Panel\Option;

/**
 * Class OptionControlField.
 */
class OptionControlField {


	/**
	 * Unique type of the field
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Unique name of the field
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Label for the field
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * Description on what the field about
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Multi select field status
	 *
	 * @var boolean
	 */
	protected $multiselect;

	/**
	 * Validation pattern string
	 *
	 * @var string
	 */
	protected $validation;

	/**
	 * Dependency pattern string
	 *
	 * @var string
	 */
	protected $dependency;

	/**
	 * Active callback (JSON string)
	 *
	 * @var string
	 */
	protected $active_callback;

	/**
	 * Binding patter string
	 *
	 * @var string
	 */
	protected $binding;

	/**
	 * Default value for the field
	 *
	 * @var string|array
	 */
	protected $default;

	/**
	 * Maximum height of the field
	 *
	 * @var int
	 */
	protected $field_max_height;

	/**
	 * Value for the field
	 *
	 * @var string|array
	 */
	protected $value;

	/**
	 * Status for the field
	 *
	 * @var string
	 */
	protected $status;

	/**
	 * Data to be rendered
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Extra Classes for the container
	 *
	 * @var array
	 */
	protected $container_extra_classes;

	/**
	 * Whether to hide this control in first rendering
	 *
	 * @var boolean|null
	 */
	protected $is_hidden;

	/**
	 * Attach title on group title
	 *
	 * @var boolean
	 */
	protected $attach_title;

	/**
	 * Is ajax call
	 *
	 * @var string
	 */
	protected $ajax_call;

	/**
	 * Class Constructor
	 */
	public function __construct() {
		$this->data                    = array();
		$this->container_extra_classes = array();
	}

	/**
	 * Basic self setup of the object
	 *
	 * @param  array $arr array representation of the field.
	 * @return OptionControlField Field object
	 */
	public function basic_make( $arr ) {
		$this->set_type( isset( $arr['type'] ) ? $arr['type'] : '' )->set_multiselect( isset( $arr['multiselect'] ) ? $arr['multiselect'] : false )->set_name( isset( $arr['name'] ) ? $arr['name'] : '' )->set_label( isset( $arr['label'] ) ? $arr['label'] : '' )->set_default( isset( $arr['default'] ) ? $arr['default'] : null )->set_description( isset( $arr['description'] ) ? $arr['description'] : '' )->set_validation( isset( $arr['validation'] ) ? $arr['validation'] : '' )->set_attach_title( isset( $arr['attach_title'] ) ? $arr['attach_title'] : false )->set_ajax_call( isset( $arr['ajax_call'] ) ? $arr['ajax_call'] : '' );

		if ( isset( $arr['dependency'] ) ) {
			$func  = $arr['dependency']['function'];
			$field = $arr['dependency']['field'];
			$this->set_dependency( $func . '|' . $field );
		}

		if ( isset( $arr['active_callback'] ) ) {
			$dependency = wp_json_encode( $arr['active_callback'] );
			$this->set_active_callback( $dependency );
		}

		if ( isset( $arr['binding'] ) ) {
			$function = $arr['binding']['function'];
			$field    = $arr['binding']['field'];
			$this->set_binding( $function . '|' . $field );
		}

		return $this;
	}

	/**
	 * Add value to render data array
	 *
	 * @param string $key selected data in array.
	 * @param mixed  $value Value to be added to render data array.
	 */
	public function add_data( $key, $value ) {
		$this->data[ $key ] = $value;
	}

	/**
	 * Get render data
	 *
	 * @return array Render data array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set render data
	 *
	 * @param array $data Render data array.
	 * @return OptionControlField
	 */
	public function set_data( $data ) {
		$this->data = $data;
		return $this;
	}

	/**
	 * Set single render data
	 *
	 * @param string $key selected data in array.
	 * @param mixed  $data Value to be added to render data array.
	 * @return OptionControlField
	 */
	public function set_single_data( $key, $data ) {
		$this->data[ $key ] = $data;
		return $this;
	}

	/**
	 * Get single render data
	 *
	 * @param array $key Render data array.
	 */
	public function get_single_data( $key ) {
		return $this->data[ $key ];
	}

	/**
	 * Add value to render data array
	 *
	 * @param string $p_key selected data in array.
	 * @param string $key selected data in array.
	 * @param mixed  $value Value to be added to render data array.
	 */
	public function add_single_data( $p_key, $key, $value ) {
		$this->data[ $p_key ][ $key ] = $value;
	}

	/**
	 * Getter for $name
	 *
	 * @return string unique name of the field
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Setter for $name
	 *
	 * @param string $name unique name of the field.
	 * @return OptionControlField
	 */
	public function set_name( $name ) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Getter for $type
	 *
	 * @return string unique type of the field.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Setter for $type
	 *
	 * @param string $type unique type of the field.
	 * @return OptionControlField
	 */
	public function set_type( $type ) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Getter for $multiselect
	 *
	 * @return boolean
	 */
	public function get_multiselect() {
		return $this->multiselect;
	}

	/**
	 * Setter for $multiselect
	 *
	 * @param boolean $multiselect Multiselect status.
	 * @return OptionControlField
	 */
	public function set_multiselect( $multiselect ) {
		$this->multiselect = $multiselect;
		return $this;
	}

	/**
	 * Getter for $status
	 *
	 * @return boolean
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Setter for $status
	 *
	 * @param boolean $status Field status.
	 * @return OptionControlField
	 */
	public function set_status( $status ) {
		$this->status = $status;
		return $this;
	}

	/**
	 * Is attach title
	 *
	 * @return boolean
	 */
	public function is_attach_title() {
		return $this->attach_title;
	}

	/**
	 * Set attach title
	 *
	 * @param boolean $attach_title Attach title.
	 * @return OptionControlField
	 */
	public function set_attach_title( $attach_title ) {
		$this->attach_title = $attach_title;
		return $this;
	}

	/**
	 * Getter for $label
	 *
	 * @return string label of the field
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Setter for $label
	 *
	 * @param string $label label of the field.
	 * @return OptionControlField
	 */
	public function set_label( $label ) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Getter for $description
	 *
	 * @return string description of the field
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Setter for $description
	 *
	 * @param string $description description of the field.
	 * @return OptionControlField
	 */
	public function set_description( $description ) {
		$this->description = $description;
		return $this;
	}

	/**
	 * Getter for $validation
	 *
	 * @return string validation pattern in string
	 */
	public function get_validation() {
		return $this->validation;
	}

	/**
	 * Setter for $validation
	 *
	 * @param string $validation validation pattern in string.
	 * @return OptionControlField
	 */
	public function set_validation( $validation ) {
		$this->validation = $validation;
		return $this;
	}

	/**
	 * Getter for $dependency
	 *
	 * @return string dependency pattern in string
	 */
	public function get_dependency() {
		return $this->dependency;
	}

	/**
	 * Getter for $active_callback
	 *
	 * @return string active callback (JSON string)
	 */
	public function get_active_callback() {
		return $this->active_callback;
	}

	/**
	 * Setter for $dependency
	 *
	 * @param string $dependency dependency pattern in string.
	 * @return OptionControlField
	 */
	public function set_dependency( $dependency ) {
		$this->dependency = $dependency;
		return $this;
	}

	/**
	 * Setter for $active_callback
	 *
	 * @param string $active_callback active callback (JSON string).
	 * @return OptionControlField
	 */
	public function set_active_callback( $active_callback ) {
		$this->active_callback = $active_callback;
		return $this;
	}

	/**
	 * Get $binding
	 *
	 * @return string bind rule string
	 */
	public function get_binding() {
		return $this->binding;
	}

	/**
	 * Set $binding
	 *
	 * @param string $binding bind rule string.
	 * @return OptionControlField
	 */
	public function set_binding( $binding ) {
		$this->binding = $binding;
		return $this;
	}

	/**
	 * Getter for $default
	 *
	 * @return mixed default value of the field
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Setter for $default
	 *
	 * @param mixed $default default value of the field.
	 * @return OptionControlField
	 */
	public function set_default( $default ) {
		$this->default = $default;
		return $this;
	}

	/**
	 * Get field value
	 *
	 * @return string|array Value of field
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Set field value
	 *
	 * @param string|array $value Value of field.
	 * @return OptionControlField
	 */
	public function set_value( $value ) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Set ajax call
	 *
	 * @param string $ajax_call Ajax call.
	 * @return OptionControlField
	 */
	public function set_ajax_call( $ajax_call ) {
		$this->ajax_call = $ajax_call;
		return $this;
	}


	/**
	 * Get ajax call
	 *
	 * @return string
	 */
	public function get_ajax_call() {
		return $this->ajax_call;
	}

	/**
	 * Getter of $field_max_height
	 *
	 * @return int Max height of the field
	 */
	public function get_field_max_height() {
		return $this->field_max_height;
	}

	/**
	 * Setter of $field_max_height
	 *
	 * @param int $field_max_height Max height of the field.
	 * @return OptionControlField
	 */
	public function set_field_max_height( $field_max_height ) {
		$this->field_max_height = $field_max_height;
		return $this;
	}

	/**
	 * Getter of $container_extra_classes
	 *
	 * @return array of Extra Classes for the container
	 */
	public function get_container_extra_classes() {
		return $this->container_extra_classes;
	}

	/**
	 * Setter of $container_extra_classes
	 *
	 * @param array $container_extra_classes Extra Classes for the container.
	 * @return OptionControlField
	 */
	public function set_container_extra_classes( $container_extra_classes ) {
		$this->container_extra_classes = $container_extra_classes;
		return $this;
	}

	/**
	 * Add container extra classes
	 *
	 * @param array|string $class Extra classes.
	 *
	 * @return array
	 */
	public function add_container_extra_classes( $class ) {
		if ( is_array( $class ) ) {
			$this->container_extra_classes = array_merge( $this->container_extra_classes, $class );
		} elseif ( ! in_array( $class, $this->container_extra_classes ) ) {
			$this->container_extra_classes[] = $class;
		}
		return $this->container_extra_classes;
	}


	/**
	 * Get is_hidden status, will set the status if a boolean passed
	 *
	 * @param null|boolean $is_hidden Is hidden.
	 * @return boolean
	 */
	public function is_hidden( $is_hidden = null ) {
		if ( ! is_null( $is_hidden ) ) {
			$this->is_hidden = (bool) $is_hidden;
		}
		return $this->is_hidden;
	}

}
