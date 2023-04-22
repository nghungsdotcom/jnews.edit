<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

class Block_13_View extends BlockViewAbstract {

	public function render_block( $post, $image_size, $type = 1 ) {
		$permalink = get_the_permalink( $post );

		$output =
			'<div class="jeg_thumb">
                ' . jnews_edit_post( $post->ID ) . '
                <a href="' . $permalink . '">' . $this->get_thumbnail( $post->ID, $image_size ) . "</a>
                <div class=\"jeg_post_category\">
                    <span>{$this->get_primary_category( $post->ID )}</span>
                </div>
            </div>
            <div class=\"jeg_postblock_content\">
                <h3 class=\"jeg_post_title\">
                    <a href=\"" . $permalink . '">' . get_the_title( $post ) . '</a>
                </h3>
                ' . $this->post_meta_1( $post ) . '
                <div class="jeg_post_excerpt">
                    <p>' . $this->get_excerpt( $post ) . '</p>
                    <a href="' . $permalink . '" class="jeg_readmore">' . jnews_return_translation( 'Read more', 'jnews', 'read_more' ) . '</a>
                </div>
            </div>';

		return $type === 1 ? $output :
				'<article ' . jnews_post_class( 'jeg_post jeg_pl_md_1', $post->ID ) . '>' .
					$output
				. '</article>';

	}


	public function build_column_1( $results ) {
		$first_block = $this->render_block( $results[0], 'jnews-350x250', 1 );

		$second_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i++ ) {
			$second_block .= $this->render_block( $results[ $i ], 'jnews-120x86', 2 );
		}

		return '<article ' . jnews_post_class( 'jeg_post jeg_pl_lg_1', $results[0]->ID ) . ">
                    {$first_block}
                </article>
                <div class=\"jeg_posts_wrap\">
                    <div class=\"jeg_posts jeg_load_more_flag\">
                        {$second_block}
                    </div>
                </div>";
	}

	public function build_column_2( $results ) {
		$first_block = $this->render_block( $results[0], 'jnews-360x504', 1 );

		$second_block = $third_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i++ ) {
			if ( $i <= 2 ) {
				$second_block .= $this->render_block( $results[ $i ], 'jnews-350x250', 2 );
			} else {
				$third_block .= $this->render_block( $results[ $i ], 'jnews-350x250', 2 );
			}
		}

		return '<div class="jeg_posts row">
                    <article ' . jnews_post_class( 'jeg_post jeg_pl_lg_1 col-sm-6', $results[0]->ID ) . ">
                        {$first_block}
                    </article>
                    <div class=\"jeg_postsmall col-sm-6\">
                        {$second_block}
                    </div>
                </div>
                <div class=\"jeg_posts_wrap\">
                    <div class=\"jeg_posts jeg_load_more_flag\">
                        {$third_block}
                    </div>
                </div>";
	}

	public function build_column_3( $results ) {
		$first_block = $this->render_block( $results[0], 'jnews-360x504', 1 );

		$second_block = $third_block = $fourth_block = '';
		$size         = sizeof( $results );
		for ( $i = 1; $i < $size; $i++ ) {
			if ( $i <= 2 ) {
				$second_block .= $this->render_block( $results[ $i ], 'jnews-350x250', 2 );
			} elseif ( $i <= 4 ) {
				$third_block .= $this->render_block( $results[ $i ], 'jnews-350x250', 2 );
			} else {
				$fourth_block .= $this->render_block( $results[ $i ], 'jnews-350x250', 2 );
			}
		}

		return '<div class="jeg_posts row">
                <article ' . jnews_post_class( 'jeg_post jeg_pl_lg_1 col-sm-4', $results[0]->ID ) . ">
                    {$first_block}
                </article>
                <div class=\"jeg_postsmall col-sm-4\">
                    {$second_block}
                </div>
                <div class=\"jeg_postsmall col-sm-4\">
                    {$third_block}
                </div>
            </div>
            <div class=\"jeg_posts_wrap\">
                <div class=\"jeg_posts jeg_load_more_flag\">
                    {$fourth_block}
                </div>
            </div>";
	}

	public function build_column_alt( $results, $type = 1 ) {
		$image_size = $type === 1 ? 'jnews-120x86' : 'jnews-350x250';

		$first_block = '';
		$size        = sizeof( $results );
		for ( $i = 0; $i < $size; $i++ ) {
			$first_block .= $this->render_block( $results[ $i ], $image_size, 2 );
		}

		return $first_block;
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
		switch ( $column_class ) {
			case 'jeg_col_1o3':
				$content = $this->build_column_1( $result );
				break;
			case 'jeg_col_3o3':
				$content = $this->build_column_3( $result );
				break;
			case 'jeg_col_2o3':
			default:
				$content = $this->build_column_2( $result );
				break;
		}

		return $content;
	}

	public function render_column_alt( $result, $column_class ) {
		$type = $column_class === 'jeg_col_1o3' ? 1 : 2;
		return $this->build_column_alt( $result, $type );
	}
}
