<?php
/**
 * @package Silent_Publish
 * @author Scott Reilly
 * @version 2.0.1
 */
/*
Plugin Name: Silent Publish
Version: 2.0.1
Plugin URI: http://coffee2code.com/wp-plugins/silent-publish/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: silent-publish
Description: Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

Compatible with WordPress 2.9+, 3.0+

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/silent-publish/

*/

/*
Copyright (c) 2009-2010 by Scott Reilly (aka coffee2code)

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
	var $field = 'silent_publish';
	var $meta_key = '_silent-publish'; // Filterable via 'silent_publish_meta_key' filter
	var $textdomain = 'silent-publish';
	var $textdomain_subdir = 'lang';

	/**
	 * Constructor
	 */
	function c2c_SilentPublish() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Register actions/filters and allow for configuration
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	function init() {
		$this->load_textdomain();

		$this->meta_key = esc_attr( apply_filters( 'silent_publish_meta_key', $this->meta_key ) );
		$this->meta_box = 'meta_box-' . $this->field;

		add_action( 'post_submitbox_misc_actions', array( &$this, 'add_ui' ) );
		add_filter( 'wp_insert_post_data', array( &$this, 'save_silent_publish_status' ), 2, 2 );
		add_action( 'publish_post', array( &$this, 'publish_post' ), 1, 1 );
	}

	/**
	 * Loads the localization textdomain for the plugin.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	function load_textdomain() {
		$subdir = empty( $this->textdomain_subdir ) ? '' : '/'.$this->textdomain_subdir;
		load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . $subdir );
	}

	/**
	 * Draws the UI to prompt user if silent publish should be enabled for the post.
	 *
	 * @since 2.0
	 *
	 * @return void (Text is echoed.)
	 */
	function add_ui() {
		global $post;
		if ( $post->post_status == 'publish' )
			return;
		$value = get_post_meta( $post->ID, $this->meta_key, true );
		$checked = checked( $value, '1', false );
		echo "<div class='misc-pub-section'><label class='selectit c2c-silent-publish' for='{$this->field}' title='";
		esc_attr_e( 'If checked, upon publication of this post do not perform any pingbacks, trackbacks, or update service notifications.', $this->textdomain );
		echo "'>\n";
		echo "<input id='{$this->field}' type='checkbox' $checked value='1' name='{$this->field}' />\n";
		_e( 'Silent publish?', $this->textdomain );
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
	function save_silent_publish_status( $data, $postarr ) {
		if ( isset( $postarr['post_type'] ) && ( 'revision' != $postarr['post_type'] ) ) {
			$new_value = isset( $postarr[$this->field] ) ? $postarr[$this->field] : '';
			update_post_meta( $postarr['ID'], $this->meta_key, $new_value );
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
	function publish_post( $post_id ) {
		if ( isset( $_POST[$this->field] ) && $_POST[$this->field] ) {
			define( 'WP_IMPORTING', true );
			update_post_meta( $post_id, $this->meta_key, 1 );
		}
	}

} // end c2c_SilentPublish

$GLOBALS['c2c_silent_publish'] = new c2c_SilentPublish();

endif; // end if !class_exists()

?>