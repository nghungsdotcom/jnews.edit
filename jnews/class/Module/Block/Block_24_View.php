<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

class Block_24_View extends BlockViewAbstract {

	public function render_block_type( $post, $image_size, $type = 1 ) {
		$post_id          = $post->ID;
		$additional_class = ( ! has_post_thumbnail( $post_id ) ) ? ' no_thumbnail' : '';
		$permalink        = get_the_permalink( $post );
		$title            = "<h3 class=\"jeg_post_title\">
                                    <a href=\"{$permalink}\">" . get_the_title( $post ) . '</a>
                              </h3>';

		return $type === 1 ?
				'<article ' . jnews_post_class( 'jeg_post jeg_pl_md_box' . $additional_class, $post_id ) . '>
                    <div class="box_wrap">
                        <div class="jeg_thumb">
                            ' . jnews_edit_post( $post_id ) . "
                            <a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size)}</a>
                        </div>
                        <div class=\"jeg_postblock_content\">
                            {$title}
                            {$this->post_meta_2($post)}
                        </div>
                    </div>
                </article>" :
				'<article ' . jnews_post_class( 'jeg_post jeg_pl_xs_4', $post_id ) . ">
                    <div class=\"jeg_postblock_content\">
                        {$title}
                    </div>
                </article>";
	}


	public function build_column_1( $results ) {
		$first_block  = $this->render_block_type( $results[0], 'jnews-350x250', 1 );
		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i++ ) {
			$second_block .= $this->render_block_type( $results[ $i ], null, 2 );
		}

		return "<div class=\"jeg_posts\">
                    {$first_block}
                    <div class=\"jeg_postsmall jeg_load_more_flag\">
                        {$second_block}
                    </div>
                </div>";
	}

	public function build_column_2( $results, $column_class ) {
		$first_block = '';
		$size        = sizeof( $results );
		$limit       = $column_class === 'jeg_col_2o3' ? 2 : 3;
		for ( $i = 0; $i < $size; $i++ ) {
			if ( $i < $limit ) {
				$first_block .= $this->render_block_type( $results[ $i ], 'jnews-350x250', 1 );
			} else {
				$first_block .= $this->render_block_type( $results[ $i ], null, 2 );
			}
		}

		return "<div class=\"jeg_posts jeg_load_more_flag\">
                    {$first_block}
                </div>";
	}

	public function build_column_alt( $results ) {
		$first_block = '';
		$size        = sizeof( $results );
		for ( $i = 0; $i < $size; $i++ ) {
			$first_block .= $this->render_block_type( $results[ $i ], null, 2 );
		}

		$output = $first_block;

		return $output;
	}

	public function render_output( $attr, $column_class ) {
		if ( isset( $attr['results'] ) ) {
			$results = $attr['results'];
		} else {
			$results = $this->build_query( $attr );
		}

		$navigation = $this->render_navigation( $attr, $results['next'], $results['prev'], $results['total_page'] );

		if ( ! empty( $results['result'] ) ) {
			$content = $this->render_column( $results['result'], $column_class );
		} else {
			$content = $this->empty_content();
		}

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
		return $column_class === 'jeg_col_1o3' ? $this->build_column_1( $result ) : $this->build_column_2( $result, $column_class );
	}

	public function render_column_alt( $result, $column_class ) {
		return $this->build_column_alt( $result );
	}
}
