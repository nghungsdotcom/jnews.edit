<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

Class Block_6_View extends BlockViewAbstract
{
    public function render_block_type_1($post, $image_size)
    {
        $permalink  = get_the_permalink($post);
        $post_id    = $post->ID;
        
        return  "<article " . jnews_post_class("jeg_post jeg_pl_lg_3", $post_id) . ">
                    <div class=\"jeg_thumb\">
                        " . jnews_edit_post( $post_id, 'right' ) . "
                        <a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size)}</a>
                        <div class=\"jeg_post_category\">
                            <span>{$this->get_primary_category($post_id)}</span>
                        </div>
                    </div>
                    <div class=\"jeg_postblock_content\">
                        <h3 class=\"jeg_post_title\">
                            <a href=\"{$permalink}\">" . get_the_title($post) . "</a>
                        </h3>
                        {$this->post_meta_1($post)}
                        <div class=\"jeg_post_excerpt\">
                            <p>{$this->get_excerpt($post)}</p>
                            <a href=\"{$permalink}\" class=\"jeg_readmore\">" . jnews_return_translation('Read more','jnews', 'read_more') . "</a>
                        </div>
                    </div>
                </article>";
    }

    public function render_output($attr, $column_class)
    {
        $results =  isset( $attr['results']) ? $attr['results'] : $this->build_query( $attr );
		$navigation = $this->render_navigation( $attr, $results['next'], $results['prev'], $results['total_page'] );
		$content = ! empty( $results['result'] ) ? $this->render_column( $results['result'], $column_class ) :  $this->empty_content();

        return  "<div class=\"jeg_block_container\">
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
        return "<div class=\"jeg_posts jeg_load_more_flag\">{$this->build_column($result, false)}</div>";
    }

    public function render_column_alt($result, $column_class)
    {
        return $this->build_column($result, true);
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

            $first_block .= $this->render_block_type_1($results[$i], 'jnews-350x250');
        }

        return $first_block;
    }
}
