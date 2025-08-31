<?php
/**
 * Autosuggest functionality for Comments Plus Plus.
 *
 * @package CommentsPlusPlus
 */

namespace CommentsPlusPlus\Main;

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

		$input    = sanitize_text_field( wp_unslash( $_POST['prompt'] ?? '' ) );
		$settings = get_option( 'bwpcpp_settings' );
		$provider = $settings['ai_provider'] ?? 'none';

		if ( $provider === 'none' || empty( $input ) ) {
			wp_send_json_success( array( 'suggestion' => $input ) );
		}

		$suggestion     = '';
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

		$suggestion = match ( $provider ) {
			'ollama' => $this->fetch_ollama( $input, $settings, $default_prompt ),
			'openai' => $this->fetch_openai( $input, $settings, $default_prompt ),
			'gemini' => $this->fetch_gemini( $input, $settings, $default_prompt ),
			default => '',
		};

		wp_send_json_success( array( 'suggestion' => $input . ' ' . $suggestion ) );
	}

	/**
	 * Fetch suggestion from Ollama API.
	 *
	 * @param string $input User input.
	 * @param array  $settings Plugin settings.
	 * @param string $default_prompt Default AI prompt.
	 * @return string
	 */
	private function fetch_ollama( string $input, array $settings, string $default_prompt ): string {
		$url         = ! empty( $settings['ollama_url'] ) ? rtrim( $settings['ollama_url'], '/' ) . '/api/generate' : 'http://localhost:11434/api/generate';
		$model       = $settings['ollama_model'] ?? 'llama3';
		$prompt      = $settings['ollama_prompt'] ?? $default_prompt;
		$full_prompt = $prompt . "\n\nInput: $input\nOutput:";

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode(
					array(
						'model'  => $model,
						'prompt' => $full_prompt,
						'stream' => false,
					)
				),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return trim( $data['response'] ?? '' );
	}

	/**
	 * Fetch suggestion from OpenAI API.
	 *
	 * @param string $input User input.
	 * @param array  $settings Plugin settings.
	 * @param string $default_prompt Default AI prompt.
	 * @return string
	 */
	private function fetch_openai( string $input, array $settings, string $default_prompt ): string {
		$api_key = $settings['openai_api_key'] ?? '';
		if ( empty( $api_key ) ) {
			wp_send_json_error( array( 'message' => 'OpenAI API key is not set.' ) );
		}

		$url         = 'https://api.openai.com/v1/chat/completions';
		$model       = $settings['openai_model'] ?? 'gpt-4o';
		$prompt      = $settings['openai_prompt'] ?? $default_prompt;
		$full_prompt = $prompt . "\n\nInput: $input\nOutput:";

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				),
				'body'    => wp_json_encode(
					array(
						'model'    => $model,
						'messages' => array(
							array(
								'role'    => 'user',
								'content' => $full_prompt,
							),
						),
						'stream'   => false,
					)
				),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return trim( $data['choices'][0]['message']['content'] ?? '' );
	}

	/**
	 * Fetch suggestion from Gemini API.
	 *
	 * @param string $input User input.
	 * @param array  $settings Plugin settings.
	 * @param string $default_prompt Default AI prompt.
	 * @return string
	 */
	private function fetch_gemini( string $input, array $settings, string $default_prompt ): string {
		$api_key = $settings['gemini_api_key'] ?? '';
		if ( empty( $api_key ) ) {
			wp_send_json_error( array( 'message' => 'Gemini API key is not set.' ) );
		}

		$model       = $settings['gemini_model'] ?? 'gemini-1.5-flash-latest';
		$url         = "https://generativelanguage.googleapis.com/v1/{$model}:generateContent?key={$api_key}";
		$prompt      = $settings['gemini_prompt'] ?? $default_prompt;
		$full_prompt = $prompt . "\n\nInput: $input\nOutput:";

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode(
					array(
						'contents' => array(
							array(
								'parts' => array(
									array(
										'text' => $full_prompt,
									),
								),
							),
						),
					)
				),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return trim( $data['candidates'][0]['content']['parts'][0]['text'] ?? '' );
	}
}
