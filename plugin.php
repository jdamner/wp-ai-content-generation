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
 */

namespace JdAmner\WpAiContentGeneration;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


function enqueue_assets() {
    $asset = include_once plugin_dir_path(__FILE__) . 'build/index.asset.php';
    if (! $asset) {
        return;
    }

    wp_enqueue_style(
        'list-icons-style',
        plugins_url('build/index.css', __FILE__),
        [],
        $asset['version'],
        'all'
    );

    wp_enqueue_script(
        'list-icons-script',
        plugins_url('build/index.js', __FILE__),
        $asset['dependencies'],
        $asset['version'],
        true
    );
}

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_assets' );
