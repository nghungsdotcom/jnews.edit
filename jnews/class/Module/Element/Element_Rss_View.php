<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Module\Element;

use JNews\Feed;
use JNews\Module\ModuleViewAbstract;

Class Element_Rss_View extends ModuleViewAbstract {

	public function render_module_back( $attr, $column_class ) {
		return $this->build_block_module( $attr );
	}

	public function render_module_front( $attr, $column_class ) {
		return $this->build_block_module( $attr );
	}

	public function build_block_module( $attr ) {
		$name     = apply_filters( 'jnews_get_content_layout', 'JNews_Block_' . $attr['block_type'], null );
		$name     = jnews_get_view_class_from_shortcode( $name );
		$instance = jnews_get_module_instance( $name );
		$feed     = fetch_feed( $attr['feed_url'] );

        if ( ! is_wp_error( $feed ) && $posts = $feed->get_items( 0, $attr['number_post'] ) ) {
            $result = [
                'result' => [],
            ];
            
            foreach ($posts as $post) {
                $result['result'][] = new Feed( $post, $attr );
            }

            $result['next'] = false;
            $result['prev'] = false;
            $result['total_page'] = 1;

            $attr['pagination_mode'] = 'disable';
            $attr['results']         = $result;
            return $instance->build_module( $attr );
        }

	}

    public function render_module( $attr, $column_class, $result = null ) {

		if ( defined( 'JNEWS_ESSENTIAL' ) ) {
			if ( version_compare( JNEWS_ESSENTIAL_VERSION, '9.0.0', '<' ) ) {
				return '<div class="alert alert-error">
                            <strong>' . esc_html__( 'Plugin Install', 'jnews' ) . '</strong>' . ' : ' . esc_html__( 'RSS Block Elements need JNews - Essential V9.0.0 to be installed', 'jnews' ) .
						'</div>';
			}
			if ( $this->is_on_editor() ) {
				return $this->render_module_back( $attr, $column_class );
			} else {
				return $this->render_module_front( $attr, $column_class );
			}
		}
	}

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

    public function rss_excerpt() {
	    return '';
    }
}
