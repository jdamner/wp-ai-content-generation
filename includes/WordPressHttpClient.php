<?php
/**
 * WordPress HTTP Client for OpenAI
 *
 * @package \WpAiContentGeneration
 */

declare(strict_types=1);

namespace WpAiContentGeneration;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

/**
 * WordPress HTTP Client implementation for PSR-18
 * 
 * This client uses WordPress's wp_remote_request() functions
 */
class WordPressHttpClient implements ClientInterface {

	/**
	 * Send a PSR-7 request and return a PSR-7 response.
	 *
	 * @param RequestInterface $request The request to send.
	 * @return ResponseInterface The response.
	 * @throws WordPressHttpClientException If the HTTP request fails.
	 */
	public function sendRequest( RequestInterface $request ): ResponseInterface {
		$url     = (string) $request->getUri();
		$method  = $request->getMethod();
		$headers = array();
		$body    = (string) $request->getBody();

		// Convert PSR-7 headers to WordPress format.
		foreach ( $request->getHeaders() as $name => $values ) {
			$headers[ $name ] = implode( ', ', $values );
		}

		// Prepare WordPress HTTP request arguments.
		$args = array(
			'method'      => $method,
			'headers'     => $headers,
			'body'        => $body,
			'redirection' => 5,
			'httpversion' => '1.1',
			'user-agent'  => 'wp-ai-content-generation/1.0',
			'sslverify'   => true,
		);

		// Make the request using WordPress HTTP API.
		$response = \wp_remote_request( $url, $args );

		// Handle WordPress HTTP errors.
		if ( \is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			throw new WordPressHttpClientException( 'HTTP request failed: ' . \esc_html( $error_message ) );
		}

		// Extract response data.
		$status_code = \wp_remote_retrieve_response_code( $response );
		$body        = \wp_remote_retrieve_body( $response );
		$headers     = \wp_remote_retrieve_headers( $response );

		// Convert WordPress headers back to PSR-7 format.
		$psr7_headers = array();
		if ( is_array( $headers ) ) {
			foreach ( $headers as $name => $value ) {
				$psr7_headers[ $name ] = is_array( $value ) ? $value : array( $value );
			}
		}

		// Create and return PSR-7 response.
		return new Response(
			$status_code,
			$psr7_headers,
			$body
		);
	}
}
