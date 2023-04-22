<?php
/**
 * @author : Jegtheme
 */

namespace JNews;

/**
 * Class Plugin Metabox
 */
Class Metabox {
	public function __construct() {
		global $pagenow;
		if($pagenow === 'post.php' || $pagenow === 'post-new.php' || is_customize_preview() || !is_admin()){
			if ( apply_filters( 'jnews_load_default_metabox', false ) ) {
				add_action( 'after_setup_theme', array( $this, 'metabox' ) );
			}
		}
	}

	public function metabox(){
		if( class_exists( 'VP_Metabox' )){
			//Blog Metabox
			new \VP_Metabox( JNEWS_THEME_DIR . '/class/metabox/post-single.php' );
			new \VP_Metabox( JNEWS_THEME_DIR . '/class/metabox/post-primary-category.php' );

			//Page Metabox
			new \VP_Metabox( JNEWS_THEME_DIR . '/class/metabox/page-loop.php' );
			new \VP_Metabox( JNEWS_THEME_DIR . '/class/metabox/page-default.php' );
		}
	}
}