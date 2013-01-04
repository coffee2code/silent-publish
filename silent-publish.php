<?php
/**
 * @package Silent_Publish
 * @author Scott Reilly
 * @version 2.3
 */
/*
Plugin Name: Silent Publish
Version: 2.3
Plugin URI: http://coffee2code.com/wp-plugins/silent-publish/
Author: Scott Reilly
Author URI: http://coffee2code.com/
Text Domain: silent-publish
Domain Path: /lang/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Description: Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

Compatible with WordPress 2.9+ through 3.5+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/silent-publish/
*/

/*
	Copyright (c) 2009-2013 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_SilentPublish' ) ) :

class c2c_SilentPublish {

	private static $field    = 'silent_publish';
	private static $meta_key = '_silent-publish'; // Filterable via 'c2c_silent_publish_meta_key' filter

	/**
	 * Returns version of the plugin.
	 *
	 * @since 2.2.1
	 */
	public static function version() {
		return '2.3';
	}

	/**
	 * Initializer
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Register actions/filters and allow for configuration
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_silent_publish_meta_key' with default meta key name
	 */
	public static function do_init() {

		// Load textdomain
		load_plugin_textdomain( 'silent-publish', false, basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'lang' );

		// Deprecated as of 2.3
		self::$meta_key = apply_filters( 'silent_publish_meta_key', self::$meta_key );

		// Apply custom filter to obtain meta key name. Leave blank to disable saving the silent
		// publish status in a custom field.
		self::$meta_key = esc_attr( apply_filters( 'c2c_silent_publish_meta_key', self::$meta_key ) );

		// Register hooks
		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'add_ui' ) );
		add_filter( 'wp_insert_post_data',         array( __CLASS__, 'save_silent_publish_status' ), 2, 2 );
		add_action( 'publish_post',                array( __CLASS__, 'publish_post' ), 1, 1 );
	}

	/**
	 * Draws the UI to prompt user if silent publish should be enabled for the post.
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_silent_publish_default' with silent publish state default (false)
	 *
	 * @return void (Text is echoed.)
	 */
	public static function add_ui() {
		global $post;

		if ( 'publish' == $post->post_status )
			return;

		if ( (bool) apply_filters( 'c2c_silent_publish_default', false, $post ) )
			$value = '1';
		else
			$value = get_post_meta( $post->ID, self::$meta_key, true );

		$checked = checked( $value, '1', false );

		echo "<div class='misc-pub-section'><label class='selectit c2c-silent-publish' for='" . self::$field . "' title='";
		esc_attr_e( 'If checked, upon publication of this post do not perform any pingbacks, trackbacks, or update service notifications.', 'silent-publish' );
		echo "'>\n";
		echo "<input id='" . self::$field . "' type='checkbox' $checked value='1' name='" . self::$field . "' />\n";
		_e( 'Silent publish?', 'silent-publish' );
		echo '</label></div>' . "\n";
	}

	/**
	 * Update the value of the silent publish custom field, but only if it is supplied.
	 *
	 * @since 2.0
	 *
	 * @param array $data Data
	 * @param array $postarr Array of post fields and values for post being saved
	 * @return array The unmodified $data
	 */
	public static function save_silent_publish_status( $data, $postarr ) {
		if ( self::$meta_key &&
			 isset( $postarr['post_type'] ) &&
			 ( 'revision' != $postarr['post_type'] ) &&
			 ! ( isset( $_POST['action'] ) && 'inline-save' == $_POST['action'] )
			) {
			$new_value = isset( $postarr[ self::$field ] ) ? $postarr[ self::$field ] : '';
			update_post_meta( $postarr['ID'], self::$meta_key, $new_value );
		}
		return $data;
	}

	/**
	 * Handles silent publishing if the associated checkbox is checked.
	 *
	 * Save the fact this post was silently published
	 * This does not attempt to clear this value if the post later gets republished without silent publishing.
	 * Also, this stored value is not currently used, merely saved.
	 *
	 * @since 1.0
	 *
	 * @param int $post_id Post ID
	 * @return void
	 */
	public static function publish_post( $post_id ) {

		// Look for the custom POST field
		if ( isset( $_POST[ self::$field ] ) && $_POST[ self::$field ] ) {

			// Trick WP into being silent by invoking its import mode
			define( 'WP_IMPORTING', true );

			// If a meta key name is defined, then set its value to 1
			if ( self::$meta_key )
				update_post_meta( $post_id, self::$meta_key, 1 );
		}
	}

} // end c2c_SilentPublish

c2c_SilentPublish::init();

endif; // end if !class_exists()
