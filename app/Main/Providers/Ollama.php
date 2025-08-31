<?php
namespace CommentsPlusPlus\Main\Providers;

use CommentsPlusPlus\Main\Provider;

class Ollama extends Provider {
	public function get_suggestion( string $input, string $prompt ): string {
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
}
