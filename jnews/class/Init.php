<?php
/**
 * @author : Jegtheme
 */
namespace JNews;

use JNews\Asset\BackendAsset;
use JNews\Asset\FrontendAsset;
use JNews\Comment\CommentNumber;
use JNews\Customizer\CustomizerRedirect;
use JNews\Footer\FooterBuilder;
use JNews\Helper\StyleHelper;
use JNews\Image\Image;
use JNews\Menu\CustomMegaMenu;
use JNews\Menu\Menu;
use JNews\Elementor\ModuleElementor;
use JNews\Module\ModuleManager;
use JNews\Module\ModuleVC;
use JNews\Module\TemplateLibrary;
use JNews\Multilang\Polylang;
use JNews\Multilang\WPML;
use JNews\Single\SinglePostTemplate;
use JNews\Archive\Builder\ArchiveBuilder;
use JNews\Asset\GoogleAnalytics;
use JNews\Asset\GoogleFonts;
use JNews\Dashboard\AdminDashboard;
use JNews\Util\RestAPI;
use JNews\Util\SchemeStyle;
use JNews\Util\Updater;
use JNews\Util\ValidateLicense;
use JNews\Util\VideoAttribute;
use JNews\Widget\AdditionalWidget;
use JNews\Widget\EditWidgetArea;
use JNews\Widget\Module\RegisterModuleWidget;
use JNews\Widget\Normal\RegisterNormalWidget;
use JNews\Widget\Widget;
use JNews\Widget\WidgetTitle;

/**
 * Starting Point for JNews Themes
 *
 * Class JNews Init
 */
class Init {

