<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Hero;

Class Hero_Skew_View extends HeroViewAbstract
{
    public function render_block($post, $index)
    {
        $index = $index + 1;

        if($post) {
            $post_id    = $post->ID;
            $permalink  = get_the_permalink($post);

            return  "<article " . jnews_post_class("jeg_post jeg_hero_item_{$index}", $post_id) . " style=\"padding: 0 0 {$this->margin}px {$this->margin}px;\">
                        <div class=\"jeg_block_container\">
                            <span class=\"jeg_postformat_icon\"></span>
                            <div class=\"jeg_thumb\">
                                <a href=\"{$permalink}\" >{$this->get_thumbnail($post->ID, 'jnews-featured-750')}</a>
                            </div>
                            <div class=\"jeg_postblock_content\">
                                <div class=\"jeg_post_category\">
                                    {$this->get_primary_category($post_id)}
                                </div>
                                <div class=\"jeg_post_info\">
                                    <h2 class=\"jeg_post_title\">
                                        <a href=\"{$permalink}\">" . get_the_title($post) . "</a>
                                    </h2>
                                    {$this->post_meta_2($post)}
                                </div>
                            </div>
                        </div>
                    </article>";
        }
        return "<article class=\"jeg_post jeg_hero_item_{$index} jeg_hero_empty\" style=\"padding: 0 0 {$this->margin}px {$this->margin}px;\">
                    <div class=\"jeg_block_container\"></div>
                </article>";
    }

    public function render_element($result)
    {
        $first_block    = isset($result[0]) ? $this->render_block($result[0], 0) : '';

        $item           = isset($result[1]) ? $result[1] : '';
        $second_block   = $this->render_block($item, 1);

        return "{$first_block}{$second_block}";
    }
}
