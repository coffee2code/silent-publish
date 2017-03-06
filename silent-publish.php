<?php
/**
 * Plugin Name: Silent Publish
 * Version:     2.5
 * Plugin URI:  http://coffee2code.com/wp-plugins/silent-publish/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: silent-publish
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.
 *
 * Compatible with WordPress 4.6+ through 4.7+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/silent-publish/
 *
 * @package Silent_Publish
 * @author  Scott Reilly
 * @version 2.5
 */

/*
 * TODO:
 * - Make it work for direct, non-UI calls to publish_post()
 */

/*
	Copyright (c) 2009-2017 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_SilentPublish' ) ) :

class c2c_SilentPublish {

	/**
	 * The name of the associated form field.
	 *
	 * @access private
	 * @var string
	 */
	private static $field    = 'silent_publish';

	/**
	 * Prevents instantiation.
	 *
	 * @since 2.6
	 */
	private function __construct() {}

	/**
	 * Prevents unserializing an instance.
	 *
	 * @since 2.6
	 */
	private function __wakeup() {}

	/**
	 * Returns version of the plugin.
	 *
	 * @since 2.2.1
	 */
	public static function version() {
		return '2.5';
	}

	/**
	 * Initializer.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Performs initialization tasks such as registering hooks.
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_silent_publish_meta_key' with default meta key name.
	 */
	public static function do_init() {
		// Load textdomain.
		load_plugin_textdomain( 'silent-publish' );

		// Register hooks.
		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'add_ui' ) );
		add_filter( 'wp_insert_post_data',         array( __CLASS__, 'save_silent_publish_status' ), 2, 2 );
		add_action( 'publish_post',                array( __CLASS__, 'publish_post' ), 1, 1 );
	}

	/**
	 * Returns the name of the meta key.
	 *
	 * @since 2.6
	 *
	 * @return string
	 */
	public static function get_meta_key_name() {
		// Default value.
		$meta_key = '_silent-publish';

		// Deprecated as of 2.3.
		$meta_key = apply_filters( 'silent_publish_meta_key', $meta_key );

		// Apply custom filter to obtain meta key name. Use blank string to disable
		// saving the silent publish status in a custom field.
		$meta_key = apply_filters( 'c2c_silent_publish_meta_key', $meta_key );

		return $meta_key;
	}

	/**
	 * Outputs the UI to prompt user if silent publish should be enabled for the post.
	 *
	 * Displays the UI outright if the post is not published. If published, it either
	 * displays hidden when the meta field has a value, or not at all.
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_silent_publish_default' with silent publish state default (false)
	 */
	public static function add_ui() {
		global $post;

		$hide = ( 'publish' == $post->post_status );

		if ( (bool) apply_filters( 'c2c_silent_publish_default', false, $post ) ) {
			$value = '1';
		} else {
			$value = get_post_meta( $post->ID, self::get_meta_key_name(), true );
		}

		$checked = checked( $value, '1', false );

		if ( ! $hide ) {
			printf(
				'<div class="misc-pub-section"><label class="selectit c2c-silent-publish" for="%1$s" title="%2$s">' . "\n",
				esc_attr( self::$field ),
				esc_attr_e( 'If checked, upon publication of this post do not perform any pingbacks, trackbacks, or update service notifications.', 'silent-publish' )
			);
		}

		if ( ! $hide || $checked ) {
			if ( $hide ) {
				$type = 'hidden';
				$checked = '';
			} else {
				$type = 'checkbox';
			}

			printf(
				'<input id="%1$s" type="%2$s" %3$s value="1" name="%4$s" />' . "\n",
				esc_attr( self::$field ),
				$type,
				$checked,
				esc_attr( self::$field )
			);
		}

		if ( ! $hide ) {
			_e( 'Silent publish?', 'silent-publish' );
			echo '</label></div>' . "\n";
		}
	}

	/**
	 * Updates the value of the silent publish custom field.
	 *
	 * @since 2.0
	 *
	 * @param  array $data    Data.
	 * @param  array $postarr Array of post fields and values for post being saved.
	 *
	 * @return array The unmodified $data.
	 */
	public static function save_silent_publish_status( $data, $postarr ) {
		$meta_key = self::get_meta_key_name();

		if (
			$meta_key
			&&
			isset( $postarr['post_type'] )
			&&
			'revision' != $postarr['post_type']
			&&
			! ( isset( $_POST['action'] ) && 'inline-save' == $_POST['action'] )
		) {
			// Update the value of the silent publish custom field.
			if ( isset( $postarr[ self::$field ] ) && $postarr[ self::$field ] ) {
				update_post_meta( $postarr['ID'], $meta_key, 1 );
			} else {
				delete_post_meta( $postarr['ID'], $meta_key );
			}
		}

		return $data;
	}

	/**
	 * Handles silent publishing if the associated checkbox is checked.
	 *
	 * Saves the fact this post was silently published.
	 * This does not attempt to clear this value if the post later gets republished without silent publishing.
	 * Also, this stored value is not currently used, merely saved.
	 *
	 * @since 1.0
	 *
	 * @param int $post_id Post ID.
	 */
	public static function publish_post( $post_id ) {

		// Look for the custom POST field
		if ( isset( $_POST[ self::$field ] ) && $_POST[ self::$field ] ) {

			// Trick WP into being silent by invoking its import mode
			define( 'WP_IMPORTING', true );

			// If a meta key name is defined, then set its value to 1
			if ( $meta_key = self::get_meta_key_name() ) {
				update_post_meta( $post_id, $meta_key, 1 );
			}

		}
	}

} // end c2c_SilentPublish

c2c_SilentPublish::init();

endif; // end if !class_exists()
