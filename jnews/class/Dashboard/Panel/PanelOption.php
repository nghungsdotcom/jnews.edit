<?php
/**
 * Panel Option
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel
 */

namespace JNews\Dashboard\Panel;

use JNews\Dashboard\Panel\Option\OptionControlFieldImpexp;
use JNews\Dashboard\Panel\Option\OptionControlFieldRestore;
use JNews\Dashboard\Panel\Option\OptionControlGroupMenu;
use JNews\Dashboard\Panel\Option\OptionControlGroupSection;

/**
 * Class used for managing option.
 */
class PanelOption {

	/**
	 * Option Key
	 *
	 * @var string
	 */
	private $option_key;

	/**
	 * Template
	 *
	 * @var string|array
	 */
	private $template;

	/**
	 * Is Dev Mode
	 *
	 * @var null|boolean
	 */
	private $is_dev_mode;

	/**
	 * Use util menu
	 *
	 * @var null|boolean
	 */
	private $use_util_menu;

	/**
	 * Use auto group naming
	 *
	 * @var null|boolean
	 */
	private $use_auto_group_naming;

	/**
	 * Minimum role
	 *
	 * @var string
	 */
	private $minimum_role;

	/**
	 * Layout
	 *
	 * @var string
	 */
	private $layout;

	/**
	 * Options set
	 *
	 * @var null|Option\OptionControlSet
	 */
	private $options_set = null;

	/**
	 * Options
	 *
	 * @var mixed|array
	 */
	private $options = null;

	/**
	 * Pool
	 *
	 * @var PanelOption
	 */
	public static $pool;

	/**
	 * Construction of PanelOption
	 *
	 * @param array $configs Config for panel.
	 * @throws \Exception Throw if error.
	 */
	public function __construct( array $configs ) {

		// Extract and set default value.
		$panel_config = array_merge(
			array(
				'is_dev_mode'           => false,
				'use_auto_group_naming' => true,
				'use_util_menu'         => true,
				'minimum_role'          => 'edit_theme_options',
				'layout'                => 'fixed',
			),
			$configs
		);

		// Check and set required configs.
		if ( isset( $panel_config['option_key'] ) ) {
			$this->set_option_key( $panel_config['option_key'] );
		} else {
			throw new \Exception( 'Option Key is required', 1 );
		}
		if ( isset( $panel_config['template'] ) ) {
			$this->set_template( $panel_config['template'] );
		} else {
			throw new \Exception( 'Template Array/File is required', 1 );
		}

		// Swim in the pool.
		self::$pool[ $this->get_option_key() ] = &$this;

		// Check and set the remaining configs.
		if ( isset( $panel_config['is_dev_mode'] ) ) {
			$this->is_dev_mode( $panel_config['is_dev_mode'] );
		}
		if ( isset( $panel_config['use_util_menu'] ) ) {
			$this->use_util_menu( $panel_config['use_util_menu'] );
		}
		if ( isset( $panel_config['use_auto_group_naming'] ) ) {
			$this->use_auto_group_naming( $panel_config['use_auto_group_naming'] );
		}
		if ( isset( $panel_config['minimum_role'] ) ) {
			$this->set_minimum_role( $panel_config['minimum_role'] );
		}
		if ( isset( $panel_config['layout'] ) ) {
			$this->set_layout( $panel_config['layout'] );
		}

		// Add first_activation hook to save initial values to db.
		add_action( 'jnews_panel_option_first_activation', array( $this, 'initial_db_setup' ) );

		// Check if option key not existed init data from default values.
		$options = \JNews\Dashboard\Panel\Panel::get_panel_option( $this->get_option_key() );
		if ( false === $options ) {
			do_action( 'jnews_panel_option_first_activation' );
		}

		// Init options from db and expose to the api.
		$this->init_options_from_db();

		$this->setup();

		add_filter( 'jnews_panel_request_save', array( $this, 'request_save' ), 10, 3 );
		add_filter( 'jnews_panel_request_restore', array( $this, 'request_restore' ), 10, 2 );
		add_filter( 'jnews_panel_request_import_option', array( $this, 'request_import_option' ), 10, 3 );
		add_filter( 'jnews_panel_request_export_option', array( $this, 'request_export_option' ), 10, 2 );
	}

	/**
	 * Get pool
	 *
	 * @return PanelOption|array
	 */
	public static function get_pool() {
		return self::$pool;
	}

	/**
	 * Init options from DB
	 */
	public function init_options_from_db() {
		$options = \JNews\Dashboard\Panel\Panel::get_panel_option( $this->get_option_key() );
		if ( false !== $options ) {
			$this->set_options( $options );
		}
	}

