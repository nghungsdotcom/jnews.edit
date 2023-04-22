<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Module\Post;

use JNews\Single\SinglePost;

class Post_Meta_View extends PostViewAbstract {
	/** Backend */

	public function render_module_back( $attr, $column_class ) {
		$left_html = $right_html = '';

		$lefts = is_array( $attr['meta_left'] ) ? $attr['meta_left'] : explode( ',', $attr['meta_left'] );
		foreach ( $lefts as $left ) {
			$left_html .= $this->render_meta_back( $left, $attr );
		}
		$left_html = "<div class='meta_left'>{$left_html}</div>";

		$rights = is_array( $attr['meta_right'] ) ? $attr['meta_right'] : explode( ',', $attr['meta_right'] );
		foreach ( $rights as $right ) {
			$right_html .= $this->render_meta_back( $right, $attr );
		}
		$right_html = "<div class='meta_right'>{$right_html}</div>";

		return "<div {$this->element_id($attr)} class='jeg_post_meta jeg_custom_meta_wrapper {$attr['scheme']} {$attr['el_class']} {$this->get_vc_class_name()}'>" . $left_html . $right_html . '</div>';
	}

	public function render_meta_back( $meta, $attr ) {
		if ( ! empty( $meta ) ) {
			$func = 'render_' . $meta . '_back';

			return apply_filters( 'jnews_post_meta_element_render_meta_back', '', $this, $func, $attr );
		}
	}

	public function render_date_back( $attr ) {
		$date = $attr['post_date'] === 'modified' ? get_the_modified_date() : get_the_date();

		return "<div class=\"jeg_meta_date\">
                <a href=\"#\">{$date}</a>
            </div>";
	}

	public function render_category_back( $attr ) {
		return '<div class="jeg_meta_category">
                <span>
                    <span class="meta_text">' . jnews_return_translation( 'in', 'jnews', 'in' ) . '</span>
                    <a href="#" rel="category tag">Dummy</a>, 
                    <a href="#" rel="category tag">Another</a>, 
                    <a href="#" rel="category tag">Category</a>            
                </span>
            </div>';
	}

	public function render_comment_back( $attr ) {
		return '<div class="jeg_meta_comment">
                    <a href="/#respond"><i class="fa fa-comment-o"></i> 0</a>
                </div>';
	}

	public function render_zoom_back( $attr ) {
		return '<div class="jeg_meta_zoom">
					<div class="zoom-dropdown">
						<div class="zoom-icon">
							<span class="zoom-icon-small">A</span>
							<span class="zoom-icon-big">A</span>
						</div>
						<div class="zoom-item-wrapper">
							<div class="zoom-item">
								<button class="zoom-out"><span>A</span></button>
								<button class="zoom-in"><span>A</span></button>
								<div class="zoom-bar-container">
									<div class="zoom-bar"></div>
								</div>
								<button class="zoom-reset"><i>Reset</i></button>
							</div>
						</div>
					</div>
				</div>';
	}

	public function render_reading_time_back() {
		return '<div class="jeg_meta_reading_time">
					<span>3 min read</span>
			    </div>';
	}

	public function render_trending_back( $attr ) {
		return '<div class="jeg_meta_trending"><i class="fa fa-bolt"></i></div>';
	}

	public function render_author_back( $attr ) {
		$avatar = '';
		if ( $attr['show_avatar'] ) {
			$avatar = '<img alt="admin" src="https://secure.gravatar.com/avatar/2af1c8d5b4f403f0549caed4d53c438e?s=80&d=mm&r=g" class="avatar avatar-80 photo" data-pin-no-hover="true" width="80" height="80">';
		}

		return "<div class=\"jeg_meta_author\"> {$avatar}" .
			'<span class="meta_text"> ' .
			jnews_return_translation( 'by', 'jnews', 'by' ) .
			' </span>' .
			"<a href='#'>admin</a>" .
			'</div>';
	}


	/** Frontend */

	public function render_module_front( $attr, $column_class ) {
		$left_html = $right_html = '';

		add_filter(
			'theme_mod_jnews_global_post_date',
			function () use ( $attr ) {
				return $attr['post_date'];
			}
		);

		$lefts = is_array( $attr['meta_left'] ) ? $attr['meta_left'] : explode( ',', $attr['meta_left'] );
		foreach ( $lefts as $left ) {
			$left_html .= $this->render_meta( $left );
		}
		$left_html = "<div class='meta_left'>{$left_html}</div>";

		$rights = is_array( $attr['meta_right'] ) ? $attr['meta_right'] : explode( ',', $attr['meta_right'] );
		foreach ( $rights as $right ) {
			$right_html .= $this->render_meta( $right );
		}
		$right_html = "<div class='meta_right'>{$right_html}</div>";

		return "<div {$this->element_id($attr)} class='jeg_post_meta jeg_custom_meta_wrapper {$attr['scheme']} {$attr['el_class']} {$this->get_vc_class_name()}'>" . $left_html . $right_html . '</div>';
	}

