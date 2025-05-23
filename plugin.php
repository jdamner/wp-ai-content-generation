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

namespace BoxUk\WpAiContentGeneration;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
$dotenv->load();

require_once plugin_dir_path( __FILE__ ) . 'includes/api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/assets.php';

// Initialise on relevant hooks.
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_assets' );
add_action( 'rest_api_init', __NAMESPACE__ . '\\register_api_routes' );
