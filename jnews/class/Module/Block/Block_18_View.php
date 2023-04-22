<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Module\Block;

use JNews\Image\ImageNormalLoad;

class Block_18_View extends BlockViewAbstract {

	protected $attribute;

	public function get_like( $post ) {
		ob_start();
		do_action( 'jnews_render_meta_like', $post->ID );

		return ob_get_clean();
	}

	public function get_thumbnail( $post_id, $size ) {
		return isset( $this->attribute['force_normal_image_load'] ) && ( 'true' === $this->attribute['force_normal_image_load'] || 'yes' === $this->attribute['force_normal_image_load'] ) ?
				ImageNormalLoad::getInstance()->image_thumbnail_unwrap( $post_id, $size ) :
				apply_filters( 'jnews_image_thumbnail_unwrap', $post_id, $size );
	}

	public function render_block_type_1( $post, $image_size ) {
		$post_id   = $post->ID;
		$permalink = get_the_permalink( $post );

		return '<article ' . jnews_post_class( 'jeg_post jeg_pl_lg_8', $post_id ) . ">
					<div class=\"jeg_postblock_heading\">
						<h3 class=\"jeg_post_title\">
							<a href=\"{$permalink}\">" . get_the_title( $post ) . '</a>
						</h3>
					</div>
					<div class="jeg_postblock_content">
						<div class="jeg_thumb">
							' . jnews_edit_post( $post_id ) . "
							<a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size )}</a>
						</div>
						" . $this->post_meta_1( $post ) . "
						<div class=\"jeg_share_button clearfix\">
							<div class='jeg_reaction jeg_meta_like'>
								{$this->get_like( $post )}
							</div>
							" . apply_filters( 'jnews_share_block_output', '', $post_id ) . '
						</div>
					</div>
				</article>';
	}

	public function build_column( $results, $column_class, $is_ajax ) {
		$first_block  = '';
		$size         = sizeof( $results );
		$ads_position = $this->random_ads_position( $size );

		$image_size = $column_class === 'jeg_col_1o3' ? 'jnews-350x250' : 'jnews-featured-750';

		for ( $i = 0; $i < $size; $i ++ ) {
			if ( $i == $ads_position ) {
				$first_block .= $this->render_module_ads();
			}

			$first_block .= $is_ajax ? $this->render_block_type_1( $results[ $i ], $image_size ) : $this->render_block_type_1( $results[ $i ], $image_size );
		}

		return $first_block;
	}

	public function render_output( $attr, $column_class ) {
		$results         = isset( $attr['results'] ) ? $attr['results'] : $this->build_query( $attr );
		$navigation      = $this->render_navigation( $attr, $results['next'], $results['prev'], $results['total_page'] );
		$this->attribute = $attr;
		$content         = ! empty( $results['result'] ) ? $this->render_column( $results['result'], $column_class ) : $this->empty_content();

		return "<div class=\"jeg_block_container\">
					{$this->get_content_before($attr)}
					{$content}
					{$this->get_content_after($attr)}
				</div>
				<div class=\"jeg_block_navigation\">
					{$this->get_navigation_before($attr)}
					{$navigation}
					{$this->get_navigation_after($attr)}
				</div>";
	}

	public function render_column( $result, $column_class ) {
		return "<div class=\"jeg_posts jeg_load_more_flag\">{$this->build_column($result, $column_class, false)}</div>";
	}

	public function render_column_alt( $result, $column_class ) {
		return $this->build_column( $result, $column_class, true );
	}
}