	public function render_meta( $meta ) {
		if ( ! empty( $meta ) ) {
			$func = 'render_' . $meta;

			return apply_filters( 'jnews_post_meta_element_render_meta', '', $this, $func );
		}
	}

	public function render_date() {
		$date = SinglePost::getInstance()->post_date_format( get_post() );

		return '<div class="jeg_meta_date">
                <a href="' . get_the_permalink() . '">' . esc_html( $date ) . '</a>
            </div>';
	}

	public function render_category() {
		return '<div class="jeg_meta_category">
                <span>
                    <span class="meta_text">' . jnews_return_translation( 'in', 'jnews', 'in' ) . '</span>
                    ' . get_the_category_list( '<span class="category-separator">, </span>' ) . ' 
                </span>
            </div>';
	}

	public function render_comment() {
		return '<div class="jeg_meta_comment"><a href="' . jnews_get_respond_link() . '"><i class="fa fa-comment-o"></i> ' . esc_html( jnews_get_comments_number() ) . '</a></div>';
	}

	public function render_zoom() {
		$zoom_out_step = get_theme_mod( 'jnews_single_zoom_button_out_step', 2 );
		$zoom_in_step  = get_theme_mod( 'jnews_single_zoom_button_in_step', 3 );

		return '<div class="jeg_meta_zoom" data-in-step="' . $zoom_in_step . '" data-out-step="' . $zoom_out_step . '">
					<div class="zoom-dropdown">
						<div class="zoom-icon">
							<span class="zoom-icon-small">A</span>
							<span class="zoom-icon-big">A</span>
						</div>
						<div class="zoom-item-wrapper">
							<div class="zoom-item">
								<button class="zoom-out"><span>A</span></button>
								<button class="zoom-in"><span>A</span></button>
								<div class="zoom-bar-container">
									<div class="zoom-bar"></div>
								</div>
								<button class="zoom-reset"><span>' . jnews_return_translation( 'Reset', 'jnews', 'zoom_reset' ) . '</span></button>
							</div>
						</div>
					</div>
				</div>';
	}

	public function render_trending() {
		$flag = vp_metabox( 'jnews_single_post.trending_post', null, get_the_ID() );

		if ( $flag ) {
			return '<div class="jeg_meta_trending"><i class="fa fa-bolt"></i></div>';
		}

	}

	public function render_reading_time() {

		global $post;
		$output = '';

		$content = get_post_field( 'post_content', $post->ID );
		$wpm     = (int) get_theme_mod( 'jnews_single_reading_time_wpm', 300 );

		if ( $content && $wpm ) {
			$content      = strip_shortcodes( $content );
			$word_count   = str_word_count( $content );
			$word_count   = ceil( $word_count / $wpm );
			$reading_time = jnews_return_translation( 'Reading Time: ', 'jnews', 'reading_time' );
			if ( defined( 'JNEWS_FRONT_TRANSLATION' ) ) {
				$reading_time .= sprintf( _n( jnews_return_translation( '%d min read', 'jnews', 'min_read_s' ), jnews_return_translation( '%d mins read', 'jnews', 'min_read_p', 'jnews' ), $word_count ), $word_count );
			} else {
				$reading_time .= sprintf( _n( '%d min read', '%d mins read', $word_count, 'jnews' ), $word_count );
			}

			if ( $word_count ) {
				$output =
					'<div class="jeg_meta_reading_time">
			            <span>
			            	' . $reading_time . '
			            </span>
			        </div>';
			}
		}

		return $output;
	}

	public function render_author() {
		global $post;

		if ( jnews_check_coauthor_plus() ) {
			return '<div class="jeg_meta_author coauthor">' .
				jnews_get_author_coauthor( $post->ID, true, null, 2 ) .
				'</div>';
		}

		return '<div class="jeg_meta_author">' .
			get_avatar( get_the_author_meta( 'ID', $post->post_author ), 80, null, get_the_author_meta( 'display_name', $post->post_author ) ) .
			'<span class="meta_text">' .
			jnews_return_translation( 'by', 'jnews', 'by' ) .
			'</span>' .
			jnews_the_author_link( $post->post_author, false ) .
			'</div>';
	}
}
