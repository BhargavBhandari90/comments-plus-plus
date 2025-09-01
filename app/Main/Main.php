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

		wp_enqueue_style(
			'comments-plus-plus',
			trailingslashit( BWPCPP_URL ) . 'build/main.css',
			array(),
			BWPCPP_VERSION
		);

		wp_enqueue_script(
			'comments-plus-plus',
			trailingslashit( BWPCPP_URL ) . 'build/main.js',
			array( 'jquery' ),
			BWPCPP_VERSION,
			true
		);

		wp_localize_script(
			'comments-plus-plus',
			'BWPCPP',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'bwp_cpp_nonce' => wp_create_nonce( 'bwp-cpp-nounce' ),
			)
		);
	}
}
