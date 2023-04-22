<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

class Block_29_View extends BlockViewAbstract {

	public function render_block( $post ) {
		$attr = $this->attribute;
		$date = isset( $attr['show_date'] ) && $attr['show_date'] ? $this->post_meta_2( $post ) : '';

		return '<article ' . jnews_post_class( 'jeg_post jeg_pl_xs', $post->ID ) . '>
                    <div class="jeg_postblock_content">
                        <h3 class="jeg_post_title">
                            <a href="' . get_the_permalink( $post ) . '">' . get_the_title( $post ) . "</a>
                        </h3>
                        {$date}
                    </div>
                </article>";
	}

	public function build_column( $results ) {
		$first_block  = '';
		$size         = sizeof( $results );
		$ads_position = $this->random_ads_position( $size );

		for ( $i = 0; $i < $size; $i++ ) {
			if ( $i == $ads_position ) {
				$first_block .= $this->render_module_ads( 'jeg_ajax_loaded anim_' . $i );
			}

			$first_block .= $this->render_block( $results[ $i ] );
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
		$attr        = $this->attribute;
		$show_border = isset( $attr['show_border'] ) && $attr['show_border'] ? 'show_border' : '';

		return "<div class=\"jeg_posts {$show_border}\">
                    <div class=\"jeg_postsmall jeg_load_more_flag\">
                        {$this->build_column($result)}
                    </div>
                </div>";
	}

	public function render_column_alt( $result, $column_class ) {
		return $this->build_column( $result );
	}
}
