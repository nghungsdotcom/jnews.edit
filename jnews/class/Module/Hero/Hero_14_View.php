<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Hero;

class Hero_14_View extends HeroViewAbstract {

	public function render_block_type_1( $post ) {
		if ( $post ) {
			$post_id   = $post->ID;
			$permalink = get_the_permalink( $post );

			return '<article ' . jnews_post_class( 'jeg_post jeg_pl_lg_7', $post_id ) . '>
                        <div class="jeg_thumb">
                            ' . jnews_edit_post( $post_id ) . "
                            <a href=\"{$permalink}\">" . apply_filters( 'jnews_image_thumbnail', $post_id, 'jnews-750x536' ) . "</a>
                            <div class=\"jeg_post_category\">
                                {$this->get_primary_category($post_id)}
                            </div>
                        </div>
                        <div class=\"jeg_postblock_content\">
                            <h2 class=\"jeg_post_title\">
                                <a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
                            </h2>
                            <div class=\"jeg_post_meta\">
                                {$this->post_meta_3($post)}
                            </div>
                            <div class=\"jeg_post_excerpt\">
                                <p>" . $this->get_excerpt( $post ) . "</p>
                                <a href=\"{$permalink}\" class=\"jeg_readmore\">" . jnews_return_translation( 'Read more', 'jnews', 'read_more' ) . '</a>
                            </div>
                        </div>
                    </article>';
		}
		return '<article class="jeg_post jeg_pl_md_box jeg_hero_empty">
                    <div class="jeg_block_container"></div>
                </article>';
	}

	public function render_block_type_2( $post ) {
		if ( $post ) {
			$post_id = $post->ID;

			return '<article ' . jnews_post_class( 'jeg_post jeg_pl_sm_2', $post_id ) . ">
                        <div class=\"jeg_postblock_content\">
                            <div class=\"jeg_post_category\">
                                {$this->get_primary_category($post_id)}
                            </div>
                            <h3 class=\"jeg_post_title\">
                                <a href=\"" . get_the_permalink( $post ) . '">' . get_the_title( $post ) . "</a>
                            </h3>
                            {$this->post_meta_2($post)}
                        </div>
                    </article>";
		}
		return '<article class="jeg_post jeg_pl_md_box jeg_hero_empty">
                    <div class="jeg_block_container"></div>
                </article>';
	}

	public function render_block_type_3( $post ) {
		if ( $post ) {
			$post_id   = $post->ID;
			$permalink = get_the_permalink( $post );

			return '<article ' . jnews_post_class( 'jeg_post jeg_pl_md_box', $post_id ) . '>
                        <div class="box_wrap">
                            <div class="jeg_thumb">
                                ' . jnews_edit_post( $post_id ) . "
                                <a href=\"{$permalink}\">" . apply_filters( 'jnews_image_thumbnail', $post_id, 'jnews-350x250' ) . "</a>
                            </div>
                            <div class=\"jeg_postblock_content\">
                                <h3 class=\"jeg_post_title\">
                                    <a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
                                </h3>
                                {$this->post_meta_2($post)}
                            </div>
                        </div>
                    </article>";
		}
		return '<article class="jeg_post jeg_pl_md_box jeg_hero_empty">
                    <div class="jeg_block_container"></div>
                </article>';
	}

	public function render_element( $result ) {
		$first_block = $second_block = $third_block = '';
		$number_post = $this->get_number_post() - 1;

		for ( $i = 0; $i <= $number_post; $i++ ) {
			$item = isset( $result[ $i ] ) ? $result[ $i ] : '';
			if ( $i < 1 ) {
				$first_block .= $this->render_block_type_1( $item );
			} elseif ( $i < 5 ) {
				$second_block .= $this->render_block_type_2( $item );
			} else {
				$third_block .= $this->render_block_type_3( $item );
			}
		}

		return "<div class=\"jeg_postbig\">
                    {$first_block}
                </div>
                <div class=\"jeg_postsmall left\">
                    {$second_block}
                </div>
                <div class=\"jeg_postsmall right\">
                    {$third_block}
                </div>";
	}

	public function render_output( $result, $attr, $column_class ) {
		return "<div {$this->element_id($attr)} class=\"jeg_heropost jeg_heropost_14 jeg_heropost_1 jeg_postblock {$column_class} {$this->unique_id} {$this->get_vc_class_name()} {$this->color_scheme()} {$attr['el_class']}\">
                    {$this->render_content($result)}
                </div>";
	}

	public function get_primary_category( $post_id ) {
		$cat_id       = jnews_get_primary_category( $post_id );
		$inline_style = $category = $css = '';

		if ( $cat_id ) {
			$override_color = get_theme_mod( 'jnews_category_override_color_' . $cat_id );
			$category_color = get_theme_mod( 'jnews_category_color_' . $cat_id );

			$category = get_category( $cat_id );
			$class    = '';

			if ( isset( $category->slug ) && $category->slug ) {
				$class = 'class="category-' . $category->slug . '"';
			}

			if ( $override_color ) {
				$css .= empty( $category_color ) ? '' : 'color: ' . $category_color . ';';

				$inline_style = empty( $css ) ? '' : 'style="' . $css . '"';
			}

			$category = '<a href="' . get_category_link( $cat_id ) . "\" {$inline_style} {$class}>" . $category->name . '</a>';
		}

		return $category;
	}

	public function set_hero_option() {
		$this->options['date_format']        = 'default';
		$this->options['date_format_custom'] = 'Y/m/d';
	}
}
