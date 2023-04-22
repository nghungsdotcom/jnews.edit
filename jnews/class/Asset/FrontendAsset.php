<?php
/**
 * @author : Jegtheme
 *
 * Code harus di sync dengan webpack config
 */

namespace JNews\Asset;

use JNews\Importer;
use JNews\Module\ModuleManager;

/**
 * Class JNews Load Assets
 */
class FrontendAsset extends AssetAbstract {
	/**
	 * @var FrontendAsset
	 */
	private static $instance;

	private $load_action = array();

	private $is_debugging = false;

	private $font_preloading_enabled = false;

	/**
	 * @return FrontendAsset
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function __construct() {
		$this->is_debugging            = SCRIPT_DEBUG || get_theme_mod( 'jnews_load_necessary_asset', false );
		$this->font_preloading_enabled = get_theme_mod( 'jnews_enable_font_preloading', false );

		add_action( 'wp_head', array( $this, 'load_jnews_library' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_style' ), 98 );
		add_action( 'get_footer', array( $this, 'load_additional_style' ) );

		// preload jegicon.woff and fontawesome-webfont.woff2
		add_filter( 'style_loader_tag', array( $this, 'preload_style' ), 10, 2 );
		// if using autoptimize, load css before </head> so the font still in preload mode
		add_filter( 'autoptimize_filter_css_replacetag', array( $this, 'autoptimize_order' ), 10, 1 );

		$au_js = self::autoptimize_option( 'autoptimize_js' );
		if ( get_theme_mod( 'jnews_enable_async_javascript', false ) && $au_js ) {
			// change defer mode autoptimize
			add_filter( 'autoptimize_filter_js_defer', array( $this, 'autoptimize_defer' ), 12 );
		}

		if ( $this->is_debugging && ! is_user_logged_in() ) {
			if ( get_theme_mod( 'jnews_extreme_autoptimize_script_loader', false ) && self::autoptimize_option( 'autoptimize_js_aggregate' ) && $au_js ) {
				// extreme optimization with autoptimize
				add_filter( 'autoptimize_filter_base_replace_cdn', array( $this, 'autoptimize_store_js' ) );
				add_filter( 'autoptimize_filter_js_bodyreplacementpayload', array( $this, 'autoptimize_after_minify' ) );
				// inject script loader
				add_action( 'init', array( $this, 'autoptimize_script_loader' ), 99 );
			}
			// move inline css to footer
			add_action( 'jeg_before_inline_dynamic_css', array( $this, 'start_inline_dynamic_css' ), 1 );
			add_action( 'jeg_after_inline_dynamic_css', array( $this, 'end_inline_dynamic_css' ), 99 );
			add_action( 'wp_footer', array( $this, 'render_inline_dynamic_css' ), 99 );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'load_script' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_vc' ), 99 );
		add_action( 'wp_head', array( $this, 'add_typekit' ) );
		add_action( 'wp_head', array( $this, 'add_additional_header_script' ), 99 );
		add_action( 'wp_footer', array( $this, 'add_additional_script' ), 99 );
		add_filter( 'script_loader_tag', array( $this, 'filter_script_loader_tags' ), 10, 3 );

		// First Load Ajax Action
		add_action( 'wp_footer', array( $this, 'first_load_footer_action' ), 1 );
		add_action( 'wp_footer', array( $this, 'first_load_footer_action_script' ), 99 );
		add_action( 'jnews_push_first_load_action', array( $this, 'push_action' ) );

		// MCE CSS
		add_filter( 'mce_css', array( $this, 'load_mce_css' ) );
	}

	public function start_inline_dynamic_css() {
		ob_start();
	}

	public function end_inline_dynamic_css() {
		$this->inline_dynamic_css = ob_get_clean();
	}

	public function render_inline_dynamic_css() {
		if ( isset( $this->inline_dynamic_css ) ) {
			echo jnews_sanitize_output( $this->inline_dynamic_css );
		}
	}

	public function push_action( $action ) {
		if ( is_array( $action ) ) {
			$this->load_action = array_merge( $this->load_action, $action );
		} else {
			$this->load_action[] = $action;
		}

		$this->load_action = array_unique( $this->load_action );
	}

	public function first_load_footer_action() {
		$footer_script = '<script type="text/javascript">var jfla = ' . json_encode( $this->load_action ) . '</script>';
		echo jnews_sanitize_output( $footer_script );
	}

	public function first_load_footer_action_script() {
		$firstload     = $this->load_file( get_parent_theme_file_path( 'assets/js/jnewsfirstload.js' ) );
		$footer_script = "<script type=\"text/javascript\">;{$firstload}</script>";
		echo jnews_sanitize_output( $footer_script );
	}

	/**
	 * Load JNews library and Preload Next Page Script
	 */
	public function load_jnews_library() {
		$library = $this->load_file( get_parent_theme_file_path( 'assets/js/admin/jnewslibrary.js' ) );
		$script  = "<script type=\"text/javascript\">;{$library}</script>";
		if ( get_theme_mod( 'jnews_enable_preload_page', true ) ) {
			$preloadpage = $this->load_file( get_parent_theme_file_path( 'assets/js/admin/preloadpage.js' ) );
			$script     .= "<script type=\"module\">;{$preloadpage}</script>";
		}
		echo jnews_sanitize_output( $script );
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

	public function add_additional_header_script() {
		$script = get_theme_mod( 'jnews_additional_header_js', '' );

		if ( ! empty( $script ) ) {
			$html       = strip_tags( $script );
			$script_tag =
				"<script>$html</script>";

			echo jnews_sanitize_output( $script_tag );
		}
	}

	public function add_additional_script() {
		$script = get_theme_mod( 'jnews_additional_js', '' );
		if ( get_theme_mod( 'jnews_extreme_autoptimize_script_loader', false ) && self::autoptimize_option( 'autoptimize_js_aggregate' ) && self::autoptimize_option( 'autoptimize_js' ) ) {
			$ads_loader = $this->load_file( get_parent_theme_file_path( 'assets/js/jnewsadsloader.js' ) );
			if ( ! empty( $ads_loader ) ) {
				$ads_loader = "<script>{$ads_loader}</script>";
				echo jnews_sanitize_output( $ads_loader );
			}
		}

		if ( ! empty( $script ) ) {
			$html       = strip_tags( $script );
			$script_tag =
				"<script>$html</script>";

			echo jnews_sanitize_output( $script_tag );
		}
	}

	public function add_typekit() {
		$typekit = get_theme_mod( 'jnews_type_kit_id', '' );
		if ( ! empty( $typekit ) ) {
			$typekit =
				'<script type="text/javascript" src="https://use.typekit.net/' . $typekit . '.js"></script>
                 <script>try{Typekit.load({ async: true });}catch(e){}</script>';

			echo jnews_sanitize_output( $typekit );
		}
	}

	public function preload_style( $html, $handle ) {
		$preload_style = array(
			'jnews-icon-webfont',
			'font-awesome-webfont',
			'vc-font-awesome-brands-webfont',
			'vc-font-awesome-regular-webfont',
			'vc-font-awesome-solid-webfont',
			'elementor-font-awesome-webfont',
		);
		if ( in_array( $handle, $preload_style, true ) ) {
			$type    = 'jnews-icon-webfont' === $handle ? 'font/woff' : 'font/woff2';
			$version = in_array( $handle, array( 'elementor-font-awesome-webfont', 'font-awesome-webfont' ), true ) ? '?v=' : '?ver=';
			$html    = str_replace(
				array( "rel='stylesheet'", '?ver=' ),
				array( "rel='preload' as='font' type='{$type}' crossorigin", $version ),
				$html
			);
		}

		return $html;
	}

	public function autoptimize_order( $replacetag ) {
		if ( $this->font_preloading_enabled ) {
			return array( '</head>', 'before' );
		}

		return $replacetag;
	}

	public function autoptimize_defer( $defer ) {
		if ( false === is_admin() ) {
			$autoptimize_method = ( 'async' === get_theme_mod( 'jnews_async_javascript_method', 'async' ) ) ? 'async' : 'defer';
			return ' ' . $autoptimize_method . "='" . $autoptimize_method . "' ";
		}
		return $defer;
	}

	public function autoptimize_after_minify( $value ) {
		return '';
	}

	public function autoptimize_store_js( $url ) {
		$exclude_script  = false;
		$exclude_scripts = array(
			'js/jquery/jquery.min.js',
			'https://cdn.onesignal.com/sdks/OneSignalSDK.js',
			'https://www.google.com/recaptcha/api.js',
		);

		/* Include Scripts in `Exclude scripts from Autoptimize` to Exclude Scripts */
		$exclude_scripts_autoptimize = get_option( 'autoptimize_js_exclude', '' );
		$exclude_scripts_autoptimize = apply_filters( 'autoptimize_filter_js_exclude', $exclude_scripts_autoptimize );

		if ( '' !== $exclude_scripts_autoptimize ) {
			if ( is_array( $exclude_scripts_autoptimize ) ) {
				$remove_keys = array_keys( $exclude_scripts_autoptimize, 'remove' );
				if ( false !== $remove_keys ) {
					foreach ( $remove_keys as $remove_key ) {
						unset( $exclude_scripts_autoptimize[ $remove_key ] );
						$this->jsremovables[] = $remove_key;
					}
				}
				$excl_js_arr = array_keys( $exclude_scripts_autoptimize );
			} else {
				$excl_js_arr = array_filter( array_map( 'trim', explode( ',', $exclude_scripts_autoptimize ) ) );
			}
			$exclude_scripts = array_merge( $exclude_scripts, $excl_js_arr );
		}

		foreach ( $exclude_scripts as $script ) {
			if ( $exclude_script = strpos( $url, $script ) !== false ) {
				break;
			}
		}

		if ( ( ! $exclude_script ) && strpos( $url, '.js' ) > 0 ) {
			global $jnews_au_scripts;
			$jnews_au_scripts = is_array( $jnews_au_scripts ) ? $jnews_au_scripts : array();
			$defer            = apply_filters( 'autoptimize_filter_js_defer', 'defer' );
			$script           = array(
				'url' => $url,
			);
			if ( ! empty( $defer ) ) {
				$async = strpos( $defer, 'async' );
				if ( false !== $async && $async > 0 ) {
					$script['async'] = true;
				} else {
					$script['defer'] = true;
				}
			}
			$jnews_au_scripts[] = $script;
			$url                = '';
		}
		return $url;
	}

	public static function autoptimize_option( $key ) {
		if ( function_exists( 'autoptimize' ) && class_exists( '\autoptimizeConfig' ) && method_exists( '\autoptimizeConfig', 'instance' ) ) {
			$conf = \autoptimizeConfig::instance();
			return $conf->get( $key );
		}
		return false;
	}

	public function autoptimize_script_loader() {
		if ( function_exists( 'autoptimize' ) ) {
			if ( autoptimize()->should_buffer() ) {
				if ( apply_filters( 'autoptimize_filter_obkiller', false ) ) {
					while ( ob_get_level() > 0 ) {
						ob_end_clean();
					}
				}

				// Now, start the real thing!
				ob_start( array( '\JNews\\' . jnews_custom_text( 'xajAdnetnorF' ), jnews_custom_text( 'gnireffub_dne' ) ) );
			}
		}
	}

	public function load_style() {
		$asset_url     = $this->get_asset_uri();
		$theme_version = $this->get_theme_version();

		if ( ! $this->is_login_page() ) {
			if ( get_theme_mod( 'jnews_enable_global_mediaelement', false ) || is_user_logged_in() ) {
				wp_enqueue_style( 'wp-mediaelement' );
			}
			add_theme_support( 'responsive-embeds' );
			wp_register_style( 'jnews-owlcarousel', $asset_url . 'js/owl-carousel2/assets/owl.carousel.min.css', null, $theme_version );
			wp_register_style( 'tiny-slider', $asset_url . 'js/tiny-slider/tiny-slider.css', null, $theme_version );
			wp_register_style( 'jnews-global-slider', $asset_url . 'css/slider/jnewsglobalslider.css', array( 'tiny-slider' ), $theme_version );
			wp_register_style( 'jnews-videoplaylist', $asset_url . 'css/jnewsvidplaylist.css', array( 'jnews-global-slider' ), $theme_version );
			wp_register_style( 'jnews-newsticker', $asset_url . 'css/jnewsticker.css', null, $theme_version );
			wp_register_style( 'jnews-hero', $asset_url . 'css/jnewshero.css', array( 'jnews-global-slider' ), $theme_version );
			wp_register_style( 'jnews-overlayslider', $asset_url . 'css/joverlayslider.css', array( 'jnews-global-slider' ), $theme_version );

			if ( $this->is_debugging ) {
				/***** Code harus di sync dengan webpack config */
				wp_register_style( 'jnews-slider', $asset_url . 'css/slider/jnewsslider.css', array( 'jnews-global-slider' ), $theme_version );
				wp_register_style( 'jnews-carousel', $asset_url . 'css/slider/jnewscarousel.css', array( 'jnews-global-slider' ), $theme_version, false );

				/** Load font first */
				if ( $this->font_preloading_enabled ) {
					wp_enqueue_style( 'font-awesome-webfont', $asset_url . 'fonts/font-awesome/fonts/fontawesome-webfont.woff2', null, '4.7.0' );
					wp_enqueue_style( 'jnews-icon-webfont', $asset_url . 'fonts/jegicon/fonts/jegicon.woff', null, null );
					if ( function_exists( 'vc_asset_url' ) && get_theme_mod( 'jnews_enable_font_preloading_vc', false ) ) {
						wp_enqueue_style( 'vc-font-awesome-brands-webfont', vc_asset_url( 'lib/bower/font-awesome/webfonts/fa-brands-400.woff2' ), null, null );
						wp_enqueue_style( 'vc-font-awesome-regular-webfont', vc_asset_url( 'lib/bower/font-awesome/webfonts/fa-regular-400.woff2' ), null, null );
						wp_enqueue_style( 'vc-font-awesome-solid-webfont', vc_asset_url( 'lib/bower/font-awesome/webfonts/fa-solid-900.woff2' ), null, null );
					}
					if ( defined( 'ELEMENTOR_ASSETS_URL' ) && get_theme_mod( 'jnews_enable_font_preloading_elementor', false ) ) {
						wp_enqueue_style( 'elementor-font-awesome-webfont', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/fonts/fontawesome-webfont.woff2', null, '4.7.0' );
					}
				}

				wp_enqueue_style( 'font-awesome', $asset_url . 'fonts/font-awesome/font-awesome.min.css', null, $theme_version );
				wp_enqueue_style( 'jnews-icon', $asset_url . 'fonts/jegicon/jegicon.css', null, $theme_version );
				wp_enqueue_style( 'jscrollpane', $asset_url . 'css/jquery.jscrollpane.css', null, $theme_version );
				wp_enqueue_style( 'oknav', $asset_url . 'css/okayNav.css', null, $theme_version );
				wp_enqueue_style( 'magnific-popup', $asset_url . 'css/magnific-popup.css', null, $theme_version );
				wp_enqueue_style( 'chosen', $asset_url . 'css/chosen/chosen.css', null, $theme_version );

				if ( get_theme_mod( 'jnews_single_popup_script', 'magnific' ) === 'photoswipe' ) {
					wp_enqueue_style( 'photoswipe', $asset_url . 'css/photoswipe/photoswipe.css', null, $theme_version );
					wp_enqueue_style( 'photoswipe-default', $asset_url . 'css/photoswipe/default-skin/default-skin.css', null, $theme_version );
				}

				wp_enqueue_style( 'jnews-main', $asset_url . 'css/main.css', null, $theme_version );
				if ( is_user_logged_in() ) {
					wp_enqueue_style( 'jnews-carousel' );
					wp_enqueue_style( 'jnews-slider' );
					wp_enqueue_style( 'jnews-hero' );
					wp_enqueue_style( 'jnews-newsticker' );
					wp_enqueue_style( 'jnews-videoplaylist' );
					wp_enqueue_style( 'jnews-overlayslider' );
				}

				if ( ! is_front_page() ) {
					wp_enqueue_style( 'jnews-pages', $asset_url . 'css/pages.css', null, $theme_version );
				}

				if ( get_theme_mod( 'jnews_sidefeed_enable', false ) ) {
					wp_enqueue_style( 'jnews-sidefeed', $asset_url . 'css/sidefeed.css', null, $theme_version );
				}

				$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
				if ( is_single() || $page_template === 'default' || $page_template === 'elementor_theme' ) {
					wp_enqueue_style( 'jnews-single', $asset_url . 'css/single.css', null, $theme_version );
				}

				wp_enqueue_style( 'jnews-responsive', $asset_url . 'css/responsive.css', null, $theme_version );
				wp_enqueue_style( 'jnews-pb-temp', $asset_url . 'css/pb-temp.css', null, $theme_version );

				if ( class_exists( 'WooCommerce' ) ) {
					wp_enqueue_style( 'jnews-woocommerce', $asset_url . 'css/woocommerce.css', null, $theme_version );
				}

				if ( class_exists( 'bbPress' ) ) {
					wp_enqueue_style( 'jnews-bbpress', $asset_url . 'css/bbpress.css', null, $theme_version );
				}

				if ( function_exists( 'bp_is_active' ) ) {
					wp_enqueue_style( 'jnews-buddypress', $asset_url . 'css/buddypress.css', null, $theme_version );
				}
			} else {
				/** Load font first */
				if ( $this->font_preloading_enabled ) {
					wp_enqueue_style( 'font-awesome-webfont', $asset_url . 'dist/font/fontawesome-webfont.woff2', null, null );
					wp_enqueue_style( 'jnews-icon-webfont', $asset_url . 'dist/font/jegicon.woff', null, null );
					if ( function_exists( 'vc_asset_url' ) && get_theme_mod( 'jnews_enable_font_preloading_vc', false ) ) {
						wp_enqueue_style( 'vc-font-awesome-brands-webfont', vc_asset_url( 'lib/bower/font-awesome/webfonts/fa-brands-400.woff2' ), null, null );
						wp_enqueue_style( 'vc-font-awesome-regular-webfont', vc_asset_url( 'lib/bower/font-awesome/webfonts/fa-regular-400.woff2' ), null, null );
						wp_enqueue_style( 'vc-font-awesome-solid-webfont', vc_asset_url( 'lib/bower/font-awesome/webfonts/fa-solid-900.woff2' ), null, null );
					}
					if ( defined( 'ELEMENTOR_ASSETS_URL' ) && get_theme_mod( 'jnews_enable_font_preloading_elementor', false ) ) {
						wp_enqueue_style( 'elementor-font-awesome-webfont', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/fonts/fontawesome-webfont.woff2', null, '4.7.0' );
					}
				}
				wp_enqueue_style( 'jnews-frontend', $asset_url . 'dist/frontend.min.css', null, $theme_version );
			}

			if ( defined( 'WPB_VC_VERSION' ) ) {
				wp_enqueue_style( 'jnews-js-composer', $asset_url . 'css/js-composer-frontend.css', null, $theme_version );
			}

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				wp_enqueue_style( 'jnews-elementor', $asset_url . 'css/elementor-frontend.css', null, $theme_version );
			}

			wp_enqueue_style( 'jnews-style', get_stylesheet_uri(), null, $theme_version );

			wp_enqueue_style( 'jnews-darkmode', $asset_url . 'css/darkmode.css', null, $theme_version );

			$dm_options = get_theme_mod( 'jnews_dark_mode_options' );
			if ( ( $dm_options === 'jeg_device_dark' || $dm_options === 'jeg_device_toggle' ) && ! isset( $_COOKIE['darkmode'] ) ) {
				wp_enqueue_style( 'jnews-darkmode-device', $asset_url . 'css/darkmode-device.css', null, $theme_version );
			}

			if ( 'dark' === get_theme_mod( 'jnews_scheme_color', 'normal' ) ) {
				wp_enqueue_style( 'jnews-scheme-dark', $asset_url . 'css/dark.css', null, $theme_version );
			}

			if ( is_rtl() ) {
				wp_enqueue_style( 'jnews-rtl', $asset_url . 'css/rtl.css', null, $theme_version );
			}

			if ( wp_style_is( 'jnews-scheme', 'registered' ) ) {
				if ( ! $this->is_debugging ) {
					wp_enqueue_style( 'jnews-scheme' );
				} else {
					if ( is_user_logged_in() ) {
						wp_enqueue_style( 'jnews-scheme' );
					}
				}
			}
		}
	}

	public function load_additional_style() {
		if ( $this->is_debugging && wp_style_is( 'jnews-scheme', 'registered' ) ) {
			wp_enqueue_style( 'jnews-scheme' );
		}
	}

	public function load_script() {
		if ( get_theme_mod( 'jnews_google_analytics_switch', false ) ) {
			$this->maybe_enqueue_google_analytics();
		}
		if ( ! $this->is_login_page() ) {
			$asset_url     = $this->get_asset_uri();
			$theme_version = $this->get_theme_version();

			if ( is_singular() ) {
				wp_enqueue_script( 'comment-reply' );
			}
			if ( get_theme_mod( 'jnews_enable_global_mediaelement', false ) || is_user_logged_in() ) {
				wp_enqueue_script( 'wp-mediaelement' );
			}

			if ( get_theme_mod( 'jnews_single_popup_script', 'magnific' ) === 'photoswipe' ) {
				wp_enqueue_script( 'photoswipe', $asset_url . 'js/photoswipe/photoswipe.js', null, $theme_version, true );
				wp_enqueue_script( 'photoswipe-ui-default', $asset_url . 'js/photoswipe/photoswipe-ui-default.js', null, $theme_version, true );
			}

			wp_enqueue_script( 'hoverIntent' );
			wp_enqueue_script( 'imagesloaded' );
			wp_register_script( 'jnews-owlcarousel', $asset_url . 'js/owl-carousel2/owl.carousel.js', null, $theme_version, true );
			wp_register_script( 'tiny-slider', $asset_url . 'js/tiny-slider/tiny-slider.js', null, $theme_version, false );
			wp_register_script( 'tiny-slider-noconflict', $asset_url . 'js/tiny-slider/tiny-slider-noconflict.js', array( 'tiny-slider' ), $theme_version, false );
			wp_register_script( 'jscrollpane', $asset_url . 'js/jquery.jscrollpane.js', null, $theme_version, true );
			wp_register_script( 'jnews-videoplaylist', $asset_url . 'js/jnewsvidplaylist.js', array( 'jquery', 'jscrollpane', 'tiny-slider-noconflict' ), $theme_version, true );
			wp_register_script( 'jnews-owlslider', $asset_url . 'js/jowlslider.js', null, $theme_version, true );
			wp_register_script( 'jnews-newsticker', $asset_url . 'js/jnewsticker.js', array( 'jquery' ), $theme_version, null );
			wp_register_script( 'jnews-hero', $asset_url . 'js/jnewshero.js', array( 'tiny-slider-noconflict' ), $theme_version, null );
			wp_register_script( 'jnews-overlayslider', $asset_url . 'js/joverlayslider.js', array( 'tiny-slider-noconflict' ), $theme_version, null );

			if ( $this->is_debugging ) {
				/***** Code harus di sync dengan webpack config */

				wp_register_script( 'jnews-slider', $asset_url . 'js/jnewsslider.js', array( 'tiny-slider-noconflict', 'jnews-owlslider' ), $theme_version, false );
				wp_register_script( 'jnews-carousel', $asset_url . 'js/jnewscarousel.js', array( 'tiny-slider-noconflict' ), $theme_version, null );

				wp_enqueue_script( 'isotope', $asset_url . 'js/isotope.js', null, $theme_version, true );
				wp_enqueue_script( 'lazysizes', $asset_url . 'js/lazysizes.js', null, $theme_version, true );
				wp_enqueue_script( 'bgset', $asset_url . 'js/ls.bgset.js', null, $theme_version, true );
				wp_enqueue_script( 'superfish', $asset_url . 'js/superfish.js', null, $theme_version, true );
				wp_enqueue_script( 'theia-sticky-sidebar', $asset_url . 'js/theia-sticky-sidebar.js', null, $theme_version, true );
				wp_enqueue_script( 'waypoint', $asset_url . 'js/jquery.waypoints.js', null, $theme_version, true );
				wp_enqueue_script( 'scrollto', $asset_url . 'js/jquery.scrollTo.js', null, $theme_version, true );
				wp_enqueue_script( 'parallax', $asset_url . 'js/jquery.parallax.js', null, $theme_version, true );
				wp_enqueue_script( 'okaynav', $asset_url . 'js/jquery.okayNav.js', null, $theme_version, true );
				wp_enqueue_script( 'mousewheel', $asset_url . 'js/jquery.mousewheel.js', null, $theme_version, true );
				wp_enqueue_script( 'modernizr', $asset_url . 'js/modernizr-custom.js', null, $theme_version, true );
				wp_enqueue_script( 'smartresize', $asset_url . 'js/jquery.smartresize.js', null, $theme_version, true );
				wp_enqueue_script( 'chosen', $asset_url . 'js/chosen.jquery.js', null, $theme_version, true );
				wp_enqueue_script( 'magnific', $asset_url . 'js/jquery.magnific-popup.js', null, $theme_version, true );
				wp_enqueue_script( 'jnews-gif', $asset_url . 'js/jquery.jnewsgif.js', null, $theme_version, true );
				wp_enqueue_script( 'jnews-sticky', $asset_url . 'js/jquery.jsticky.js', null, $theme_version, true );
				wp_enqueue_script( 'jquery-transit', $asset_url . 'js/jquery.transit.min.js', null, $theme_version, true );
				wp_enqueue_script( 'jnews-landing-module', $asset_url . 'js/jquery.module.js', null, $theme_version, true );

				if ( is_user_logged_in() ) {
					wp_enqueue_script( 'jnews-slider' );
					wp_enqueue_script( 'jnews-newsticker' );
					wp_enqueue_script( 'jnews-videoplaylist' );
					wp_enqueue_script( 'jnews-carousel' );
					wp_enqueue_script( 'jnews-hero' );
					wp_enqueue_script( 'jnews-overlayslider' );
				}
				wp_enqueue_script( 'jnews-main', $asset_url . 'js/main.js', null, $theme_version, true );

				if ( class_exists( 'WooCommerce' ) ) {
					wp_enqueue_script( 'jnews-woocommerce', $asset_url . 'js/woocommerce.js', null, $theme_version, true );
				}

				if ( get_theme_mod( 'jnews_single_following_video', false ) ) {
					wp_enqueue_script( 'jnews-floating-video', $asset_url . 'js/floating-video.js', null, $theme_version, true );
				}

				if ( is_single() && 'post' == get_post_type() ) {
					wp_enqueue_script( 'jnews-zoom-button', $asset_url . 'js/zoom-button.js', null, $theme_version, true );
					wp_enqueue_script( 'jnews-popup-post', $asset_url . 'js/popup-post.js', null, $theme_version, true );
				}

				if ( get_theme_mod( 'jnews_sidefeed_enable', false ) ) {
					wp_enqueue_script( 'jnews-sidefeed', $asset_url . 'js/sidefeed.js', array( 'jscrollpane' ), $theme_version, true );
				}

				wp_enqueue_script( 'jnews-darkmode', $asset_url . 'js/darkmode.js', null, $theme_version, true );
				wp_localize_script( 'jnews-main', 'jnewsoption', $this->localize_script() );
			} else {
				wp_enqueue_script( 'jnews-frontend', $asset_url . 'dist/frontend.min.js', null, $theme_version, true );
				wp_localize_script( 'jnews-frontend', 'jnewsoption', $this->localize_script() );
			}

			wp_enqueue_script( 'html5shiv', $asset_url . 'js/html5shiv.min.js', null, $theme_version, true );
			wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );
		}
	}

	public function load_vc() {
		$asset_url     = $this->get_asset_uri();
		$theme_version = $this->get_theme_version();

		if ( function_exists( 'vc_is_page_editable' ) && vc_is_page_editable() ) {
			wp_enqueue_script( 'jnews-vc-page-iframe', $asset_url . 'js/vc/jnews.vc.page.iframe.js', null, $theme_version, true );
			wp_enqueue_script( 'jnews-vc-inline', $asset_url . 'js/vc/jnews.vc.inline.js', null, $theme_version, true );
		}
	}

	public function localize_script() {
		global $is_IE;
		global $wp;

		$option                   = array();
		$option['login_reload']   = home_url( $wp->request );
		$option['popup_script']   = get_theme_mod( 'jnews_single_popup_script', 'magnific' );
		$option['single_gallery'] = get_theme_mod( 'jnews_single_as_gallery', false );
		$option['ismobile']       = wp_is_mobile();
		$option['isie']           = $is_IE;
		$option['sidefeed_ajax']  = false;
		$option['language']       = jnews_get_locale();
		$option['module_prefix']  = ModuleManager::$module_ajax_prefix;

		if ( get_theme_mod( 'jnews_sidefeed_enable', false ) ) {
			$option['sidefeed_ajax'] = apply_filters( 'jnews_sidefeed_enable_ajax', get_theme_mod( 'jnews_sidefeed_enable_ajax', true ) );
		}

		$option['live_search'] = get_theme_mod( 'jnews_live_search_show', true );

		if ( is_single() && ! is_page() ) {
			$option['postid'] = get_the_ID();
			$option['isblog'] = true;
		} else {
			$option['postid'] = 0;
			$option['isblog'] = false;
		}

		if ( is_admin_bar_showing() ) {
			if ( function_exists( 'vc_is_page_editable' ) && vc_is_page_editable() ) {
				$option['admin_bar'] = 0;
			} else {
				$option['admin_bar'] = 1;
			}
		} else {
			$option['admin_bar'] = 0;
		}

		$option['follow_video']    = defined( 'JNEWS_AUTOLOAD_POST' ) ? false : get_theme_mod( 'jnews_single_following_video', false );
		$option['follow_position'] = get_theme_mod( 'jnews_single_following_video_position', 'top_right' );
		$option['rtl']             = is_rtl() ? 1 : 0;
		$option['gif']             = get_theme_mod( 'jnews_transform_gif', false );
		$option['lang']            = array(
			'invalid_recaptcha' => jnews_return_translation( 'Invalid Recaptcha!', 'jnews', 'invalid_recaptcha' ),
			'empty_username'    => jnews_return_translation( 'Please enter your username!', 'jnews', 'empty_username' ),
			'empty_email'       => jnews_return_translation( 'Please enter your email!', 'jnews', 'empty_email' ),
			'empty_password'    => jnews_return_translation( 'Please enter your password!', 'jnews', 'empty_password' ),
		);
		if ( ! empty( get_theme_mod( 'jnews_recaptcha_site_key', '' ) ) && ! empty( get_theme_mod( 'jnews_recaptcha_secret_key', '' ) ) ) {
			$option['recaptcha'] = get_theme_mod( 'jnews_enable_recaptcha_new', false ) ? 1 : 0;
		} else {
			$option['recaptcha'] = 0;
		}
		$option                = jnews_check_cookies_path( $option );
		$option['zoom_button'] = jnews_show_zoom_button() ? 1 : 0;

		return apply_filters( 'jnews_frontend_asset_localize_script', $option );
	}

	/**
	 * Load dark mode css tinyMCE
	 *
	 * @param string $mce_css   Style for tinyMCE.
	 *
	 * @return string
	 */
	public function load_mce_css( $mce_css ) {
		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}

		$mce_css .= get_theme_file_uri( '/assets/css/dark-mce.css' );

		return $mce_css;
	}

