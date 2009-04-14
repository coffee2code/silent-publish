<?php
/*
Plugin Name: Silent Publish
Version: 1.0
Plugin URI: http://coffee2code.com/wp-plugins/silent-publish
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

This plugin adds a "Publish silently" checkbox to the "Write Post" admin page.  If checked when the post is published, that
post will not trigger the pingbacks, trackbacks, and update service notifications that might typically occur.

In every other manner, the post is published as usual: it'll appear on the front page, archives, and feeds as expected, and no
other aspect of the post is affected.

While trackbacks and pingsbacks can already be disabled from the Add New Post/Page page, this plugin makes things easier by allowing
a single checkbox to disable those things, in addition to disabling notification of update services which otherwise could only
be disabled by clearing the value of the global setting, which would then affect all authors and any subsequently published posts.

If a post is silently published, a custom field '_silent_publish' for the post is set to a value of 1 as a means of recording the
action.  However, this value is not then used for any purpose as of yet.  Nor is the custom field unset or changed if the post is
later re-published.

Compatible with WordPress 2.6+, 2.7+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://coffee2code.com/wp-plugins/silent-publish.zip and unzip it into your 
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. Click the 'Publish silently' checkbox when publishing a post to prevent triggering of pingbacks, trackbacks, or notifications to update services.

*/

/*
Copyright (c) 2009 by Scott Reilly (aka coffee2code)

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

if ( !class_exists('SilentPublish') ) :

class SilentPublish {
	var $field = 'silent_publish';
	var $title = 'Silent Publish';
	var $label = 'Publish silently';
	var $help = 'If checked, upon publication of this post do not perform any pingbacks, trackbacks, or update service notifications.';
	var $meta_key = '_silent_publish';

	function SilentPublish() {
		$this->title = __($this->title);
		$this->label = __($this->label);
		$this->help = __($this->help);
		$this->meta_box = 'meta_box-' . $this->field;

		if ( is_admin() ) {
			add_action('admin_head', array(&$this, 'add_js'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
		}
		add_action('publish_post', array(&$this, 'publish_post'), 1, 1);
	}

	function admin_menu() {
		global $pagenow;
		$post_ID = (int) $_GET['post'];
		if ( $post_ID )
			$post = get_post($post_ID);

		// The silent publish capability is only exposed for a new post/page, or an existing post/page that is a draft or pending.
		if ( in_array($pagenow, array('post-new.php', 'page-new.php')) ||
			(!empty($post->ID) && in_array($post->post_status, array('draft', 'pending'))) ) {
			add_meta_box($this->meta_box, $this->title, array(&$this, 'add_meta_box'), 'post', 'side');
			add_meta_box($this->meta_box, $this->title, array(&$this, 'add_meta_box'), 'page', 'side');
		}
	}

	// For those with JS enabled, the checkbox is moved into the Publish meta_box and the plugin's meta_box is hidden.
	// The fallback for non-JS people is that the plugin's meta_box is shown and the checkbox can be found there.
	function add_js() {
		echo <<<JS
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#div-{$this->field}').insertBefore($('.misc-pub-section-last'));
			$('#{$this->meta_box}').hide();
		});
	
		</script>
JS;
	}

	function add_meta_box() {
		echo <<<HTML
		<div id="div-{$this->field}" class="misc-pub-section"><label class="selectit" for="{$this->field}" title="{$this->help}"><input type="checkbox" value="open" id="{$this->field}" name="{$this->field}" /> {$this->label}</label></div>
		<p>{$this->help}</p>
HTML;
	}

	function publish_post( $post_id ) {
		if ( isset($_POST[$this->field]) && $_POST[$this->field] ) {
			define('WP_IMPORTING', true);
			// Save the fact this post was silently published
			// This does not attempt to clear this value if the post later gets republished without silent publishing.
			// Also, this stored value is not currently used, merely saved.
			update_post_meta($post_id, $this->meta_key, 1);
		}
	}

} // end SilentPublish

endif; // end if !class_exists()

if ( class_exists('SilentPublish') )
	new SilentPublish();

?>