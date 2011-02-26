=== Silent Publish ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: publish, ping, no ping, trackback, update services, post, coffee2code
Requires at least: 2.9
Tested up to: 3.1
Stable tag: 2.1
Version: 2.1

Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.


== Description ==

Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

This plugin adds a "Publish silently?" checkbox to the "Write Post" admin page.  If checked when the post is published, that post will not trigger the pingbacks, trackbacks, and update service notifications that might typically occur.

In every other manner, the post is published as usual: it'll appear on the front page, archives, and feeds as expected, and no other aspect of the post is affected.

While trackbacks and pingsbacks can already be disabled from the Add New Post/Page page, this plugin makes things easier by allowing a single checkbox to disable those things, in addition to disabling notification of update services which otherwise could only be disabled by clearing the value of the global setting, which would then affect all authors and any subsequently published posts.

If a post is silently published, a custom field '_silent_publish' for the post is set to a value of 1 as a means of recording the action.  However, this value is not then used for any purpose as of yet.  Nor is the custom field unset or changed if the post is later re-published.

Also see my "Stealth Publish" plugin if you want make a new post but prevent it from appearing on the front page of your blog and not appear in feeds.  (That plugin incorporates this plugin's functionality, so you won't need both.)

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/silent-publish/) | [Author Homepage](http://coffee2code.com)


== Installation ==
1. Unzip `silent-publish.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Click the 'Publish silently?' checkbox when publishing a post to prevent triggering of pingbacks, trackbacks, or notifications to update services.


== Screenshots ==

1. A screenshot of the 'Publish' sidebar box on the Add New Post admin page.  The 'Publish silently?' checkbox is integrated alongside the existing fields.
2. A screenshot of the 'Silent publish?' checkbox displaying help text when hovering over the checkbox.


== Frequently Asked Questions ==

= Why would I want to silent publish a post? =

Perhaps for a particular post you don't want any external notifications sent out.  If checked when the post is published, that post will not trigger the pingbacks, trackbacks, and update service notifications that might typically occur.


== Filters ==

The plugin is further customizable via one filter. Typically, these customizations would be put into your active theme's functions.php file, or used by another plugin.

= silent_publish_meta_key (filter) =

The 'silent_publish_meta_key' filter allows you to override the name of the custom field key used by the plugin to store a post's silent publish status.  This isn't a common need.

Arguments:

* $custom_field_key (string): The custom field key to be used by the plugin.  By default this is '_silent-publish'.

Example:

`
add_filter( 'silent_publish_meta_key', 'override_silent_publish_key' );
function override_silent_publish_key( $custom_field_key ) {
	return '_my_custom_silent-publish';
}
`


== Changelog ==

= 2.1 =
* Switch from object instantiation to direct class invocation
* Explicitly declare all functions public static and class variables private static
* Remove setting unnecessary variable
* Note compatibility through WP 3.1+
* Update copyright date (2011)

= 2.0.1 =
* Bugfix for auto-save losing value of silent publish status

= 2.0 =
* Re-implemented entire approach
* Allow overriding of custom field used via 'silent_publish_meta_key' filter
* Add class of 'c2c-silent-publish' to admin UI div containing checkbox
* Add filter 'silent_publish_meta_key' to allow overriding custom field key name
* Remove function add_js(), admin_menu(), add_meta_box()
* Add functions init(), add_ui(), save_silent_publish_status(), load_textdomain()
* Add true localization support
* Move definition of strings into init() and properly support localization
* Full support for localization
* Store plugin instance in global variable, $c2c_silent_publish, to allow for external manipulation
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Minor code reformatting (spacing)
* Add PHPDoc documentation
* Note compatibility with WP 2.9+, 3.0+
* Drop compatibility with versions of WP older than 2.9
* Update screenshots
* Update copyright date
* Add package info to top of plugin file
* Add Changelog, Frequently Asked Questions, Filters, and Upgrade Notice sections to readme.txt
* Add .pot file
* Add to plugin repository

= 1.0 =
* Initial release


== Upgrade Notice ==

= 2.1 =
Minor update: implementation changes; noted compatibility with WP 3.1+ and updated copyright date.

= 2.0.1 =
Recommended bugfix release.  Fixes bug where auto-save can lose value of silent publish status.

= 2.0 =
Recommended major update! Highlights: re-implemented; added filters for customization; localization support; use hidden custom field; misc non-functionality changes; verified WP 3.0 compatibility; dropped compatibility with version of WP older than 2.9.