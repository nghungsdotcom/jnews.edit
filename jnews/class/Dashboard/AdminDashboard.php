<?php
/**
 * Dashboard Class
 *
 * @author Jegtheme
 */

namespace JNews\Dashboard;

use JNews\Util\Api\Plugin;
use JNews\Util\RestAPI;
use JNews\Util\ValidateLicense;

/**
 * Class Init
 *
 * @package JNews\Dashboard
 */
class AdminDashboard {
	/**
	 * Type
	 *
	 * @var string
	 */
	const TYPE = 'jnews-panel';

	/**
	 * Instance of Dashboard class
	 *
	 * @var Dashboard
	 */
	private static $instance;

	/**
	 * @var SystemDashboard
	 */
	private $system;

	/**
	 * @var PluginDashboard
	 */
	private $plugin;

	/**
	 * @var ImportDashboard
	 */
	private $import;

	/**
	 * @var \JNews\Template
	 */
	private $template;

	private $register_location = array(
		'toplevel_page_jnews',
		'appearance_page_jnews',
	);

	/**
	 * Instance of Dashboard
	 *
	 * @return Dashboard
	 */
	public static function getInstance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init constructor.
	 */
	private function __construct() {
		$revert_dashboard = apply_filters( 'jnews_revert_dashboard', false );
		if ( $revert_dashboard ) {
			add_filter( 'jnews_get_admin_slug', array( &$this, 'admin_slug' ) );
		}

		if ( is_admin() ) {
			$this->setup_init();
			$this->admin_menu();
			$this->setup_hook();
		}
	}

	/**
	 * Setup Init
	 */
	private function setup_init() {
		$revert_dashboard = apply_filters( 'jnews_revert_dashboard', false );
		$this->template   = new \JNews\Template( JNEWS_THEME_DIR . 'class/Dashboard/template/' );

		if ( $revert_dashboard ) {
			global $pagenow;
			if ( 'admin.php' === $pagenow || 'themes.php' === $pagenow || 'admin-ajax.php' === $pagenow ) {
				$this->system = new SystemDashboard( $this->template );
				$this->import = new ImportDashboard( $this->template );
			}
			$this->plugin = new PluginDashboard( $this->template );
		}
	}

