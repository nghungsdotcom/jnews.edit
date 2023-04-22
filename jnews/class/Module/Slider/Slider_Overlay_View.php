<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Slider;

class Slider_Overlay_View extends SliderViewAbstract {

	private function load_initial_script() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();
		}
		clearstatcache(true);
		$script = $wp_filesystem->get_contents( get_parent_theme_file_path( 'assets/js/joverlayslider-initial.js' ) );
		if ( ! $script ) {
			$script = @file_get_contents( get_parent_theme_file_path( 'assets/js/joverlayslider-initial.js' ) );
		}

		return $script;
	}

	public function slider( $results ) {
		$slider = '';

		foreach ( $results as $key => $post ) {
			$image = get_the_post_thumbnail_url( $post->ID, 'full' );

			if ( $key === 0 ) {
				$slider .= '<div ' . jnews_post_class( 'jeg_overlay_slider_bg loaded active', $post->ID ) . " style=\"background-image: url('" . esc_url( $image ) . "');\"></div>";
			} else {
				$slider .= '<div ' . jnews_post_class( 'jeg_overlay_slider_bg', $post->ID ) . " data-bg=\"{$image}\"></div>";
			}
		}

		return $slider;
	}

	public function content( $results ) {
		$content = '';
		foreach ( $results as $key => $post ) {
			$category      = jnews_get_primary_category( $post->ID );
			$category_text = $category ? '<a href="' . get_category_link( $category ) . '">' . get_cat_name( $category ) . '</a>' : '';
			$trending      = ( vp_metabox( 'jnews_single_post.trending_post', null, $post->ID ) ) ? '<div class="jeg_meta_trending"><a href="' . get_the_permalink( $post ) . '"><i class="fa fa-bolt"></i></a></div>' : '';

			$active   = ( $key === 0 ) ? 'active' : '';
			$content .=
				"<div class=\"jeg_overlay_caption_container {$active}\">
					{$trending}
                    <div class=\"jeg_post_category\">{$category_text}</div>
                    <h2 class=\"jeg_post_title\">
                        <a href=\"" . get_the_permalink( $post ) . '">' . get_the_title( $post ) . '</a>
                    </h2>
                </div>';
		}

		return $content;
	}

	public function carousel( $results ) {
		$content = '';
		foreach ( $results as $key => $post ) {
			$active   = ( $key === 0 ) ? 'active' : '';
			$content .=
				"<div class=\"jeg_overlay_slider_item_wrapper {$active}\" data-id=\"{$key}\"><a class=\"jeg_overlay_slider_item\" href=\"" . get_the_permalink( $post ) . '">
                    <h3><span>' . get_the_title( $post ) . '</span></h3>
                </a></div>';
		}

		return $content;
	}

	public function render_element( $result, $attr ) {
		if ( ! empty( $result ) ) {
			if ( ( SCRIPT_DEBUG || get_theme_mod( 'jnews_load_necessary_asset', false ) ) && ! is_user_logged_in() ) {
				wp_dequeue_style( 'jnews-scheme' );
				wp_enqueue_style( 'jnews-overlayslider' );
				wp_enqueue_script( 'jnews-overlayslider' );
				wp_enqueue_style( 'jnews-scheme' );
			}

			$slider   = $this->slider( $result );
			$content  = $this->content( $result );
			$carousel = $this->carousel( $result );
			$style    = '';

			if ( $attr['overlay_option'] === 'normal' && ! empty( $attr['normal_overlay'] ) ) {
				$style = ".{$this->unique_id} .jeg_overlay_slider_wrapper:before { background: {$attr['normal_overlay']} }";
			}

			if ( $attr['overlay_option'] === 'gradient' && $attr['gradient_overlay_enable'] ) {
				$gradient_overlay_degree = isset( $attr['gradient_overlay_degree']['size'] ) ? $attr['gradient_overlay_degree']['size'] : $attr['gradient_overlay_degree'];
				$style .=
					".{$this->unique_id} .jeg_overlay_slider_wrapper:before {
                            background: -moz-linear-gradient({$gradient_overlay_degree}deg, {$attr['gradient_overlay_start_color']} 0%, {$attr['gradient_overlay_end_color']} 100%);
                            background: -webkit-linear-gradient({$gradient_overlay_degree}deg, {$attr['gradient_overlay_start_color']} 0%, {$attr['gradient_overlay_end_color']} 100%);
                            background: linear-gradient({$gradient_overlay_degree}deg, {$attr['gradient_overlay_start_color']} 0%, {$attr['gradient_overlay_end_color']} 100%);
                        }";
			}

			if ( ! empty( $style ) ) {
				$style = "<style>$style</style>";
			}

			$additional_class  = $attr['enable_nav'] ? 'shownav' : '';
			$additional_class .= $attr['overlay_option'] === 'none' ? ' no-overlay' : '';

			$joverlayslider_initial = $this->load_initial_script();
			$script                 = "<script type=\"text/javascript\">;{$joverlayslider_initial}</script>";

			$output =
				"<div {$this->element_id($attr)} class=\"jeg_overlay_slider {$additional_class} {$this->unique_id} {$this->get_vc_class_name()} {$attr['el_class']}\" data-fullscreen=\"{$attr['enable_fullscreen']}\" data-nav=\"{$attr['enable_nav']}\">
                    <div class=\"jeg_overlay_slider_wrapper\">
                        {$slider}
                    </div>
                    <div class=\"jeg_overlay_container\">
                        <div class=\"jeg_overlay_slider_loader\">
                            <div class=\"jeg_overlay_slider_loader_circle\"></div>
                        </div>
                        <div class=\"jeg_overlay_caption_wrapper\">
                            {$content}
						</div>
						<div class=\"jeg_overlay_slider_bottom_wrapper\">
                        <div class=\"jeg_overlay_slider_bottom\">
                            {$carousel}
						</div>
						</div>
                    </div>
                    {$style}
                    {$script}
                </div>";

			return "<div class='row vc_row'><div class='jeg-vc-wrapper'><div class='jeg_section'><div class='container vc_column_container'>$output</div></div></div></div>";
		} else {
			return $this->empty_content();
		}
	}

	public function render_module( $attr, $column_class ) {
		$vc_editable = isset( $_GET['vc_editable'] ) ? sanitize_text_field( $_GET['vc_editable'] ) : null;
		$action      = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : null;

		if ( ! ( $vc_editable === 'true' ) && ! ( $action === 'elementor' ) && ! $this->manager->is_overlay_slider_rendered() ) {
			$this->manager->overlay_slider_rendered();
			return parent::render_module( $attr, $column_class );
		}

		if ( ( $vc_editable === 'true' ) || ( $action === 'elementor' ) ) {
			return "<div class='jnews_overlay_slider_notice'>" . esc_html__( 'JNews Overlay Slider cannot be rendered with Editor Mode. You can still see it on Your Website.', 'jnews' ) . '</div>';
		}
		return null;
	}
}
