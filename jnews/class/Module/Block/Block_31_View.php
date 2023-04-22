<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

Class Block_31_View extends BlockViewAbstract
{
    public function render_block($post, $attr)
    {
        $post_id = $post->ID;
        return  "<article " . jnews_post_class("jeg_post jeg_pl_sm_2", $post_id) . ">
                    <div class=\"jeg_postblock_content\">
                        <div class=\"jeg_post_category\">
                            {$this->get_primary_category($post_id)}
                        </div>
                        <h3 class=\"jeg_post_title\">
                            <a href=\"" . get_the_permalink($post) . "\">" . get_the_title($post) . "</a>
                        </h3>
                        {$this->post_meta_2($post)}
                    </div>
                </article>";
    }

    public function build_column($results, $attr){
        $output       = '';
        $size = sizeof($results);
        $ads_position = $this->random_ads_position($size);
        
        for ( $i = 0; $i < $size; $i++ )
        {
            if ( $i == $ads_position )
            {
                $output .= $this->render_module_ads('jeg_ajax_loaded anim_' . $i);
            }

            $output .= $this->render_block($results[$i], $attr);
        }

        return $output;
    }

    public function render_output($attr, $column_class)
    {
	    $results =  isset( $attr['results']) ? $attr['results'] : $this->build_query( $attr );
	    $navigation = $this->render_navigation($attr, $results['next'], $results['prev'], $results['total_page']);
        $content = ! empty( $results['result'] ) ? $this->render_column( $results['result'], $column_class ) :  $this->empty_content();

        return 
            "<div class=\"jeg_block_container\">
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

    public function render_column($result, $attr)
    {
        return "<div class=\"jeg_posts jeg_load_more_flag \">
                    {$this->build_column($result, $attr)}
                </div>";
    }

    public function render_column_alt($result, $attr)
    {
        return $this->build_column($result, $attr);
    }
}
