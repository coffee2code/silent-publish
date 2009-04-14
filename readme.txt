=== Silent Publish ===
Contributors: Scott Reilly
Donate link: http://coffee2code.com
Tags: publish, ping, no ping, trackback, update services, post
Requires at least: 2.6
Tested up to: 2.7.1
Stable tag: trunk
Version: 1.0

Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

== Description ==

Adds the ability to publish a post without triggering pingbacks, trackbacks, or notifying update services.

This plugin adds a "Publish silently" checkbox to the "Write Post" admin page.  If checked when the post is published, that post will not trigger the pingbacks, trackbacks, and update service notifications that might typically occur.

In every other manner, the post is published as usual: it'll appear on the front page, archives, and feeds as expected, and no other aspect of the post is affected.

While trackbacks and pingsbacks can already be disabled from the Add New Post/Page page, this plugin makes things easier by allowing a single checkbox to disable those things, in addition to disabling notification of update services which otherwise could only be disabled by clearing the value of the global setting, which would then affect all authors and any subsequently published posts.

If a post is silently published, a custom field '_silent_publish' for the post is set to a value of 1 as a means of recording the action.  However, this value is not then used for any purpose as of yet.  Nor is the custom field unset or changed if the post is later re-published.


== Installation ==

1. Unzip `silent-publish-v1.0.zip` inside the `/wp-content/plugins/` directory for your site
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Click the 'Publish silently' checkbox when publishing a post to prevent triggering of pingbacks, trackbacks, or notifications to update services.

== Screenshots ==

1. A screenshot of the 'Publish' sidebar box on the write/edit post admin page when Javascript is enabled.  The 'Publish silently' checkbox is integrated alongside the existing fields.
2. A screenshot of the 'Silent Publish' sidebar box on the write/edit post admin page when Javascript is disabled for the admin user.


