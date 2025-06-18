<?php
/**
 * API interface
 *
 * @package \WpAiContentGeneration
 */

declare(strict_types=1);

namespace WpAiContentGeneration;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Handles the AI Content Generation API endpoints.
 */
class Api {

	const ANALYSE_CONTENT_HOOK   = 'wp-ai-content-generation-analysis-hook';
	const ANALYSE_INTENT_HOOK    = 'wp-ai-content-generation-analyse-intent-hook';
	const SELECT_COMPONENTS_HOOK = 'wp-ai-content-generation-select-components-hook';

	/**
	 * Constructor for the API class.
	 * 
	 * @param \OpenAI\Client $client The OpenAI client instance.
	 */
	public function __construct(
		private \OpenAI\Client $client
	) {}

	/**
	 * Initialize the API class.
	 */
	public function init(): void {
		// Register the REST API routes.
		add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );

		// Register the action hooks for content generation.
		add_action( self::ANALYSE_CONTENT_HOOK, array( $this, 'analyse_content' ), 10, 1 );
		add_action( self::ANALYSE_INTENT_HOOK, array( $this, 'analyse_intent' ), 10, 1 );
		add_action( self::SELECT_COMPONENTS_HOOK, array( $this, 'select_components' ), 10, 1 );
	}

	/**
	 * Register the REST API routes for content generation.
	 *
	 * @return void
	 */
	public function register_api_routes(): void {
		register_rest_route(
			'wp-ai-content-generation/v1',
			'/generate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'generate' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			'wp-ai-content-generation/v1',
			'/generate/(?P<id>[A-z0-9]+)',
			array(
				'methods'             => 'GET',
				'permission_callback' => array( $this, 'check_permissions' ),
				'callback'            => array( $this, 'get_by_id' ),
			)
		);
	}

	/**
	 * Check permissions for the REST API endpoint.
	 *
	 * @return bool True if the user has permission, false otherwise.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Get generated content by ID.
	 *
	 * @param \WP_REST_Request $request The REST request object.
	 * @return \WP_REST_Response The response containing the generated content.
	 */
	public function get_by_id( \WP_REST_Request $request ): \WP_REST_Response {
		$id = $request->get_param( 'id' );

		if ( empty( $id ) ) {
			return new \WP_REST_Response(
				array(
					'status'  => 'error',
					'message' => 'ID is required.',
				),
				400
			);
		}

		$model  = new Model( $id, $this->client );
		$status = $model->get_status();

		switch ( $status ) { 
			case 'complete':
				return new \WP_REST_Response(
					array(
						'id'         => $id,
						'components' => $model->get_components(),
						'status'     => $status,
					),
					200
				);
			
			case 'pending':
			case 'content_analysis_pending':
			case 'intent_analysis_pending':
			case 'components_pending':
				return new \WP_REST_Response(
					array(
						'id'     => $id,
						'status' => $status,
					),
					202
				);
			case 'error':
			default:
				return new \WP_REST_Response(
					array(
						'id'      => $id,
						'status'  => $status,
						'message' => $model->get_error_message(),
					),
					500
				);
		}
	}

	/**
	 * Generate content based on the provided prompt.
	 *
	 * @param \WP_REST_Request $request The REST request object.
	 * @return \WP_REST_Response The response containing the generated content.
	 */
	public function generate( \WP_REST_Request $request ): \WP_REST_Response {
		$prompt = $request->get_param( 'prompt' );

		if ( empty( $prompt ) ) {
			return new \WP_REST_Response(
				array(
					'status'  => 'error',
					'message' => 'Prompt is required.',
				),
				400
			);
		}

		$id    = uniqid();
		$model = new Model( $id, $this->client );
		$model->set_content( $prompt );

		return new \WP_REST_Response(
			array(
				'id'     => $id,
				'status' => $model->get_status(),
			),
			202
		);
	}

	/**
	 * Analyse the content structure and extract key information.
	 * 
	 * @param string $id The unique identifier for the content generation process.
	 */
	public function analyse_content( string $id ): void {
		$model = new Model( $id, $this->client );
		$model->analyse_content();
	}

	/**
	 * Analyse the intent of the content.
	 *
	 * @param string $id The unique identifier for the content generation process.
	 */
	public function analyse_intent( string $id ): void {
		$model = new Model( $id, $this->client );
		$model->analyse_intent();
	}

	/**
	 * Select the components based on the intent and content analysis.
	 * 
	 * @param string $id The unique identifier for the content generation process.
	 */
	public function select_components( string $id ): void {
		$model = new Model( $id, $this->client );
		$model->generate_components();
	}
}
