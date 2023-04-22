<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Util\CssMin;

/**
 * CSS Minifier
 */
class CssMin {

	/**
	 * Class instance
	 *
	 * @var CssMin
	 */
	private static $instance;

	/**
	 * Class Minifier
	 *
	 * @var Minifier
	 */
	private $minifier;

	/**
	 * Return class instance
	 *
	 * @return CssMin
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
		$this->minifier = new Minifier();
	}

	/**
	 * Minify CSS
	 *
	 * @param string $css CSS content.
	 *
	 * @return string
	 */
	public function minify_css( $css ) {
		$this->minifier->setMemoryLimit( '256M' );
		$this->minifier->setMaxExecutionTime( 120 );
		return $this->minifier->run( $css );
	}
}
