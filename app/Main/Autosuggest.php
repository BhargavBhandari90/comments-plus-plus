<?php
/**
 * Autosuggest functionality for Comments Plus Plus.
 *
 * @package CommentsPlusPlus
 */

namespace CommentsPlusPlus\Main;

use CommentsPlusPlus\Main\Providers\Gemini;
use CommentsPlusPlus\Main\Providers\Ollama;
use CommentsPlusPlus\Main\Providers\OpenAI;
use Exception;

/**
 * Class Autosuggest
 *
 * Handles AI-based sentence completion.
 */
class Autosuggest {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_bwp_cpp_autosuggest', array( $this, 'handle_suggestion' ) );
	}

	/**
	 * Handles AJAX request for AI auto-suggestions.
	 *
	 * @return void
	 */
	public function handle_suggestion(): void {
		check_ajax_referer( 'bwp-cpp-nounce', 'security' );

		$input         = sanitize_text_field( wp_unslash( $_POST['prompt'] ?? '' ) );
		$settings      = get_option( 'bwpcpp_settings' );
		$provider_name = $settings['ai_provider'] ?? 'none';

		if ( 'none' === $provider_name || empty( $input ) ) {
			wp_send_json_success( array( 'suggestion' => $input ) );
		}

		$default_prompt = "Complete the user's sentence in a natural, human, conversational tone. 
Do not add a period. Do not explain. Just return the next part of the sentence. 
Consider you are responding to an email.

Examples:
Input: How are 
Output: you doing?

Input: This course helped me  
Output: understand the basics without feeling overwhelmed.

Input: I'm looking forward to  
Output: trying out the new way.

Now complete:";

		try {
			$provider = match ( $provider_name ) {
				'ollama' => new Ollama( $settings ),
				'openai' => new OpenAI( $settings ),
				'gemini' => new Gemini( $settings ),
				default => null,
			};

			if ( ! $provider ) {
				wp_send_json_success( array( 'suggestion' => $input ) );
			}

			$prompt     = $settings[ "{$provider_name}_prompt" ] ?? $default_prompt;
			$suggestion = $provider->get_suggestion( $input, $prompt );

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}

		wp_send_json_success( array( 'suggestion' => $input . ' ' . ( $suggestion ?? '' ) ) );
	}
}
