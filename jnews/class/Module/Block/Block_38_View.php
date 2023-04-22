<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

Class Block_38_View extends BlockViewAbstract
{
	public function render_block_type_1($post)
	{
		$post_id	= $post->ID;
		$thumb_id   = get_post_thumbnail_id($post_id);
		$thumb_data = wp_get_attachment_image_src($thumb_id, 'full');
		$style      = "style='background-image: url({$thumb_data[0]})'";
		$permalink	= get_the_permalink($post);

		return	"<article " . jnews_post_class("jeg_post", $post_id) . ">
					" . jnews_edit_post($post_id, 'right' ) . "
					<div class=\"jeg_thumb\" {$style}></div>
					<div class=\"box_wrap\">
						<div class=\"jeg_post_category\">
							<span>{$this->get_primary_category($post_id)}</span>
						</div>
						<div class=\"jeg_postblock_content\">
							<h3 class=\"jeg_post_title\">
								<a href=\"{$permalink}\">" . get_the_title($post) . "</a>
							</h3>
							<div class=\"jeg_post_excerpt\">
								<p>{$this->get_excerpt($post)}</p>
							</div>
							{$this->post_meta_3($post)}
						</div>
						<div class=\"jeg_readmore_arrow\">
							<a href=\"{$permalink}\"><i class=\"fa fa-long-arrow-right\"></i></a>
						</div>
					</div>
				</article>";
	}

	public function build_column($results, $is_ajax){
		$first_block  = '';
		$size = sizeof($results);
		$ads_position = $this->random_ads_position($size);

		for ( $i = 0; $i < $size; $i++ )
		{
			if ( $i == $ads_position )
			{ 
				$first_block .= $is_ajax ? $this->render_module_ads('jeg_ajax_loaded anim_' . $i) : $this->render_module_ads();
			}

			$first_block .= $this->render_block_type_1($results[$i]);
		}

		return	$first_block;
	}

	public function render_output($attr, $column_class)
	{
		$results =  isset( $attr['results']) ? $attr['results'] : $this->build_query( $attr );
		$navigation = $this->render_navigation( $attr, $results['next'], $results['prev'], $results['total_page'] );
        $content = ! empty( $results['result'] ) ? $this->render_column( $results['result'], $column_class ) :  $this->empty_content();

		return	"<div class=\"jeg_block_container\">
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

	public function render_column($result, $column_class)
	{
		return	"<div class=\"jeg_posts jeg_load_more_flag\">
					{$this->build_column($result, false)}
				</div>";
	}

	public function render_column_alt($result, $column_class)
	{
		return $this->build_column($result, true);
	}
}
