<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Category;

/**
 * Class Theme Category
 */
class Category extends CategoryAbstract {

	public function get_content_width() {
		$width = parent::get_content_width();

		if ( in_array( $this->get_page_layout(), array( 'right-sidebar', 'left-sidebar' ) ) ) {
			$sidebar = $this->get_content_sidebar();
			if ( ! is_active_sidebar( $sidebar ) ) {
				return 12;
			}
		}

		return $width;
	}

	public function is_overwritten() {
		$term = $this->term->term_id;
		if ( function_exists( 'pll_get_term' ) ) {
			$term = pll_get_term( $this->term->term_id, pll_default_language() );
		}
		return apply_filters( 'jnews_category_override', false, $term );
	}

	// Header Breadcrumb Type
	public function get_header_type() {
		$option = get_theme_mod( 'jnews_category_header', '1' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_header_' . $term, '1' );
		}

		return apply_filters( 'jnews_category_header', $option, $this->term->term_id );
	}

	public function get_header_background() {
		$option = get_theme_mod( 'jnews_category_header_bg_color', '#f5f5f5' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_header_bg_color_' . $term, '#f5f5f5' );
		}

		return apply_filters( 'jnews_category_header_bg_color', $option, $this->term->term_id );
	}

	public function get_header_style() {
		$option = get_theme_mod( 'jnews_category_header_style', 'dark' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_header_style_' . $term, 'dark' );
		}

		return apply_filters( 'jnews_category_header_style', $option, $this->term->term_id );
	}

	public function get_header_image() {
		$option = get_theme_mod( 'jnews_category_header_bg_image', '' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_header_bg_image_' . $term, '' );
		}

		$link = apply_filters( 'jnews_category_header_bg_image', $option, $this->term->term_id );

		/* For JNews - Extended Category Option Plugin */
		if ( is_numeric( $link ) ) {
			$link = wp_get_attachment_image_src( $link, 'full' );
			return $link[0];
		}

		return $link;
	}

	// Hero Header
	public function show_hero() {
		$option = get_theme_mod( 'jnews_category_hero_show', true );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_hero_show_' . $term, true );
		}

