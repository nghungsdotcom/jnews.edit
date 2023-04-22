<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Hero;

Class Hero_1_View extends HeroViewAbstract
{
    public function render_block_type($post, $index, $type = 1){ 
        $is_type_1  = $type === 1;
        $index      = $is_type_1 ? $index : $index + 1;

        if($post) {
            $post_id    = $post->ID;
            $permalink  = get_the_permalink($post);
            $meta       = $is_type_1 ? $this->post_meta_3($post) : $this->post_meta_2($post);
            $image      = $index > 2 ? 'jnews-350x250' : 'jnews-featured-750';

            return  "<article " . jnews_post_class("jeg_post jeg_hero_item_{$index}", $post_id) . " style=\"padding: 0 0 {$this->margin}px {$this->margin}px;\">
                        <div class=\"jeg_block_container\">
                            " . jnews_edit_post($post_id) . "
                            <span class=\"jeg_postformat_icon\"></span>
                            <div class=\"jeg_thumb\">
                                <a href=\"{$permalink}\" >{$this->get_thumbnail($post_id, $image)}</a>
                            </div>
                            <div class=\"jeg_postblock_content\">
                                <div class=\"jeg_post_category\">{$this->get_primary_category($post_id)}</div>
                                <div class=\"jeg_post_info\">
                                    <h2 class=\"jeg_post_title\">
                                        <a href=\"{$permalink}\" >" . get_the_title($post) . "</a>
                                    </h2>
                                    {$meta}
                                </div>
                            </div>
                        </div>
                    </article>";
        } 
        return  "<article class=\"jeg_post jeg_hero_item_{$index} jeg_hero_empty\" style=\"padding: 0 0 {$this->margin}px {$this->margin}px;\">
                    <div class=\"jeg_block_container\"></div>
                </article>";
    }

    public function render_element($result)
    {
        $first_block    = $this->render_block_type($result[0], 1, 1);
        $second_block   = '';
        $number_post    = $this->get_number_post() - 1;
        
        for($i = 1; $i <= $number_post; $i++){
            $item = isset($result[$i]) ? $result[$i] : '';
            $second_block .=$this->render_block_type($item, $i, 2);
        }

        return  "{$first_block}
                <div class=\"jeg_heroblock_scroller\">
                    {$second_block}
                </div>";
    }
}