	/**
	 * Setup Hook
	 */
	private function setup_hook() {
		add_filter( 'jnews_get_admin_slug', array( &$this, 'admin_slug' ) );
		add_action( 'after_switch_theme', array( &$this, 'switch_themes' ), 99 );

		add_action( 'vp_before_render_set', array( &$this, 'render_header' ) );
		add_filter( 'jnews_get_admin_menu', array( &$this, 'get_admin_menu' ) );

		add_action( 'in_admin_header', array( $this, 'remove_notice' ), 1000 );
		add_action( 'admin_menu', array( $this, 'parent_menu' ) );
		add_action( 'admin_menu', array( $this, 'child_menu' ) );
		add_filter( 'admin_footer_text', '__return_empty_string', 11 );
		add_filter( 'update_footer', '__return_empty_string', 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function admin_menu() {
		add_action( 'admin_menu', array( $this, 'parent_menu' ) );
		add_action( 'admin_menu', array( $this, 'child_menu' ) );
	}

	/**
	 * Switch Themes
	 */
	public function switch_themes() {
		$slug = $this->admin_slug();
		global $pagenow;

		if ( is_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=' . $slug['dashboard'] ) );
			exit;
		}
	}

	/**
	 * Admin slug
	 *
	 * @return array
	 */
	public function admin_slug() {
		$admin_slug = array(
			'dashboard'     => 'jnews',
			'plugin'        => 'jnews_plugin',
			'import'        => 'jnews_import',
			'documentation' => 'jnews_documentation',
			'system'        => 'jnews_system',
			'option'        => 'jnews_option',
		);

		return apply_filters( 'jnews_admin_slug', $admin_slug );
	}

	/**
	 * Render Header Tab
	 */
	public function render_header() {
		$this->template->render( 'admin-header-tab', null, true );
	}

	/**
	 * Get admin menu
	 *
	 * @return array
	 */
	public function get_admin_menu() {
		$revert_dashboard = apply_filters( 'jnews_revert_dashboard', false );
		$slug             = $this->admin_slug();
		$admin_url        = defined( 'JNEWS_ESSENTIAL' ) ? 'admin.php' : 'themes.php';
		$menu             = array(
			array(
				'title'        => esc_html__( 'Dashboard', 'jnews' ),
				'menu'         => esc_html__( 'JNews Dashboard', 'jnews' ),
				'slug'         => $slug['dashboard'],
				'action'       => $revert_dashboard ? array( &$this, 'dashboard_landing' ) : array( &$this, 'load_jnews_dashboard' ),
				'priority'     => 51,
				'show_on_menu' => true,
			),
			array(
				'title'        => esc_html__( 'Import Demo & Style', 'jnews' ),
				'menu'         => esc_html__( 'Import Demo & Style', 'jnews' ),
				'slug'         => $revert_dashboard ? $slug['import'] : $slug['dashboard'] . '&path=' . $slug['import'],
				'action'       => $revert_dashboard ? array( &$this, 'import_content' ) : array( &$this, 'load_jnews_dashboard' ),
				'priority'     => 53,
				'show_on_menu' => true,
			),
			array(
				'title'        => esc_html__( 'Install Plugin', 'jnews' ),
				'menu'         => esc_html__( 'Install Plugin', 'jnews' ),
				'slug'         => $revert_dashboard ? $slug['plugin'] : $slug['dashboard'] . '&path=' . $slug['plugin'],
				'action'       => $revert_dashboard ? array( &$this, 'install_plugin' ) : array( &$this, 'load_jnews_dashboard' ),
				'priority'     => 52,
				'show_on_menu' => true,
			),
			array(
				'title'        => esc_html__( 'Customize Style', 'jnews' ),
				'menu'         => esc_html__( 'Customize Style', 'jnews' ),
				'slug'         => 'customize.php',
				'action'       => false,
				'priority'     => 55,
				'show_on_menu' => true,
			),
			array(
				'title'        => esc_html__( 'System Status', 'jnews' ),
				'menu'         => esc_html__( 'System Status', 'jnews' ),
				'slug'         => $revert_dashboard ? $slug['system'] : $slug['dashboard'] . '&path=' . $slug['system'],
				'action'       => $revert_dashboard ? array( &$this, 'system_status' ) : array( &$this, 'load_jnews_dashboard' ),
				'priority'     => 57,
				'show_on_menu' => true,
			),
			array(
				'title'        => esc_html__( 'Video Documentation', 'jnews' ),
				'menu'         => esc_html__( 'Video Documentation', 'jnews' ),
				'slug'         => $revert_dashboard ? $slug['documentation'] : $slug['dashboard'] . '&path=' . $slug['documentation'],
				'action'       => $revert_dashboard ? array( &$this, 'documentation' ) : array( &$this, 'load_jnews_dashboard' ),
				'priority'     => 56,
				'show_on_menu' => true,
			),
		);

		return apply_filters( 'jnews_admin_menu', $menu );
	}

	public function parent_menu() {
		do_action(
			'jnews_admin_dashboard_parent',
			array(
				esc_html__( 'JNews', 'jnews' ),
				esc_html__( 'JNews', 'jnews' ),
				'edit_theme_options',
				'jnews',
				null,
				'none',
				3.001,
			)
		);
	}

	public function child_menu() {
		$revert_dashboard = apply_filters( 'jnews_revert_dashboard', false );
		$self             = $this;
		$menus            = $this->get_admin_menu();
		$slug             = $this->admin_slug();

		foreach ( $menus as $menu ) {
			if ( $revert_dashboard ) {
				if ( $menu['show_on_menu'] ) {
					if ( $menu['action'] ) {
						do_action(
							'jnews_admin_dashboard_child',
							array(
								'jnews',
								$menu['title'],
								$menu['menu'],
								'edit_theme_options',
								$menu['slug'],
								function () use ( $self, $menu ) {
										$self->render_header();
										call_user_func( $menu['action'] );
								},
							)
						);
					} else {
						do_action(
							'jnews_admin_dashboard_child',
							array(
								'jnews',
								$menu['title'],
								$menu['menu'],
								'edit_theme_options',
								$menu['slug'],
							)
						);
					}
				}
			} else {
				if ( $menu['show_on_menu'] ) {
					if ( $menu['action'] ) {
						if ( ! is_string( $menu['action'] ) ) {
							do_action(
								'jnews_admin_dashboard_child',
								array(
									'jnews',
									$menu['title'],
									$menu['menu'],
									'edit_theme_options',
									$menu['slug'],
									function () use ( $self, $menu, $slug ) {
										if ( $menu['show_on_menu'] && $slug['dashboard'] !== $menu['slug'] ) {
											$self->render_header();
										}
										call_user_func( $menu['action'] );
									},
								)
							);
						} else {
							do_action(
								'jnews_admin_dashboard_child',
								array(
									'jnews',
									$menu['title'],
									$menu['menu'],
									'edit_theme_options',
									$menu['action'],
									'',
								)
							);
						}
					} else {
						do_action(
							'jnews_admin_dashboard_child',
							array(
								'jnews',
								$menu['title'],
								$menu['menu'],
								'edit_theme_options',
								$menu['slug'],
							)
						);
					}
				}
			}
		}

	}

	/**
	 * Remove notice only on JNews dashboard
	 */
	public function remove_notice() {
		if ( in_array( get_current_screen()->id, $this->register_location, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @param string $hook .
	 */
	public function enqueue_scripts( $hook ) {
		if ( in_array( $hook, $this->register_location, true ) ) {
			$include = include JNEWS_THEME_DIR . '/lib/dependencies/dashboard.asset.php';
			wp_enqueue_script(
				self::TYPE . '-dashboard',
				JNEWS_THEME_URL . '/assets/js/admin/dashboard.js',
				$include['dependencies'],
				JNEWS_THEME_VERSION,
				true
			);
			wp_localize_script( self::TYPE . '-dashboard', 'JNewsDashboard', self::jnews_dashboard() );
			wp_set_script_translations( self::TYPE . '-dashboard', 'jnews' );

			wp_enqueue_style(
				self::TYPE . '-dashboard',
				JNEWS_THEME_URL . '/assets/css/admin/dashboard.css',
				null,
				JNEWS_THEME_VERSION
			);
		}
	}

	/**
	 * Get admin dashboard menu
	 *
	 * @return array
	 */
	public static function get_dashboard_menu() {
		$allmenu = apply_filters( 'jnews_get_admin_menu', array() );
		$menus   = array();
		foreach ( $allmenu as $menu ) {
			$plugin  = isset( $menu['plugin'] ) ? $menu['plugin'] : false;
			$pageurl = menu_page_url( $menu['slug'], false );
			if ( 'customize.php' === $menu['slug'] ) {
				$pageurl = admin_url() . 'customize.php';
			}
			if ( 'jnews' === $menu['slug'] ) {
				$pageurl = '';
			}
			$menus[] = array(
				'name'   => $menu['slug'],
				'title'  => $menu['title'],
				'url'    => $pageurl,
				'plugin' => $plugin,
			);
		}
		return $menus;
	}

	/**
	 * Get theme detail information
	 *
	 * @return array
	 */
	public static function get_theme_info() {
		$theme = wp_get_theme();
		$data  = array(
			'name'    => $theme->get( 'Name' ),
			'version' => $theme->get( 'Version' ),
		);
		if ( $theme->parent() && null !== $theme->parent() ) {
			$data['parentName']    = $theme->parent()->get( 'Name' );
			$data['parentVersion'] = $theme->parent()->get( 'Version' );
		}
		return $data;
	}

	/**
	 * JNews Dashboard Config
	 *
	 * @return array
	 */
	public static function jnews_dashboard() {
		$config = array();
		// Theme data.
		$config['demoData']    = ( new ImportDashboard( '' ) )->jnews_dashboard_config();
		$config['themeInfo']   = self::get_theme_info();
		$config['menus']       = self::get_dashboard_menu();
		$config['licenseData'] = ValidateLicense::getInstance()->jnews_dashboard_config();

		// Theme additional data.
		$config['userId']      = get_current_user_id();
		$config['currentTime'] = ( new \DateTime() )->getTimestamp();

		// Theme URL.
		$config['nonceAPI']    = wp_create_nonce( 'wp_rest' );
		$config['endpointAPI'] = '/wp-json/' . RestAPI::ENDPOINT;
		$config['themeURL']    = JNEWS_THEME_URL;

		// Plugin.
		$config['pluginData'] = Plugin::jnews_dashboard_config();

		// Site URL.
		$config['adminURL']  = untrailingslashit( get_admin_url() );
		$config['domainURL'] = home_url();

		// External URL.
		$config['JegthemeServerURL'] = JEGTHEME_SERVER;
		$config['JNewsServerURL']    = JNEWS_THEME_SERVER;

		return $config;
	}

	/**
	 * Load JNews Dashboard Page
	 */
	public function load_jnews_dashboard() {
		?>
		<div id="jnews-dashboard"></div>
		<?php
	}


	/**
	 * START Revert Dashboard
	 */
	public function dashboard_landing() {
		$this->template->render( 'dashboard-landing', null, true );
	}

	public function documentation() {
		$this->template->render( 'documentation', null, true );
	}

	public function system_status() {
		$this->system->system_status();
	}

	public function import_content() {
		$this->import->import_view();
	}

	public function install_plugin() {
		$this->plugin->install_plugin();
	}
	/**
	 * END Revert Dashboard
	 */
}
