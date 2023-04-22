<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

class Block_17_View extends BlockViewAbstract {

	public function render_block_type( $post, $image_size, $type = 1 ) {
		$post_id   = $post->ID;
		$permalink = get_the_permalink( $post );
		$post_meta = "{$this->post_meta_1($post)}
                        <div class=\"jeg_post_excerpt\">
                            <p>{$this->get_excerpt($post)}</p>
                        </div>";
		$category  = "<div class=\"jeg_post_category\">
                            <span>{$this->get_primary_category($post_id)}</span>
                        </div>";
		$pl        = 'jeg_post jeg_pl_md_1';

		if ( $type === 2 ) {
			$post_meta        = $this->post_meta_2( $post );
			$category         = '';
			$additional_class = ( ! has_post_thumbnail( $post_id ) ) ? ' no_thumbnail' : '';
			$pl               = 'jeg_post jeg_pl_sm' . $additional_class;
		}

		return '<article ' . jnews_post_class( $pl, $post_id ) . '>
                    <div class="jeg_thumb">
                        ' . jnews_edit_post( $post_id ) . "
                        <a href=\"{$permalink}\">{$this->get_thumbnail($post_id , $image_size)}</a>
                        {$category}
                    </div>
                    <div class=\"jeg_postblock_content\">
                        <h3 class=\"jeg_post_title\">
                            <a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
                        </h3>
                        {$post_meta}
                    </div>
                </article>";
	}

	public function build_column( $results, $column_class ) {

		$is_col_1o3  = $column_class === 'jeg_col_1o3';
		$image_size  = 'jnews-750x536';
		$first_block = '';
		$start       = 0;
		$limit       = 2;

		if ( $is_col_1o3 ) {
			$first_block = $this->render_block_type( $results[0], 'jnews-360x180', 1 );
			$start       = 1;
		} elseif ( $column_class === 'jeg_col_3o3' ) {
			$image_size = 'jnews-360x180';
			$limit      = 3;
		}

		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = $start; $i < $size; $i++ ) {
			if ( $is_col_1o3 ) {
				$second_block .= $this->render_block_type( $results[ $i ], 'jnews-120x86', 2 );
			} else {
				$second_block .= $i < $limit ? $this->render_block_type( $results[ $i ], $image_size, 1 ) : $this->render_block_type( $results[ $i ], 'jnews-120x86', 2 );
			}
		}

		return "<div class=\"jeg_posts_wrap\">
                    <div class=\"jeg_posts jeg_load_more_flag\">
                        {$first_block}
                        {$second_block}
                    </div>
                </div>";
	}

	public function build_column_1_alt( $results ) {
		$first_block = '';
		$size        = sizeof( $results );
		for ( $i = 0; $i < $size; $i++ ) {
			$first_block .= $this->render_block_type( $results[ $i ], 'jnews-120x86', 2 );
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
		return $this->build_column_1_alt( $result );
	}
}
