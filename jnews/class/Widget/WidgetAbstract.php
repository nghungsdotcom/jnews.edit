<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Widget;

abstract class WidgetAbstract extends \WP_Widget
{
    /**
     * @var WidgetGenerator
     */
    protected $generator;

    public function __construct($id_base = false, $name = null, $widget_options = array(), $control_options = array())
    {
        // apply selective review
        $widget_options['customize_selective_refresh'] = true;
        parent::__construct( $id_base, $name , $widget_options , $control_options );
    }

    public function get_default_group()
    {
    	return esc_html__('General', 'jnews');
    }

    /**
	 * We need to detect request uri for rest request widget-types
	 *
	 * @return boolean
	 */
	public function is_block_editor() {
		if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'widget-types' ) !== false ) {
			return true;
		}
		return false;
	}
}
