<?php
/**
 * Asset-related functions and filters.
 *
 * This file holds some setup actions for scripts and styles as well as a helper
 * functions for work with assets.
 *
 * @package    Mythic
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2018, Justin Tadlock
 * @link       https://themehybrid.com/themes/mythic
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Mythic;

use Hybrid\App;

/**
 * Enqueue scripts/styles for the front end.
 *
 * @link   https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @link   https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @since  1.0.0
 * @access public
 * @return void
 */
add_action( 'wp_enqueue_scripts', function() {

	// Load WordPress' comment-reply script where appropriate.
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_script( 'mythic-app', asset( 'scripts/app.js' ), null, null, true );

	wp_enqueue_style( 'mythic-screen', asset( 'styles/screen.css' ), null, null );

}, 5 );

/**
 * Enqueue scripts/styles for the editor.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
add_action( 'enqueue_block_editor_assets', function() {

	wp_enqueue_style( 'mythic-editor', asset( 'styles/editor.css' ), null, null );

	// Unregister core block and theme styles.
	wp_deregister_style( 'wp-core-blocks' );
	wp_deregister_style( 'wp-core-blocks-theme' );

	// Re-register core block and theme styles when empty string. This is
	// necessary to get styles set up correctly.
	wp_register_style( 'wp-core-blocks', '' );
	wp_register_style( 'wp-core-blocks-theme', '' );

}, 5 );

/**
 * Helper function for outputting an asset URL in the theme. This integrates
 * with Laravel Mix for handling cache busting. If used when you enqueue a script
 * or style, it'll append an ID to the filename.
 *
 * @link   https://laravel.com/docs/5.6/mix#versioning-and-cache-busting
 * @since  1.0.0
 * @access public
 * @param  string  $path
 * @return string
 */
function asset( $path ) {

	// Get the Laravel Mix manifest.
	$manifest = App::resolve( 'mythic/mix' );

	// Make sure to trim any slashes from the front of the path.
	$path = '/' . ltrim( $path, '/' );

	if ( $manifest && isset( $manifest[ $path ] ) ) {
		$path = $manifest[ $path ];
	}

	return get_theme_file_uri( 'dist' . $path );
}
