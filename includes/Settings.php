<?php
/**
 * Settings page handler
 *
 * @package WpAiContentGeneration
 */

declare(strict_types=1);

namespace WpAiContentGeneration;

/**
 * Handles the plugin settings page and OpenAI API key management.
 */
class Settings {

	const OPTION_NAME   = 'wp_ai_content_generation_settings';
	const API_KEY_FIELD = 'openai_api_key';

	/**
	 * Initialize the Settings class.
	 */
	public function init(): void {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'show_api_key_notice' ) );
	}

	/**
	 * Register the plugin settings.
	 */
	public function register_settings(): void {
		register_setting(
			'writing',
			self::OPTION_NAME,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		add_settings_section(
			'wp_ai_content_generation_api_section',
			__( 'AI Content Generation', 'wp-ai-content-generation' ),
			array( $this, 'render_api_section' ),
			'writing'
		);

		add_settings_field(
			self::API_KEY_FIELD,
			__( 'OpenAI API Key', 'wp-ai-content-generation' ),
			array( $this, 'render_api_key_field' ),
			'writing',
			'wp_ai_content_generation_api_section'
		);
	}

	/**
	 * Render the API section description.
	 */
	public function render_api_section(): void {
		?>
		<p>
			<?php
			printf(
				/* translators: %s: Link to OpenAI API keys page */
				esc_html__( 'Enter your OpenAI API key to enable AI content generation. You can get your API key from %s.', 'wp-ai-content-generation' ),
				'<a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener noreferrer">' . esc_html__( 'OpenAI Platform', 'wp-ai-content-generation' ) . '</a>'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render the API key input field.
	 */
	public function render_api_key_field(): void {
				$api_key = $this->get_api_key();
		?>
		<input
			type="password"
			id="<?php echo esc_attr( self::API_KEY_FIELD ); ?>"
			name="<?php echo esc_attr( self::OPTION_NAME . '[' . self::API_KEY_FIELD . ']' ); ?>"
			value="<?php echo esc_attr( $api_key ); ?>"
			class="regular-text"
			placeholder="<?php esc_attr_e( 'sk-...', 'wp-ai-content-generation' ); ?>"
		/>
		<p class="description">
			<?php esc_html_e( 'Your OpenAI API key will be stored in the WordPress database.', 'wp-ai-content-generation' ); ?>
		</p>
		<?php
	}

	/**
	 * Sanitize the settings before saving.
	 *
	 * @param array $input The input settings array.
	 * @return array The sanitized settings array.
	 */
	public function sanitize_settings( array $input ): array {
		$sanitized = array();

		if ( isset( $input[ self::API_KEY_FIELD ] ) ) {
			$sanitized[ self::API_KEY_FIELD ] = self::sanitize_api_key( $input[ self::API_KEY_FIELD ] );
		}

		return $sanitized;
	}

	/**
	 * Get the OpenAI API key from the database.
	 *
	 * @return string The API key or empty string if not set.
	 */
	public static function get_api_key(): string {
		$options = get_option( self::OPTION_NAME, array() );
		
		if ( is_array( $options ) && isset( $options[ self::API_KEY_FIELD ] ) ) {
			return self::sanitize_api_key( $options[ self::API_KEY_FIELD ] );
		}
		
		return '';
	}

	/**
	 * Check if the API key is configured and valid format.
	 *
	 * @return bool True if the API key appears to be valid, false otherwise.
	 */
	public static function is_api_key_configured(): bool {
		return ! empty( self::get_api_key() );
	}

	/**
	 * Sanitize the API key input.
	 *
	 * @param mixed $api_key The API key to sanitize.
	 * @return string
	 */
	public static function sanitize_api_key( mixed $api_key ): string {
		if ( ! is_string( $api_key ) ) {
			return '';
		}
		
		if ( ! str_starts_with( $api_key, 'sk-' ) ) {
			return '';
		}
		return sanitize_text_field( $api_key );
	}

	/**
	 * Show admin notice if API key is not configured.
	 */
	public function show_api_key_notice(): void {
		// Only show on admin pages and not on the writing settings page itself.
		$current_screen = get_current_screen();
		if ( ! $current_screen || 'options-writing' === $current_screen->id ) {
			return;
		}

		// Only show to users who can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Only show if API key is not configured.
		if ( self::is_api_key_configured() ) {
			return;
		}

		$settings_url = admin_url( 'options-writing.php' );
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: Link to settings page */
					esc_html__( 'AI Content Generation plugin requires an OpenAI API key to function. Please %s to configure it.', 'wp-ai-content-generation' ),
					'<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'visit the Writing settings page', 'wp-ai-content-generation' ) . '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}
}
