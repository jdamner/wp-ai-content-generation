<?php
/**
 * Asset Loader
 *
 * @package BoxUk\WpAiContentGeneration
 */

declare(strict_types=1);

namespace BoxUk\WpAiContentGeneration;

/**
 * Enqueue scripts and styles for the admin area.
 *
 * @return void
 */
function enqueue_assets() {
	$asset = include_once plugin_dir_path( __DIR__ ) . 'build/index.asset.php';
	if ( ! $asset ) {
		return;
	}

	wp_enqueue_script(
		'list-icons-script',
		plugins_url( 'build/index.js', __DIR__ ),
		$asset['dependencies'],
		$asset['version'],
		true
	);
}
