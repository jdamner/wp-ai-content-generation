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
 * @package BoxUk\WpAiContentGeneration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load Deps.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-assets.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-model.php';

// Load environment variables.
$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
$dotenv->load();
$dotenv->required( 'OPENAI_API_KEY' )->notEmpty();

// Initialize the plugin..
( new \BoxUk\WpAiContentGeneration\Api( \OpenAI::client( $_ENV['OPENAI_API_KEY'] ?? '' ) ) )->init(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
( new \BoxUk\WpAiContentGeneration\Assets() )->init();
