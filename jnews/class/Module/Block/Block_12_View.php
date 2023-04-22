<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

Class Block_12_View extends BlockViewAbstract
{
    public function render_block_type_1($post, $image_size, $column_class)
    {
        $post_id            =   $post->ID;
        $primary_category   =   jnews_get_primary_category($post_id);
        $additional_class   =   (!has_post_thumbnail($post_id)) ? ' no_thumbnail' : '';
        $permalink          =   get_the_permalink($post);
        $post_meta          =   $column_class === 'jeg_col_1o3' ? $this->post_meta_3($post) : $this->post_meta_1($post) . "
                                <div class=\"jeg_post_excerpt\">
                                    <p>{$this->get_excerpt($post)}</p>
                                    <a href=\"{$permalink}\" class=\"jeg_readmore\">" . jnews_return_translation('Read more','jnews', 'read_more') . "</a>
                                </div>";
        
        return  "<article " . jnews_post_class("jeg_post jeg_pl_lg_card" . $additional_class, $post_id) . ">
                    <div class=\"jeg_inner_post\">
                        <div class=\"jeg_thumb\">
                            " . jnews_edit_post($post_id) . "
                            <a href=\"{$permalink}\">{$this->get_thumbnail($post_id, $image_size)}</a>
                        </div>
                        <div class=\"jeg_postblock_content\">
                            <div class=\"jeg_post_category\">
                                <span>
                                    <a href=\"" . get_category_link($primary_category) . "\">" . get_cat_name($primary_category) . "</a>
                                </span>
                            </div>
                            <h3 class=\"jeg_post_title\">
                                <a href=\"{$permalink}\">" . get_the_title($post) . "</a>
                            </h3>
                            {$post_meta}
                        </div>
                    </div>
                </article>";
    }

    public function build_column($results, $column_class, $is_ajax){
        $first_block  = '';
        $size = sizeof($results);
        $ads_position = $this->random_ads_position($size);

        $image_size = 'jnews-750x375';
        if($column_class === 'jeg_col_1o3'){
            $image_size = 'jnews-350x250';
        }else if($column_class === 'jeg_col_3o3'){
            $image_size = 'jnews-1140x570';
        }

        for ( $i = 0; $i < $size; $i++ )
        {
            if ( $i == $ads_position )
            {
                $first_block .= $is_ajax ? $this->render_module_ads('jeg_ajax_loaded anim_' . $i) : $this->render_module_ads();
            }

            $first_block .= $this->render_block_type_1($results[$i], $image_size, $column_class);
        }

        return $first_block;
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
        return "<div class=\"jeg_posts jeg_load_more_flag\">{$this->build_column($result, $column_class, false)}</div>";
    }

    public function render_column_alt($result, $column_class)
    {
        return $this->build_column($result, $column_class, true);
    }
}
