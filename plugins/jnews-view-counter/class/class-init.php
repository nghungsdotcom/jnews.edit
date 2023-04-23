<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class JNews View Counter Init
 */
class Init {
	/**
	 * @var Init
	 */
	private static $instance;

	/**
	 * @var Counter
	 */
	public $counter;

	/**
	 * @var Crawler_Detect
	 */
	public $crawler_detect;

	/**
	 * @var Frontend
	 */
	public $frontend;

	/**
	 * @var Options\Options
	 */
	public $settings;

	/**
	 * @var Dashboard
	 */
	public $dashboard;

	/**
	 * View counter options
	 *
	 * @var array
	 */
	public $options;

	/**
	 * View counter options
	 *
	 * @var array
	 */
	public $defaults = array(
		'general' => array(
			'strict_counts'       => false,
			'time_between_counts' => array(
				'number' => 0,
				'type'   => 'hours',
			),
			'exclude'             => array(
				'groups' => array( 'robots' ),
				'roles'  => array(),
			),
			'log'                 => array(
				'limit'         => false,
				'expires_after' => 180,
			),
		),
		'display' => array(),
		'config'  => array(
			'dates'         => '',
			'range'         => 'last7days',
			'time_quantity' => 24,
			'time_unit'     => 'hours',
			'limit'         => 10,
			'freshness'     => false,
			'post_type'     => 'post',
		),
	);

	/**
	 * Disable object cloning.
	 */
	public function __clone() {}

	/**
	 * Disable unserializing of the class.
	 */
	public function __wakeup() {}

	/**
	 * Instance of Init JNews View Counter
	 *
	 * @return Init
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Init ) ) {
			self::$instance = new Init();
			self::$instance->includes();
			self::$instance->counter        = new Counter();
			self::$instance->crawler_detect = new Crawler_Detect();
			self::$instance->frontend       = new Frontend();
			self::$instance->template       = new Template();
			self::$instance->settings       = new Options\Options();
			if ( is_admin() || ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'wp-cron.php' ) !== false ) ) {
				self::$instance->dashboard = new Dashboard();
			}
		}
		return self::$instance;
	}

	public function includes() {
		require_once JNEWS_VIEW_COUNTER_DIR . 'class/functions.php';
	}

	/**
	 * Construct of JNews View Counter
	 */
	private function __construct() {
		$this->options = array(
			'general' => array_merge( $this->defaults['general'], Helper::get_view_counter_option( 'general', $this->defaults['general'] ) ),
			'display' => array_merge( $this->defaults['display'], Helper::get_view_counter_option( 'display', $this->defaults['display'] ) ),
			'config'  => array_merge( $this->defaults['config'], Helper::get_view_counter_option( 'config', $this->defaults['config'] ) ),
		);
		register_activation_hook( JNEWS_VIEW_COUNTER_FILE, array( 'JNEWS_VIEW_COUNTER\Activator', 'activate' ) );
		register_deactivation_hook( JNEWS_VIEW_COUNTER_FILE, array( 'JNEWS_VIEW_COUNTER\Deactivator', 'deactivate' ) );
		$this->setup_hook();
		$this->load_plugin_text_domain();
	}

	private function setup_hook() {
		add_action( 'wp_enqueue_scripts', array( 'JNEWS_VIEW_COUNTER\Helper', 'wp_localize_vanillajs_datepicker' ), 1000 );
		add_action( 'admin_enqueue_scripts', array( 'JNEWS_VIEW_COUNTER\Helper', 'wp_localize_vanillajs_datepicker' ), 1000 );
	}

	/**
	 * Load plugin text domain
	 */
	private function load_plugin_text_domain() {
		load_plugin_textdomain( JNEWS_VIEW_COUNTER, false, basename( JNEWS_VIEW_COUNTER_DIR ) . '/languages/' );
	}

}
