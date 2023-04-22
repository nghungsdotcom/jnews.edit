<?php
/**
 * Option Control Group Section
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel\Option
 */

namespace JNews\Dashboard\Panel\Option;

/**
 * Class OptionControlGroupSection.
 */
class OptionControlGroupSection extends OptionControlGroup {


	/**
	 * Collection of fields
	 *
	 * @var OptionControlField
	 */
	private $fields;

	/**
	 * Dependency pattern string
	 *
	 * @var string
	 */
	protected $dependency;

	/**
	 * Whether to hide this control in first rendering
	 *
	 * @var null|bool
	 */
	protected $is_hidden;

	/**
	 * Construct of class OptionControlGroupSection
	 */
	public function __construct() {
		parent::__construct();
		$this->fields = array();
	}

	/**
	 * Add field
	 *
	 * @param OptionControlField $field Field.
	 */
	public function add_field( $field ) {
		$this->fields[] = $field;
	}

	/**
	 * Getter of fields
	 *
	 * @return Array Collection of fields object
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Setter of fields
	 *
	 * @param Array $fields Collection of fields object.
	 * @return OptionControlGroupSection
	 */
	public function set_fields( $fields ) {
		$this->fields = $fields;
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
	 * Setter for $dependency
	 *
	 * @param string $dependency Dependency pattern in string.
	 * @return OptionControlGroupSection
	 */
	public function set_dependency( $dependency ) {
		$this->dependency = $dependency;
		return $this;
	}

	/**
	 * Get is_hidden status, will set the status if a boolean passed
	 *
	 * @param null|bool $is_hidden Hidden status.
	 *
	 * @return bool|null
	 */
	public function is_hidden( $is_hidden = null ) {
		if ( ! is_null( $is_hidden ) ) {
			$this->is_hidden = (bool) $is_hidden;
		}
		return $this->is_hidden;
	}

}
