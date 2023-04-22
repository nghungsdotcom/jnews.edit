<?php
/**
 * Option Control Group
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel\Option
 */

namespace JNews\Dashboard\Panel\Option;

/**
 * Abstract class OptionControlGroup.
 */
abstract class OptionControlGroup {



	/**
	 * Name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Title
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Data
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
	 * Construct of class OptionControlGroup
	 */
	public function __construct() {
		$this->container_extra_classes = array();
	}

	/**
	 * Getter of $name
	 *
	 * @return string Group unique name
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Setter of $name
	 *
	 * @param string $name Group unique name.
	 * @return OptionControlGroup|OptionControlGroupMenu|OptionControlGroupSection
	 */
	public function set_name( $name ) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Getter of $type
	 *
	 * @return string Group type
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Setter of $type
	 *
	 * @param string $type Group type.
	 * @return OptionControlGroup|OptionControlGroupMenu|OptionControlGroupSection
	 */
	public function set_type( $type ) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Getter of title
	 *
	 * @return string Group title
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Setter of title
	 *
	 * @param string $title Group title.
	 * @return OptionControlGroup|OptionControlGroupMenu|OptionControlGroupSection
	 */
	public function set_title( $title ) {
		$this->title = $title;
		return $this;
	}


	/**
	 * Getter of $description
	 *
	 * @return string Group description
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Setter of $description
	 *
	 * @param string $description Group description.
	 * @return OptionControlGroup|OptionControlGroupMenu|OptionControlGroupSection
	 */
	public function set_description( $description ) {
		$this->description = $description;
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
	 * @return OptionControlGroup|OptionControlGroupMenu|OptionControlGroupSection
	 */
	public function set_data( $data ) {
		$this->data = $data;
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
	 * @return OptionControlGroup|OptionControlGroupMenu|OptionControlGroupSection
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
		} elseif ( ! in_array( $class, $this->container_extra_classes, true ) ) {
			$this->container_extra_classes[] = $class;
		}
		return $this->container_extra_classes;
	}

}
