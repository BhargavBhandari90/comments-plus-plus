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
if ( ! class_exists( 'Comment_Plus_Plus_Autosuggest' ) ) {

	/**
	 * Class for fofc core.
	 */
	class Comment_Plus_Plus_Autosuggest {

		/**
		 * Constructor for class.
		 */
		public function __construct() {

			// Get auto-suggested text.
			add_action( 'wp_ajax_bwp_cpp_autosuggest', array( $this, 'bwp_cpp_handle_suggestion' ) );
		}

		/**
		 * Get auto suggestion from AI.
		 *
		 * @return void
		 */
		public function bwp_cpp_handle_suggestion() {

			check_ajax_referer( 'bwp-cpp-nounce', 'security' );

			$input = sanitize_text_field( wp_unslash( $_POST['prompt'] ) ?? '' );

			$settings = get_option( 'bwpcpp_settings' );
			$provider = $settings['ai_provider'] ?? 'none';

			if ( 'none' === $provider || empty( $input ) ) {
				wp_send_json_success( array( 'suggestion' => $input ) );
				return;
			}

			$suggestion     = '';
			$default_prompt = "Complete the user's sentence in a natural, human, conversational tone. Do not add a period. Do not explain. Just return the next part of the sentence. Consider you are responsding an email.

Examples:
Input: How are 
Output: you doing?

Input: This course helped me  
Output: understand the basics without feeling overwhelmed.

Input: I'm looking forward to  
Output: trying out the new way.

Now complete:";

			switch ( $provider ) {
				case 'ollama':
					$url         = ! empty( $settings['ollama_url'] ) ? rtrim( $settings['ollama_url'], '/' ) . '/api/generate' : 'http://localhost:11434/api/generate';
					$model       = $settings['ollama_model'] ?? 'llama3';
					$prompt      = ! empty( $settings['ollama_prompt'] ) ? $settings['ollama_prompt'] : $default_prompt;
					$full_prompt = $prompt . "

Input: $input
Output:";

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

					$data       = json_decode( wp_remote_retrieve_body( $response ), true );
					$suggestion = trim( $data['response'] ?? '' );
					break;

				case 'openai':
					$api_key = $settings['openai_api_key'] ?? '';
					if ( empty( $api_key ) ) {
						wp_send_json_error( array( 'message' => 'OpenAI API key is not set.' ) );
					}

					$url         = 'https://api.openai.com/v1/chat/completions';
					$model       = $settings['openai_model'] ?? 'gpt-4o';
					$prompt      = $settings['openai_prompt'] ?? $default_prompt;
					$full_prompt = $prompt . "

Input: $input
Output:";

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

					$data       = json_decode( wp_remote_retrieve_body( $response ), true );
					$suggestion = trim( $data['choices'][0]['message']['content'] ?? '' );
					break;

				case 'gemini':
					$api_key = $settings['gemini_api_key'] ?? '';
					if ( empty( $api_key ) ) {
						wp_send_json_error( array( 'message' => 'Gemini API key is not set.' ) );
					}

					$model       = $settings['gemini_model'] ?? 'gemini-1.5-flash-latest';
					$url         = 'https://generativelanguage.googleapis.com/v1/models/' . $model . ':generateContent?key=' . $api_key;
					$prompt      = $settings['gemini_prompt'] ?? $default_prompt;
					$full_prompt = $prompt . "

Input: $input
Output:";

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

					$data       = json_decode( wp_remote_retrieve_body( $response ), true );
					$suggestion = trim( $data['candidates'][0]['content']['parts'][0]['text'] ?? '' );
					break;
			}

			wp_send_json_success( array( 'suggestion' => $input . ' ' . $suggestion ) );
		}
	}

	new Comment_Plus_Plus_Autosuggest();
}
