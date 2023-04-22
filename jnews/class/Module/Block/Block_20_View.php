<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

class Block_20_View extends BlockViewAbstract {

	public function render_block_type( $post, $image_size, $type = 1 ) {
		$post_id          = $post->ID;
		$additional_class = ( ! has_post_thumbnail( $post_id ) ) ? ' no_thumbnail' : '';
		$permalink        = get_the_permalink( $post );
		$content          = "<div class=\"jeg_postblock_content\">
                                    <h3 class=\"jeg_post_title\">
                                        <a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
                                    </h3>
                                    {$this->post_meta_2($post)}
                                </div>";

		return $type === 1 ?
				'<article ' . jnews_post_class( 'jeg_post jeg_pl_sm' . $additional_class, $post_id ) . '>
                    <div class="jeg_thumb">
                        ' . jnews_edit_post( $post_id ) . "
                        <a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size)}</a>
                    </div>
                    {$content}
                </article>" :
				'<article ' . jnews_post_class( 'jeg_post jeg_pl_xs', $post_id ) . ">
                    {$content}
                </article>";
	}

	public function build_column( $results, $column_class ) {

		$is_column_1o3 = $column_class === 'jeg_col_1o3';
		$first_block   = $this->render_block_type( $results[0], 'jnews-120x86', 1 );
		$start         = $is_column_1o3 ? 1 : 0;
		$limit         = $column_class === 'jeg_col_2o3' ? 2 : 3;

		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = $start; $i < $size; $i++ ) {
			if ( $is_column_1o3 ) {
				$second_block .= $this->render_block_type( $results[ $i ], null, 2 );
			} else {
				$second_block .= $i < $limit ? $this->render_block_type( $results[ $i ], 'jnews-120x86', 1 ) : $this->render_block_type( $results[ $i ], null, 2 );
			}
		}

		$postsmall = "<div class=\"jeg_postsmall jeg_load_more_flag\">
                            {$second_block}
                        </div>";

		return $is_column_1o3 ?
				"<div class=\"jeg_posts\">
                    {$first_block}
                    {$postsmall}
                </div>" : $postsmall;
	}

	public function build_column_alt( $results ) {
		$first_block = '';
		$size        = sizeof( $results );
		for ( $i = 0; $i < $size; $i++ ) {
			$first_block .= $this->render_block_type( $results[ $i ], null, 2 );
		}

		return $first_block;
	}

	public function render_output( $attr, $column_class ) {
		$results    = isset( $attr['results'] ) ? $attr['results'] : $this->build_query( $attr );
		$navigation = $this->render_navigation( $attr, $results['next'], $results['prev'], $results['total_page'] );
		$content    = ! empty( $results['result'] ) ? $this->render_column( $results['result'], $column_class ) : $this->empty_content();

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
		return $this->build_column( $result, $column_class );
	}

	public function render_column_alt( $result, $column_class ) {
		return $this->build_column_alt( $result );
	}
}
