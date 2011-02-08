=== OptionTree ===
Contributors: valendesigns
Donate link:
Tags: admin, theme options, options, admin interface, ajax
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.1.4

Customizable WordPress Theme Options Admin Interface

== Description ==

Theme Options are what make a WordPress Theme truly custom. OptionTree attempts to bridge the gap between developers, designers and end-users by solving the admin User Interface issues that arise when creating a custom theme. Designers shouldn't have to be limited to what they can create visually because their programming skills aren't as developed as they would like. Also, programmers shouldn't have to recreate the wheel for every new project, so in walks OptionTree.

With OptionTree you can create as many Theme Options as your project requires and use them how you see fit. When you add a option to the Settings page, it will be available on the Theme Options page for use in your theme. 

Included is the ability to Import/Export all the theme options and data for packaging with custom themes or local development. With the Import/Export feature you can get a theme set up on a live server in minutes. Theme authors can now create different version of their themes and include them with the download. It makes setting up different theme styles & options easier than ever because a theme user installs the plugin and theme and either adds their own settings or imports your defaults.

OptionTree is a project sponsored by ThemeForest, the largest WordPress theme marketplace on the web, and was originally conceived to help ThemeForest authors quickly power up their themes. But it's here for the benefit of one and all, so option up folks!

== Installation ==

1. Upload `option-tree` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If included in your theme, import the XML file & data by going to `OptionTree->Settings->Import`
1. Click the `OptionTree->Documentation` link in the WordPress admin sidebar menu for further setup assistance.

== Frequently Asked Questions ==

= Is this plugin PHP5 only? =

Sorry, but yes. OptionTree requires PHP5 to work correctly.

== Screenshots ==

1. Settings
2. Theme Options
3. Documentation

== Changelog ==

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

= 1.1.4 =
Fixed the returned value of the get_option_tree() function when $is_array is set to false. If you have created any slider or measurement option types please read the updated documentation for examples on how to use them in your theme.