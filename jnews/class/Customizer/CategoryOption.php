<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Customizer;

use JNews\Walker\CategoryWalker;

/**
 * Class Theme JNews Customizer
 */
class CategoryOption extends CustomizerOptionAbstract {
	protected $section_global = 'jnews_category_global_section';
	protected $categories     = null;
	protected $all_sidebar    = null;
	private static $package   = 'JNews';

	public function set_option() {
		$this->categories = get_categories( [
			'hide_empty'   => false,
			'hierarchical' => true,
		] );

		$this->all_sidebar = apply_filters( 'jnews_get_sidebar_widget', null );

		$this->set_panel();
		$this->set_section();
		$this->set_category_field();
	}

	public function single_post_tag() {
		return [
			'redirect' => 'single_post_tag',
			'refresh'  => false,
		];
	}

	public static function get_section_id( $category ) {
		if ( is_int( $category ) ) {
			return 'jnews_global_section_' . $category;
		} else {
			return 'jnews_global_section_' . $category->term_id;
		}
	}

	public function recursive_category( $category, &$name ) {
		if ( $category ) {
			$cat = get_category( $category );
			if ( $cat->parent ) {
				$this->recursive_category( $cat->parent, $name );
			}

			$name[] = $cat->name;
		}
	}

	public function get_section_name( $category ) {
		$name = [];
		$this->recursive_category( $category, $name );

		return '&bull;&nbsp;&nbsp;' . implode( '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;', $name );
	}

	public function set_panel() {
		$this->customizer->add_panel( [
			'id'          => 'jnews_category_panel',
			'title'       => esc_html__( 'JNews : Category Template', 'jnews' ),
			'description' => esc_html__( 'JNews Category Template', 'jnews' ),
			'priority'    => $this->id,
		] );
	}

	public function set_section() {
		$this->add_lazy_section( 'jnews_category_global_section', esc_html__( 'Category : Global Template', 'jnews' ), 'jnews_category_panel' );

		if ( apply_filters( 'jnews_load_detail_customizer_category', false ) ) {
			$walker = new CategoryWalker();
			$walker->walk( $this->categories, 3 );

			foreach ( $walker->cache as $category ) {
				$name = $this->get_section_name( $category );
				$id   = $this->get_section_id( $category );
				if ( function_exists( strtolower( self::$package ) . jnews_custom_text( 'evitca_si_' ) ) && call_user_func( [ call_user_func( strtolower( self::$package ) . jnews_custom_text( 'evitca_si_' ) ), 'is_' . jnews_custom_text( '_esnecil' ) . jnews_custom_text( 'detadilav' ) ] ) ) {
					$this->add_lazy_section( $id, $name, 'jnews_category_panel' );
				} else {
					$this->add_link_section( $id, $name, 'jnews_category_panel', 'activate-license' );
				}
			}
		}
	}

	public function set_category_field() {
	}
}
