<?php
/**
 * Option Parser
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel\Option
 */

namespace JNews\Dashboard\Panel\Option;

/**
 * Class OptionParser
 */
class OptionParser {


	/**
	 * Parse array options
	 *
	 * @param array   $arr Options.
	 * @param boolean $auto_group_naming Group naming.
	 *
	 * @return OptionControlSet
	 */
	public function parse_array_options( $arr, $auto_group_naming ) {
		$set = new OptionControlSet();

		if ( empty( $arr['title'] ) ) {
			$arr['title'] = 'Vafpress';
		}
		if ( empty( $arr['logo'] ) ) {
			$arr['logo'] = 'jnews-panel-logo.png';
		}

		$set->set_title( isset( $arr['title'] ) ? $arr['title'] : '' )->set_logo( isset( $arr['logo'] ) ? $arr['logo'] : '' )->set_version( isset( $arr['version'] ) ? $arr['version'] : '' );

		$auto_menu_index = 0;
		$auto_menu       = 'the_menu_';

		// Loops trough all the menus.
		if ( ! empty( $arr['menus'] ) ) {
			foreach ( $arr['menus'] as $menu ) {
				// Create menu object and add to set.
				$jnews_panel_menu = new OptionControlGroupMenu();

				if ( $auto_group_naming ) {
					if ( isset( $menu['name'] ) && ! empty( $menu['name'] ) ) {
						$jnews_panel_menu->set_name( $menu['name'] );
					} else {
						$jnews_panel_menu->set_name( $auto_menu . $auto_menu_index );
						$auto_menu_index++;
					}
				}

				$jnews_panel_menu->set_title( isset( $menu['title'] ) ? $menu['title'] : '' )->set_icon( isset( $menu['icon'] ) ? $menu['icon'] : '' );

				$set->add_menu( $jnews_panel_menu );

				// Loops through every submenu in each menu.
				if ( ! empty( $menu['menus'] ) && is_array( $menu['menus'] ) ) {
					foreach ( $menu['menus'] as $submenu ) {
						$jnews_panel_submenu = new OptionControlGroupMenu();

						if ( $auto_group_naming ) {
							if ( isset( $submenu['name'] ) && ! empty( $submenu['name'] ) ) {
								$jnews_panel_submenu->set_name( $submenu['name'] );
							} else {
								$jnews_panel_submenu->set_name( $auto_menu . $auto_menu_index );
								$auto_menu_index++;
							}
						}

						$jnews_panel_submenu->set_title( isset( $submenu['title'] ) ? $submenu['title'] : '' )->set_icon( isset( $submenu['icon'] ) ? $submenu['icon'] : '' );

						$jnews_panel_menu->add_menu( $jnews_panel_submenu );

						// Loops through every control in each submenu.
						if ( ! empty( $submenu['controls'] ) ) {
							foreach ( $submenu['controls'] as $control ) {
								if ( 'section' === $control['type'] ) {
									$control = $this->parse_section( $control );
								} else {
									$control = $this->parse_field( $control );
								}
								$jnews_panel_submenu->add_control( $control );
							}
						}
					}
				} else {
					// Loops through every control in each submenu.
					if ( ! empty( $menu['controls'] ) && is_array( $menu['controls'] ) ) {
						foreach ( $menu['controls'] as $control ) {
							if ( 'section' === $control['type'] ) {
								$control = $this->parse_section( $control );
							} else {
								$control = $this->parse_field( $control );
							}
							$jnews_panel_menu->add_control( $control );
						}
					}
				}
			}
		}

		return $set;
	}

	/**
	 * Parse Section
	 *
	 * @param array $section Control section config.
	 *
	 * @return OptionControlGroupSection
	 */
	private function parse_section( $section ) {
		$jnews_panel_sec = new OptionControlGroupSection();
		$jnews_panel_sec->set_name( isset( $section['name'] ) ? $section['name'] : '' )->set_title( isset( $section['title'] ) ? $section['title'] : '' )->set_description( isset( $section['description'] ) ? $section['description'] : '' )->set_type( isset( $section['type'] ) ? $section['type'] : 'section' );

		if ( isset( $section['dependency'] ) ) {
			$func  = $section['dependency']['function'];
			$field = $section['dependency']['field'];
			$jnews_panel_sec->set_dependency( $func . '|' . $field );
		}

		// Loops through every field in each submenu.
		if ( ! empty( $section['fields'] ) ) {
			foreach ( $section['fields'] as $field ) {
				$jnews_panel_field = $this->parse_field( $field );
				$jnews_panel_sec->add_field( $jnews_panel_field );
			}
		}
		return $jnews_panel_sec;
	}

	/**
	 * Parse Field
	 *
	 * @param array $field Field config.
	 *
	 * @return OptionControlField
	 */
	private function parse_field( $field ) {
		$instance = new OptionControlField();
		$instance->basic_make( $field );
		return $instance;
	}

}
