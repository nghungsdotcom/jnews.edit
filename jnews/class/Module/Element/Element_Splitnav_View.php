<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Element;

use JNews\Module\ModuleViewAbstract;

class Element_Splitnav_View extends ModuleViewAbstract {

	public function render_module( $attr, $column_class ) {
		if ( defined( 'JNEWS_SPLIT_URL' ) ) {
			$dependencies_style = array();
			if ( SCRIPT_DEBUG || get_theme_mod( 'jnews_load_necessary_asset', false ) ) {
				$dependencies_style[] = 'jnews-global-slider';
			}
			wp_enqueue_style( 'jnews-split', JNEWS_SPLIT_URL . '/assets/css/splitpost.css', $dependencies_style, null, false );
		}

		$mega_nav = '';
		$menus    = is_array( $attr['menu'] ) ? $attr['menu'] : explode( ',', $attr['menu'] );

		foreach ( $menus as $menu ) {
			$menu_content = wp_nav_menu(
				array(
					'menu'      => $menu,
					'container' => 'ul',
					'depth'     => 3,
					'echo'      => false,
				)
			);
			$mega_nav    .= '<div class="jeg_meganav">' . $menu_content . '</div>';
		}

		$output =
			"<div {$this->element_id($attr)} class=\"jeg_meganav_bar jeg_splitpost_bar jeg_splitpost_3 {$this->color_scheme()} {$attr['el_class']}\">
                <div class=\"nav_wrap\">
                    <h3 class=\"current_title\">" . get_the_title( get_the_ID() ) . "</h3>

                    <div class=\"jeg_meganav_wrap\">
                        {$mega_nav}
                    </div>
                </div>
            </div>";

		return $output;
	}

}
