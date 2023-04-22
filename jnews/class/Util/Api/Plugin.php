<?php
/**
 * Plugin
 *
 * @author Jegtheme
 * @package JNews\Util\Api
 */

namespace JNews\Util\Api;

/**
 * Class used for managing plugin.
 */
class Plugin {

	/**
	 * Get bulk actions
	 *
	 * @return array
	 */
	public static function get_bulk_actions() {
		$actions = array();

		$actions['activate']   = __( 'Activate', 'jnews' );
		$actions['deactivate'] = __( 'Deactivate', 'jnews' );

		if ( current_user_can( 'update_plugins' ) ) {
			$actions['update'] = __( 'Update', 'jnews' );
		}

		if ( current_user_can( 'install_plugins' ) ) {
			$actions['install'] = __( 'Install', 'jnews' );
		}

		return $actions;
	}

	/**
	 * Dashboard plugin config
	 *
	 * @return array
	 */
	public static function jnews_dashboard_config() {
		$plugin_data               = array();
		$plugin_data['bulkAction'] = self::get_bulk_actions();
		return $plugin_data;
	}

	/**
	 * Get group list
	 *
	 * @return array
	 */
	public static function get_plugin_group() {
		$groups = include JNEWS_THEME_DIR_PLUGIN . 'plugin-group.php';
		return $groups;
	}

	/**
	 * Get plugin list
	 *
	 * @return array
	 */
	public static function get_plugin_list() {
		$plugins = include JNEWS_THEME_DIR_PLUGIN . 'plugin-list.php';

		return $plugins;
	}

	/**
	 * Get bundle plugin list
	 *
	 * @return array
	 */
	public static function get_bundle_plugin_list() {
		$plugins      = array();
		$plugins_list = self::get_plugin_list();

		foreach ( $plugins_list as $slug => $plugin ) {
			if ( isset( $plugin['file'] ) ) {
				$plugins[ $slug ] = array(
					'name'    => $plugin['name'],
					'version' => $plugin['version'],
					'file'    => $plugin['file'],
					'source'  => $plugin['source'],
				);
			}
		}

		return $plugins;
	}

