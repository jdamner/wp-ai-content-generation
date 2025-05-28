<?php
/**
 * Asset Loader
 *
 * @package BoxUk\WpAiContentGeneration
 */

declare(strict_types=1);

namespace BoxUk\WpAiContentGeneration;

/**
 * Asset Loader
 */
class Assets { 

	/**
	 * Initialize the Assets class.
	 */
	public function init(): void {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue scripts and styles for the admin area.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$asset = include_once plugin_dir_path( __DIR__ ) . 'build/index.asset.php';
		if ( ! $asset ) {
			wp_die(
				'Asset file not found. Please run `npm run build` to generate the assets.',
				'Asset Error',
				array( 'response' => 500 )
			);
		}

		wp_enqueue_script(
			'list-icons-script',
			plugins_url( 'build/index.js', __DIR__ ),
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
