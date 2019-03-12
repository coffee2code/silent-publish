=== Silent Publish ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: publish, ping, no ping, trackback, update services, post, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 5.1
Stable tag: 2.6.1

Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.


== Description ==

This plugin gives you the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

A "Publish silently?" checkbox is added to the "Add New Post" and "Edit Post" admin pages (the latter only for unpublished posts). If checked when the post is published, that post will not trigger the pingbacks, trackbacks, and update service notifications that would typically occur.

In every other manner, the post is published as usual: it'll appear on the front page, archives, and feeds as expected, and no other aspect of the post is affected.

While trackbacks and pingsbacks can already be disabled from the Add New Post/Page page, this plugin makes things easier by allowing a single checkbox to disable those things, in addition to disabling notification of update services which otherwise could only be disabled by clearing the value of the global setting, which would then affect all authors and any subsequently published posts.

If a post is silently published, a custom field '_silent_publish' for the post is set to a value of 1 as a means of recording the action. However, this value is not used after publish for any purpose as of yet. Nor is the custom field unset or changed if the post is later re-published.

Also see my [Stealth Publish](https://wordpress.org/plugins/stealth-publish/) plugin if you want make a new post but prevent it from appearing on the front page of your blog and in feeds. (That plugin incorporates this plugin's functionality, so you won't need both.)

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/silent-publish/) | [Plugin Directory Page](https://wordpress.org/plugins/silent-publish/) | [GitHub](https://github.com/coffe2code/silent-publish/) | [Author Homepage](http://coffee2code.com)


== Installation ==
1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
2. Install via the built-in WordPress plugin installer. Or download and unzip `silent-publish.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
3. Activate the plugin through the 'Plugins' admin menu in WordPress
4. Click the 'Publish silently?' checkbox when publishing a post to prevent triggering of pingbacks, trackbacks, or notifications to update services.


== Screenshots ==

1. A screenshot of the 'Publish' sidebar box on the Add New Post admin page. The 'Publish silently?' checkbox is integrated alongside the existing fields.
2. A screenshot of the 'Silent publish?' checkbox displaying help text when hovering over the checkbox.


== Frequently Asked Questions ==

= Why would I want to silent publish a post? =

Perhaps for a particular post you don't want any external notifications sent out. If checked when the post is published, that post will not trigger the pingbacks, trackbacks, and update service notifications that might typically occur.

= Can I have the checkbox checked by default? =

Yes. See the Filters section (under Other Notes) and look for the example using the 'c2c_silent_publish_default' filter. You'll have to put that code into your active theme's functions.php file or a mu-plugin file.

= Why is the "Silent publish?" checkbox disabled? =

If the "Silent publish?" checkbox had been checked at the time a post is published, the field will be shown but will disabled for that published post. Once a post is published, changing the value of the checkbox has no meaning, so there is no need to make it checkable. If you unpublish the post, the checkbox will again be clickable.

= Why did the "Silent publish?" checkbox disappear? =

If the "Silent publish?" checkbox had not been checked at the time a post is published, the field will no longer be shown for that published post. Once a post is published, changing the value of the checkbox has no meaning, so there is no need to show it. If you unpublish the post, the checkbox will reappear.

= Can I change my mind after I silently publish a post to post it again without it being silent? =

Yes. You must first unpublish the post (by making it a draft or pending). Then uncheck the "Publish silently?" checkbox and republish the post.

= Does this prevent email notifications from going out to people subscribed to receive a notice about new posts to the site? =

No.

= Does this prevent the post being automatically shared to, or announced on, social media sites (Facebook, Twitter, etc)? =

No. your posts will continue to be shared to social media sites upon publication (assuming it is configured to do so by whatever plugins you have in place to share your posts).

= Does this plugin include unit tests? =

Yes.


== Hooks ==

The plugin is further customizable via two filters. Code using these filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain). Less ideally, you could put them in your active theme's functions.php file.

**c2c_silent_publish_meta_key (filter)**

The 'c2c_silent_publish_meta_key' filter allows you to override the name of the custom field key used by the plugin to store a post's silent publish status. This isn't a common need.

Arguments:

* $custom_field_key (string): The custom field key to be used by the plugin. By default this is '_silent-publish'.

Example:

`
/**
 * Defines a custom meta key to be used by Silent Publish.
 *
 * @param string $custom_field_key The default custom field key name.
 * @return string
 */
function override_silent_publish_key( $custom_field_key ) {
	return '_my_custom_silent-publish';
}
add_filter( 'c2c_silent_publish_meta_key', 'override_silent_publish_key' );
`

**c2c_silent_publish_default (filter)**

The 'c2c_silent_publish_default' filter allows you to override the default state of the 'Silent Publish?' checkbox.

Arguments:

* $state (boolean): The default state of the checkbox. By default this is false.
* $post (WP_Post): The post currently being created/edited.

Example:

`
// Have the Silent Publish? checkbox checked by default.
add_filter( 'c2c_silent_publish_default', '__return_true' );
`


== Changelog ==

= () =
* New: Add `is_silent_publish_on_by_default()` to determine if silent publish should be enabled for posts by default
* New: Add CHANGELOG.md and move all but most recent changelog entries into it
* New: Add inline documentation for hooks
* Fix: Use proper variable name when obtaining default meta key name
* Change: Initialize plugin on 'plugins_loaded' action instead of on load
* Change: Merge `do_init()` into `init()`
* Change: Use `apply_filters_deprecated()` to formally deprecate the 'silent_publish_meta_key' filter
* Change: Note compatibility through WP 5.1+
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS

= 2.6.1 (2018-07-12) =
* New: Add README.md
* New: Add GitHub link to readme
* Bugfix: Fix a pair of unit tests by correctly applying `do_action()` instead of `apply_filters()`
* Change: Minor whitespace tweaks to unit test bootstrap
* Change: Note compatibility through WP 4.9+
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Update copyright date (2018)

= 2.6 (2017-03-08) =
* Change: Overhaul how setting gets saved
    * Hook 'save_post' action instead of 'wp_insert_post_data'
    * Ensure setting value isn't saved if no meta key name is set, or the post is a revision or autosave
* Change: Overhaul how silent publishing is implemented
    * Instead of set `WP_IMPORTING` constant, unhook `_publish_post_hook()` from 'publish_post' action
    * No need to potentially save the value of the meta field
* Change: Show the "Silent publish?" checkbox as checked but disabled once a post has been silently published
* Change: Add nonce alongside checkbox
* Change: Add `get_meta_key_name()` as getter for meta_key name, allowing for late filtering
* Change: Prevent object instantiation of the class
* Change: Use `sprintf()` to produce markup rather than concatenating various strings, function calls, and variables
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Minor inline code documentation formatting changes (punctuation, verb tense)
* Change: Note compatibility through WP 4.7+
* Change: Remove support for WordPress older than 4.6 (should still work for earlier versions back to WP 3.6)
* Change: Update readme.txt content and formatting
* Change: Add more FAQs
* Change: Add more unit tests
* Change: Update copyright date (2017)

= 2.5 (2016-03-28) =
Highlights:
* This release adds support for language packs and has some minor behind-the-scenes changes.

Details:
* Change: Run `esc_attr()` on the field name before display for safety.
* Change: Don't run `esc_attr()` on meta key name as it need not be so restrictive (and isn't for display).
* Change: Add support for language packs:
    * Don't load textdomain from file.
    * Remove 'Domain Path' from plugin header.
    * Remove .pot file and /lang subdirectory.
* New: Add LICENSE file.
* New: Add empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Add docblocks for class variables.
* Change: Add docblock to example code.
* Change: Note compatibility through WP 4.5+.
* Change: Update copyright date (2016).

= Full changelog is available in [CHANGELOG.md](CHANGELOG.md). =


== Upgrade Notice ==

= 2.6.1 =
Trivial update: fixed a couple broken unit tests, noted compatibility through WP 4.9+, added README.md for GitHub, and updated copyright date (2018)

= 2.6 =
Recommended release: fairly significant rewrite, show "Silent publish?" checkbox as checked but disabled once post has been silently published, noted compatibility through WP 4.7+, dropped compatibility with WP older than 4.6, more

= 2.5 =
Minor release: improved support for localization; verified compatibility through WP 4.5; updated copyright date (2016)

= 2.4.2 =
Bugfix release (for sites using the ancient PHP 5.2): revert use of __DIR__ constant since it wasn't introduced until PHP 5.3

= 2.4.1 =
Trivial update: added more unit tests; noted compatibility through WP 4.1+; updated copyright date (2015); added plugin icon

= 2.4 =
Recommended minor update: fix to preserve silent publishing status after being published; added unit tests; noted compatibility through WP 3.8+; dropped compatibility with versions of WP older than 3.6

= 2.3 =
Recommended update: renamed and deprecated a filter; noted compatibility through WP 3.5+; and more.

= 2.2.1 =
Minor update: moved .pot file into 'lang' subdirectory; noted compatibility through WP 3.3+.

= 2.2 =
Minor update: fixed bug with losing Silent Publish status during Quick Edit; added new filter to allow making checkbox checked by default; noted compatibility through WP 3.2+

= 2.1 =
Minor update: implementation changes; noted compatibility with WP 3.1+ and updated copyright date.

= 2.0.1 =
Recommended bugfix release. Fixes bug where auto-save can lose value of silent publish status.

= 2.0 =
Recommended major update! Highlights: re-implemented; added filters for customization; localization support; use hidden custom field; misc non-functionality changes; verified WP 3.0 compatibility; dropped compatibility with version of WP older than 2.9.
