<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

use JNews\Importer;

class Block_9_View extends BlockViewAbstract {

	public function get_demo_style() {
		$additional = get_option( Importer::$option );

		if ( isset( $additional['style'] ) && $additional['style'] ) {
			return $additional['style'];
		}

		return false;
	}

	public function get_image_size() {
		$demo_style = $this->get_demo_style();
		return $demo_style === 'gag' || $demo_style === 'viral' ? 'jnews-350x250' : 'jnews-360x180';
	}

	public function render_block_type_1( $post, $image_size ) {
		$post_id   = $post->ID;
		$permalink = get_the_permalink( $post );

		return '<article ' . jnews_post_class( 'jeg_post jeg_pl_md_1', $post_id ) . '>
                    <div class="jeg_thumb">
                        ' . jnews_edit_post( $post_id ) . "
                        <a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size)}</a>
                        <div class=\"jeg_post_category\">
                            <span>{$this->get_primary_category($post_id)}</span>
                        </div>
                    </div>
                    <div class=\"jeg_postblock_content\">
                        <h3 class=\"jeg_post_title\">
                            <a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
                        </h3>
                        {$this->post_meta_2($post)}
                    </div>
                </article>";
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
		return "<div class=\"jeg_posts_wrap\"><div class=\"jeg_posts jeg_load_more_flag\">{$this->build_column($result, $column_class)}</div></div>";
	}

	public function render_column_alt( $result, $column_class ) {
		return $this->build_column( $result, $column_class );
	}

	public function build_column( $results, $column_class ) {
		$first_block = '';

		$image_size = $column_class === 'jeg_col_1o3' ? 'jnews-360x180' : $this->get_image_size();

		$size = sizeof( $results );
		for ( $i = 0; $i < $size; $i++ ) {
			$first_block .= $this->render_block_type_1( $results[ $i ], $image_size );
		}

		return $first_block;
	}
}
