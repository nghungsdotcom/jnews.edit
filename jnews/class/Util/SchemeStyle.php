<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Util;

/**
 * Scheme Style
 */
class SchemeStyle {

	/**
	 * Class instance
	 *
	 * @var SchemeStyle
	 */
	private static $instance;

	/**
	 * CSS Minifier
	 *
	 * @var \JNews\Util\CssMin\CssMin
	 */
	private $css_min;

	/**
	 * File Name
	 *
	 * @var string
	 */
	private $filename = 'scheme';

	/**
	 * Extension
	 *
	 * @var string
	 */
	private $extension = 'css';

	/**
	 * Folder name to save file
	 *
	 * @var string
	 */
	private $folder_name = 'jnews';

	/**
	 * Return class instance
	 *
	 * @return SchemeStyle
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Class constructor
	 */
	private function __construct() {
		$this->css_min = \JNews\Util\CssMin\CssMin::instance();
		add_action( 'customize_save_after', array( $this, 'generate_scheme_style_file' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scheme' ), 97 );
	}

	/**
	 * Load Scheme
	 */
	public function load_scheme() {
		if ( file_exists( $this->get_file_info( 'path' ) ) ) {
			wp_register_style( 'jnews-scheme', $this->get_file_info( 'url' ), null, $this->get_version() );
		} else {
			if ( ! get_theme_mod( 'jnews_scheme_style' ) ) {
				$theme_version = jnews_get_theme_version();
				$additional    = get_option( \JNews\Importer::$option );

				if ( isset( $additional['style'] ) ) {
					$style = $additional['style'];

					if ( ! empty( $style ) && $style !== 'default' ) {
						wp_register_style( 'jnews-scheme', JNEWS_THEME_URL . '/' . \JNews\Importer::$default_path . $style . '/scheme.css', null, $theme_version );
					}
				}
			}
		}
	}

	/**
	 * Set Version
	 */
	public function set_version() {
		set_theme_mod( 'jnews_scheme_style_version', time() );
	}

	/**
	 * Get Version
	 *
	 * @return int
	 */
	public function get_version() {
		return get_theme_mod( 'jnews_scheme_style_version', time() );
	}

	/**
	 * Get scheme style info
	 *
	 * @return string
	 */
	public function get_file_info( $type = 'path' ) {
		$wp_upload_dir   = wp_upload_dir();
		$before_filename = '';

		switch ( $type ) {
			case 'url':
				$before_filename = $wp_upload_dir['baseurl'];
				break;
			case 'path':
			default:
				$before_filename = $wp_upload_dir['basedir'];
				break;
		}

		return sprintf( '%s/jnews/%s.%s', $before_filename, $this->filename, $this->extension );
	}

	/**
	 * Create and check folder
	 *
	 * @return bool
	 */
	public function check_folder() {
		$wp_upload_dir = wp_upload_dir();

		if ( ! is_dir( $wp_upload_dir['basedir'] . '/' . $this->folder_name ) ) {
			if ( ! mkdir( $wp_upload_dir['basedir'] . '/' . $this->folder_name, 0777 ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove Generated Scheme Style File
	 */
	public function remove_scheme_style_file() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();
		}

		$file_path = $this->get_file_info( 'path' );

		if ( $this->check_folder() ) {
			$wp_filesystem->delete( $file_path );
		}
	}

	/**
	 * Generate Scheme Style File
	 *
	 * @param \WP_Customize_Manager $wp_customize Customize manager.
	 */
	public function generate_scheme_style_file( $wp_customize ) {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();
		}

		$file_path = $this->get_file_info( 'path' );

		$styles = get_theme_mod( 'jnews_scheme_style', '' );

		if ( empty( $styles ) ) {
			$this->remove_scheme_style_file();
			return;
		}
		try {
			$styles = $this->css_min->minify_css( $styles );
		} catch ( \Exception $e ) {
			$styles = '';
		}
		if ( $this->check_folder() && ! empty( $styles ) ) {
			$this->set_version();
			if ( ! $wp_filesystem->put_contents( $file_path, $styles, 0777 ) ) {
                // phpcs:disable WordPress.WP.AlternativeFunctions
				file_put_contents( $file_path, $styles );
				// phpcs:enable WordPress.WP.AlternativeFunctions
			}
		}
	}
}
