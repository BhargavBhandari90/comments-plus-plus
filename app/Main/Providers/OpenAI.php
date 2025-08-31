<?php
/**
 * OpenAI provider for Comments Plus Plus.
 *
 * @package CommentsPlusPlus
 */

namespace CommentsPlusPlus\Main\Providers;

use CommentsPlusPlus\Main\Provider;
use Exception;

/**
 * Class OpenAI
 *
 * Handles suggestions from OpenAI.
 */
class OpenAI extends Provider {

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param array $settings Plugin settings.
	 */
	public function __construct( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Get suggestion from OpenAI.
	 *
	 * @param string $input User input.
	 * @param string $prompt System prompt.
	 * @return string
	 * @throws Exception If API call fails.
	 */
	public function get_suggestion( string $input, string $prompt ): string {
		$api_key = $this->settings['openai_api_key'] ?? '';
		if ( empty( $api_key ) ) {
			throw new Exception( 'OpenAI API key is not set.' );
		}

		$url         = 'https://api.openai.com/v1/chat/completions';
		$model       = $this->settings['openai_model'] ?? 'gpt-4o';
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
			throw new Exception( esc_html( $response->get_error_message() ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $data['error'] ) ) {
			throw new Exception( esc_html( $data['error']['message'] ) );
		}

		return trim( $data['choices'][0]['message']['content'] ?? '' );
	}
}
