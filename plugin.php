<?php
/**
 * Plugin Name: Tweets Block
 * Plugin URI: https://github.com/Claude12/tweets-block
 * Description: Tweets Block — is a Gutenberg plugin created via create-guten-block.
 * Author: Claude12
 * Author URI: https://AhmadAwais.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