	/**
	 * @var Init
	 */
	private static $instance;
	/**
	 * @return Init
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	private function __construct() {
		$this->load_helper();
		$this->init_themes();
		$this->load_textdomain();
		$this->setup_hook();

		if ( defined( 'WPB_VC_VERSION' ) ) {
			TemplateLibrary::getInstance();
		}
	}

	public function load_helper() {
		defined( 'JNEWS_THEME_DIR_PLUGIN' ) || define( 'JNEWS_THEME_DIR_PLUGIN', JNEWS_THEME_DIR . 'plugins/' );
		defined( 'JNEWS_THEME_SERVER' ) || define( 'JNEWS_THEME_SERVER', 'https://jnews.io/' );
		defined( 'JEGTHEME_SERVER' ) || define( 'JEGTHEME_SERVER', 'https://support.jegtheme.com/' );
		// Load Plugin Helper
		require_once get_parent_theme_file_path( 'lib/theme-helper.php' );
		require_once get_parent_theme_file_path( 'lib/theme-filter.php' );

		// Load class helper if there's no essential
		if ( defined( 'JNEWS_ESSENTIAL' ) ) {
			$this->essential_script();
		} else {
			require_once get_parent_theme_file_path( 'lib/class-helper.php' );
		}

	}

	public function populate_metabox() {
		new Metabox();
	}

	public function load_textdomain() {
		load_theme_textdomain( 'jnews', get_parent_theme_file_path( 'languages' ) );
	}

	public function multilang() {
		if ( class_exists( 'Polylang' ) ) {
			// multilanguage
			Polylang::getInstance();
		}

		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			// WPML multilanguage
			WPML::getInstance();
		}
	}

	public function init_themes() {
		// Rest API
		RestAPI::instance();

		// Themes Menu
		Menu::getInstance();

		// Initialize Image
		Image::getInstance();

		// Customizer
		Customizer::getInstance();

		// SchemeStyle
		SchemeStyle::instance();

		// Dashboard
		AdminDashboard::getInstance();

		// Multi language Initialize
		$this->multilang();

		GoogleAnalytics::getInstance();

		GoogleFonts::getInstance();

		// Updater.
		Updater::instance();

		if ( is_admin() ) {
			// Back End

			// License
			ValidateLicense::getInstance();

			// load backend asset
			BackendAsset::getInstance();

		} else {
			// Front End

			// Frontend Ajax
			FrontendAjax::getInstance();

			// load google analytics

			// load frontend asset
			FrontendAsset::getInstance();

			// Comment Number
			CommentNumber::getInstance();

			// Google Captcha
			Captcha::getInstance();
		}

		// Load Widget
		$this->load_widget();
	}

	public function essential_script() {
		// Custom Mega Menu
		CustomMegaMenu::getInstance();

		// Footer Builder
		FooterBuilder::getInstance();

		// Archive Builder
		ArchiveBuilder::getInstance();

		// Single Post Builder
		SinglePostTemplate::getInstance();

		// Account Page
		AccountPage::getInstance();

		// Social Callback
		SocialCallback::getInstance();

		// Init Gutenberg
		Gutenberg::getInstance();

		 // Need to load video attribute
		 VideoAttribute::getInstance();

		 // Shortcode
		 Shortcode::getInstance();

		if ( is_admin() ) {
			// Back End
			// Load header builder on backend
			HeaderBuilder::getInstance();
		} else {
			// Front End

			// Style Helper
			StyleHelper::getInstance();

			 // Ads
			Ads::getInstance();
		}

		// Load Visual Composer
		$this->load_module();

		$this->populate_metabox();
	}

	public function load_module() {
		ModuleManager::getInstance();
		if ( defined( 'WPB_VC_VERSION' ) ) {
			ModuleVC::getInstance();
		}
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			ModuleElementor::getInstance();
		}
	}

	public function load_widget() {
		Widget::getInstance();
		WidgetTitle::getInstance();

		if ( apply_filters( 'jnews_load_all_widget', false ) ) {
			$this->load_widget_element();
		}
	}

	public function load_widget_element() {
		global $pagenow;
		EditWidgetArea::getInstance();
		AdditionalWidget::getInstance();
		if ( $pagenow === 'widgets.php' || $pagenow === 'post.php' || $pagenow === 'admin-ajax.php' || is_customize_preview() || ! is_admin() || apply_filters( 'jnews_load_register_widget', false ) ) {
			RegisterNormalWidget::getInstance();
			RegisterModuleWidget::getInstance();
		}
	}

	public function setup_hook() {
		define( 'YP_THEME_MODE', 'true' );

		add_action( 'admin_init', array( $this, 'disable_vc_auto_update' ), 9 );
		add_action( 'after_setup_theme', array( $this, 'themes_support' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_style' ) );
		add_action( 'customize_preview_init', array( $this, 'preview_init' ) );
		add_action( 'admin_head', array( $this, 'admin_ajax_url' ), 1 );
		add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules' ) );

		if ( apply_filters( 'jnews_load_post_subtitle', false ) ) {
			// Post Subtitle Field
			add_action( 'edit_form_before_permalink', array( $this, 'post_subtitle_field' ) );
			add_action( 'edit_post', array( $this, 'post_subtitle' ) );
			add_action( 'save_post', array( $this, 'post_subtitle' ) );
		}
		add_action( 'admin_notices', array( $this, 'plugin_update_notice' ) );

		add_filter( 'jquery_migrate_panel', array( $this, 'migrate_panel' ) );
		add_action( 'jnews_update_themes', array( $this, 'update_themes' ) );
	}

	/**
	 * Disable Visual Composer auto update
	 */
	public function disable_vc_auto_update() {
		if ( function_exists( 'vc_license' ) && function_exists( 'vc_updater' ) && ! vc_license()->isActivated() ) {
			remove_filter( 'upgrader_pre_download', array( vc_updater(), 'preUpgradeFilter' ), 10 );
			remove_filter( 'pre_set_site_transient_update_plugins', array( vc_updater()->updateManager(), 'check_update' ) );
		}
	}

	/**
	 * Provide custom schedule update status for themes
	 */
	public function update_themes() {
		AdminDashboard::getInstance();
		if ( is_admin() || wp_doing_cron() ) {
			$validate  = ValidateLicense::getInstance();
			$transient = get_site_transient( 'update_themes' );
			$transient = $validate->transient_update_themes( $transient );

			set_site_transient( 'update_themes', $transient );
		}
	}

	public function migrate_panel( $panel ) {
		return 'jnews_global_panel';
	}

	public function plugin_update_notice() {
		if ( is_admin() ) {
			$plugins = \JNews\Util\Api\Plugin::get_plugin_list();
			echo jnews_sanitize_by_pass( $this->print_plugin_update_notice( $plugins ) );
		}
	}

