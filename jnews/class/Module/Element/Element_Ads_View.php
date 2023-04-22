<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Element;

use JNews\Module\ModuleViewAbstract;

class Element_Ads_View extends ModuleViewAbstract {

	public function render_module( $attr, $column_class ) {
		if ( apply_filters( 'jnews_ads_global_enable', true, get_the_ID(), 'element_ads' ) ) {
			return "<div {$this->element_id($attr)} class='jeg_ad jeg_ad_module {$this->unique_id} {$this->additional_class($attr)} {$this->get_vc_class_name()} {$attr['el_class']}'>" . $this->build_module_ads( $attr ) . '</div>';
		}

		return '';
	}

	protected function additional_class( $attr ) {
		$class = array();

		if ( $attr['google_desktop'] === 'hide' ) {
			$class[] = 'jeg_ads_hide_desktop';
		}

		if ( $attr['google_tab'] === 'hide' ) {
			$class[] = 'jeg_ads_hide_tab';
		}

		if ( $attr['google_phone'] === 'hide' ) {
			$class[] = 'jeg_ads_hide_phone';
		}

		return implode( ' ', $class );
	}
}
