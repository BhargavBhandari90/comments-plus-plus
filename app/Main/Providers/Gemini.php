<?php
/**
 * Gemini provider for Comments Plus Plus.
 *
 * @package CommentsPlusPlus
 */

namespace CommentsPlusPlus\Main\Providers;

use CommentsPlusPlus\Main\Provider;
use Exception;

/**
 * Class Gemini
 *
 * Handles suggestions from Gemini.
 */
class Gemini extends Provider {

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
	 * Get suggestion from Gemini.
	 *
	 * @param string $input User input.
	 * @param string $prompt System prompt.
	 * @return string
	 * @throws Exception If API call fails.
	 */
	public function get_suggestion( string $input, string $prompt ): string {
		$api_key = $this->settings['gemini_api_key'] ?? '';
		if ( empty( $api_key ) ) {
			throw new Exception( 'Gemini API key is not set.' );
		}

		$model       = $this->settings['gemini_model'] ?? 'gemini-1.5-flash-latest';
		$url         = 'https://generativelanguage.googleapis.com/v1/' . $model . ':generateContent?key=' . rawurlencode( $api_key );
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
			throw new Exception( esc_html( $response->get_error_message() ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $data['error'] ) ) {
			throw new Exception( esc_html( $data['error']['message'] ) );
		}

		return trim( $data['candidates'][0]['content']['parts'][0]['text'] ?? '' );
	}
}
