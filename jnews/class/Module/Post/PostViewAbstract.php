<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Post;

use JNews\Module\ModuleViewAbstract;

abstract class PostViewAbstract extends ModuleViewAbstract {

	public function is_on_editor() {
		if ( function_exists( 'jeg_is_frontend_vc' ) && jeg_is_frontend_vc() ) {
			return true;
		}

		if ( isset( $_REQUEST['action'] ) ) {
			if ( ( $_REQUEST['action'] === 'elementor' || $_REQUEST['action'] === 'elementor_ajax' ) ) {
				return true;
			}
		}

		return false;
	}

	public function render_module( $attr, $column_class ) {
		add_filter( 'jnews_post_meta_element_render_meta', array( $this, 'render_module_front_handler' ), 10, 3 );
		add_filter( 'jnews_post_meta_element_render_meta_back', array( $this, 'render_module_back_handler' ), 10, 4 );
		if ( $this->is_on_editor() ) {
			return $this->render_module_back( $attr, $column_class );
		} else {
			return $this->render_module_front( $attr, $column_class );
		}
	}

	/**
	 * Handle render module front
	 *
	 * @param string $callback
	 * @param class  $class
	 * @param string $func
	 *
	 * @return string
	 */
	public function render_module_front_handler( $data, $class, $func ) {
		if ( method_exists( $class, $func ) ) {
			return $class->$func();
		}
		return $data;
	}

	/**
	 * Handle render module back
	 *
	 * @param string $data
	 * @param class  $class
	 * @param string $func
	 * @param array  $attr
	 *
	 * @return string
	 */
	public function render_module_back_handler( $data, $class, $func, $attr ) {
		if ( method_exists( $class, $func ) ) {
			return $class->$func( $attr );
		}
		return $data;
	}

	abstract public function render_module_back( $attr, $column_class);
	abstract public function render_module_front( $attr, $column_class);
}
