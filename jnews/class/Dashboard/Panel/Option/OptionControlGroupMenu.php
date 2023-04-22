<?php
/**
 * Option Control Group Menu
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel\Option
 */

namespace JNews\Dashboard\Panel\Option;

/**
 * Class OptionControlGroupMenu.
 */
class OptionControlGroupMenu extends OptionControlGroup {


	/**
	 * Collection of $menu
	 *
	 * @var OptionControlGroup
	 */
	private $menus;

	/**
	 * Collection of controls
	 *
	 * @var OptionControlField
	 */
	private $controls;

	/**
	 * Icon
	 *
	 * @var string
	 */
	private $icon;

	/**
	 * Construct of class OptionControlGroupMenu
	 */
	public function __construct() {
		parent::__construct();
		$this->menus    = array();
		$this->controls = array();
	}

	/**
	 * Add menu
	 *
	 * @param OptionControlGroupMenu $menu Group menu.
	 */
	public function add_menu( $menu ) {
		$this->menus[] = $menu;
	}

	/**
	 * Getter of $menus
	 *
	 * @return array
	 */
	public function get_menus() {
		return $this->menus;
	}

	/**
	 * Setter of $menus
	 *
	 * @param array $menus Collection of menus object.
	 * @return OptionControlGroupMenu
	 */
	public function set_menus( $menus ) {
		$this->menus = $menus;
		return $this;
	}

	/**
	 * Add control
	 *
	 * @param OptionControlGroupSection|OptionControlField $control Control.
	 */
	public function add_control( $control ) {
		$this->controls[] = $control;
	}

	/**
	 * Getter of controls
	 *
	 * @return array Collection of controls object
	 */
	public function get_controls() {
		return $this->controls;
	}

	/**
	 * Get menu icon
	 *
	 * @return string Icon URL
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Set menu icon
	 *
	 * @param string $icon Icon URL.
	 * @return OptionControlGroupMenu
	 */
	public function set_icon( $icon ) {
		$this->icon = $icon;
		return $this;
	}

}
