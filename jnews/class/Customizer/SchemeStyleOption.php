<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Customizer;

/**
 * Class Theme JNews Customizer
 */
class SchemeStyleOption extends CustomizerOptionAbstract {
	public function set_option() {
		$this->set_section();
	}

	public function set_section() {
		$this->add_lazy_section( 'jnews_scheme_style_section', esc_html__( 'JNews : Scheme Style', 'jnews' ), null );
	}
}
