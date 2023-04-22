<?php
/**
 * @author : Jegtheme
 */
namespace JNews\Asset;

use JNews\Module\ModuleManager;

/**
 * Class JNews Load Assets
 */
class BackendAsset extends AssetAbstract {

	/**
	 * @var BackendAsset
	 */
	private static $instance;

	/**
	 * @return BackendAsset
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	public function __construct() {
		add_action( 'admin_print_scripts', array( $this, 'load_jnews_library' ), 99 );
		add_action( 'embed_head', array( $this, 'load_jnews_library' ), 99 );
		add_action( 'admin_head', array( $this, 'load_jnews_library' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'backend_script' ), 99 );
		add_action( 'wp_print_scripts', array( $this, 'backend_script_filter' ), 100 );
	}

	private function load_file( $file ) {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();
		}
		clearstatcache( true );
		$content = $wp_filesystem->get_contents( $file );
		if ( ! $content ) {
			$content = @file_get_contents( $file );
		}
		return $content;
	}

	public function load_jnews_library() {
		$script     = $this->load_file( get_parent_theme_file_path( 'assets/js/admin/jnewslibrary.js' ) );
		$script_tag = '<script>' . $script . '</script>';
		echo jnews_sanitize_output( $script_tag );
	}

	public function backend_script_filter() {
		global $pagenow;

		// OneSignal
		if ( is_admin() && isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) === 'onesignal-push' ) {
			wp_dequeue_script( 'bootstrap' );
		}

		// wpdatatables
		if ( is_admin() && ( isset( $_GET['page'] ) && 0 === strpos( sanitize_text_field( $_GET['page'] ), 'wpdatatables' ) ) ) {
			wp_dequeue_script( 'bootstrap' );
			wp_deregister_script( 'bootstrap' );
		}

		// pollylang
		if ( is_customize_preview() ) {
			wp_dequeue_script( 'pll_widgets' );
		}

		if ( $pagenow != 'widgets.php' ) {
			wp_dequeue_script( 'jeg-form-widget-script' );
		}

		if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' && $pagenow !== 'widgets.php' && $pagenow !== 'nav-menus.php' ) {
			wp_dequeue_script( 'bootstrap-iconpicker' );
			wp_dequeue_script( 'bootstrap-iconpicker-iconset' );
		}
	}

	public function backend_script() {
		$asset_url     = $this->get_asset_uri();
		$theme_version = $this->get_theme_version();
		global $pagenow;
		$wp_util = array( 'wp-util' );

		wp_enqueue_style( 'jquery-ui-accordion' );
		wp_enqueue_style( 'jnews-admin', $asset_url . 'css/admin/admin-style.css', null, $theme_version );
		wp_enqueue_style( 'font-awesome', $asset_url . 'css/font-awesome.min.css', null, $theme_version );
		wp_enqueue_style( 'tooltipster', $asset_url . 'css/admin/tooltipster.css', null, $theme_version );

		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'tooltipster', $asset_url . 'js/admin/jquery.tooltipster.min.js', array( 'jquery' ), $theme_version, true );
		wp_enqueue_script( 'jnews-admin', $asset_url . 'js/admin/jnews-admin.js', array( 'jquery', 'jnews-essential-local' ), $theme_version, true );
		wp_localize_script( 'jnews-admin', 'jnewsadmin', array( 'version' => $theme_version ) );

		if ( defined( 'JNEWS_ESSENTIAL' ) ) {
			$wp_util = array( 'jquery', 'wp-util' );
			if ( $pagenow === 'widgets.php' || is_customize_preview() ) {
				wp_enqueue_script( 'jnews-widget', $asset_url . 'js/admin/widget.js', array( 'jquery' ), $theme_version, true );
			}
			if ( $pagenow === 'post.php' || $pagenow === 'post-new.php' ) {
				wp_enqueue_script( 'jnews-post', $asset_url . 'js/admin/jnews-post.js', array( 'jnews-essential-local' ), $theme_version, true );
				wp_localize_script( 'jnews-post', 'jnewsoption', $this->localize_script() );
			}
			if ( function_exists( 'vc_is_frontend_editor' ) && vc_is_frontend_editor() ) {
				wp_enqueue_script( 'jnews-vc-frontend', $asset_url . '/js/vc/jnews.vc.frontend.js', array( 'jquery' ), $theme_version, true );
				wp_localize_script( 'jnews-vc-frontend', 'jnewsmodule', ModuleManager::getInstance()->populate_module() );
			}

			// Dequeue script and style when it's not needed.
			if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' && $pagenow !== 'widgets.php' && $pagenow !== 'nav-menus.php' ) {
				wp_dequeue_script( 'bootstrap-iconpicker' );
				wp_dequeue_script( 'bootstrap-iconpicker-iconset' );
			}
		}

		if ( $pagenow === 'admin.php' || $pagenow === 'themes.php' ) {
			wp_enqueue_style( 'vex', $asset_url . 'css/admin/vex.css' );
			wp_enqueue_script( 'vex', $asset_url . 'js/admin/vex.combined.min.js', array( 'jquery' ), null, true );
			wp_enqueue_script( 'jnews-dashboard', $asset_url . 'js/admin/jnews-dashboard.js', array_merge( $wp_util, array( 'jnews-essential-local' ) ), $theme_version, true );
			wp_localize_script( 'jnews-dashboard', 'jnewsoption', $this->localize_script() );
		}
	}

	public function localize_script() {
		$menu                      = apply_filters( 'jnews_get_admin_slug', array() );
		$option                    = array();
		$option['plugin_admin']    = get_admin_url() . 'admin.php?page=jnews_plugin';
		$option['jnews_dashboard'] = esc_url( menu_page_url( $menu['dashboard'], false ) );
		$option['import_track']    = array(
			'url'            => esc_url( home_url( '/' ) ),
			'license'        => apply_filters( 'jnews_check_is_license_validated', false ),
			'data_license'   => get_option( 'jnews_license' ),
			'demo'           => '',
			'import_type'    => 'content',
			'install_plugin' => 1,
		);
		return $option;
	}
}
