<?php
/**
 * Class for custom work.
 *
 * @package Comment_PP
 */

namespace CommentsPlusPlus\Admin;

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for fofc core.
 */
class Settings {

	/**
	 * Constructor for class.
	 */
	public function __construct() {

		// Plugin's setting page.
		add_action( 'admin_menu', array( $this, 'bwp_cpp_settings_page' ) );

		// Register settings fields.
		add_action( 'admin_init', array( $this, 'bwp_cpp_register_settings' ) );
	}

	/**
	 * Add settings page.
	 *
	 * @return void
	 */
	public function bwp_cpp_settings_page() {
		add_menu_page(
			esc_html__( 'Comment Plus Plus Settings', 'comments-plus-plus' ),
			esc_html__( 'Comment Plus Plus', 'comments-plus-plus' ),
			'manage_options',
			'comments-plus-plus',
			array( $this, 'bwp_cpp_admin_settings' ),
			'dashicons-testimonial',
			80
		);
	}

	/**
	 * Settings fields.
	 *
	 * @return void
	 */
	public function bwp_cpp_admin_settings() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Comment Plus Plus Settings', 'comments-plus-plus' ); ?></h1>
			<form method="post" action="options.php" novalidate="novalidate">
				<?php settings_fields( 'bwpcpp_settings' ); ?>
				<?php do_settings_sections( 'comments-plus-plus' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register setting fields.
	 *
	 * @return void
	 */
	public function bwp_cpp_register_settings() {

		register_setting(
			'bwpcpp_settings',
			'bwpcpp_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'bwp_cpp_sanitize_settings' ),
			)
		);

		// AI Provider Section.
		add_settings_section(
			'bwpcpp_provider_section',
			esc_html__( 'AI Provider', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_section_cb' ),
			'comments-plus-plus',
			array(
				'description' => esc_html__( 'Select which AI provider to use.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'ai_provider',
			esc_html__( 'Enable AI', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_provider_section',
			array(
				'name'    => 'ai_provider',
				'type'    => 'radio',
				'options' => array(
					'none'   => 'None',
					'ollama' => 'Ollama',
					'openai' => 'OpenAI',
					'gemini' => 'Gemini AI',
				),
			)
		);

		// Ollama Section.
		add_settings_section(
			'bwpcpp_ollama_section',
			esc_html__( 'Ollama', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_section_cb' ),
			'comments-plus-plus',
			array(
				'description' => esc_html__( 'Settings for Ollama.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'ollama_url',
			esc_html__( 'URL', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_ollama_section',
			array(
				'name'        => 'ollama_url',
				'class'       => 'regular-text',
				'type'        => 'text',
				'description' => esc_html__( 'Enter your Ollama API URL.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'ollama_model',
			esc_html__( 'Model', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_ollama_section',
			array(
				'name'        => 'ollama_model',
				'class'       => 'regular-text',
				'type'        => 'text',
				'description' => esc_html__( 'Enter the Ollama model to use (e.g., llama3).', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'ollama_prompt',
			esc_html__( 'Prompt', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_ollama_section',
			array(
				'name'        => 'ollama_prompt',
				'class'       => 'large-text',
				'type'        => 'textarea',
				'description' => esc_html__( 'Set the system prompt for Ollama.', 'comments-plus-plus' ),
			)
		);

		// OpenAI Section.
		add_settings_section(
			'bwpcpp_openai_section',
			esc_html__( 'OpenAI', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_section_cb' ),
			'comments-plus-plus',
			array(
				'description' => esc_html__( 'Settings for OpenAI.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'openai_api_key',
			esc_html__( 'API Key', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_openai_section',
			array(
				'name'        => 'openai_api_key',
				'class'       => 'regular-text',
				'type'        => 'password',
				'description' => __( 'Get OpenAI key from <a href="https://platform.openai.com/api-keys" target="_blank">here</a>.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'openai_model',
			esc_html__( 'Model', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_openai_section',
			array(
				'name'        => 'openai_model',
				'class'       => 'regular-text',
				'type'        => 'text',
				'description' => esc_html__( 'Enter the OpenAI model to use (e.g., gpt-4o).', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'openai_prompt',
			esc_html__( 'Prompt', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_openai_section',
			array(
				'name'        => 'openai_prompt',
				'class'       => 'large-text',
				'type'        => 'textarea',
				'description' => esc_html__( 'Set the system prompt for OpenAI.', 'comments-plus-plus' ),
			)
		);

		// Gemini AI Section.
		add_settings_section(
			'bwpcpp_gemini_section',
			esc_html__( 'Gemini AI', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_section_cb' ),
			'comments-plus-plus',
			array(
				'description' => esc_html__( 'Settings for Gemini AI.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'gemini_api_key',
			esc_html__( 'API Key', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_gemini_section',
			array(
				'name'        => 'gemini_api_key',
				'class'       => 'regular-text',
				'type'        => 'password',
				'description' => __( 'Get Gemini key from <a href="https://aistudio.google.com/app/apikey" target="_blank">here</a>.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'gemini_model',
			esc_html__( 'Model', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_gemini_section',
			array(
				'name'        => 'gemini_model',
				'class'       => 'regular-text',
				'type'        => 'select',
				'options'     => $this->bwp_cpp_gemini_models(),
				'description' => esc_html__( 'Select model. If you don\'t see any, save your API key first.', 'comments-plus-plus' ),
			)
		);

		add_settings_field(
			'gemini_prompt',
			esc_html__( 'Prompt', 'comments-plus-plus' ),
			array( $this, 'bwp_cpp_setting_field_callback' ),
			'comments-plus-plus',
			'bwpcpp_gemini_section',
			array(
				'name'        => 'gemini_prompt',
				'class'       => 'large-text',
				'type'        => 'textarea',
				'description' => esc_html__( 'Set the system prompt for Gemini AI.', 'comments-plus-plus' ),
			)
		);
	}

	/**
	 * Sanitize setting.
	 *
	 * @param  array $input Setting array.
	 * @return array        Sanitized array.
	 */
	public function bwp_cpp_sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			return $input;
		}

		$sanitized = array();

		$existing_settings = get_option( 'bwpcpp_settings' );
		if ( ! is_array( $existing_settings ) ) {
			$existing_settings = array();
		}
		$input = array_merge( $existing_settings, $input );

		foreach ( $input as $key => $value ) {
			switch ( $key ) {
				case 'ollama_prompt':
				case 'gemini_prompt':
				case 'openai_prompt':
					$sanitized[ $key ] = sanitize_textarea_field( $value );
					break;
				case 'ollama_url':
					$sanitized[ $key ] = esc_url_raw( $value );
					break;
				default:
					$sanitized[ $key ] = sanitize_text_field( $value );
			}
		}

		if ( isset( $sanitized['ai_provider'] ) ) {
			$providers = array( 'none', 'ollama', 'openai', 'gemini' );
			if ( ! in_array( $sanitized['ai_provider'], $providers, true ) ) {
				$sanitized['ai_provider'] = 'none';
			}
		} else {
			$sanitized['ai_provider'] = 'none';
		}

		return $sanitized;
	}
	/**
	 * Settings description.
	 *
	 * @param  array $args array of settings parameters.
	 * @return void
	 */
	public function bwp_cpp_setting_section_cb( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php echo wp_kses_post( $args['description'] ); ?></p>
		<table class="form-table" role="presentation">
		<?php
	}

	/**
	 * Display fields.
	 *
	 * @param array $args array of settings.
	 * @return void
	 */
	public function bwp_cpp_setting_field_callback( $args ) {

		$field_name = $args['name'];
		$settings   = get_option( 'bwpcpp_settings' );
		$value      = isset( $settings[ $field_name ] ) ? $settings[ $field_name ] : '';
		$class      = isset( $args['class'] ) ? $args['class'] : '';

		switch ( $args['type'] ) {

			case 'text':
			case 'password':
				echo wp_sprintf(
					'<input type="%s" name="bwpcpp_settings[%s]" value="%s" class="%s" /><p class="description">%s</p>',
					esc_attr( $args['type'] ),
					esc_attr( $field_name ),
					esc_attr( $value ),
					esc_attr( $class ),
					wp_kses_post( $args['description'] )
				);
				break;
			case 'textarea':
				echo wp_sprintf(
					'<textarea name="bwpcpp_settings[%s]" class="%s" rows="5" cols="50">%s</textarea><p class="description">%s</p>',
					esc_attr( $field_name ),
					esc_attr( $class ),
					esc_textarea( $value ),
					wp_kses_post( $args['description'] )
				);
				break;
			case 'checkbox':
				echo wp_sprintf(
					'<input type="checkbox" name="bwpcpp_settings[%s]" value="1" %s /><p class="description">%s</p>',
					esc_attr( $field_name ),
					checked( $value, '1', false ),
					wp_kses_post( $args['description'] )
				);
				break;
			case 'radio':
				$options = isset( $args['options'] ) ? $args['options'] : array();

				foreach ( $options as $key => $label ) {
					echo wp_sprintf(
						'<label style="padding-right: 15px;"><input type="radio" name="bwpcpp_settings[%s]" value="%s" %s /> %s</label>',
						esc_attr( $field_name ),
						esc_attr( $key ),
						checked( $value, $key, false ),
						esc_html( $label )
					);
				}
				if ( ! empty( $args['description'] ) ) {
					echo wp_sprintf( '<p class="description">%s</p>', wp_kses_post( $args['description'] ) );
				}
				break;
			case 'select':
				$options = isset( $args['options'] ) ? $args['options'] : array();
				?>
				<select
					id="<?php echo esc_attr( $field_name ); ?>"
					name="bwpcpp_settings[<?php echo esc_attr( $field_name ); ?>]"
				>
					<option value="">
						<?php esc_html_e( 'Select Option', 'comments-plus-plus' ); ?>
					</option>
					<?php if ( ! empty( $options ) ) : ?>
						<?php foreach ( $options as $key => $option ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"
								<?php echo ! empty( $value ) ? selected( $value, $key, false ) : ''; ?>
							>
								<?php echo esc_html( $option ); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				<p class="description">
					<?php echo wp_kses_post( $args['description'] ); ?>
				</p>
				<?php
				break;
		}
	}

	/**
	 * Retrieves a list of available Gemini AI models from the Google Cloud API.
	 *
	 * @return array|false The list of available Gemini AI models or false if an error occurred.
	 *                     Returns false if the API key is empty.
	 */
	public function bwp_cpp_gemini_models() {

		$options = get_option( 'bwpcpp_settings' );

		$api_key = ! empty( $options['gemini_api_key'] )
			? $options['gemini_api_key']
			: '';

		if ( empty( $api_key ) ) {
			return false;
		}

		$models = get_transient( 'bwpcpp_gemini_models' );

		if ( ! empty( $models ) ) {
			return $models;
		}

		$url = 'https://generativelanguage.googleapis.com/v1/models?key=' . $api_key;

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body   = json_decode( wp_remote_retrieve_body( $response ), true );
		$models = ! empty( $body['models'] ) ? $body['models'] : false;
		$models = wp_list_pluck( $models, 'displayName', 'name' );

		set_transient( 'bwpcpp_gemini_models', $models, DAY_IN_SECONDS );

		return $models;
	}
}