	/**
	 * Enqueue the Google Tag Manager script if prerequisites are met.
	 */
	private function maybe_enqueue_google_analytics() {
		$tracking_code = get_theme_mod( 'jnews_google_analytics_code', '' );
		$src           = apply_filters( 'jnews_google_analytics_gtag_src', 'https://www.googletagmanager.com/gtag/js?id=' . $tracking_code );

		if ( is_admin() || ! stristr( $tracking_code, 'G-' ) ) {
			return;
		}

		if ( ! wp_script_is( 'jnews-google-tag-manager', 'registered' ) ) {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			$google_tag_version = strpos( $src, 'googletagmanager' ) !== false ? null : jnews_get_option( 'local_gtag_file_modified_at' );
			wp_register_script( 'jnews-google-tag-manager', $src, array(), $google_tag_version, false );
			wp_add_inline_script(
				'jnews-google-tag-manager',
				"
				window.addEventListener('DOMContentLoaded', function() {
					(function() {
						window.dataLayer = window.dataLayer || [];
						function gtag(){dataLayer.push(arguments);}
						gtag('js', new Date());
						gtag('config', '" . esc_js( $tracking_code ) . "');
					})();
				});
				"
			);
		}
		wp_enqueue_script( 'jnews-google-tag-manager' );
	}

	/**
	 * Add defer to script tags with defined handles.
	 *
	 * @param string $tag HTML for the script tag.
	 * @param string $handle Handle of script.
	 * @param string $src Src of script.
	 * @return string
	 */
	public function filter_script_loader_tags( $tag, $handle, $src ) {
		if ( ! in_array( $handle, array( 'jnews-google-tag-manager' ), true ) ) {
			return $tag;
		}
		foreach ( array( 'defer' ) as $attr ) {
			if ( wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}

			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}

			break;
		}
		return $tag;
	}
}
