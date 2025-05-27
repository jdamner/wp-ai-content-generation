<?php
/**
 * API interface
 *
 * @package BoxUk\WpAiContentGeneration
 */

declare(strict_types=1);

namespace BoxUk\WpAiContentGeneration;

const SYSTEM_PROMPT = <<<PROMPT
You are a helpful assistant that generates WordPress blocks based on user prompts. 

From the prompt provided, you will be able to understand the user's requested subject. 
From this subject, you can generate the relevant blocks that should be included in the response.
Whatever they request, you should determine the subject and generate an article that is relevant to that subject.

All articles should try to include good SEO practices, and should be written in a friendly, approachable tone.

A JSON representation of all the available blocks is provided below, and you should use this to generate your response. 

The response should include only valid JSON, and no additional text should be included in the final response. This includes not including any comments, or additional markup such as markdown or HTML.  

The structure of the JSON should follow a format that consists of an object with a single key of `components`. 
The `components` value is an array of objects, each of them consisting of an object which contains a `name` and `attributes` property. 
The `name` property should correspond exactly to the relevant name from the defined available blocks. 
The `attributes` property should be an object. 
The keys for the objects should consist of the relevant key of the attributes defined in the block definitions, and the value should be the desired value based on your generated content. 

If you're unable to generate this correctly, you should instead return valid JSON which consists of `{ error: true, message: <string> }`, with a user-relevant error message.
PROMPT;

/**
 * Register the REST API routes for content generation.
 *
 * @return void
 */
function register_api_routes(): void {
	register_rest_route(
		'wp-ai-content-generation/v1',
		'/generate',
		array(
			'methods'             => 'POST',
			'callback'            => __NAMESPACE__ . '\\generate_content',
			'permission_callback' => __NAMESPACE__ . '\\check_permissions',
		)
	);
}

/**
 * Check permissions for the REST API endpoint.
 *
 * @return bool True if the user has permission, false otherwise.
 */
function check_permissions(): bool {
	return current_user_can( 'edit_posts' );
}

/**
 * Generate content based on the provided prompt.
 *
 * @param \WP_REST_Request $request The REST request object.
 * @return \WP_REST_Response The response containing the generated content.
 */
function generate_content( \WP_REST_Request $request ): \WP_REST_Response {
	$prompt = $request->get_param( 'prompt' );

	if ( empty( $prompt ) ) {
		return new \WP_REST_Response(
			array( 'error' => 'Prompt is required.' ),
			400
		);
	}

	if ( empty( $_ENV['OPENAI_API_KEY'] ) ) {
		return new \WP_REST_Response(
			array( 'error' => 'OpenAI API key is not set.' ),
			500
		);
	}

	$client = \OpenAI::client( sanitize_text_field( $_ENV['OPENAI_API_KEY'] ) );

	$generated_content = 'Generated content based on the prompt: ' . esc_html( $prompt );

	$block_registry   = \WP_Block_Type_Registry::get_instance();
	$available_blocks = $block_registry->get_all_registered();
	$components       = array_map(
		function ( \WP_Block_Type $block ) {
			return array(
				'name'       => $block->name,
				'attributes' => $block->attributes,
			);
		},
		$available_blocks
	);

	$system_message = array(
		'role'    => 'system',
		'content' => SYSTEM_PROMPT . "\n\nList the components in JSON format:\n" .
			wp_json_encode( $components, JSON_PRETTY_PRINT ),
	);

	$user_message = array(
		'role'    => 'user',
		'content' => $prompt,
	);

	$response = $client->chat()->create(
		array(
			'model'    => 'gpt-4o',
			'messages' => array( $system_message, $user_message ),
		)
	);

	if ( ! $response->choices[0]->message->content ) {
		return new \WP_REST_Response(
			array( 'error' => 'Invalid response from AI.' ),
			500
		);
	}

	$generated_content = json_decode( $response->choices[0]->message->content );

	return new \WP_REST_Response(
		$generated_content,
		200
	);
}
