<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Module\Block;

Class Block_27_View extends BlockViewAbstract
{
    public function render_block_type($post, $image_size, $type = 1){

        $post_id    =   $post->ID;
        $permalink  =   get_the_permalink($post);
        $thumbnail  =   $this->get_thumbnail($post_id, $image_size);
        $category   =   jnews_get_primary_category($post_id);
        $category   =   "<a href=\"" . get_category_link($category) . "\">" . get_cat_name($category) . "</a>";
	    $trending   =   (vp_metabox('jnews_single_post.trending_post', null, $post_id)) ? "<div class=\"jeg_meta_trending\"><a href=\"" . get_the_permalink($post) . "\"><i class=\"fa fa-bolt\"></i></a></div>" : "";
        $excerpt    =   $type === 1 ? null : 
                        "<div class=\"jeg_post_excerpt\">
                            <p>" . $this->get_excerpt($post) . "</p>
                        </div>";

        $post_meta =    ( get_theme_mod('jnews_show_block_meta', true) && get_theme_mod('jnews_show_block_meta_date', true) ) ?
                        "<div class=\"jeg_post_meta\">
                            {$trending}
                            <div class=\"jeg_meta_date\"><i class=\"fa fa-clock-o\"></i> {$this->format_date($post)}</div>
                        </div>" : "";

        return  "<article ". jnews_post_class("jeg_post jeg_pl_md_4", $post_id) .">
                    <div class=\"jeg_thumb\">
                        " . jnews_edit_post($post_id) . "
                        <a href=\"{$permalink}\">{$thumbnail}</a>
                    </div>
                    <div class=\"jeg_postblock_content\">
                        <div class=\"jeg_post_category\">
                            <span>{$category}</span>
                        </div>
                        <h3 class=\"jeg_post_title\"><a href=\"{$permalink}\" >" . get_the_title($post) . "</a></h3>
                        {$post_meta}
                        {$excerpt}
                    </div>
                </article>";
    }

    public function build_column($results, $column_class){
        $first_block = '';
        $size = sizeof($results);
        for($i = 0; $i < $size; $i++) {
            $first_block .= $column_class === 'jeg_col_1o3' ? $this->render_block_type($results[$i], 'jnews-350x250', 1) :  $this->render_block_type($results[$i], 'jnews-350x250', 2);
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
        return "<div class=\"jeg_posts jeg_load_more_flag\">{$this->build_column($result, $column_class)}</div>";
    }

    public function render_column_alt($result, $column_class)
    {
        return $this->build_column($result, $column_class);
    }

    public function render_module($attr, $column_class)
    {
        $heading        = $this->render_header($attr);
	    $name           = str_replace('jnews_block_','',$this->class_name);
        $style_output   = jnews_header_styling($attr, $this->unique_id . ' ');
	    $style_output   .= jnews_module_custom_color($attr, $this->unique_id . ' ', $name);
        $content        = $this->render_output($attr, $column_class);
        $style          = !empty($style_output) ? "<style scoped>{$style_output}</style>" : "";
        $script         = $this->render_script($attr, $column_class);

        return  "<div {$this->element_id($attr)} class=\"jeg_postblock_27 jeg_postblock_blog_2 jeg_postblock jeg_module_hook jeg_pagination_{$attr['pagination_mode']} {$column_class} {$this->unique_id} {$this->get_vc_class_name()} {$this->color_scheme()} {$attr['el_class']}\" data-unique=\"{$this->unique_id}\">
                    {$heading}
                    {$content}
                    {$style}
                    {$script}
                </div>";
    }
}
