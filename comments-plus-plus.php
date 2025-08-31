<?php
/**
 * Plugin Name:     Comments Plus Plus
 * Description:     A different experience of commenting.
 * Author:          BuntyWP
 * Author URI:      https://biliplugins.com/
 * Text Domain:     comments-plus-plus
 * Domain Path:     /languages
 * Version:         1.0.0
 * License:         GPL-2.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.6
 * Requires PHP:     8.0
 *
 * @package         Comment_PP
 */

namespace CommentsPlusPlus;

use CommentsPlusPlus\Admin\Settings;
use CommentsPlusPlus\Main\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class.
 */
final class CommentsPlusPlus {

	/**
	 * Class instance.
	 *
	 * @var CommentsPlusPlus|null
	 */
	private static ?CommentsPlusPlus $instance = null;

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->include_autoloader();
		$this->init_hooks();
	}

	/**
	 * Singleton instance
	 */
	public static function instance(): CommentsPlusPlus {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Plugin Constants.
	 *
	 * @return void
	 */
	private function define_constants(): void {
		if ( ! defined( 'BWPCPP_VERSION' ) ) {
			/** * The version of the plugin. */
			define( 'BWPCPP_VERSION', '1.0.0' );
		} if ( ! defined( 'BWPCPP_PATH' ) ) {
			/** * The server file system path to the plugin directory. */
			define( 'BWPCPP_PATH', plugin_dir_path( __FILE__ ) );
		} if ( ! defined( 'BWPCPP_URL' ) ) {
			/** * The url to the plugin directory. */
			define( 'BWPCPP_URL', plugin_dir_url( __FILE__ ) );
		} if ( ! defined( 'BWPCPP_BASE_NAME' ) ) {
			/** * The url to the plugin directory. */
			define( 'BWPCPP_BASE_NAME', plugin_basename( __FILE__ ) );
		} if ( ! defined( 'BWPCPP_MAIN_FILE' ) ) {
			/** * The url to the plugin directory. */
			define( 'BWPCPP_MAIN_FILE', __FILE__ );
		}
	}

	/**
	 * Load autoloader.
	 *
	 * @return void
	 */
	private function include_autoloader(): void {
		$autoload = trailingslashit( BWPCPP_PATH ) . 'vendor/autoload.php';
		if ( file_exists( $autoload ) ) {
			require_once $autoload;
		}
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		add_filter( 'plugin_action_links_' . BWPCPP_BASE_NAME, array( $this, 'add_settings_link' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'plugin_loader' ) );
	}

	/**
	 * Plugin setting link.
	 *
	 * @param array $links Array of plugin links.
	 * @return array       Modified array of plugin links.
	 */
	public function add_settings_link( array $links ): array {
		$settings_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			'admin.php?page=comments-plus-plus',
			esc_html__( 'Settings', 'comments-plus-plus' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Load language files.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		$mofile = trailingslashit( BWPCPP_PATH ) . 'languages/cpp-' . get_locale() . '.mo';

		if ( ! file_exists( $mofile ) ) {
			$mofile = trailingslashit( BWPCPP_PATH ) . 'languages/default.mo';
		}

		if ( file_exists( $mofile ) ) {
			load_textdomain( 'comments-plus-plus', $mofile );
		}
	}

	/**
	 * Load plugin files.
	 *
	 * @return void
	 */
	public function plugin_loader(): void {

		new Main();

		if ( is_admin() ) {
			new Settings();
		}
	}
}

// Initialize plugin.
CommentsPlusPlus::instance();
