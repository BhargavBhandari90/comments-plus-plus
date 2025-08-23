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
if ( ! class_exists( 'BWPCPP__Actions_Filters' ) ) {

	/**
	 * Class for custom actions and filters.
	 */
	class BWPCPP__Actions_Filters {

		/**
		 * Constructor for class.
		 */
		public function __construct() {
			// Hook goes here.
		}
	}

	new BWPCPP__Actions_Filters();
}