	public function plugin_update_notice_text( $plugin, $action ) {
		$link = apply_filters( 'jnews_plugin_action_url', $plugin['slug'], $action );
		// phpcs:disable WordPress.WP.I18n.UnorderedPlaceholdersText
		$html =
			'<div class="notice notice-warning">
                <p>
                <span class="jnews-notice-heading">' . sprintf( esc_html__( '%s Requires %s', 'jnews' ), $plugin['name'], ucfirst( $action ) ) . '</span>
                <span style="display: block;">' . sprintf( __( 'Please %s %s plugin to version <strong>%s</strong> or higher.', 'jnews' ), $action, $plugin['name'], '9.0.0' ) . '</span>
                <span class="jnews-notice-button">
                    <a href="' . esc_url( $link ) . '" class="button-primary">' . ucfirst( $action ) . ' Now</a>
                    </span>
                </p>
                <span class="close-button"><i class="fa fa-times"></i></span>
            </div>';
		// phpcs:enable WordPress.WP.I18n.UnorderedPlaceholdersText

		return $html;
	}

	/**
	 * Print plugin update notice
	 *
	 * @param array $plugins List plugins.
	 *
	 * @return string
	 */
	public function print_plugin_update_notice( $plugins ) {
		$all_plugins = get_plugins();
		$notice      = '';
		$required    = array( 'jnews-essential' );
		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin['slug'], $required, true ) ) {

				// Maybe in the future need to send warning notice if the plugin isn't installed.
				if ( isset( $plugin['file'] ) && ! empty( $all_plugins[ $plugin['file'] ] ) ) {

					// Send warning notice to users about the required plugin version in the latest theme version.

					if ( defined( 'JNEWS_ESSENTIAL' ) ) {
						$plugin_data    = get_plugin_data( JNEWS_ESSENTIAL_FILE );
						$plugin_version = $plugin_data['Version'];

						if ( version_compare( $plugin_version, '9.0.0', '<' ) ) {

							$notice .= $this->plugin_update_notice_text( $plugin, 'update' );

						}
					}
				}
			}
		}

		return $notice;
	}

	public function flush_rewrite_rules() {
		// $this->add_rewrite_rule();

		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	public function preview_init() {
		// Theme Customizer Redirect Tag Init
		CustomizerRedirect::getInstance();
	}

	public function load_admin_style() {
		add_editor_style( get_parent_theme_file_uri( 'assets/css/admin/editor.css' ) );
		if ( is_rtl() ) {
			add_editor_style( get_parent_theme_file_uri( 'assets/css/admin/editor-rtl.css' ) );
		}
	}

	public function themes_support() {
		// support feed link
		add_theme_support( 'automatic-feed-links' );

		// title tag
		add_theme_support( 'title-tag' );

		// featured image
		add_theme_support( 'post-thumbnails' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// selective refresh widget
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Supported post type
		add_theme_support( 'post-formats', array( 'gallery', 'video' ) );

		// HTML 5 support
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );

		// support woocommerce
		add_theme_support( 'woocommerce' );

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// auto load next post
		add_theme_support( 'auto-load-next-post' );

		// gutenberg optimized
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style-editor.css' );

		// add excerpt to page to avoid showing shortode on search page
		add_post_type_support( 'page', 'excerpt' );

		// global variables to avoid duplicate queries
		global $jnews_get_all_custom_archive_template;
		$jnews_get_all_custom_archive_template = jnews_get_all_custom_archive_template();
	}

	public function post_subtitle_field( $post ) {
		if ( $post->post_type === 'post' ) {
			$post_subtitle = get_post_meta( $post->ID, 'post_subtitle', true );
			$subtitle      = ! empty( $post_subtitle ) ? esc_html( $post_subtitle ) : '';

			echo '<div id="jnews_post_subtitle">
                    <input type="text" name="post_subtitle" value="' . $subtitle . '" spellcheck="true" autocomplete="off" placeholder="' . esc_attr__( 'Enter subtitle here', 'jnews' ) . '" />
                </div>';
		}
	}

	public function post_subtitle( $post_id ) {
		if ( ! defined( 'XMLRPC_REQUEST' ) && isset( $_POST['post_subtitle'] ) ) {
			$post_subtitle = sanitize_text_field( $_POST['post_subtitle'] );
			update_post_meta( $post_id, 'post_subtitle', $post_subtitle );
			update_post_meta( $post_id, 'post_subtitle_flag', false );
		}
	}

	public function admin_ajax_url() {
		?>
		<script type="text/javascript">
			var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		</script>
		<?php
	}
}
