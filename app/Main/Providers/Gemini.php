<?php
namespace CommentsPlusPlus\Main\Providers;

use CommentsPlusPlus\Main\Provider;

class Gemini extends Provider {
	public function get_suggestion( string $input, string $prompt ): string {
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
