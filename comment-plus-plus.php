<?php
/**
 * Plugin Name:     Comment Plus Plus
 * Description:     A different experience of commenting.
 * Author:          BuntyWP
 * Author URI:      https://biliplugins.com/
 * Text Domain:     comment-plus-plus
 * Domain Path:     /languages
 * Version:         1.0.0
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.6
 * Requires PHP:     7.4
 *
 * @package         Comment_PP
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package    Comment_PP
 */
if ( ! defined( 'BWPCPP_VERSION' ) ) {
	/**
	 * The version of the plugin.
	 */
	define( 'BWPCPP_VERSION', '1.0.0' );
}

if ( ! defined( 'BWPCPP_PATH' ) ) {
	/**
	 *  The server file system path to the plugin directory.
	 */
	define( 'BWPCPP_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BWPCPP_URL' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'BWPCPP_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BWPCPP_BASE_NAME' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'BWPCPP_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'BWPCPP_MAIN_FILE' ) ) {
	/**
	 * The url to the plugin directory.
	 */
	define( 'BWPCPP_MAIN_FILE', __FILE__ );
}

/**
 * Include files.
 */
$files = array(
	'app/includes/common-functions',
	'app/main/class-comment-plus-plus-main',
	'app/admin/class-comment-plus-plus-admin-main',
);

if ( ! empty( $files ) ) {

	foreach ( $files as $file ) {

		// Include functions file.
		if ( file_exists( BWPCPP_PATH . $file . '.php' ) ) {
			require BWPCPP_PATH . $file . '.php';
		}
	}
}

/**
 * Plugin Setting page.
 *
 * @param array $links Array of plugin links.
 * @return array Array of plugin links.
 */
function bwp_cpp_settings_link( $links ) {

	$settings_link = sprintf(
		'<a href="%1$s">%2$s</a>',
		'admin.php?page=comment-plus-plus',
		esc_html__( 'Settings', 'bpai-core' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . BWPCPP_BASE_NAME, 'bwp_cpp_settings_link' );

/**
 * Apply translation file as per WP language.
 */
function bwp_cpp_text_domain_loader() {

	// Get mo file as per current locale.
	$mofile = BWPCPP_PATH . 'languages/bp-ai-' . get_locale() . '.mo';

	// If file does not exists, then apply default mo.
	if ( ! file_exists( $mofile ) ) {
		$mofile = BWPCPP_PATH . 'languages/default.mo';
	}

	if ( file_exists( $mofile ) ) {
		load_textdomain( 'comment-plus-plus', $mofile );
	}
}

add_action( 'plugins_loaded', 'bwp_cpp_text_domain_loader' );
