<?php
/**
 * Plugin Name:       WordPress AI Content Generation
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            James Amner<jdamner@me.com>
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-ai-content-generation
 * 
 * @package WpAiContentGeneration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load Deps.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

try {
	// Load environment variables.
	$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
	$dotenv->load();
	$dotenv->required( 'OPENAI_API_KEY' )->notEmpty();

} catch ( \Dotenv\Exception\ValidationException $e ) {
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is a valid use case for error_log.
	error_log( 'Failed to load environment variables: ' . $e->getMessage() );
	return;
}

// Load Action Scheduler.
require_once plugin_dir_path( __FILE__ ) . 'vendor/woocommerce/action-scheduler/action-scheduler.php';

// Initialize the plugin..
( new \WpAiContentGeneration\Api( \OpenAI::client( $_ENV['OPENAI_API_KEY'] ?? '' ) ) )->init(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
( new \WpAiContentGeneration\Assets() )->init();
