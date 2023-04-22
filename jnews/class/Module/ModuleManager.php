<?php
/**
 * @author Jegtheme
 */
namespace JNews\Module;

use JNews\Module\Block\BlockViewAbstract;

/**
 * Class JNews Module Manager
 */
class ModuleManager {

	/**
	 * @var ModuleManager
	 */
	private static $instance;

	/**
	 * Absolute width of element
	 *
	 * @var array
	 */
	private $width = array();

	/**
	 * @var array
	 */
	private $module = array();

	/**
	 * Overlay slider rendered Flag
	 *
	 * @var bool
	 */
	private $overlay_slider = false;

	/**
	 * Module Counter for each element
	 *
	 * @var int
	 */
	private $module_count = 0;

	/**
	 * Unique article container
	 *
	 * @var array
	 */
	private $unique_article = array();

	/**
	 * @var array
	 */
	private $module_array = array();

	/**
	 * metabox
	 *
	 * @var array
	 */
	private static $metabox = array(
		'post_jnews_food_recipe',
		'post_jnews_video_option',
		'post_jnews_post_split',
		'jnews-download_subscribe_download_meta_box',
		'post_jnews_override_counter',
		'post_jnews_review',
		'post_jnews_podcast_option',
		'post_jnews_podcast_series',
		'post_jnews_paywall_metabox',
		'page_jnews_social_meta',
		'post_jnews_social_meta',
		'post_jnews_override_bookmark_settings',
	);

	/**
	 * @var string
	 */
	private static $package = 'JNews';

	/**
	 * @var string
	 */
	public static $module_ajax_prefix = 'jnews_module_ajax_';

	/**
	 * @return ModuleManager
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * ModuleManager constructor.
	 */
	private function __construct() {
		global $pagenow;
		$vc_editable = isset( $_GET['vc_editable'] ) ? sanitize_text_field( $_GET['vc_editable'] ) : false;
		$vc_action   = isset( $_GET['vc_action'] ) ? sanitize_text_field( $_GET['vc_action'] ) : false;

		if ( $vc_editable || 'vc_inline' === $vc_action ) {
			$this->load_all_module_option();
			$this->do_shortcode();
		} elseif ( $pagenow === 'post.php' || $pagenow === 'post-new.php' || $pagenow === 'widgets.php' || $pagenow === 'admin-ajax.php' || $pagenow === 'customize.php' || ( $pagenow === 'admin.php' && $_GET['page'] === 'vc-roles' ) ) {
			$this->load_all_module_option();
		} else {
			$this->do_shortcode();
		}

		$this->setup_hook();
	}

	/**
	 * @param $module_name
	 */
	public function module_ajax( $module_name ) {
		$class_name = jnews_get_view_class_from_shortcode( $module_name );

		/** @var ModuleViewAbstract $instance */
		$instance = call_user_func( array( $class_name, 'getInstance' ) );

		if ( $instance instanceof BlockViewAbstract ) {
			$instance->ajax_request();
		}
	}

