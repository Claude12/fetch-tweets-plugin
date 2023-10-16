<?php

/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks, following the latest Gutenberg recommendations.
 * Ensures performance optimization and security in alignment with WP best practices.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly for security.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @since 1.0.0
 */
function tweets_block_cgb_block_assets()
{
	// Styles path.
	$style_path = 'dist/blocks.style.build.css';

	// JS path.
	$js_path = 'dist/blocks.build.js';

	// Editor styles path.
	$editor_style_path = 'dist/blocks.editor.build.css';

	// Register block styles for both frontend + backend.
	wp_register_style(
		'tweets_block-cgb-style-css',
		plugins_url($style_path, dirname(__FILE__)),
		is_admin() ? array('wp-editor') : null,
		filemtime(plugin_dir_path(dirname(__FILE__)) . $style_path) // File-based versioning.
	);

	// Register block editor script for backend.
	wp_register_script(
		'tweets_block-cgb-block-js',
		plugins_url($js_path, dirname(__FILE__)),
		array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'),
		filemtime(plugin_dir_path(dirname(__FILE__)) . $js_path), // File-based versioning.
		true // Enqueue in the footer for performance.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'tweets_block-cgb-block-editor-css',
		plugins_url($editor_style_path, dirname(__FILE__)),
		array('wp-edit-blocks'),
		filemtime(plugin_dir_path(dirname(__FILE__)) . $editor_style_path) // File-based versioning.
	);

	// Localize script for dynamic data access.
	wp_localize_script(
		'tweets_block-cgb-block-js',
		'cgbGlobal',
		[
			'pluginDirPath' => plugin_dir_path(__DIR__),
			'pluginDirUrl'  => plugin_dir_url(__DIR__),
		]
	);

	// Register the block for both frontend and backend.
	register_block_type(
		'cgb/block-tweets-block',
		array(
			'style'         => 'tweets_block-cgb-style-css',
			'editor_script' => 'tweets_block-cgb-block-js',
			'editor_style'  => 'tweets_block-cgb-block-editor-css',
			'render_callback' => 'render_tweets_block',
		)
	);
}

/**
 * Render the block's content.
 *
 * @return string Block content.
 */
function render_tweets_block()
{
	$transient_key = 'cached_tweets_block_headers';
	$cached_data = get_transient($transient_key);

	if (false !== $cached_data) {
		return $cached_data; // Return the cached data if it exists.
	}

	$response = wp_remote_get('https://httpbin.org/get');
	if (is_wp_error($response)) {
		return 'Error fetching headers.';
	}

	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body);
	$headersData = $data->headers;

	$output = '<div class="wp-block-cgb-block-tweets-block"><h4>Headers from API</h4><ul>';
	foreach ($headersData as $key => $value) {
		$output .= "<li><strong>$key</strong>: $value</li>";
	}
	$output .= '</ul></div>';

	// Store the output into a transient, set to expire in 12 hours (43200 seconds).
	set_transient($transient_key, $output, 43200);

	return $output;
}

add_action('rest_api_init', function () {
	register_rest_route('wp/v2', '/get-tweets-headers', array(
		'methods' => 'GET',
		'callback' => 'render_tweets_block',
	));
});

// Enqueue block assets.
add_action('init', 'tweets_block_cgb_block_assets');
