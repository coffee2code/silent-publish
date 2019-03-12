# Changelog

## 2.6.1 _(2018-07-12)_
* New: Add README.md
* New: Add GitHub link to readme
* Bugfix: Fix a pair of unit tests by correctly applying `do_action()` instead of `apply_filters()`
* Change: Minor whitespace tweaks to unit test bootstrap
* Change: Note compatibility through WP 4.9+
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Update copyright date (2018)

## 2.6 _(2017-03-08)_
* Change: Overhaul how setting gets saved
    * Hook `save_post` action instead of `wp_insert_post_data`
    * Ensure setting value isn't saved if no meta key name is set, or the post is a revision or autosave
* Change: Overhaul how silent publishing is implemented
    * Instead of set `WP_IMPORTING` constant, unhook `_publish_post_hook()` from `publish_post` action
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

## 2.5 _(2016-03-28)_
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

## 2.4.2 _(2015-02-21)_
* Revert to using `dirname(__FILE__)`; `__DIR__` is only supported in PHP 5.3+

## 2.4.1 _(2015-02-17)_
* Add more unit tests
* Reformat plugin header
* Use `__DIR__` instead of `dirname(__FILE__)`
* Note compatibility through WP 4.1+
* Change documentation links to wp.org to be https
* Minor documentation spacing changes throughout
* Update copyright date (2015)
* Add plugin icon
* Regenerate .pot

## 2.4 _(2014-01-23)_
* Fix to preserve silent publishing status when post gets re-edited after being published
* Delete meta data if saving a post that doesn't have the checkbox checked
* Add unit tests
* Minor documentation improvements
* Minor code reformatting (spacing, bracing)
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.6
* Update copyright date (2014)
* Regenerate .pot
* Change donate link
* Update screenshots
* Add banner

## 2.3
* Deprecate `silent_publish_meta_key` filter in favor of `c2c_silent_publish_meta_key` (but keep it temporarily for backwards compatibility)
* Don't store the fact that a post was silently published in post meta if the meta key value is blank or false
	(effectively allows filter to disable custom field usage)
* Remove private static $textdomain and its use; include textdomain name as string in translation calls
* Remove function `load_textdomain()`
* Add check to prevent execution of code if file is directly accessed
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Regenerate .pot
* Minor improvements to inline and readme documentation
* Minor code reformatting
* Remove ending PHP close tag
* Note compatibility through WP 3.5+
* Tweak installation instructions in readme.txt
* Update copyright date (2013)
* Move screenshots into repo's assets directory

## 2.2.1
* Add version() to return plugin's version
* Update readme with example and documentation for new filter
* Note compatibility through WP 3.3+
* Update screenshots for WP 3.3
* Use DIRECTORY_SEPARATOR instead of hardcoded '/'
* Create 'lang' subdirectory and move .pot file into it
* Regenerate .pot
* Add 'Domain Path' directive to top of main plugin file
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

## 2.2
* Fix bug where using Quick Edit on post caused Silent Publish status to be cleared
* Add filter `c2c_silent_publish_default` to allow configuring checkbox to be checked by default
* Note compatibility through WP 3.2+
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

## 2.1
* Switch from object instantiation to direct class invocation
* Explicitly declare all functions public static and class variables private static
* Remove setting unnecessary variable
* Note compatibility through WP 3.1+
* Update copyright date (2011)

## 2.0.1
* Bugfix for auto-save losing value of silent publish status

## 2.0
* Re-implemented entire approach
* Allow overriding of custom field used via `silent_publish_meta_key` filter
* Add class of `c2c-silent-publish` to admin UI div containing checkbox
* Add filter `silent_publish_meta_key` to allow overriding custom field key name
* Remove function `add_js()`, `admin_menu()`, `add_meta_box()`
* Add functions `init()`, `add_ui()`, `save_silent_publish_status()`, `load_textdomain()`
* Add true localization support
* Move definition of strings into init() and properly support localization
* Full support for localization
* Store plugin instance in global variable, `$c2c_silent_publish`, to allow for external manipulation
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

## 1.0
* Initial release