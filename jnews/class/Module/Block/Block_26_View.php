<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

Class Block_26_View extends BlockViewAbstract
{

    public function render_block_type($post, $image_size, $type = 1){
        $post_id        = $post->ID;
        $permalink      = get_the_permalink($post);
        $thumbnail      = $this->get_thumbnail($post_id, $image_size);
        $category       = jnews_get_primary_category($post_id); 
        $category       = "<a href=\"" . get_category_link($category) . "\">" . get_cat_name($category) . "</a>";
	    $trending       = (vp_metabox('jnews_single_post.trending_post', null, $post_id)) ? "<div class=\"jeg_meta_trending\"><a href=\"" . get_the_permalink($post) . "\"><i class=\"fa fa-bolt\"></i></a></div>" : "";

        // author detail
        $author         = $post->post_author;
	    $author_text    = jnews_check_coauthor_plus() ? "<div class=\"jeg_meta_author coauthor\">" . jnews_get_author_coauthor($post_id, false, 'label', 1) . "</div>" : "<div class=\"jeg_meta_author\"><span class=\"label\">" . jnews_return_translation('by', 'jnews', 'by') . "</span> <a href=\"" . get_author_posts_url($author) . "\">" .  get_the_author_meta('display_name', $author) . "</a></div>";

        $post_meta      =   (get_theme_mod('jnews_show_block_meta', true) && get_theme_mod('jnews_show_block_meta_date', true) ) ?
                            "<div class=\"jeg_post_meta\">
                                {$trending}
                                <div class=\"jeg_meta_date\"><i class=\"fa fa-clock-o\"></i> {$this->format_date($post)}</div>
                            </div>" : "";
        $share_bar      = $type === 1 ? null :  apply_filters('jnews_share_flat_output', '', $post_id);


        return  "<article " . jnews_post_class("jeg_post jeg_pl_lg_9", $post_id) . ">
                    <header class=\"jeg_postblock_heading\">
                        <div class=\"jeg_post_category\"><span>{$category}</span></div>
                        <h3 class=\"jeg_post_title\"><a href=\"{$permalink}\">" . get_the_title($post) . "</a></h3>
                        {$post_meta}
                    </header>
                    <div class=\"jeg_thumb\"> 
                        " . jnews_edit_post($post_id) . "
                        <a href=\"{$permalink}\">{$thumbnail}</a> 
                    </div>
                    <div class=\"jeg_postblock_content\">
                        <div class=\"jeg_post_excerpt\">
                            <p>" . $this->get_excerpt($post) . "</p>
                        </div>
                        <div class=\"jeg_readmore_wrap\">
                            <a href=\"{$permalink}\" class=\"jeg_readmore\">" . jnews_return_translation('Read more','jnews', 'read_more') . "</a>
                        </div>
                    </div>
                    <div class=\"jeg_meta_footer clearfix\">
                        {$author_text}
                        {$share_bar}
                        <div class=\"jeg_meta_comment\"><i class=\"fa fa-comment-o\"></i> <a href=\"" . jnews_get_respond_link($post_id) . "\">" . jnews_get_comments_number($post_id) . ' ' . jnews_return_translation('Comments', 'jnews', 'comments') . "</a></div>
                    </div>
                </article>";
    }

    public function build_column($results, $column_class){
        $first_block  = '';
        $size = sizeof($results);
        $ads_position = $this->random_ads_position($size);

        $image_size = 'jnews-750x375';
        if($column_class === 'jeg_col_1o3'){
            $image_size = 'jnews-360x180';
        } else if($column_class === "jeg_col_3o3") {
            $image_size = 'jnews-1140x570';
        }

        for ( $i = 0; $i < $size; $i++ )
        {
            if ( $i == $ads_position )
            {
                $first_block .= $this->render_module_ads('jeg_ajax_loaded anim_' . $i);
            }

            $first_block .= $column_class === 'jeg_col_1o3'? $this->render_block_type($results[$i], $image_size, 1) : $this->render_block_type($results[$i], $image_size, 2);
        }

        return  $first_block;
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
