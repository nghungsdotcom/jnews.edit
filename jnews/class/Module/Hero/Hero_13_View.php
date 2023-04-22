<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Hero;

Class Hero_13_View extends HeroViewAbstract
{
    public function get_module_name()
    {
        return esc_html__('JNews - Hero 13', 'jnews');
    }

    public function render_block_type_1($post)
    {
        if($post) {
            $post_id    = $post->ID;
            $permalink  =  get_the_permalink($post);

            return  "<article " . jnews_post_class("jeg_post jeg_hero_item_1", $post_id) . " style=\"padding: 0 0 {$this->margin}px {$this->margin}px;\">
                        <div class=\"jeg_block_container\">
                            " . jnews_edit_post($post_id) . "
                            <span class=\"jeg_postformat_icon\"></span>
                            <div class=\"jeg_thumb\">
                                <a href=\"{$permalink}\" >{$this->get_thumbnail($post_id, 'jnews-featured-1140')}</a>
                            </div>
                            <div class=\"jeg_postblock_content\">
                                <div class=\"jeg_post_category\">{$this->get_primary_category($post_id)}</div>
                                <div class=\"jeg_post_info\">
                                    <h2 class=\"jeg_post_title\">
                                        <a href=\"{$permalink}\" >" . get_the_title($post) . "</a>
                                    </h2>
                                    {$this->post_meta_3($post)}
                                </div>
                            </div>
                        </div>
                    </article>";
        }
        return null;
    }

    public function render_element($result)
    {
        return $this->render_block_type_1($result[0]);
    }
}
