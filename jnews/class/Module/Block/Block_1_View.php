<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Module\Block;

class Block_1_View extends BlockViewAbstract {

	public function render_block_type_1( $post, $image_size ) {
		$post_id   = $post->ID;
		$permalink = get_the_permalink( $post );

		return '<div class="jeg_thumb">
					' . jnews_edit_post( $post_id ) . "
					<a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size )}</a>
					<div class=\"jeg_post_category\">
						<span>{$this->get_primary_category($post_id)}</span>
					</div>
				</div>
				<div class=\"jeg_postblock_content\">
					<h3 property=\"headline\" class=\"jeg_post_title\">
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
		$permalink        = get_the_permalink( $post );
		$additional_class = ( ! has_post_thumbnail( $post_id ) ) ? ' no_thumbnail' : '';

		return '<article ' . jnews_post_class( 'jeg_post jeg_pl_sm' . $additional_class, $post_id ) . '>
					<div class="jeg_thumb">
						' . jnews_edit_post( $post_id ) . "
						<a href=\"{$permalink}\">
							{$this->get_thumbnail($post_id, $image_size )}
						</a>
					</div>
					<div class=\"jeg_postblock_content\">
						<h3 class=\"jeg_post_title\">
							<a href=\"{$permalink}\">" . get_the_title( $post ) . "</a>
						</h3>
						{$this->post_meta_2( $post )}
					</div>
				</article>";
	}

	public function render_block_type_3( $post ) {
		return '<article ' . jnews_post_class( 'jeg_post jeg_pl_xs_2', $post->ID ) . '>
					<div class="jeg_postblock_content">
						<h3 class="jeg_post_title"><a href="' . get_permalink( $post ) . '">' . get_the_title( $post ) . "</a></h3>
						{$this->post_meta_2( $post )}
					</div>
				</article>";
	}

	public function build_column_1( $results ) {
		$first_block = $this->render_block_type_1( $results[0], 'jnews-360x180' );

		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i ++ ) {
			$second_block .= $this->render_block_type_2( $results[ $i ], 'jnews-120x86' );
		}

		return '<div class="jeg_posts">
					<article ' . jnews_post_class( 'jeg_post jeg_pl_lg_1', $results[0]->ID ) . ">
						$first_block
					</article>
					<div class=\"jeg_postsmall\">
						$second_block
					</div>
				</div>";
	}

	public function build_column_1_alt( $results ) {
		$first_block = '';
		$size        = sizeof( $results );
		for ( $i = 0; $i < $size; $i ++ ) {
			$first_block .= $this->render_block_type_2( $results[ $i ], 'jnews-120x86' );
		}

		return "<div class=\"jeg_posts\">
					<div class=\"jeg_postsmall\">
						$first_block
					</div>
				</div>";
	}

	public function build_column_2( $results ) {
		$first_block = $this->render_block_type_1( $results[0], 'jnews-360x180' );

		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i ++ ) {
			$second_block .= $this->render_block_type_2( $results[ $i ], 'jnews-120x86' );
		}

		return '<div class="jeg_posts row">
					<article ' . jnews_post_class( 'jeg_post jeg_pl_lg_1 col-sm-6', $results[0]->ID ) . ">
						$first_block
					</article>
					<div class=\"jeg_postsmall col-sm-6\">
						$second_block
					</div>
				</div>";
	}

	public function build_column_3( $results ) {
		$first_block = $this->render_block_type_1( $results[0], 'jnews-360x180' );

		$size        = sizeof( $results );
		$first_limit = (int) ceil( ( $size - 1 ) * 2 / 5 ) + 1;

		$second_block = '';
		for ( $i = 1; $i < $first_limit; $i ++ ) {
			$second_block .= $this->render_block_type_2( $results[ $i ], 'jnews-120x86' );
		}

		$third_block = '';
		for ( $i = $first_limit; $i < $size; $i ++ ) {
			$third_block .= $this->render_block_type_3( $results[ $i ] );
		}

		return '<div class="jeg_posts row">
					<article ' . jnews_post_class( 'jeg_post jeg_pl_lg_1 col-sm-4', $results[0]->ID ) . ">
						$first_block
					</article>
					<div class=\"jeg_postsmall col-sm-4\">
						$second_block
					</div>
					<div class=\"jeg_postsmall col-sm-4\">
						$third_block
					</div>
				</div>";
	}

	public function render_output( $attr, $column_class ) {
		$results    = isset( $attr['results'] ) ? $attr['results'] : $this->build_query( $attr );
		$navigation = $this->render_navigation( $attr, $results['next'], $results['prev'], $results['total_page'] );
		$content    = ! empty( $results['result'] ) ? $this->render_column( $results['result'], $column_class ) : $this->empty_content();

		return "<div class=\"jeg_block_container jeg_load_more_flag\">
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
		if ( $column_class === 'jeg_col_1o3' ) {
			return $this->build_column_1( $result );
		} elseif ( $column_class === 'jeg_col_3o3' ) {
			return $this->build_column_3( $result );
		}

		return $this->build_column_2( $result );
	}

	public function render_column_alt( $result, $column_class ) {
		if ( $column_class === 'jeg_col_1o3' ) {
			return $this->build_column_1_alt( $result );
		} elseif ( $column_class === 'jeg_col_3o3' ) {
			return $this->build_column_3( $result );
		}

		return $this->build_column_2( $result );
	}
}
