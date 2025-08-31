<?php
/**
 * Ollama provider for Comments Plus Plus.
 *
 * @package CommentsPlusPlus
 */

namespace CommentsPlusPlus\Main\Providers;

use CommentsPlusPlus\Main\Provider;
use Exception;

/**
 * Class Ollama
 *
 * Handles suggestions from Ollama.
 */
class Ollama extends Provider {

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
	 * Get suggestion from Ollama.
	 *
	 * @param string $input User input.
	 * @param string $prompt System prompt.
	 * @return string
	 * @throws Exception If API call fails.
	 */
	public function get_suggestion( string $input, string $prompt ): string {
		$url         = ! empty( $this->settings['ollama_url'] ) ? rtrim( $this->settings['ollama_url'], '/' ) . '/api/generate' : 'http://localhost:11434/api/generate';
		$model       = $this->settings['ollama_model'] ?? 'llama3';
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
			throw new Exception( $response->get_error_message() );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $data['error'] ) ) {
			throw new Exception( $data['error'] );
		}

		return trim( $data['response'] ?? '' );
	}
}
