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

// Fix for WordPress Playground compatibility - define missing constants.
if ( ! defined( 'STDERR' ) ) {
	define( 'STDERR', fopen( 'php://stderr', 'w' ) );
}

// Load Deps.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Load Action Scheduler.
require_once plugin_dir_path( __FILE__ ) . 'vendor/woocommerce/action-scheduler/action-scheduler.php';

// Initialize the plugin settings.
( new \WpAiContentGeneration\Settings() )->init();

// Initialize the plugin assets.
( new \WpAiContentGeneration\Assets() )->init();

// Initialize the API only if API key is configured.
add_action(
	'init',
	function () {
		if ( \WpAiContentGeneration\Settings::is_api_key_configured() ) {
			( new \WpAiContentGeneration\Api(
				\OpenAI::factory()
				->withApiKey( \WpAiContentGeneration\Settings::get_api_key() )
				->withHttpClient( new \WpAiContentGeneration\WordPressHttpClient() )
				->make() 
			) )->init();
		}
	} 
);
