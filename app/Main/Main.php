<?php
/**
 * Main plugin class for Comments Plus Plus.
 *
 * @package CommentsPlusPlus
 */

namespace CommentsPlusPlus\Main;

use CommentsPlusPlus\Main\Autosuggest;

/**
 * Main plugin class.
 *
 * Handles initialization, assets loading, and includes.
 */
class Main {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		new Autosuggest();
	}

	/**
	 * Enqueue plugin CSS and JS.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		$css_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'plugin.css' : 'plugin.min.css';
		$js_file  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'plugin.js' : 'plugin.min.js';

		wp_enqueue_style(
			'comment-plus-plus',
			trailingslashit( BWPCPP_URL ) . 'assets/css/' . $css_file,
			array(),
			BWPCPP_VERSION
		);

		wp_enqueue_script(
			'comment-plus-plus',
			trailingslashit( BWPCPP_URL ) . 'assets/js/' . $js_file,
			array( 'jquery' ),
			BWPCPP_VERSION,
			true
		);

		wp_localize_script(
			'comment-plus-plus',
			'BWPCPP',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'bwp_cpp_nonce' => wp_create_nonce( 'bwp-cpp-nounce' ),
			)
		);
	}
}