	/**
	 * Setup
	 */
	public function setup() {
		$this->init_options_set();
		$this->init_options();
	}

	/**
	 * Save and reinit
	 */
	public function save_and_reinit() {
		// Do saving.
		$result = $this->get_options_set()->save( $this->get_option_key() );
		// Re-init $opt.
		$this->init_options_from_db();

		return $result;
	}

	/**
	 * Verify nonce
	 *
	 * @param string $nonce Panel nonce.
	 *
	 * @return array
	 */
	public function verify_nonce( $nonce ) {
		$result = array();
		$verify = wp_verify_nonce( $nonce, 'vafpress' );
		if ( $verify ) {
			$result['status']  = true;
			$result['message'] = __( 'Successful', 'jnews' );
		} else {
			$result['status']  = false;
			$result['message'] = __( 'Unverified Access', 'jnews' );
		}

		return $result;
	}

	/**
	 * Request save handler
	 *
	 * @param array  $option List options Panel.
	 * @param string $action Get options action.
	 * @param string $nonce Get nonce action.
	 *
	 * @return array
	 */
	public function request_save( $option, $action, $nonce ) {
		$result = $this->verify_nonce( $nonce );
		if ( $this->get_option_key() === $action ) {
			if ( $result['status'] ) {
				$this->init_options_set();
				$this->init_options();

				$option = \JNews\Dashboard\Panel\Panel::unite( $option, 'name', 'value' );
				$option = $this->get_options_set()->normalize_values( $option );

				// Stripslashes added by WP in $_GET / $_POST.
				$option = stripslashes_deep( $option );

				// Get old options from set.
				$old_opt = $this->get_options_set()->get_values();

				$this->get_options_set()->populate_values( $option, true );

				// Get back options from set.
				$opt = $this->get_options_set()->get_values();

				// Before ajax save action hook.
				do_action( 'jnews_panel_before_request_save', $opt );

				// Save and re-init options.
				$result = $this->save_and_reinit();

				// After ajax save action hook.
				do_action( 'jnews_panel_after_request_save', $opt, $old_opt, $result['status'], $this->get_option_key() );

				// Option key specific after ajax save action hook.
				do_action( 'jnews_panel_after_request_save_' . $this->get_option_key(), $opt, $old_opt, $result['status'] );
			}
		} else {
			return $option;
		}
		return $result;
	}

	/**
	 * Request restore handler
	 *
	 * @param string $action Get options action.
	 * @param string $nonce Get nonce action.
	 *
	 * @return array|string
	 */
	public function request_restore( $action, $nonce ) {
		$result = $this->verify_nonce( $nonce );
		if ( $this->get_option_key() === $action ) {
			if ( $result['status'] ) {
				$this->init_options_set();
				$set     = $this->get_options_set();
				$options = $set->get_defaults();

				// Get old options from set.
				$old_opt = $this->get_options_set()->get_values();

				// Set options so that default value can be accessed in binding done in `setup`.
				$this->set_options( $options );

				// Setup and process values.
				$set->setup( $options );

				// Before ajax save action hook.
				do_action( 'jnews_panel_before_request_restore', $options );

				// Save and re-init options.
				$result = $this->save_and_reinit();

				if ( \JNews\Dashboard\Panel\Option\OptionControlSet::SAVE_SUCCESS === $result['code'] || \JNews\Dashboard\Panel\Option\OptionControlSet::SAVE_NOCHANGES === $result['code'] ) {
					$result['message'] = __( 'Restoring successful', 'jnews' );
				} else {
					$result['message'] = __( 'Restoring failed', 'jnews' );
				}

				$options = $this->get_options_set()->get_values();

				// After ajax restore action hook.
				do_action( 'jnews_panel_after_request_restore', $options, $old_opt, $result['status'], $this->get_option_key() );

				// After ajax restore action hook.
				do_action( 'jnews_panel_after_request_restore_' . $this->get_option_key(), $options, $old_opt, $result['status'] );
			}
		} else {
			return $action;
		}
		return $result;
	}

