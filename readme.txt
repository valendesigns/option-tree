=== OptionTree ===
Contributors: valendesigns
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=accounts@valendesigns.com&item_name=OptionTree
Tags: admin, theme options, options, admin interface, ajax
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.1.8.1
License: GPLv2

Extremely customizable Theme Options interface for WordPress.

== Description ==

Theme Options are what make a WordPress Theme truly custom. OptionTree attempts to bridge the gap between developers, designers and end-users by solving the admin User Interface issues that arise when creating a custom theme. Designers shouldn't have to be limited to what they can create visually because their programming skills aren't as developed as they would like. Also, programmers shouldn't have to recreate the wheel for every new project, so in walks OptionTree.

With OptionTree you can create as many Theme Options as your project requires and use them how you see fit. When you add a option to the Settings page, it will be available on the Theme Options page for use in your theme. 

Included is the ability to Import/Export all the theme options and data for packaging with custom themes or local development. With the Import/Export feature you can get a theme set up on a live server in minutes. Theme authors can now create different version of their themes and include them with the download. It makes setting up different theme styles & options easier than ever because a theme user installs the plugin and theme and either adds their own settings or imports your defaults.

**Update**: v1.1.8.1 Removed get_option_tree() in the WordPress admin area due to theme conflicts.

**Update**: Since v1.1.8 you can build custom CSS code that will automatically get inserted into dynamic.css (created by the server) or any file you choose, just be sure it's permissions are writable. As well, typography & background options were added with a ton of filters to extend them.

**Update**: Since v1.1.7 you can create layouts (theme variations) and import/export those layouts. You can also activate them at anytime from the Theme Options page. Added an upload feature to the slider.

**Update**: Since v1.1.6 it's now possible to have a default XML file included in your theme to populate the theme options and hide the 'Settings' and 'Documentation' pages from the end uses. You can read more about this in the plugins built in documentation by clicking the 'Theme Integration' tab.

OptionTree is a project sponsored by <a href="http://themeforest.net/?ref=valendesigns">ThemeForest</a>, the largest WordPress theme marketplace on the web, and was originally conceived to help ThemeForest authors quickly power up their themes. But it's here for the benefit of one and all, so option up folks!

== Installation ==

1. Upload `option-tree` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If included in your theme, import the XML file & data by going to `OptionTree->Settings->Import`
1. Click the `OptionTree->Documentation` link in the WordPress admin sidebar menu for further setup assistance.

== Frequently Asked Questions ==

= Is this plugin PHP5 only? =

Sorry, but yes. OptionTree requires PHP5 to work correctly (so does WP 3.2+).

== Screenshots ==

1. Settings
2. Theme Options
3. Documentation

== Changelog ==

= 1.1.8.1 =
* Removed get_option_tree() in the WordPress admin area due to theme conflicts.
* Removed demo files in the assets folder at the request of WordPress

= 1.1.8 =
* Fixed scrolling issue on extra tall pages
* Added ability to show/hide settings & documentation via the User Profile page.
* Added Background option type.
* Added Typography option type.
* Added CSS option type.
* Better looking selects with 1=Yes,2=No where '1' is the value and 'Yes' is the text in the select.
* Made the AJAX message CSS more prominent.
* functions.load.php will now only load option type functions if viewing an OT admin page.
* Deregistered the custom jQuery UI in the 'Cispm Mail Contact' plugin when viewing an OptionTree page.
* Can now save layouts from the Theme Options page.
* You can now change the slider fields by targeting a specific "Option Key"
* Modified upload for situations where you manually enter a relative path
* Allow get_option_tree() function to be used in WP admin
* Changed permissions to edit_theme_options

= 1.1.7.1 =
* Revert functions.load.php, will fix and update in next version

= 1.1.7 =
* Added layout (theme variation) support with save/delete/activate/import/export capabilities. Contributions form Brian of flauntbooks.com
* Allow layout change on Theme Options page.
* Full Multisite compatibility by manually adding xml mime type for import options.
* Replaced eregi() with preg_match() for 5.3+ compatibility.
* Changed test data in the assets directory for new layout option.
* Made it so when the slider & upload image changes it's reflected on blur.
* Gave the slider image an upload button.
* Added do_action('option_tree_import_data') to option_tree_import_data() function before exit.
* Added do_action('option_tree_array_save') to option_tree_array_save() function before exit.
* Added do_action('option_tree_save_layout') to option_tree_save_layout() function before exit.
* Added do_action('option_tree_delete_layout') to option_tree_delete_layout() function before exit.
* Added do_action('option_tree_activate_layout') to option_tree_activate_layout() function before exit.
* Added do_action('option_tree_import_layout') to option_tree_import_layout() function before redirect.
* Added do_action('option_tree_admin_header') hook before all admin pages.
* Fixed bug where users could add a color without a hash.
* Only load option type function on Theme Options page
* Loading resources with absolute paths, no longer relative.
* Fixed a bug with uploader creating extra option-tree draft pages.
* Fixed slider toggle bug, now the sliders close when you open another or create new slide.

= 1.1.6 =
* Theme Integration added.
* Made the upload XML file openbase_dir compliant.

= 1.1.5 =
* Fixed multiple sliders issue

= 1.1.4 =
* Patch for get_option_tree() $is_array being false and still returning an array

= 1.1.3 =
* Added Slider option type with filter for changing the optional fields
* Fixed the text displayed for Measurement option type after options are reset
* Added filter to measurement units
* Code cleanup in the option_tree_array_save() function
* Fixed double quotes on front-end display

= 1.1.2 =
* Fixed double quotes in Textarea option type
* Added Measurement option type for CSS values
* Fixed Post option type only returning 5 items
* Added a scrolling window for checkboxes > 10

= 1.1.1 =
* Fixed the 'remove' icon from showing when nothing's uploaded

= 1.1 =
* Fixed the Undefined index: notices when WP_DEBUG is set to true

= 1.0.0 =
* Initial version

== Upgrade Notice ==

= 1.1.8.1 =
Removed get_option_tree() in the WordPress admin area due to theme conflicts.

= 1.1.8 =
Added Typography, Background, & CSS option types. Lots of way to extend them, as well.

= 1.1.7 =
Lots of additions, none critical just fun. Added layouts & upload to slider. As well, started including action hooks for extending and integrating with other plugins.

= 1.1.6 =
Added theme integration for developers. It's now possible to have a default XML file included in your theme to populate the theme options and hide the settings and docs pages. Read more about this in the plugins built in documentation.

= 1.1.5 =
Having multiple sliders caused a naming collision in the JavaScript and is now fixed. Upgrade ASAP to have multiple sliders available in the UI.

= 1.1.4 =
Fixed the returned value of the get_option_tree() function when $is_array is set to false. If you have created any slider or measurement option types please read the updated documentation for examples on how to use them in your theme.