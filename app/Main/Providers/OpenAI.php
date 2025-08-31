<?php
namespace CommentsPlusPlus\Main\Providers;

use CommentsPlusPlus\Main\Provider;

class OpenAI extends Provider {
	public function get_suggestion( string $input, string $prompt ): string {
		$settings = get_option( 'bwpcpp_settings' );
		$api_key  = $settings['openai_api_key'] ?? '';

		if ( empty( $api_key ) ) {
			return '';
		}

		$url   = 'https://api.openai.com/v1/chat/completions';
		$model = $settings['openai_model'] ?? 'gpt-4o';

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
								'content' => $prompt . "\n\nInput: $input\nOutput:",
							),
						),
						'stream'   => false,
					)
				),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return trim( $data['choices'][0]['message']['content'] ?? '' );
	}
}
