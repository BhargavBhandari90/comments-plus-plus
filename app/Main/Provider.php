<?php
/**
 * Abstract Provider class for Comments Plus Plus.
 *
 * @package CommentsPlusPlus
 */

namespace CommentsPlusPlus\Main;

/**
 * Abstract Class Provider.
 *
 * Defines the contract for AI providers.
 */
abstract class Provider {
	/**
	 * Method to send prompt to AI provider.
	 *
	 * @param string $input User input.
	 * @param string $prompt Prompt.
	 * @return string
	 */
	abstract public function get_suggestion( string $input, string $prompt ): string;
}
