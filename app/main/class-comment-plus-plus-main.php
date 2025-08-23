<?php
/**
 * Class for custom work.
 *
 * @package Comment_PP
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'Comment_Plus_Plus_Main' ) ) {

	/**
	 * Class for fofc core.
	 */
	class Comment_Plus_Plus_Main {

		/**
		 * Constructor for class.
		 */
		public function __construct() {

			$files = array(
				'app/main/class-custom-actions-filters',
				'app/main/class-comment-plus-plus-autosuggest',
			);

			foreach ( $files as $file ) {
				// Include functions file.
				if ( file_exists( BWPCPP_PATH . $file . '.php' ) ) {
					require BWPCPP_PATH . $file . '.php';
				}
			}

			// Add Plugin style and script.
			add_action( 'wp_enqueue_scripts', array( $this, 'bwp_cpp_enqueue_style_script' ) );
		}

		/**
		 * Plugin syle and script.
		 *
		 * @return void
		 */
		public function bwp_cpp_enqueue_style_script() {

			$css_file = 'plugin.min.css';
			$js_file  = 'plugin.min.js';

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$css_file = 'plugin.css';
				$js_file  = 'plugin.js';
			}

			// Plugin style.
			wp_enqueue_style(
				'comment-plus-plus',
				trailingslashit( BWPCPP_URL ) . 'assets/css/' . $css_file,
				'',
				BWPCPP_VERSION
			);

			// Plugin script.
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

	new Comment_Plus_Plus_Main();
}
