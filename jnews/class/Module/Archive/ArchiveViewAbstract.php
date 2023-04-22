<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Module\Archive;

use JNews\Module\ModuleViewAbstract;
use JNews\Module\ModuleQuery;

abstract class ArchiveViewAbstract extends ModuleViewAbstract {
	public $post_per_page;

	protected static $term;

	protected static $index;

	protected static $result = [];

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

	public function render_module( $attr, $column_class, $result = null ) {
		return $this->is_on_editor() ? $this->render_module_back( $attr, $column_class ) : $this->render_module_front( $attr, $column_class );
	}

	public function get_term() {
		return ! self::$term ? get_queried_object() : self::$term;
	}

	public function get_number_post() {

		if ( ! $this->post_per_page ) {

			$this->post_per_page = get_option( 'posts_per_page' );

			if ( is_category() ) {
				if ( get_theme_mod( 'jnews_category_page_layout', 'right-sidebar' ) === 'custom-template' ) {
					$this->post_per_page = (int) get_theme_mod( 'jnews_category_custom_template_number_post', 10 );
				}
			} elseif ( is_author() ) {
				if ( get_theme_mod( 'jnews_author_page_layout', 'right-sidebar' ) === 'custom-template' ) {
					$this->post_per_page = (int) get_theme_mod( 'jnews_author_custom_template_number_post', 10 );
				}
			} elseif ( is_archive() ) {
				if ( get_theme_mod( 'jnews_archive_page_layout', 'right-sidebar' ) === 'custom-template' ) {
					$this->post_per_page = (int) get_theme_mod( 'jnews_archive_custom_template_number_post', 10 );
				}
			}
		}

		return $this->post_per_page;
	}

	protected function do_query( $attr ) {
		if ( ! self::$result ) {

			if ( is_category() ) {
				$term = $this->get_term();

				if ( isset( $term->term_id ) ) {
					$attr['include_category'] = $term->term_id;
					$this->post_per_page      = $this->get_number_post();
				}
			} elseif ( is_tag() ) {
				$term = $this->get_term();

				if ( isset( $term->term_id ) ) {
					$attr['include_tag'] = $term->term_id;
					$this->post_per_page = $this->get_number_post();
				}
			} elseif ( is_author() ) {
				$user = get_userdata( get_query_var( 'author' ) );

				if ( isset( $user->ID ) ) {
					$attr['include_author'] = $user->ID;
					$this->post_per_page    = $this->get_number_post();
				}
			} elseif ( is_date() ) {
				$attr['date_query'] = [ //fix custom archive template date issue (see: #H1Gk3Nfv)
					[
						'year'  => get_query_var( 'year' ) ? get_query_var( 'year' ) : null,
						'month' => get_query_var( 'monthnum' ) ? get_query_var( 'monthnum' ) : null,
						'day'   => get_query_var( 'day' ) ? get_query_var( 'day' ) : null,
					],
				];
				$this->post_per_page = $this->get_number_post();
			}

			$attr['sort_by']                = 'latest';
			$attr['post_type']              = 'post';
			$attr['post_offset']            = 0;
			$attr['number_post']            = $this->post_per_page;
			$attr['pagination_number_post'] = $this->post_per_page;
			$attr['paged']                  = jnews_get_post_current_page();

			$result = ModuleQuery::do_query( $attr );

			if ( isset( $result['result'] ) ) {
				self::$result = $result;
			}
		}

		return self::$result;
	}

	protected function get_result( $attr, $number_post ) {
		$result = $this->do_query( $attr );

		if ( ! empty( $result['result'] ) && is_array( $result['result'] ) ) {

			if ( isset( $number_post['size'] ) ) {
				$number_post = $number_post['size'];
			}

			$result['result'] = $number_post ? array_slice( $result['result'], self::$index, $number_post ) : array_slice( $result['result'], self::$index );

			if ( ! is_admin() ) {
				self::$index += $number_post;
			}
		}

		return $result;
	}

	abstract public function render_module_back( $attr, $column_class );

	abstract public function render_module_front( $attr, $column_class );
}
