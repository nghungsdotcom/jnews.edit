<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

class Block_14_View extends BlockViewAbstract {


	public function render_block_type( $post, $image_size, $type = 1 ) {
		$post_id   = $post->ID;
		$permalink = get_the_permalink( $post );

		$content = '<div class="jeg_thumb">
                        ' . jnews_edit_post( $post_id ) . "
                        <a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size)}</a>
                    </div>
                    <div class=\"jeg_postblock_content\">
                        <div class=\"jeg_post_category\">
                            <span>{$this->get_primary_category($post_id)}</span>
                        </div>
                        <h3 class=\"jeg_post_title\">
                            <a href=\"{$permalink}\">" . get_the_title( $post ) . '</a>
                        </h3>
                        ' . $this->post_meta_3( $post ) . '
                    </div>';

		return $type === 1 ? $content : '<article ' . jnews_post_class( 'jeg_post jeg_pl_md_1', $post->ID ) . ">{$content}</article>";
	}

	public function build_column( $results, $column_class ) {

		$image_size = 'jnews-750x375';
		if ( $column_class === 'jeg_col_1o3' ) {
			$image_size = 'jnews-360x180';
		} elseif ( $column_class === 'jeg_col_3o3' ) {
			$image_size = 'jnews-1140x570';
		}

		$first_block = $this->render_block_type( $results[0], $image_size, 1 );

		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i++ ) {
			$second_block .= $this->render_block_type( $results[ $i ], 'jnews-360x180', 2 );
		}

		return '<div class="jeg_posts_wrap">
                    <div class="jeg_postbig">
                        <article ' . jnews_post_class( 'jeg_post jeg_pl_lg_box', $results[0]->ID ) . ">
                            <div class=\"box_wrap\">
                                {$first_block}
                            </div>
                        </article>
                    </div>
                    <div class=\"jeg_posts jeg_load_more_flag\">
                        {$second_block}
                    </div>
                </div>";
	}

	public function build_column_alt( $results ) {
		$first_block = '';
		$size        = sizeof( $results );
		for ( $i = 0; $i < $size; $i++ ) {
			$first_block .= $this->render_block_type( $results[ $i ], 'jnews-360x180', 2 );
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
