<?php
/**
 * @package Silent_Publish
 * @author Scott Reilly
 * @version 2.1
 */
/*
Plugin Name: Silent Publish
Version: 2.1
Plugin URI: http://coffee2code.com/wp-plugins/silent-publish/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: silent-publish
Description: Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

Compatible with WordPress 2.9+, 3.0+, 3.1+

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/silent-publish/

TODO:
	* Allow for silent publish to be enabled by default

*/

/*
Copyright (c) 2009-2011 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( !class_exists( 'c2c_SilentPublish' ) ) :

class c2c_SilentPublish {
	private static $field             = 'silent_publish';
	private static $meta_key          = '_silent-publish'; // Filterable via 'silent_publish_meta_key' filter
	private static $textdomain        = 'silent-publish';
	private static $textdomain_subdir = 'lang';

	/**
	 * Constructor
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Register actions/filters and allow for configuration
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public static function do_init() {
		self::load_textdomain();
		self::$meta_key = esc_attr( apply_filters( 'silent_publish_meta_key', self::$meta_key ) );

		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'add_ui' ) );
		add_filter( 'wp_insert_post_data',         array( __CLASS__, 'save_silent_publish_status' ), 2, 2 );
		add_action( 'publish_post',                array( __CLASS__, 'publish_post' ), 1, 1 );
	}

	/**
	 * Loads the localization textdomain for the plugin.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public static function load_textdomain() {
		$subdir = empty( self::$textdomain_subdir ) ? '' : ( '/' . self::$textdomain_subdir );
		load_plugin_textdomain( self::$textdomain, false, basename( dirname( __FILE__ ) ) . $subdir );
	}

	/**
	 * Draws the UI to prompt user if silent publish should be enabled for the post.
	 *
	 * @since 2.0
	 *
	 * @return void (Text is echoed.)
	 */
	public static function add_ui() {
		global $post;
		if ( 'publish' == $post->post_status )
			return;
		$value = get_post_meta( $post->ID, self::$meta_key, true );
		$checked = checked( $value, '1', false );
		echo "<div class='misc-pub-section'><label class='selectit c2c-silent-publish' for='" . self::$field . "' title='";
		esc_attr_e( 'If checked, upon publication of this post do not perform any pingbacks, trackbacks, or update service notifications.', self::$textdomain );
		echo "'>\n";
		echo "<input id='" . self::$field . "' type='checkbox' $checked value='1' name='" . self::$field . "' />\n";
		_e( 'Silent publish?', self::$textdomain );
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
		if ( isset( $postarr['post_type'] ) && ( 'revision' != $postarr['post_type'] ) ) {
			$new_value = isset( $postarr[self::$field] ) ? $postarr[self::$field] : '';
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
		if ( isset( $_POST[self::$field] ) && $_POST[self::$field] ) {
			define( 'WP_IMPORTING', true );
			update_post_meta( $post_id, self::$meta_key, 1 );
		}
	}

} // end c2c_SilentPublish

c2c_SilentPublish::init();

endif; // end if !class_exists()

?>