		return apply_filters( 'jnews_category_hero_show', $option, $this->term->term_id );
	}

	public function get_hero_type() {
		$option = get_theme_mod( 'jnews_category_hero', '1' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_hero_' . $term, '1' );
		}

		return apply_filters( 'jnews_category_hero', $option, $this->term->term_id );
	}

	public function get_hero_style() {
		$option = get_theme_mod( 'jnews_category_hero_style', 'jeg_hero_style_1' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_hero_style_' . $term, 'jeg_hero_style_1' );
		}

		return apply_filters( 'jnews_category_hero_style', $option, $this->term->term_id );
	}

	public function get_hero_margin() {
		$option = get_theme_mod( 'jnews_category_hero_margin', 10 );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_hero_margin_' . $term, 10 );
		}

		return apply_filters( 'jnews_category_hero_margin', $option, $this->term->term_id );
	}

	public function get_hero_date() {
		$option = get_theme_mod( 'jnews_category_hero_date', 'default' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_hero_date_' . $term, 'default' );
		}

		return apply_filters( 'jnews_category_hero_date', $option, $this->term->term_id );
	}

	public function get_hero_date_custom() {
		$option = get_theme_mod( 'jnews_category_hero_date_custom', 'Y/m/d' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_hero_date_custom_' . $term, 'Y/m/d' );
		}

		return apply_filters( 'jnews_category_hero_date_custom', $option, $this->term->term_id );
	}

	// content
	public function get_content_type() {
		$option = get_theme_mod( 'jnews_category_content', '5' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_' . $term, '5' );
		}

		return apply_filters( 'jnews_category_content', $option, $this->term->term_id );
	}

	public function get_content_excerpt() {
		$option = get_theme_mod( 'jnews_category_content_excerpt', 20 );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_excerpt_' . $term, 20 );
		}

		return apply_filters( 'jnews_category_content_excerpt', $option, $this->term->term_id );
	}

	public function get_content_date() {
		$option = get_theme_mod( 'jnews_category_content_date', 'default' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_date_' . $term, 'default' );
		}

		return apply_filters( 'jnews_category_content_date', $option, $this->term->term_id );
	}

	public function get_content_date_custom() {
		$option = get_theme_mod( 'jnews_category_content_date_custom', 'Y/m/d' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_date_custom_' . $term, 'Y/m/d' );
		}

		return apply_filters( 'jnews_category_content_date_custom', $option, $this->term->term_id );
	}

	public function get_content_pagination() {
		$option = get_theme_mod( 'jnews_category_content_pagination', 'nav_1' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_pagination_' . $term, 'nav_1' );
		}

		return apply_filters( 'jnews_category_content_pagination', $option, $this->term->term_id );
	}

	public function get_content_pagination_limit() {
		$option = get_theme_mod( 'jnews_category_content_pagination_limit', 0 );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_pagination_limit_' . $term, 0 );
		}

		return apply_filters( 'jnews_category_content_pagination_limit', $option, $this->term->term_id );
	}

	public function get_content_pagination_align() {
		$option = get_theme_mod( 'jnews_category_content_pagination_align', 'center' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_pagination_align_' . $term, 'center' );
		}

		return apply_filters( 'jnews_category_content_pagination_align', $option, $this->term->term_id );
	}

	public function get_content_pagination_navtext() {
		$option = get_theme_mod( 'jnews_category_content_pagination_show_navtext', false );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_pagination_show_navtext_' . $term, false );
		}

		return apply_filters( 'jnews_category_content_pagination_show_navtext', $option, $this->term->term_id );
	}

	public function get_content_pagination_pageinfo() {
		$option = get_theme_mod( 'jnews_category_content_pagination_show_pageinfo', false );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_content_pagination_show_pageinfo_' . $term, false );
		}

		return apply_filters( 'jnews_category_content_pagination_show_pageinfo', $option, $this->term->term_id );
	}

	public function get_page_layout() {
		$option = get_theme_mod( 'jnews_category_page_layout', 'right-sidebar' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_page_layout_' . $term, true );
		}

		return apply_filters( 'jnews_category_page_layout', $option, $this->term->term_id );
	}

	public function get_content_sidebar() {
		$option = get_theme_mod( 'jnews_category_sidebar', 'default-sidebar' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_sidebar_' . $term, 'default-sidebar' );
		}

		return apply_filters( 'jnews_category_sidebar', $option, $this->term->term_id );
	}

	public function get_second_sidebar() {
		$option = get_theme_mod( 'jnews_category_second_sidebar', 'default-sidebar' );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_second_sidebar_' . $term, 'default-sidebar' );
		}

		return apply_filters( 'jnews_category_second_sidebar', $option, $this->term->term_id );
	}

	public function sticky_sidebar() {
		$option = get_theme_mod( 'jnews_category_sticky_sidebar', true );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_sticky_sidebar_' . $term, true );
		}

		return apply_filters( 'jnews_category_sticky_sidebar', $option, $this->term->term_id );
	}

	public function get_boxed() {
		if ( ! in_array( $this->get_content_type(), array( '3', '4', '5', '6', '7', '9', '10', '14', '18', '22', '23', '25', '26', '27', '39' ) ) ) {
			return false;
		}

		$option = get_theme_mod( 'jnews_category_boxed', false );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_boxed_' . $term, false );
		}

		return apply_filters( 'jnews_category_boxed', $option, $this->term->term_id );
	}

	public function get_boxed_shadow() {
		if ( ! $this->get_boxed() ) {
			return false;
		}

		$option = get_theme_mod( 'jnews_category_boxed_shadow', false );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_boxed_shadow_' . $term, false );
		}

		return apply_filters( 'jnews_category_boxed_shadow', $option, $this->term->term_id );
	}

	public function get_box_shadow() {
		if ( ! in_array( $this->get_content_type(), array( '37', '35', '33', '36', '32', '38' ) ) ) {
			return false;
		}

		$option = get_theme_mod( 'jnews_category_box_shadow', false );

		if ( $this->is_overwritten() ) {
			$term = $this->term->term_id;
			if ( function_exists( 'pll_get_term' ) ) {
				$term = pll_get_term( $this->term->term_id, pll_default_language() );
			}
			$option = get_theme_mod( 'jnews_category_box_shadow_' . $term, false );
		}

		return apply_filters( 'jnews_category_box_shadow', $option, $this->term->term_id );
	}
}
