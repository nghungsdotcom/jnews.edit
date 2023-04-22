<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Customizer;

require_once ABSPATH . 'wp-includes/class-wp-customize-setting.php';

final class CustomizeSetting extends \WP_Customize_Setting {
	/**
	 * Import an option value for this setting.
	 *
	 * @param mixed $value The option value.
	 *
	 * @return void
	 * @since 0.3
	 */
	public function import( $value ) {
		$this->update( $value );
	}
}