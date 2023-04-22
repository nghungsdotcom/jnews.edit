<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

class Block_2_View extends BlockViewAbstract {

	public function render_block_type_1( $post, $image_size ) {
		$post_id   = $post->ID;
		$permalink = get_the_permalink( $post );

		return '<div class="jeg_thumb">
                    ' . jnews_edit_post( $post_id, 'right' ) . "
                    <a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size)}</a>
                    <div class=\"jeg_post_category\">
                        <span>{$this->get_primary_category($post_id)}</span>
                    </div>
                </div>
                <div class=\"jeg_postblock_content\">
                    <h3 class=\"jeg_post_title\">
                        <a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
                    </h3>
                    {$this->post_meta_1($post)}
                    <div class=\"jeg_post_excerpt\">
                        <p>{$this->get_excerpt($post)}</p>
                        <a href=\"{$permalink}\" class=\"jeg_readmore\">" . jnews_return_translation( 'Read more', 'jnews', 'read_more' ) . '</a>
                    </div>
                </div>';
	}


	public function render_block_type_2( $post, $image_size ) {
		$post_id          = $post->ID;
		$additional_class = ( ! has_post_thumbnail( $post_id ) ) ? ' no_thumbnail' : '';
		$permalink        = get_the_permalink( $post );

		return '<article ' . jnews_post_class( 'jeg_post jeg_pl_sm' . $additional_class, $post_id ) . '>
                    <div class="jeg_thumb">
                        ' . jnews_edit_post( $post_id ) . "
                        <a href=\"{$permalink}\">
                            {$this->get_thumbnail($post_id, $image_size)}
                        </a>
                    </div>
                    <div class=\"jeg_postblock_content\">
                        <h3 class=\"jeg_post_title\">
                            <a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
                        </h3>
                        {$this->post_meta_2($post)}
                    </div>
                </article>";
	}

	public function build_column( $results, $column_class, $is_ajax ) {
		$first_block = $this->render_block_type_1( $results[0], 'jnews-350x250' );

		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i++ ) {
			$second_block .= $this->render_block_type_2( $results[ $i ], 'jnews-120x86' );
		}

		if ( $is_ajax ) {
			return $second_block;
		}

		$content  = '<article ' . jnews_post_class( 'jeg_post jeg_pl_lg_2', $results[0]->ID ) . ">
                        {$first_block}
                    </article>";
		$content .= $column_class !== 'jeg_col_1o3' ? "<div class=\"jeg_posts_wrap\">
                                                            <div class=\"jeg_posts jeg_load_more_flag\">
                                                                {$second_block}
                                                            </div>
                                                        </div>"
													: "<div class=\"jeg_posts_wrap jeg_load_more_flag\">
                                                            {$second_block}
                                                        </div>";

		return $column_class !== 'jeg_col_1o3' ? $content : "<div class=\"jeg_posts\">{$content}</div>";
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
		return $this->build_column( $result, $column_class, false );
	}

	public function render_column_alt( $result, $column_class ) {
		return $this->build_column( $result, $column_class, true );
	}
}
