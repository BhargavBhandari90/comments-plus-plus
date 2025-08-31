<?php
namespace CommentsPlusPlus\Main;

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
