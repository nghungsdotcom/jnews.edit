<?php
/**
 * Panel Option Vafpress
 *
 * @author Jegtheme
 * @package JNews\Dashboard\Panel
 */

namespace JNews\Dashboard\Panel;

/**
 * Class used for managing option.
 */
class PanelOptionVafpress extends \VP_Option {

	/**
	 * Construct of class Panel
	 *
	 * @param array $configs Config Panel.
	 * @throws \Exception Throws some error message.
	 */
	public function __construct( $configs ) {

		// Extract and set default value.
		$panel_config = array_merge(
			array(
				'is_dev_mode'           => false,
				'use_auto_group_naming' => true,
				'use_util_menu'         => true,
				'minimum_role'          => 'edit_theme_options',
				'layout'                => 'fixed',
				'page_title'            => 'Vafpress Options',
				'menu_label'            => 'Vafpress Options',
				'priority'              => 10,
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
		$options = \JNews\Dashboard\Panel\Panel::get_panel_option( $this->get_option_key(), false, 'vafpress' );
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

		// @TODO: we need to add export, import, restore handler
	}

	/**
	 * Setup
	 */
	public function setup() {
		$this->init_options_set();
		$this->init_options();
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
			$result['message'] = 'Successful';
		} else {
			$result['status']  = false;
			$result['message'] = 'Unverified Access.';
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
				$option = $this->get_options_control_set()->normalize_values( $option );

				// Stripslashes added by WP in $_GET / $_POST.
				$option = stripslashes_deep( $option );

				// Get old options from set.
				$old_opt = $this->get_options_control_set()->get_values();

				$this->get_options_control_set()->populate_values( $option, true );

				// Get back options from set.
				$opt = $this->get_options_control_set()->get_values();

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
					$option = json_decode( $option, true );

					if ( is_array( $option ) ) {
						$set = $this->get_options_set();

						// Get old options from set.
						$old_opt = $this->get_options_set()->get_values();

						// Populate new values.
						$set->populate_values( $option, true );

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
				$db_options = get_option( $this->get_option_key() );
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
	 * Get _options_set
	 *
	 * @return \VP_Option_Control_Set _options_set
	 */
	public function get_options_control_set() {
		return $this->get_options_set();
	}
}