	/**
	 * Request import option handler
	 *
	 * @param string $option List options Panel.
	 * @param string $action Get options action.
	 * @param string $nonce Get nonce action.
	 *
	 * @return array
	 */
	public function request_import_option( $option, $action, $nonce ) {
		$options = null;
		$old_opt = null;
		$result  = $this->verify_nonce( $nonce );
		if ( $this->get_option_key() === $action ) {
			if ( $result['status'] ) {
				$this->init_options_set();

				if ( empty( $option ) ) {
					$result['status']  = false;
					$result['message'] = 'Can not be empty';
				} else {
					$option_data = json_decode( $option, true );

					if ( is_array( $option_data ) ) {
						$set = $this->get_options_set();

						// Get old options from set.
						$old_opt = $this->get_options_set()->get_values();

						// Populate new values.
						$set->populate_values( $option_data, true );

						// Save and re-init options.
						$result = $this->save_and_reinit();

						if ( \JNews\Dashboard\Panel\Option\OptionControlSet::SAVE_SUCCESS === $result['code'] || \JNews\Dashboard\Panel\Option\OptionControlSet::SAVE_NOCHANGES === $result['code'] ) {
							$result['message'] = __( 'Importing successful', 'jnews' );
						} else {
							$result['message'] = __( 'Importing failed', 'jnews' );
						}

						// Get new options.
						$options = $this->get_options_set()->get_values();
					} else {
						$result['status']  = false;
						$result['message'] = 'Invalid data';
					}
				}
			}
			// After ajax import action hook.
			do_action( 'jnews_panel_after_request_import', $options, $old_opt, $result['status'], $this->get_option_key() );

			// After ajax import action hook.
			do_action( 'jnews_panel_after_request_import_' . $this->get_option_key(), $options, $old_opt, $result['status'] );
		} else {
			return $option;
		}

		return $result;
	}

	/**
	 * Request export option handler
	 *
	 * @param string $action Get options action.
	 * @param string $nonce Get nonce action.
	 *
	 * @return array
	 */
	public function request_export_option( $action, $nonce ) {
		$sr_options = null;
		$db_options = null;
		$result     = $this->verify_nonce( $nonce );
		if ( $this->get_option_key() === $action ) {
			if ( $result['status'] ) {
				$db_options = \JNews\Dashboard\Panel\Panel::get_panel_option( $this->get_option_key() );
				$sr_options = json_encode( $db_options );

				$result = array(
					'status'  => true,
					'message' => 'Successful',
					'option'  => $sr_options,
				);
			}
			// After ajax export action hook.
			do_action( 'jnews_panel_after_request_export', $db_options, $sr_options, $result['status'], $this->get_option_key() );

			// After ajax export action hook.
			do_action( 'jnews_panel_after_request_export_' . $this->get_option_key(), $db_options, $sr_options, $result['status'] );
		} else {
			return $action;
		}
		return $result;
	}

	/**
	 * Initial DB Setup
	 */
	public function initial_db_setup() {
		// Init set and options.
		$this->init_options();
		$set = $this->get_options_set();

		// Get baked values from options set.
		$opt = $set->get_values();

		// Before db options db action hook.
		do_action( 'jnews_panel_option_before_db_init', $opt );

		// Save to db.
		$result = $this->save_and_reinit();

		// After db options db action hook.
		do_action( 'jnews_panel_option_after_db_init', $opt, $result['status'], $this->get_option_key() );
		do_action( 'jnews_panel_option_after_db_init_' . $this->get_option_key(), $opt, $result['status'] );
	}

	/**
	 * Init options
	 */
	public function init_options() {
		$this->init_options_set();
		$set = $this->get_options_set();

		// Try load option from DB.
		$db_options = \JNews\Dashboard\Panel\Panel::get_panel_option( $this->get_option_key() );
		$default    = $set->get_defaults();
		if ( ! empty( $db_options ) ) {
			// Unify, preserve option from DB but appends anything new from default.
			$options = $db_options;
			$options = $options + $default;
		} else {
			$options = $set->get_defaults();
		}

		// If dev mode, always use default, no db interaction.
		if ( $this->is_dev_mode() ) {
			$options = $set->get_defaults();
		}

		// Set options so that default value can be accessed in binding done in `setup`.
		$this->set_options( $options );

		// Setup and process values.
		$set->setup( $options );

	}

