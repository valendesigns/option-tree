<?php
/**
 * Plugin Name: OptionTree
 * Plugin URI:  https://github.com/valendesigns/option-tree/
 * Description: Theme Options UI Builder for WordPress. A simple way to create & save Theme Options and Meta Boxes for free or premium themes.
 * Version:     2.7.3
 * Author:      Derek Herman
 * Author URI:  http://valendesigns.com
 * License:     GPLv2 or later
 * Text Domain: option-tree
 *
 * @package OptionTree
 */

if ( class_exists( 'OT_Loader' ) && defined( 'OT_PLUGIN_MODE' ) && true === OT_PLUGIN_MODE && defined( 'ABSPATH' ) ) {

	add_filter( 'ot_theme_mode', '__return_false', 999 );

	/**
	 * Forces Plugin Mode when OptionTree is already loaded and displays an admin notice.
	 */
	function ot_conflict_notice() {
		echo '<div class="error"><p>' . esc_html__( 'OptionTree is installed as a plugin and also embedded in your current theme. Please deactivate the plugin to load the theme dependent version of OptionTree, and remove this warning.', 'option-tree' ) . '</p></div>';
	}

	add_action( 'admin_notices', 'ot_conflict_notice' );
}

if ( ! class_exists( 'OT_Loader' ) && defined( 'ABSPATH' ) ) {

	/**
	 * OptionTree loader class.
	 */
	class OT_Loader {

		/**
		 * Class constructor.
		 *
		 * This method loads other methods of the class.
		 *
		 * @access public
		 * @since  2.0
		 */
		public function __construct() {

			// Load OptionTree.
			add_action( 'after_setup_theme', array( $this, 'load_option_tree' ), 1 );
		}

		/**
		 * OptionTree loads on the 'after_setup_theme' action.
		 *
		 * @todo Load immediately.
		 *
		 * @access public
		 * @since 2.1.2
		 */
		public function load_option_tree() {

			// Setup the constants.
			$this->constants();

			// Include the required admin files.
			$this->admin_includes();

			// Include the required files.
			$this->includes();

			// Hook into WordPress.
			$this->hooks();
		}

		/**
		 * Constants.
		 *
		 * Defines the constants for use within OptionTree. Constants
		 * are prefixed with 'OT_' to avoid any naming collisions.
		 *
		 * @access private
		 * @since  2.0
		 */
		private function constants() {

			/**
			 * Current Version number.
			 */
			define( 'OT_VERSION', '2.7.3' );

			/**
			 * For developers: Theme mode.
			 *
			 * Run a filter and set to true to enable OptionTree theme mode.
			 * You must have this files parent directory inside of
			 * your themes root directory. As well, you must include
			 * a reference to this file in your themes functions.php.
			 *
			 * @since 2.0
			 */
			define( 'OT_THEME_MODE', apply_filters( 'ot_theme_mode', false ) );

			/**
			 * For developers: Child Theme mode. TODO document
			 *
			 * Run a filter and set to true to enable OptionTree child theme mode.
			 * You must have this files parent directory inside of
			 * your themes root directory. As well, you must include
			 * a reference to this file in your themes functions.php.
			 *
			 * @since 2.0.15
			 */
			define( 'OT_CHILD_THEME_MODE', apply_filters( 'ot_child_theme_mode', false ) );

			/**
			 * For developers: Show Pages.
			 *
			 * Run a filter and set to false if you don't want to load the
			 * settings & documentation pages in the admin area of WordPress.
			 *
			 * @since 2.0
			 */
			define( 'OT_SHOW_PAGES', apply_filters( 'ot_show_pages', true ) );

			/**
			 * For developers: Show Theme Options UI Builder
			 *
			 * Run a filter and set to false if you want to hide the
			 * Theme Options UI page in the admin area of WordPress.
			 *
			 * @since 2.1
			 */
			define( 'OT_SHOW_OPTIONS_UI', apply_filters( 'ot_show_options_ui', true ) );

			/**
			 * For developers: Show Settings Import
			 *
			 * Run a filter and set to false if you want to hide the
			 * Settings Import options on the Import page.
			 *
			 * @since 2.1
			 */
			define( 'OT_SHOW_SETTINGS_IMPORT', apply_filters( 'ot_show_settings_import', true ) );

			/**
			 * For developers: Show Settings Export
			 *
			 * Run a filter and set to false if you want to hide the
			 * Settings Import options on the Import page.
			 *
			 * @since 2.1
			 */
			define( 'OT_SHOW_SETTINGS_EXPORT', apply_filters( 'ot_show_settings_export', true ) );

			/**
			 * For developers: Show New Layout.
			 *
			 * Run a filter and set to false if you don't want to show the
			 * "New Layout" section at the top of the theme options page.
			 *
			 * @since 2.0.10
			 */
			define( 'OT_SHOW_NEW_LAYOUT', apply_filters( 'ot_show_new_layout', true ) );

			/**
			 * For developers: Show Documentation
			 *
			 * Run a filter and set to false if you want to hide the Documentation.
			 *
			 * @since 2.1
			 */
			define( 'OT_SHOW_DOCS', apply_filters( 'ot_show_docs', true ) );

			/**
			 * For developers: Custom Theme Option page
			 *
			 * Run a filter and set to false if you want to hide the OptionTree
			 * Theme Option page and build your own.
			 *
			 * @since 2.1
			 */
			define( 'OT_USE_THEME_OPTIONS', apply_filters( 'ot_use_theme_options', true ) );

			/**
			 * For developers: Meta Boxes.
			 *
			 * Run a filter and set to false to keep OptionTree from
			 * loading the meta box resources.
			 *
			 * @since 2.0
			 */
			define( 'OT_META_BOXES', apply_filters( 'ot_meta_boxes', true ) );

			/**
			 * For developers: Allow Unfiltered HTML in all the textareas.
			 *
			 * Run a filter and set to true if you want all the users to be
			 * able to add script, style, and iframe tags in the textareas.
			 * WARNING: This opens a security hole for low level users
			 * to be able to post malicious scripts, you've been warned.
			 *
			 * If a user can already post `unfiltered_html` then the tags
			 * above will be available to them without setting this to `true`.
			 *
			 * @since 2.0
			 */
			define( 'OT_ALLOW_UNFILTERED_HTML', apply_filters( 'ot_allow_unfiltered_html', false ) );

			/**
			 * For developers: Post Formats.
			 *
			 * Run a filter and set to true if you want OptionTree
			 * to load meta boxes for post formats.
			 *
			 * @since 2.4.0
			 */
			define( 'OT_POST_FORMATS', apply_filters( 'ot_post_formats', false ) );

			/**
			 * Check if in theme mode.
			 *
			 * If OT_THEME_MODE and OT_CHILD_THEME_MODE is false, set the
			 * directory path & URL like any other plugin. Otherwise, use
			 * the parent or child themes root directory.
			 *
			 * @since 2.0
			 */
			if ( false === OT_THEME_MODE && false === OT_CHILD_THEME_MODE ) {
				define( 'OT_DIR', plugin_dir_path( __FILE__ ) );
				define( 'OT_URL', plugin_dir_url( __FILE__ ) );
			} else {
				if ( true === OT_CHILD_THEME_MODE ) {
					$temp_path = explode( get_stylesheet(), str_replace( '\\', '/', dirname( __FILE__ ) ) );
					$path      = ltrim( end( $temp_path ), '/' );
					define( 'OT_DIR', trailingslashit( trailingslashit( get_stylesheet_directory() ) . $path ) );
					define( 'OT_URL', trailingslashit( trailingslashit( get_stylesheet_directory_uri() ) . $path ) );
				} else {
					$temp_path = explode( get_template(), str_replace( '\\', '/', dirname( __FILE__ ) ) );
					$path      = ltrim( end( $temp_path ), '/' );
					define( 'OT_DIR', trailingslashit( trailingslashit( get_template_directory() ) . $path ) );
					define( 'OT_URL', trailingslashit( trailingslashit( get_template_directory_uri() ) . $path ) );
				}
			}

			/**
			 * Template directory URI for the current theme.
			 *
			 * @since 2.1
			 */
			if ( true === OT_CHILD_THEME_MODE ) {
				define( 'OT_THEME_URL', get_stylesheet_directory_uri() );
			} else {
				define( 'OT_THEME_URL', get_template_directory_uri() );
			}
		}

		/**
		 * Include admin files.
		 *
		 * These functions are included on admin pages only.
		 *
		 * @access private
		 * @since  2.0
		 */
		private function admin_includes() {

			// Exit early if we're not on an admin page.
			if ( ! is_admin() ) {
				return false;
			}

			// Global include files.
			$files = array(
				'ot-functions-admin',
				'ot-functions-option-types',
				'ot-functions-compat',
				'class-ot-settings',
			);

			// Include the meta box api.
			if ( true === OT_META_BOXES ) {
				$files[] = 'class-ot-meta-box';
			}

			// Include the post formats api.
			if ( true === OT_META_BOXES && true === OT_POST_FORMATS ) {
				$files[] = 'class-ot-post-formats';
			}

			// Include the settings & docs pages.
			if ( true === OT_SHOW_PAGES ) {
				$files[] = 'ot-functions-settings-page';
				$files[] = 'ot-functions-docs-page';
			}

			// Include the cleanup api.
			$files[] = 'class-ot-cleanup';

			// Require the files.
			foreach ( $files as $file ) {
				$this->load_file( OT_DIR . 'includes' . DIRECTORY_SEPARATOR . "{$file}.php" );
			}

			// Registers the Theme Option page.
			add_action( 'init', 'ot_register_theme_options_page' );

			// Registers the Settings page.
			if ( true === OT_SHOW_PAGES ) {
				add_action( 'init', 'ot_register_settings_page' );

				// Global CSS.
				add_action( 'admin_head', array( $this, 'global_admin_css' ) );
			}
		}

		/**
		 * Include front-end files.
		 *
		 * These functions are included on every page load
		 * incase other plugins need to access them.
		 *
		 * @access private
		 * @since  2.0
		 */
		private function includes() {

			$files = array(
				'ot-functions',
				'ot-functions-deprecated',
			);

			// Require the files.
			foreach ( $files as $file ) {
				$this->load_file( OT_DIR . 'includes' . DIRECTORY_SEPARATOR . "{$file}.php" );
			}
		}

		/**
		 * Execute the WordPress Hooks.
		 *
		 * @access public
		 * @since 2.0
		 */
		private function hooks() {

			// Attempt to migrate the settings.
			if ( function_exists( 'ot_maybe_migrate_settings' ) ) {
				add_action( 'init', 'ot_maybe_migrate_settings', 1 );
			}

			// Attempt to migrate the Options.
			if ( function_exists( 'ot_maybe_migrate_options' ) ) {
				add_action( 'init', 'ot_maybe_migrate_options', 1 );
			}

			// Attempt to migrate the Layouts.
			if ( function_exists( 'ot_maybe_migrate_layouts' ) ) {
				add_action( 'init', 'ot_maybe_migrate_layouts', 1 );
			}

			// Load the Meta Box assets.
			if ( true === OT_META_BOXES ) {

				// Add scripts for metaboxes to post-new.php & post.php.
				add_action( 'admin_print_scripts-post-new.php', 'ot_admin_scripts', 11 );
				add_action( 'admin_print_scripts-post.php', 'ot_admin_scripts', 11 );

				// Add styles for metaboxes to post-new.php & post.php.
				add_action( 'admin_print_styles-post-new.php', 'ot_admin_styles', 11 );
				add_action( 'admin_print_styles-post.php', 'ot_admin_styles', 11 );

			}

			// Adds the Theme Option page to the admin bar.
			add_action( 'admin_bar_menu', 'ot_register_theme_options_admin_bar_menu', 999 );

			// Prepares the after save do_action.
			add_action( 'admin_init', 'ot_after_theme_options_save', 1 );

			// default settings.
			add_action( 'admin_init', 'ot_default_settings', 2 );

			// Import.
			add_action( 'admin_init', 'ot_import', 4 );

			// Export.
			add_action( 'admin_init', 'ot_export', 5 );

			// Save settings.
			add_action( 'admin_init', 'ot_save_settings', 6 );

			// Save layouts.
			add_action( 'admin_init', 'ot_modify_layouts', 7 );

			// Create media post.
			add_action( 'admin_init', 'ot_create_media_post', 8 );

			// Google Fonts front-end CSS.
			add_action( 'wp_enqueue_scripts', 'ot_load_google_fonts_css', 1 );

			// Dynamic front-end CSS.
			add_action( 'wp_enqueue_scripts', 'ot_load_dynamic_css', 999 );

			// Insert theme CSS dynamically.
			add_action( 'ot_after_theme_options_save', 'ot_save_css' );

			// AJAX call to create a new section.
			add_action( 'wp_ajax_add_section', array( $this, 'add_section' ) );

			// AJAX call to create a new setting.
			add_action( 'wp_ajax_add_setting', array( $this, 'add_setting' ) );

			// AJAX call to create a new contextual help.
			add_action( 'wp_ajax_add_the_contextual_help', array( $this, 'add_the_contextual_help' ) );

			// AJAX call to create a new choice.
			add_action( 'wp_ajax_add_choice', array( $this, 'add_choice' ) );

			// AJAX call to create a new list item setting.
			add_action( 'wp_ajax_add_list_item_setting', array( $this, 'add_list_item_setting' ) );

			// AJAX call to create a new layout.
			add_action( 'wp_ajax_add_layout', array( $this, 'add_layout' ) );

			// AJAX call to create a new list item.
			add_action( 'wp_ajax_add_list_item', array( $this, 'add_list_item' ) );

			// AJAX call to create a new social link.
			add_action( 'wp_ajax_add_social_links', array( $this, 'add_social_links' ) );

			// AJAX call to retrieve Google Font data.
			add_action( 'wp_ajax_ot_google_font', array( $this, 'retrieve_google_font' ) );

			// Adds the temporary hacktastic shortcode.
			add_filter( 'media_view_settings', array( $this, 'shortcode' ), 10, 2 );

			// AJAX update.
			add_action( 'wp_ajax_gallery_update', array( $this, 'ajax_gallery_update' ) );

			// Modify the media uploader button.
			add_filter( 'gettext', array( $this, 'change_image_button' ), 10, 3 );
		}

		/**
		 * Load a file.
		 *
		 * @access private
		 * @since  2.0.15
		 *
		 * @param string $file Path to the file being included.
		 */
		private function load_file( $file ) {
			include_once $file;
		}

		/**
		 * Adds CSS for the menu icon.
		 */
		public function global_admin_css() {
			?>
<style>
	@font-face {
		font-family: "option-tree-font";
		src:url("<?php echo esc_url_raw( OT_URL ); ?>assets/fonts/option-tree-font.eot");
		src:url("<?php echo esc_url_raw( OT_URL ); ?>assets/fonts/option-tree-font.eot?#iefix") format("embedded-opentype"),
			url("<?php echo esc_url_raw( OT_URL ); ?>assets/fonts/option-tree-font.woff") format("woff"),
			url("<?php echo esc_url_raw( OT_URL ); ?>assets/fonts/option-tree-font.ttf") format("truetype"),
			url("<?php echo esc_url_raw( OT_URL ); ?>assets/fonts/option-tree-font.svg#option-tree-font") format("svg");
		font-weight: normal;
		font-style: normal;
	}
	#adminmenu #toplevel_page_ot-settings .menu-icon-generic div.wp-menu-image:before {
		font: normal 20px/1 "option-tree-font" !important;
		speak: none;
		padding: 6px 0;
		height: 34px;
		width: 20px;
		display: inline-block;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
		-webkit-transition: all .1s ease-in-out;
		-moz-transition:    all .1s ease-in-out;
		transition:         all .1s ease-in-out;
	}
	#adminmenu #toplevel_page_ot-settings .menu-icon-generic div.wp-menu-image:before {
		content: "\e785";
	}
</style>
			<?php
		}

		/**
		 * AJAX utility function for adding a new section.
		 */
		public function add_section() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$count  = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$output = ot_sections_view( ot_settings_id() . '[sections]', $count );

			echo $output; // phpcs:ignore
			wp_die();
		}

		/**
		 * AJAX utility function for adding a new setting.
		 */
		public function add_setting() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$name   = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : '';
			$count  = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$output = ot_settings_view( $name, $count );

			echo $output; // phpcs:ignore
			wp_die();
		}

		/**
		 * AJAX utility function for adding a new list item setting.
		 */
		public function add_list_item_setting() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$name   = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : '';
			$count  = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$output = ot_settings_view( $name . '[settings]', $count );

			echo $output; // phpcs:ignore
			wp_die();
		}

		/**
		 * AJAX utility function for adding new contextual help content.
		 */
		public function add_the_contextual_help() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$name   = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : '';
			$count  = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$output = ot_contextual_help_view( $name, $count );

			echo $output; // phpcs:ignore
			wp_die();
		}

		/**
		 * AJAX utility function for adding a new choice.
		 */
		public function add_choice() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$name   = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : '';
			$count  = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$output = ot_choices_view( $name, $count );

			echo $output; // phpcs:ignore
			wp_die();
		}

		/**
		 * AJAX utility function for adding a new layout.
		 */
		public function add_layout() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$count  = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$output = ot_layout_view( $count );

			echo $output; // phpcs:ignore
			wp_die();
		}

		/**
		 * AJAX utility function for adding a new list item.
		 */
		public function add_list_item() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$name       = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : '';
			$count      = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$post_id    = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
			$get_option = isset( $_REQUEST['get_option'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['get_option'] ) ) : '';
			$type       = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : '';
			$settings   = isset( $_REQUEST['settings'] ) ? ot_decode( sanitize_text_field( wp_unslash( $_REQUEST['settings'] ) ) ) : array();

			ot_list_item_view( $name, $count, array(), $post_id, $get_option, $settings, $type );
			wp_die();
		}

		/**
		 * AJAX utility function for adding a new social link.
		 */
		public function add_social_links() {
			check_ajax_referer( 'option_tree', 'nonce' );

			$name       = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : '';
			$count      = isset( $_REQUEST['count'] ) ? absint( $_REQUEST['count'] ) : 0;
			$post_id    = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
			$get_option = isset( $_REQUEST['get_option'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['get_option'] ) ) : '';
			$type       = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : '';
			$settings   = isset( $_REQUEST['settings'] ) ? ot_decode( sanitize_text_field( wp_unslash( $_REQUEST['settings'] ) ) ) : array();

			ot_social_links_view( $name, $count, array(), $post_id, $get_option, $settings, $type );
			wp_die();
		}

		/**
		 * Fake the gallery shortcode.
		 *
		 * The JS takes over and creates the actual shortcode with
		 * the real attachment IDs on the fly. Here we just need to
		 * pass in the post ID to get the ball rolling.
		 *
		 * @access public
		 * @since  2.2.0
		 *
		 * @param  array  $settings The current settings.
		 * @param  object $post     The post object.
		 * @return array
		 */
		public function shortcode( $settings, $post ) {
			global $pagenow;

			if ( in_array( $pagenow, array( 'upload.php', 'customize.php' ), true ) ) {
				return $settings;
			}

			// Set the OptionTree post ID.
			if ( ! is_object( $post ) ) {
				$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : ( isset( $_GET['post_ID'] ) ? absint( $_GET['post_ID'] ) : 0 ); // phpcs:ignore
				if ( 0 >= $post_id && function_exists( 'ot_get_media_post_ID' ) ) {
					$post_id = ot_get_media_post_ID();
				}
				$settings['post']['id'] = $post_id;
			}

			// No ID return settings.
			if ( 0 >= $settings['post']['id'] ) {
				return $settings;
			}

			// Set the fake shortcode.
			$settings['ot_gallery'] = array( 'shortcode' => "[gallery id='{$settings['post']['id']}']" );

			// Return settings.
			return $settings;
		}

		/**
		 * AJAX to generate HTML for a list of gallery images.
		 *
		 * @access public
		 * @since  2.2.0
		 */
		public function ajax_gallery_update() {
			check_ajax_referer( 'option_tree', 'nonce' );

			if ( ! empty( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {

				$html = '';
				$ids  = array_filter( $_POST['ids'], 'absint' ); // phpcs:ignore

				foreach ( $ids as $id ) {

					$thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' );

					$html .= '<li><img  src="' . esc_url_raw( $thumbnail[0] ) . '" width="75" height="75" /></li>';
				}

				echo $html; // phpcs:ignore
			}

			wp_die();
		}

		/**
		 * The JSON encoded Google fonts data, or false if it cannot be encoded.
		 *
		 * @access public
		 * @since  2.5.0
		 */
		public function retrieve_google_font() {
			check_ajax_referer( 'option_tree', 'nonce' );

			if ( isset( $_POST['field_id'], $_POST['family'] ) ) {

				ot_fetch_google_fonts();

				$field_id = isset( $_POST['field_id'] ) ? sanitize_text_field( wp_unslash( $_POST['field_id'] ) ) : '';
				$family   = isset( $_POST['family'] ) ? sanitize_text_field( wp_unslash( $_POST['family'] ) ) : '';
				$html     = wp_json_encode(
					array(
						'variants' => ot_recognized_google_font_variants( $field_id, $family ),
						'subsets'  => ot_recognized_google_font_subsets( $field_id, $family ),
					)
				);

				echo $html; // phpcs:ignore
			}

			wp_die();
		}

		/**
		 * Filters the media uploader button.
		 *
		 * @access public
		 * @since  2.1
		 *
		 * @param string $translation Translated text.
		 * @param string $text        Text to translate.
		 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
		 *
		 * @return string
		 */
		public function change_image_button( $translation, $text, $domain ) {
			global $pagenow;

			if ( apply_filters( 'ot_theme_options_parent_slug', 'themes.php' ) === $pagenow && 'default' === $domain && 'Insert into post' === $text ) {

				// Once is enough.
				remove_filter( 'gettext', array( $this, 'ot_change_image_button' ) );
				return apply_filters( 'ot_upload_text', esc_html__( 'Send to OptionTree', 'option-tree' ) );

			}

			return $translation;
		}
	}

	/**
	 * Instantiate the OptionTree loader class.
	 *
	 * @since 2.0
	 */
	new OT_Loader();
}