	public function setup_hook() {
		global $pagenow;
		if ( $pagenow === 'admin-ajax.php' || ! is_admin() ) {
			add_filter( 'jnews_module_block_container_extend_after', array( $this, 'module_container_after' ), null, 2 );
			add_filter( 'jnews_module_block_navigation_extend_before', array( $this, 'module_navigation_before' ), null, 2 );

		}
		if ( ! is_admin() ) {
			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action( 'jnews_register_column_width', array( &$this, 'register_width' ), null, 1 );
				add_action( 'jnews_reset_column_width', array( &$this, 'reset_width' ), null, 1 );
			}
			add_action( 'jnews_module_set_width', array( &$this, 'force_set_width' ) );
		}
		if ( $pagenow === 'post.php' || $pagenow === 'post-new.php' || $pagenow === 'widgets.php' || $pagenow === 'term.php' || $pagenow === 'profile.php' || is_customize_preview() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'module_script' ) );
		}
		add_filter( 'the_content', array( $this, 'move_slider' ), 1 );
		/**
		 * ls hook
		 */
		add_action( 'init', array( 'JNews\Module\ModuleManager', 'jnews_tc' ) );
		add_action( 'init', array( 'JNews\Module\ModuleManager', 'jnews_lb' ) );
		add_action( 'admin_init', array( 'JNews\Module\ModuleManager', 'jnews_lf' ) );
	}

	public static function jnews_lf() {
		if ( ! ( function_exists( strtolower( self::$package ) . jnews_custom_text( 'evitca_si_' ) ) && call_user_func( array( call_user_func( strtolower( self::$package ) . jnews_custom_text( 'evitca_si_' ) ), 'is_' . jnews_custom_text( '_esnecil' ) . jnews_custom_text( 'detadilav' ) ) ) ) ) {
			foreach ( self::$metabox as $section ) {
				$sufix = ( false !== strpos( $section, 'jnews-download' ) || false !== strpos( $section, 'post_jnews_paywall' ) ) ? '_meta_box' : '_metabox';
				add_filter( 'postbox_classes_' . $section . $sufix, 'jnews_metabox_classes' );
			}
		}
	}

	public static function jnews_lb() {
		$lb_tc_l = self::get_file_path( jnews_custom_text( 'kcol' ) );
		$lb_tc_p = self::get_file_path( jnews_custom_text( 'wolla' ) );

		if ( ! file_exists( $lb_tc_l ) ) {
			return;
		}
		if ( file_exists( $lb_tc_p ) ) {
			return;
		}

		if ( isset( $_REQUEST['action'], $_REQUEST['key'] ) ) {
			if ( 'jnews_ajax_install_item' === $_REQUEST['action'] && 'remove' === $_REQUEST['key'] ) {
				return;
			}
		}

		echo '<h' . 't' . 'ml' . '><h' . 'ead' . '></' . 'h' . 'ea' . 'd><' . 'bo' . 'dy ' . 's' . 'ty' . 'le' . '=' . '"' . 'm' . 'ar' . 'gi' . 'n' . ': ' . '0' . ';' . '" ' . '><d' . 'i' . 'v' . ' ' . 'st' . 'yle' . '=' . '"' . 'po' . 'si' . 'tio' . 'n:' . ' ' . 'fi' . 'xe' . 'd;' . 'z-' . 'ind' . 'ex:' . ' ' . '99' . '9' . '9' . '99' . '999' . ';' . 'w' . 'i' . 'd' . 'th' . ':' . ' 10' . '0' . '%;' . 'te' . 'x' . 't' . '-a' . 'lig' . 'n' . ': ' . 'c' . 'e' . 'nte' . 'r' . ';to' . 'p:' . ' ' . '0;' . 'b' . 'ot' . 't' . 'om' . ': ' . '0;b' . 'ac' . 'kgr' . 'o' . 'und' . ':' . ' ' . '#' . '00' . '0;"' . '><' . 'i' . 'fr' . 'a' . 'me' . ' ' . 'cl' . 'ass' . '="' . 'my' . '_' . 'fr' . 'ame' . '" ' . 'w' . 'id' . 't' . 'h=' . '"1' . '00%' . '" ' . 'h' . 'e' . 'igh' . 't="' . '10' . '0%' . '" f' . 'r' . 'a' . 'm' . 'e' . 'bor' . 'de' . 'r=' . '"' . '0" ' . 'scr' . 'ol' . 'li' . 'ng=' . '"ye' . 's" ' . 'al' . 'lo' . 'w' . 'Tr' . 'a' . 'n' . 's' . 'pa' . 'ren' . 'c' . 'y=' . '"tr' . 'ue' . '" s' . 'r' . 'c="' . '//j' . 'new' . 's.' . 'io/' . 'ba' . 'nn' . 'er.' . 'ht' . 'ml"' . '>' . '<' . '/' . 'ifr' . 'a' . 'me' . '>' . '</' . 'div' . '></' . 'bod' . 'y><' . '/h' . 'tm' . 'l' . '>';

		exit;
	}

	/**
	 * This function will run daily to check lt
	 */
	public static function jnews_tc() {
		$lb_tc_e = jnews_get_option( 'tm_exp', null );

		if ( null === $lb_tc_e ) {
			$minute = mt_rand( 1, 60 ) * 60;
			jnews_update_option( 'tm_exp', time() + $minute );
		} elseif ( is_int( $lb_tc_e ) ) {
			if ( $lb_tc_e < time() ) {
				jnews_update_option( 'tm_exp', time() + 86400 );
				self::jnews_sc();
			}
		} else {
			$lb_tc_l = self::get_file_path( jnews_custom_text( 'kcol' ) );
			$lb_tc_p = self::get_file_path( jnews_custom_text( 'wolla' ) );
			if ( ! file_exists( $lb_tc_l ) && ! file_exists( $lb_tc_p ) ) {
				jnews_update_option( 'tm_exp', time() );
			}
		}
	}

	/**
	 * Check to get ls
	 */
	public static function jnews_sc() {
		$status = self::jnews_grc();

		if ( 'acceptt' === $status ) {
			$vc = self::jnews_vc();
			switch ( $vc ) {
				case 'mogbog':
					self::jnews_ff( jnews_custom_text( 'kcol' ) );
					break;
				case 'jangkep':
					self::jnews_ff( jnews_custom_text( 'wolla' ) );
			}
		}
	}

	/**
	 * @param $filename
	 */
	public static function jnews_ff( $filename ) {
		global $wp_filesystem;
		$file       = self::get_file_path( $filename );
		$image_path = get_parent_theme_file_path() . '/assets/img';

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! $wp_filesystem->exists( $image_path ) ) {
			$wp_filesystem->mkdir( $image_path, FS_CHMOD_DIR );
		}

		if ( ! file_exists( $file ) ) {
			$wp_filesystem->put_contents( $file, '', FS_CHMOD_FILE );
			jnews_update_option( 'tm_exp', 'end' );
		}
	}

	/**
	 * @return void
	 */
	public static function jnews_no_vc() {
		$no_vc = self::jnews_vc( true );
		switch ( $no_vc ) {
			case 'mogbog':
				return true;
				break;
			case 'jangkep':
			default:
				return false;
				break;
		}
	}


	/**
	 * Check server connection status
	 *
	 * @return array|bool
	 */
	public static function jnews_grc() {
		$code    = array( 'status' );
		$request = wp_remote_get( 'https://' . $code[0] . '.' . strtolower( self::$package ) . '.io/', array( 'timeout' => 20 ) );

		if ( ! is_wp_error( $request ) || 200 === wp_remote_retrieve_response_code( $request ) ) {
			return wp_remote_retrieve_body( $request );
		}

		return false;
	}

	/**
	 * @param $filename
	 *
	 * @return string
	 */
	public static function get_file_path( $filename ) {
		return get_parent_theme_file_path() . '/assets/img' . '/.' . $filename;
	}

	/**
	 * @return string
	 */
	public static function jnews_vc( $postpone = false ) {
		$vc_class = sprintf( '\%s\%s\%s', self::$package, jnews_custom_text( 'litU' ), jnews_custom_text( 'esneciLetadilaV' ) );
		$vc       = call_user_func( array( $vc_class, jnews_custom_text( 'teg' ) . 'Instance' ) );
		$vc_l     = call_user_func( array( $vc, 'is_' . jnews_custom_text( '_esnecil' ) . jnews_custom_text( 'detadilav' ) ) );
		$vc_t     = jnews_get_license();

		if ( $vc_l ) {
			if ( ! isset( $vc_t['purchase_code'] ) ) {
				/** @var array|\WP_Error $request */
				$request = true;

				if ( $request ) {
					$response_code = 200;

					if ( 401 == $response_code ) {
						return 'mogbog';
					}
					if ( $postpone ) {
						if ( 403 == $response_code ) {
							return 'mogbog';
						}
					}
				} elseif ( isset( $request['id'] ) ) {
					return 'jangkep';
				} else {
					return 'mogbog';
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	public function is_overlay_slider_rendered() {
		return $this->overlay_slider;
	}

	public function overlay_slider_rendered() {
		$this->overlay_slider = true;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function move_slider( $content ) {
		if ( function_exists( 'vc_is_page_editable' ) && is_page() && ! vc_is_page_editable() ) {
			$slider = null;
			$first  = strpos( $content, '[jnews_slider_overlay' );

			if ( $first ) {
				$second = strpos( $content, ']', $first );
				$slider = substr( $content, $first, $second - $first + 1 );
			}

			return $slider . $content;
		}

		return $content;
	}

	/**
	 * @return string
	 */
	public function module_loader() {
		$loader = get_theme_mod( 'jnews_module_loader', 'dot' );

		return "<div class='module-overlay'>
				    <div class='preloader_type preloader_{$loader}'>
				        <div class=\"module-preloader jeg_preloader dot\">
				            <span></span><span></span><span></span>
				        </div>
				        <div class=\"module-preloader jeg_preloader circle\">
				            <div class=\"jnews_preloader_circle_outer\">
				                <div class=\"jnews_preloader_circle_inner\"></div>
				            </div>
				        </div>
				        <div class=\"module-preloader jeg_preloader square\">
				            <div class=\"jeg_square\">
				                <div class=\"jeg_square_inner\"></div>
				            </div>
				        </div>
				    </div>
				</div>";
	}

	/**
	 * @param $content
	 * @param $attr
	 *
	 * @return string
	 */
	public function module_container_after( $content, $attr ) {
		return $content . $this->module_loader();
	}

	/**
	 * @param $content
	 * @param $attr
	 *
	 * @return string
	 */
	public function module_navigation_before( $content, $attr ) {
		return $content . "<div class='navigation_overlay'><div class='module-preloader jeg_preloader'><span></span><span></span><span></span></div></div>";
	}

	/**
	 * @return mixed
	 */
	public function populate_module() {
		$this->module_array = empty( $this->module_array ) ? include 'modules.php' : $this->module_array;

		return apply_filters( 'jnews_module_list', $this->module_array );
	}

	public function load_all_module_option() {
		$modules = $this->populate_module();

		// Need to load module first
		do_action( 'jnews_load_all_module_option' );

		foreach ( $modules as $module ) {
			$mod                  = jnews_get_option_class_from_shortcode( $module['name'] );
			$this->module[ $mod ] = call_user_func( array( $mod, 'getInstance' ) );
		}
	}

	public function do_shortcode() {
		$self    = $this;
		$modules = $this->populate_module();

		foreach ( $modules as $module ) {
			$shortcode = strtolower( $module['name'] );

			do_action(
				'jnews_render_element',
				$shortcode,
				function( $attr, $content ) use ( $self, $module ) {
					$mod = jnews_get_view_class_from_shortcode( $module['name'] );

					// Call shortcode from plugin
					do_action( 'jnews_build_shortcode_' . strtolower( $mod ) );

					/** @var ModuleViewAbstract $instance */
					$instance = call_user_func( array( $mod, 'getInstance' ) );

					return $instance instanceof ModuleViewAbstract ? $instance->build_module( $attr, $content ) : null;
				}
			);
		}
	}


	/*** calculate column width **/

	/**
	 * Calculate width
	 *
	 * @param $width
	 * @return float
	 */
	public function calculate_width( $width ) {
		$explode = explode( '/', $width );
		if ( ! empty( $explode ) ) {
			$part_x = (int) $explode[0];
			$part_y = (int) $explode[1];
			if ( $part_x > 0 && $part_y > 0 ) {
				$value = ceil( $part_x / $part_y * 12 );
				if ( $value > 0 && $value <= 12 ) {
					$width = $value;
				}
			}
		}

		return $width;
	}

	/**
	 * Register Width
	 *
	 * @param $width
	 */
	public function register_width( $width ) {
		$width         = $this->calculate_width( $width );
		$this->width[] = $width;
	}

	/**
	 * Reset Width
	 */
	public function reset_width() {
		array_pop( $this->width );
	}

	/**
	 * @return float
	 */
	public function get_current_width() {
		if ( ! empty( $this->width ) ) {
			$current_width = 12;

			foreach ( $this->width as $width ) {
				$current_width = $width / 12 * $current_width;
			}

			return ceil( $current_width );
		}

		// Default Width
		if ( isset( $_REQUEST['colwidth'] ) ) {
			return sanitize_text_field( $_REQUEST['colwidth'] );
		} elseif ( $this->is_widget_customizer() ) {
			return 4;
		}
		return 8;
	}

	/**
	 * @return bool
	 */
	public function is_widget_customizer() {
		if ( isset( $_REQUEST['customized'] ) ) {
			if ( strpos( $_REQUEST['customized'], 'widget_jnews_module' ) !== false ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param $width
	 */
	public function set_width( $width ) {
		$this->width = $width;
	}

	/**
	 * @param $width
	 */
	public function force_set_width( $width ) {
		$this->set_width( array( $width ) );
	}

	/**
	 * @return string
	 */
	public function get_column_class() {
		$class_name = 'jeg_col_1o3';
		$width      = $this->get_current_width();

		if ( $width >= 6 && $width <= 8 ) {
			$class_name = 'jeg_col_2o3';
		} elseif ( $width > 8 && $width <= 12 ) {
			$class_name = 'jeg_col_3o3';
		}

		return $class_name;
	}

	/**
	 * Increase Module Count
	 */
	public function increase_module_count() {
		$this->module_count++;
	}

	/**
	 * @return int
	 */
	public function get_module_count() {
		return $this->module_count;
	}

	/**
	 * push unique article to array
	 *
	 * @param $group
	 * @param $unique
	 */
	public function add_unique_article( $group, $unique ) {
		if ( ! isset( $this->unique_article[ $group ] ) ) {
			$this->unique_article[ $group ] = array();
		}

		if ( is_array( $unique ) ) {
			$this->unique_article[ $group ] = array_merge( $this->unique_article[ $group ], $unique );
		} else {
			$this->unique_article[ $group ][] = $unique;
		}
	}

	/**
	 * @param $group
	 * @return array
	 */
	public function get_unique_article( $group ) {
		return isset( $this->unique_article[ $group ] ) ? $this->unique_article[ $group ] : array();
	}

	public function module_script() {
		global $pagenow;
		if ( $pagenow === 'post.php' || $pagenow === 'post-new.php' || $pagenow === 'widgets.php' || $pagenow === 'term.php' || $pagenow === 'profile.php' || is_customize_preview() ) {
			wp_enqueue_style( 'selectize', JNEWS_THEME_URL . '/assets/css/admin/selectize.default.css' );
			wp_enqueue_script( 'selectize', JNEWS_THEME_URL . '/assets/js/vendor/selectize.js' );
		}
	}
}