	/**
	 * Init options set
	 *
	 * @throws \Exception Error message.
	 */
	public function init_options_set() {
		if ( ! is_null( $this->get_options_set() ) ) {
			return;
		}

		if ( is_string( $this->get_template() ) && is_file( $this->get_template() ) ) {
			$template = include $this->get_template();
		} elseif ( is_array( $this->get_template() ) ) {
			$template = $this->get_template();
		} else {
			throw new \Exception( 'Invalid template supplied', 1 );
		}

		$parser = new Option\OptionParser();
		$set    = $parser->parse_array_options( $template, $this->use_auto_group_naming() );
		$set->set_layout( $this->get_layout() );

		// Assign set object.
		$this->set_options_set( $set );

		if ( $this->use_util_menu() ) {
			// Setup utility menu.
			$util_menu = new OptionControlGroupMenu();
			$util_menu->set_title( 'Utility' );
			$util_menu->set_name( 'menu_util' );
			$util_menu->set_icon( 'font-awesome:fa-ambulance' );

			// Setup restore default section.
			$restore_section = new OptionControlGroupSection();
			$restore_section->set_title( 'Restore Default' );
			$restore_section->set_name( 'section_restore' );
			$restore_section->set_type( 'section' );

			// Setup restore button.
			$restore_button = new OptionControlFieldRestore();
			$restore_button->set_type( 'restore' );
			$restore_section->add_field( $restore_button );

			// Setup exim section.
			$exim_section = new OptionControlGroupSection();
			$exim_section->set_title( 'Export/Import' );
			$exim_section->set_name( 'section_exim' );
			$exim_section->set_type( 'section' );

			// Setup exim field.
			$exim_field = new OptionControlFieldImpexp();
			$exim_field->set_type( 'impexp' );
			$exim_section->add_field( $exim_field );

			// Add exim section.
			$util_menu->add_control( $exim_section );

			$util_menu->add_control( $restore_section );
			$set->add_menu( $util_menu );
		}
	}

	/**
	 * Get field types
	 *
	 * @return array
	 */
	public function get_field_types() {
		return $this->get_options_set()->get_field_types();
	}

	/**
	 * Start Getter and Setter
	 */

	/**
	 * Get template
	 *
	 * @return string template
	 */
	public function get_template() {
		return $this->template;
	}

	/**
	 * Set template
	 *
	 * @param string $template template.
	 * @return self
	 */
	public function set_template( $template ) {
		$this->template = $template;

		return $this;
	}

	/**
	 * Get options
	 *
	 * @return string options
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Set options
	 *
	 * @param string $options options.
	 * @return self
	 */
	public function set_options( $options ) {
		$this->options = $options;

		return $this;
	}

	/**
	 * Get options_set
	 *
	 * @return Option\OptionControlSet|\VP_Option_Control_Set options_set
	 * @return Option\OptionControlSet|\jnews_panel_Control_Set options_set
	 */
	public function get_options_set() {
		return $this->options_set;
	}

	/**
	 * Set options_set
	 *
	 * @param string $options_set options_set.
	 * @return self
	 */
	public function set_options_set( $options_set ) {
		$this->options_set = $options_set;

		return $this;
	}

	/**
	 * Set layout
	 *
	 * @return string layout
	 */
	public function get_layout() {
		return $this->layout;
	}

	/**
	 * Get layout
	 *
	 * @param string $layout layout.
	 * @return self
	 */
	public function set_layout( $layout ) {
		$this->layout = $layout;

		return $this;
	}

	/**
	 * Get minimum_role value
	 *
	 * @return string $minimum_role
	 */
	public function get_minimum_role() {
		return $this->minimum_role;
	}

	/**
	 * Set minimum_role value
	 *
	 * @param string $minimum_role minimum_role.
	 * @return self
	 */
	public function set_minimum_role( $minimum_role ) {
		$this->minimum_role = $minimum_role;

		return $this;
	}

	/**
	 * Get option_key value
	 *
	 * @return string $option_key
	 */
	public function get_option_key() {
		return $this->option_key;
	}

	/**
	 * Set option_key value
	 *
	 * @param string $option_key $option_key.
	 * @return self
	 */
	public function set_option_key( $option_key ) {
		$this->option_key = apply_filters( 'jnews_panel_option_key', $option_key );

		return $this;
	}

	/**
	 * Get/Set whether to use auto group naming or not
	 *
	 * @param null|boolean $use_auto_group_naming Auto group naming.
	 *
	 * @return bool $use_auto_group_naming
	 */
	public function use_auto_group_naming( $use_auto_group_naming = null ) {
		if ( is_null( $use_auto_group_naming ) ) {
			return $this->use_auto_group_naming;
		}
		$this->use_auto_group_naming = $use_auto_group_naming;
	}

	/**
	 * Get/Set whether to use export import menu or not
	 *
	 * @return bool $use_util_menu
	 */
	public function use_util_menu( $use_util_menu = null ) {
		if ( is_null( $use_util_menu ) ) {
			return $this->use_util_menu;
		}
		$this->use_util_menu = $use_util_menu;
	}

	/**
	 * Get/Set whether it's development mode or not
	 *
	 * @param null|boolean $dev_mode Auto group naming.
	 *
	 * @return bool $dev_mode
	 */
	public function is_dev_mode( $dev_mode = null ) {
		if ( is_null( $dev_mode ) ) {
			return $this->is_dev_mode;
		}
		$this->is_dev_mode = $dev_mode;
	}

}