	/**
	 * Validate plugin
	 *
	 * @param array $plugins Plugin list.
	 *
	 * @return array
	 */
	public static function validate_plugin( $plugins ) {
		if ( ! function_exists( 'wp_clean_plugins_cache' ) ) {
			/**
			 * Uncaught Error: Call to undefined function JNews\Util\wp_clean_plugins_cache()
			 * when call it from Rest API
			 */
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		wp_clean_plugins_cache( false );
		$repo_updates = get_site_transient( 'update_plugins' );
		$all_plugins  = get_plugins();

		foreach ( $plugins as $slug => &$data ) {
			unset( $data['file_path'], $data['installed'], $data['activated'], $data['require_update'] );
			// Check installed plugin.
			$installed_plugin = self::get_plugins( $slug, $all_plugins );
			if ( $installed_plugin ) {
				if ( isset( $installed_plugin['TextDomain'] ) ) {
					$deep_check = $slug === $installed_plugin['TextDomain'];
					if ( isset( $installed_plugin['file_path'] ) && strpos( $installed_plugin['file_path'], $slug ) !== false ) {
						$deep_check = $deep_check ? $deep_check : strpos( $installed_plugin['file_path'], $slug ) !== false;
					}
				}
				if ( $deep_check ) {
					if ( ! empty( $installed_plugin['Author'] ) ) {
						$data['author'] = $installed_plugin['Author'];
					}

					if ( ! empty( $installed_plugin['Version'] ) ) {
						$data['version'] = $installed_plugin['Version'];
					}

					// Register file_path plugin.
					$data['file_path'] = $installed_plugin['file_path'];
					$data['installed'] = true;
					$data['activated'] = is_plugin_active( $data['file_path'] );
					// Check if plugin has new version.
					if ( isset( $repo_updates->response[ $data['file_path'] ]->new_version ) ) {
						// Save current version and register new version.
						$data['current_version'] = $data['version'];
						$data['version']         = $repo_updates->response[ $data['file_path'] ]->new_version;
						// Spread installed plugin.
						foreach ( $all_plugins as $key => $item ) {
							// Check if file_path found in installed plugin.
							if ( strpos( $key, $data['file_path'] ) !== false ) {
								// Compare installed version and new version.
								if ( version_compare( $item['Version'], $data['version'], '<' ) ) {
									// Flag as new version.
									$data['require_update'] = true;
								}
							}
						}
					}
				}
			}
		}

		return $plugins;
	}

	/**
	 * Success plugin response
	 *
	 * @param array  $plugin Plugin data.
	 * @param string $slug Slug.
	 * @param string $source Source of plugin.
	 *
	 * @return string
	 */
	private static function success_plugin_response( $plugin, $slug, $source = '' ) {
		$repo_updates          = get_site_transient( 'update_plugins' );
		$plugins               = self::get_plugin_list();
		$plugin_link           = $plugin_version = '';
		$installed_version     = isset( $plugin['Version'] ) ? $plugin['Version'] : '';
		$plugin_require_update = false;
		$plugin_data           = array(
			'data-path' => isset( $plugin['file_path'] ) ? $plugin['file_path'] : '',
		);
		if ( isset( $plugins[ $slug ]['version'] ) ) {
			$minimum_version       = $plugins[ $slug ]['version'];
			$plugin_require_update = version_compare( $minimum_version, $installed_version, '>' );
		} else {
			$file_path = isset( $plugin['file_path'] ) ? $plugin['file_path'] : '';
			if ( isset( $plugin['file'] ) ) {
				$file_path = is_null( $plugin['file'] ) ? $plugin['file_path'] : $plugin['file'];
			}

			if ( isset( $repo_updates->response[ $file_path ] ) ) {
				$minimum_version       = $repo_updates->response[ $file_path ]->new_version;
				$plugin_require_update = version_compare( $minimum_version, $installed_version, '>' );
			}
		}

		if ( isset( $plugins[ $slug ]['link'] ) ) {
			foreach ( $plugins[ $slug ]['link'] as $link ) {
				$url          = str_replace( '__admin_url__', untrailingslashit( get_admin_url() ), $link['url'] );
				$target       = $link['newtab'] ? 'target="_blank"' : '';
				$plugin_link .= '[ <a href=' . $url . " {$target}>" . $link['title'] . ' </a> ] ';
			}
		}

		$plugin_version =
			'<li>
                ' . esc_html__( 'Required Version :', 'jnews' ) . '
                <strong class="' . esc_attr( $plugin_require_update ? 'newversion' : '' ) . '">
                    ' . esc_html( $plugin_require_update ? $minimum_version : $installed_version ) . '
                </strong>
            </li>';

		$plugin_info =
		   '<h3 class="jnews-item-title">
                ' . ( isset( $plugin['Name'] ) ? esc_html( $plugin['Name'] ) : '' ) . '
                ' . $plugin_link . '
            </h3>
            <em>
                ' . esc_html__( 'by', 'jnews' ) . '
                <strong>' . ( isset( $plugin['AuthorName'] ) ? esc_html( $plugin['AuthorName'] ) : '' ) . '</strong>
            </em>
            <p>
                ' . ( isset( $plugin['Description'] ) ? trim( $plugin['Description'] ) : '' ) . '
            </p>
            <div class="jnews-plugin-version">
                <ul>
                    <li>
                        ' . esc_html__( 'Installed Version :', 'jnews' ) . '
                        <strong>' . esc_html( $installed_version ) . '</strong>
                    </li>
                    ' . $plugin_version . '
                </ul>
            </div>
            <script id="' . ( isset( $plugin['TextDomain'] ) ? $plugin['TextDomain'] : '' ) . '-data" type="application/json">' . wp_json_encode( $plugin_data ) . '</script>';

		return $plugin_info;
	}

	/**
	 * Get list plugins or detail plugin
	 *
	 * @param string $file Plugins directory.
	 * @param array  $plugins All plugin files with plugin data.
	 *
	 * @return array|boolean
	 */
	private static function get_plugins( $file = '', $plugins = array() ) {
		if ( empty( $plugins ) ) {
			wp_clean_plugins_cache( false );
			$plugins = get_plugins();
		}
		if ( ! empty( $file ) ) {
			foreach ( $plugins as $key => $item ) {
				if ( strpos( $key, $file ) !== false ) {
					if ( preg_match( '|^' . $file . '/|', $key ) || strpos( $file, '.php' ) !== false ) {
						$item['file_path'] = $key;
					}
					return $item;
				}
			}
			return false;
		}
		return $plugins;
	}

	/**
	 * Attempts activation of plugin in a "sandbox" and redirects on success.
	 *
	 * @param string $file File path of plugin.
	 *
	 * @return \WP_Error|boolean
	 */
	private static function activate_plugin( $file ) {
		$silent        = false;
		$silent_plugin = array(
			'revslider/revslider.php',
		);
		if ( current_user_can( 'activate_plugin', $file ) ) {
			if ( is_plugin_inactive( $file ) ) {
				if ( in_array( $file, $silent_plugin, true ) ) {
					$silent = true;
				}
				$result = activate_plugin( $file, false, false, $silent );

				if ( is_wp_error( $result ) ) {
					return $result;
				} else {
					return true;
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * Deactivate a single plugin or multiple plugins.
	 *
	 * @param string $file File path of plugin.
	 *
	 * @return \WP_Error|boolean
	 */
	private static function deactive_plugin( $file ) {
		if ( current_user_can( 'deactivate_plugin', $file ) ) {
			if ( is_plugin_active( $file ) ) {
				$result = deactivate_plugins( $file, false, false );

				if ( is_wp_error( $result ) ) {
					return $result;
				} else {
					return true;
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * Register data for plugin update
	 *
	 * @param array $plugin Plugin data.
	 */
	private static function register_update_plugin( $plugin ) {
		extract( $plugin );

		$repo_updates = get_site_transient( 'update_plugins' );

		if ( ! is_object( $repo_updates ) ) {
			$repo_updates = new \stdClass();
		}

		$file_path = $file;

		if ( empty( $repo_updates->response[ $file_path ] ) ) {
			$repo_updates->response[ $file_path ] = new \stdClass();
		}

		$repo_updates->response[ $file_path ]->slug        = $slug;
		$repo_updates->response[ $file_path ]->plugin      = $file_path;
		$repo_updates->response[ $file_path ]->new_version = $version;
		$repo_updates->response[ $file_path ]->package     = $source;
		$repo_updates->response[ $file_path ]->url         = '';

		set_site_transient( 'update_plugins', $repo_updates );
	}

	/**
	 * Retrieves plugin installer pages from the WordPress.org Plugins API.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return \WP_Error|object|array
	 */
	public static function retrieve_plugin_source( $slug ) {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => sanitize_key( wp_unslash( $slug ) ),
				'fields' => array(
					'sections' => false,
				),
			)
		);

		return $api;
	}

	/**
	 * Turn off reject unsafe urls
	 *
	 * @param array $args Arguments used for the HTTP request.
	 *
	 * @return array
	 */
	public static function turn_off_reject_unsafe_urls( $args ) {
		$args['reject_unsafe_urls'] = false;

		return $args;
	}

	/**
	 * Installing give plugin
	 *
	 * @param array               $plugin Plugin data.
	 * @param string              $doing Action plugin.
	 * @param \JNews\Util\RestAPI $rest_api Rest api instance.
	 * @param string              $from Request source.
	 *
	 * @return WP_Error|array
	 */
	public static function manage_plugin( $plugin, $rest_api, $doing = '', $from = '' ) {
		if ( ! $rest_api instanceof \JNews\Util\RestAPI ) {
			return;
		}
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$plugin_details              = $plugin;
		$all_plugins                 = get_plugins();
		$is_plugin_already_installed = $is_plugin_need_updated = false;

		if ( isset( $plugin_details['file'] ) ) {
			foreach ( $all_plugins as $key => $item ) {
				if ( strpos( $key, $plugin_details['file'] ) !== false ) {
					$is_plugin_already_installed = true;
					if ( version_compare( $item['Version'], $plugin_details['version'], '<' ) ) {
						$is_plugin_need_updated = true;
					}
				}
			}
		}

		add_filter( 'http_request_args', '\JNews\Util\Api\Plugin::turn_off_reject_unsafe_urls' );

		if ( ! empty( $doing ) ) {
			switch ( $doing ) {
				case 'deactivate':
					if ( $is_plugin_already_installed ) {
						$plugin_status = self::deactive_plugin( $plugin_details['file'] );
						$status        = array(
							'success' => true,
						);
					}
					break;
				case 'activate':
				case 'install':
				case 'update':
					if ( $is_plugin_already_installed && 'activate' === $doing ) {
						$plugin_status = self::activate_plugin( $plugin_details['file'] );
						$status        = array(
							'success' => true,
						);
					} else {
						$status = array(
							'success' => false,
						);

						if ( isset( $plugin_details['type'] ) && 'server' === $plugin_details['type'] ) {
							$status['plugin'] = $plugin_details['slug'];
							$plugin_source    = $plugin_details['source'];
						} elseif ( isset( $plugin_details['type'] ) && 'bundle' === $plugin_details['type'] ) {
							$status['plugin']         = $plugin_details['slug'];
							$plugin_details['source'] = JNEWS_THEME_DIR_PLUGIN . $plugin_details['source'];
							$plugin_source            = $plugin_details['source'];
						} else {
							$api = self::retrieve_plugin_source( $plugin_details['slug'] );

							if ( is_wp_error( $api ) ) {
								remove_filter( 'http_request_args', '\JNews\Util\Api\Plugin::turn_off_reject_unsafe_urls' );
								return $rest_api->response_error( rtrim( $api->get_error_message(), '.' ) );
							}
							$plugin_source = $api->download_link;
						}

						$skin     = new \WP_Ajax_Upgrader_Skin();
						$upgrader = new \Plugin_Upgrader( $skin );

						if ( $is_plugin_need_updated && 'update' === $doing ) {
							if ( ! current_user_can( 'update_plugins' ) ) {
								$message = esc_html__( 'Sorry, you are not allowed to manage plugins automatic updates.', 'jnews' );
								remove_filter( 'http_request_args', '\JNews\Util\Api\Plugin::turn_off_reject_unsafe_urls' );
								return $rest_api->response_error( rtrim( $message, '.' ) );
							}
							if ( isset( $plugin_details['type'] ) && 'bundle' === $plugin_details['type'] ) {
								self::register_update_plugin( $plugin_details );
							}
							$is_active     = is_plugin_active( $plugin_details['file'] ) || 'activate' === $doing;
							$result        = $upgrader->upgrade(
								$plugin_details['file'],
								array(
									'clear_update_cache' => false,
								)
							);
							$error_handler = self::error_handler_plugin( $result, $skin, $rest_api );
							if ( $error_handler ) {
								remove_filter( 'http_request_args', '\JNews\Util\Api\Plugin::turn_off_reject_unsafe_urls' );
								return $error_handler;
							}
							if ( $is_active ) {
								$plugin_status = self::activate_plugin( $plugin_details['file'] );
							}
						} else {
							$is_active = 'activate' === $doing;
							if ( $is_active ) {
								$result = self::activate_plugin_handler( $plugin_details, $plugin_source, $upgrader, $skin, $rest_api );
								if ( $result ) {
									remove_filter( 'http_request_args', '\JNews\Util\Api\Plugin::turn_off_reject_unsafe_urls' );

									return $result;
								}
							} else {
								$result = self::install_plugin_handler( $plugin_source, $upgrader, $skin, $rest_api );
								if ( $result ) {
									remove_filter( 'http_request_args', '\JNews\Util\Api\Plugin::turn_off_reject_unsafe_urls' );

									return $result;
								}
							}
						}

						$plugin_status     = true;
						$status['success'] = true;
					}
					break;

				default:
					// code...
					break;
			}
		}

		if ( $plugin_status && ! is_wp_error( $plugin_status ) ) {
			$status['description'] = self::success_plugin_response( self::get_plugins( isset( $plugin_details['file'] ) ? $plugin_details['file'] : '' ), $plugin_details['slug'] );
			if ( in_array( $doing, array( 'activate', 'deactivate', 'install' ), true ) && $status['success'] ) {
				$status['status']    = esc_html__( 'The plugin is successfully activated.', 'jnews' );
				$status['refresh']   = isset( $plugin_details['refresh'] ) && 'activate' === $doing ? $plugin['refresh'] : false;
				$status['add_class'] = 'plugin-activated';
				if ( 'deactivate' === $doing ) {
					$status['status']    = esc_html__( 'The plugin is successfully deactivated.', 'jnews' );
					$status['add_class'] = 'plugin-installed';
				}
				if ( 'install' === $doing ) {
					$status['add_class'] = 'plugin-installed';
					$status['status']    = esc_html__( 'The plugin is successfully installed.', 'jnews' );
				}
			}
			if ( 'update' === $doing ) {
				$status['add_class'] = is_plugin_active( $plugin_details['file'] ) ? 'plugin-activated' : 'plugin-installed';
				$status['status']    = esc_html__( 'The plugin is successfully updated.', 'jnews' );
				$status['refresh']   = isset( $plugin_details['refresh'] ) && is_plugin_active( $plugin_details['file'] ) ? $plugin['refresh'] : false;
			}
		}

		if ( ! empty( $from ) && 'react' === $from ) {
			$status['add_class'] = str_replace( 'plugin-', '', $status['add_class'] );
		}

		remove_filter( 'http_request_args', '\JNews\Util\Api\Plugin::turn_off_reject_unsafe_urls' );

		return $rest_api->response_success( $status );
	}

	/**
	 * Activate plugin handler
	 *
	 * @param array                       $plugin_source
	 * @param string                      $plugin_source
	 * @param \Plugin_Upgrader            $upgrader
	 * @param \WP_Ajax_Upgrader_Skin      $skin
	 * @param \JNews\Util\RestAPI|boolean $rest_api Rest api instance.
	 *
	 * @return \WP_REST_Response|boolean
	 */
	public static function activate_plugin_handler( $plugin_details, $plugin_source, $upgrader, $skin, $rest_api = false ) {
		$installed_plugin = self::get_plugins( $plugin_details['file'] );
		if ( $installed_plugin ) {
			$plugin_details['file'] = $installed_plugin['file_path'];
			$plugin_status          = self::activate_plugin( $plugin_details['file'] );
		} else {
			$result = self::install_plugin_handler( $plugin_source, $upgrader, $skin, $rest_api );
			if ( $result ) {
				return $result;
			}
			return self::activate_plugin_handler( $plugin_details, $plugin_source, $upgrader, $skin, $rest_api );
		}
		return false;
	}

	/**
	 * Install plugin handler
	 *
	 * @param string                      $plugin_source
	 * @param \Plugin_Upgrader            $upgrader
	 * @param \WP_Ajax_Upgrader_Skin      $skin
	 * @param \JNews\Util\RestAPI|boolean $rest_api Rest api instance.
	 *
	 * @return \WP_REST_Response|boolean
	 */
	public static function install_plugin_handler( $plugin_source, $upgrader, $skin, $rest_api = false ) {
		$result        = $upgrader->install(
			$plugin_source,
			array(
				'clear_update_cache' => false,
			)
		);
		$error_handler = self::error_handler_plugin( $result, $skin, $rest_api );
		if ( $error_handler ) {
			return $error_handler;
		}
		return false;
	}

	/**
	 * Error handler plugin
	 *
	 * @param bool|WP_Error               $result
	 * @param \WP_Ajax_Upgrader_Skin      $skin
	 * @param \JNews\Util\RestAPI|boolean $rest_api Rest api instance.
	 *
	 * @return \WP_REST_Response|boolean
	 */
	public static function error_handler_plugin( $result, $skin, $rest_api = false ) {
		if ( is_wp_error( $result ) ) {
			return $rest_api ? $rest_api->response_error( rtrim( $result->get_error_message(), '.' ) ) : rtrim( $result->get_error_message(), '.' );
		} elseif ( is_wp_error( $skin->result ) ) {
			return $rest_api ? $rest_api->response_error( rtrim( $skin->result->get_error_message(), '.' ) ) : rtrim( $skin->result->get_error_message(), '.' );
		} elseif ( $skin->get_errors()->has_errors() ) {
			return $rest_api ? $rest_api->response_error( rtrim( $skin->get_error_messages(), '.' ) ) : rtrim( $skin->get_error_messages(), '.' );
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$message = esc_html__( 'Unable to connect to the wp_filesystem.', 'jnews' );

			if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
				$message = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			return $rest_api ? $rest_api->response_error( rtrim( $message, '.' ) ) : rtrim( $message, '.' );
		}
		return false;
	}
}
