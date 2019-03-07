<?php
/**
 * OptionTree Settings Page Functions.
 *
 * @package OptionTree
 */

if ( ! defined( 'OT_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'ot_type_theme_options_ui' ) ) {

	/**
	 * Create option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_theme_options_ui() {
		global $blog_id;

		echo '<form method="post" id="option-tree-settings-form">';

		// Form nonce.
		wp_nonce_field( 'option_tree_settings_form', 'option_tree_settings_nonce' );

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textblock has-desc">';

		// Description.
		echo '<div class="description">';

		echo '<h4>' . esc_html__( 'Warning!', 'option-tree' ) . '</h4>';

		/* translators: %s: link to theme options */
		$string = esc_html__( 'Go to the %s page if you want to save data, this page is for adding settings.', 'option-tree' );
		echo '<p class="warning">' . sprintf( $string, '<a href="' . esc_url_raw( get_admin_url( $blog_id, apply_filters( 'ot_theme_options_parent_slug', 'themes.php' ) ) . '?page=' . apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ) ) . '"><code>' . esc_html__( 'Appearance->Theme Options', 'option-tree' ) . '</code></a>' ) . '</p>'; // phpcs:ignore

		/* translators: %s: link to documentation */
		$string = esc_html__( 'If you\'re unsure or not completely positive that you should be editing these settings, you should read the %s first.', 'option-tree' );
		echo '<p class="warning">' . sprintf( $string, '<a href="' . esc_url_raw( get_admin_url( $blog_id, 'admin.php?page=ot-documentation' ) ) . '"><code>' . esc_html__( 'OptionTree->Documentation', 'option-tree' ) . '</code></a>' ) . '</p>'; // phpcs:ignore

		echo '<h4>' . esc_html__( 'Things could break or be improperly displayed to the end-user if you do one of the following:', 'option-tree' ) . '</h4>';
		echo '<p class="warning">' . esc_html__( 'Give two sections the same ID, give two settings the same ID, give two contextual help content areas the same ID, don\'t create any settings, or have a section at the end of the settings list.', 'option-tree' ) . '</p>';
		echo '<p>' . esc_html__( 'You can create as many settings as your project requires and use them how you see fit. When you add a setting here, it will be available on the Theme Options page for use in your theme. To separate your settings into sections, click the "Add Section" button, fill in the input fields, and a new navigation menu item will be created.', 'option-tree' ) . '</p>';
		echo '<p>' . esc_html__( 'All of the settings can be sorted and rearranged to your liking with Drag & Drop. Don\'t worry about the order in which you create your settings, you can always reorder them.', 'option-tree' ) . '</p>';

		echo '</div>';

		// Get the saved settings.
		$settings = get_option( ot_settings_id() );

		// Wrap settings array.
		echo '<div class="format-setting-inner">';

		// Set count to zero.
		$count = 0;

		// Loop through each section and its settings.
		echo '<ul class="option-tree-setting-wrap option-tree-sortable" id="option_tree_settings_list" data-name="' . esc_attr( ot_settings_id() ) . '[settings]">';

		if ( isset( $settings['sections'] ) ) {

			foreach ( $settings['sections'] as $section ) {

				// Section.
				echo '<li class="' . ( $count == 0 ? 'ui-state-disabled' : 'ui-state-default' ) . ' list-section">' . ot_sections_view( ot_settings_id() . '[sections]', $count, $section ) . '</li>'; // phpcs:ignore

				// Increment item count.
				$count++;

				// Settings in this section.
				if ( isset( $settings['settings'] ) ) {

					foreach ( $settings['settings'] as $setting ) {

						if ( isset( $setting['section'] ) && $setting['section'] === $section['id'] ) {

							echo '<li class="ui-state-default list-setting">' . ot_settings_view( ot_settings_id() . '[settings]', $count, $setting ) . '</li>'; // phpcs:ignore

							// Increment item count.
							$count++;
						}
					}
				}
			}
		}

		echo '</ul>';

		// Buttons.
		echo '<a href="javascript:void(0);" class="option-tree-section-add option-tree-ui-button button hug-left">' . esc_html__( 'Add Section', 'option-tree' ) . '</a>';
		echo '<a href="javascript:void(0);" class="option-tree-setting-add option-tree-ui-button button">' . esc_html__( 'Add Setting', 'option-tree' ) . '</a>';
		echo '<button class="option-tree-ui-button button button-primary right hug-right">' . esc_html__( 'Save Changes', 'option-tree' ) . '</button>';

		// Sidebar textarea.
		echo '
		<div class="format-setting-label" id="contextual-help-label">
			<h3 class="label">' . esc_html__( 'Contextual Help', 'option-tree' ) . '</h3>
		</div>
		<div class="format-settings" id="contextual-help-setting">
			<div class="format-setting type-textarea no-desc">
				<div class="description"><strong>' . esc_html__( 'Contextual Help Sidebar', 'option-tree' ) . '</strong>: ' . esc_html__( 'If you decide to add contextual help to the Theme Option page, enter the optional "Sidebar" HTML here. This would be an extremely useful place to add links to your themes documentation or support forum. Only after you\'ve added some content below will this display to the user.', 'option-tree' ) . '</div>
				<div class="format-setting-inner">
					<textarea class="textarea" rows="10" cols="40" name="' . esc_attr( ot_settings_id() ) . '[contextual_help][sidebar]">' . ( isset( $settings['contextual_help']['sidebar'] ) ? esc_html( $settings['contextual_help']['sidebar'] ) : '' ) . '</textarea>
				</div>
			</div>
		</div>';

		// Set count to zero.
		$count = 0;

		// Loop through each contextual_help content section.
		echo '<ul class="option-tree-setting-wrap option-tree-sortable" id="option_tree_settings_help" data-name="' . esc_attr( ot_settings_id() ) . '[contextual_help][content]">';

		if ( isset( $settings['contextual_help']['content'] ) ) {

			foreach ( $settings['contextual_help']['content'] as $content ) {

				// Content.
				echo '<li class="ui-state-default list-contextual-help">' . ot_contextual_help_view( ot_settings_id() . '[contextual_help][content]', $count, $content ) . '</li>'; // phpcs:ignore

				// Increment content count.
				$count++;
			}
		}

		echo '</ul>';

		echo '<a href="javascript:void(0);" class="option-tree-help-add option-tree-ui-button button hug-left">' . esc_html__( 'Add Contextual Help Content', 'option-tree' ) . '</a>';
		echo '<button class="option-tree-ui-button button button-primary right hug-right">' . esc_html__( 'Save Changes', 'option-tree' ) . '</button>';

		echo '</div>';

		echo '</div>';

		echo '</form>';
	}
}

if ( ! function_exists( 'ot_type_import_settings' ) ) {

	/**
	 * Import Settings option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_import_settings() {

		echo '<form method="post" id="import-settings-form">';

		// Form nonce.
		wp_nonce_field( 'import_settings_form', 'import_settings_nonce' );

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea has-desc">';

		// Description.
		echo '<div class="description">';

		echo '<p>' . esc_html__( 'To import your Settings copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the "Import Settings" button.', 'option-tree' ) . '</p>';

		echo '<button class="option-tree-ui-button button button-primary right hug-right">' . esc_html__( 'Import Settings', 'option-tree' ) . '</button>';

		echo '</div>';

		echo '<div class="format-setting-inner">';

		echo '<textarea rows="10" cols="40" name="import_settings" id="import_settings" class="textarea"></textarea>';

		echo '</div>';

		echo '</div>';

		echo '</form>';

	}
}

if ( ! function_exists( 'ot_type_import_data' ) ) {

	/**
	 * Import Data option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_import_data() {

		echo '<form method="post" id="import-data-form">';

		// Form nonce.
		wp_nonce_field( 'import_data_form', 'import_data_nonce' );

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea has-desc">';

		// Description.
		echo '<div class="description">';

		if ( OT_SHOW_SETTINGS_IMPORT ) {
			echo '<p>' . esc_html__( 'Only after you\'ve imported the Settings should you try and update your Theme Options.', 'option-tree' ) . '</p>';
		}

		echo '<p>' . esc_html__( 'To import your Theme Options copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the "Import Theme Options" button.', 'option-tree' ) . '</p>';

		echo '<button class="option-tree-ui-button button button-primary right hug-right">' . esc_html__( 'Import Theme Options', 'option-tree' ) . '</button>';

		echo '</div>';

		echo '<div class="format-setting-inner">';

		echo '<textarea rows="10" cols="40" name="import_data" id="import_data" class="textarea"></textarea>';

		echo '</div>';

		echo '</div>';

		echo '</form>';
	}
}

if ( ! function_exists( 'ot_type_import_layouts' ) ) {

	/**
	 * Import Layouts option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_import_layouts() {

		echo '<form method="post" id="import-layouts-form">';

		// Form nonce.
		wp_nonce_field( 'import_layouts_form', 'import_layouts_nonce' );

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea has-desc">';

		// Description.
		echo '<div class="description">';

		if ( OT_SHOW_SETTINGS_IMPORT ) {
			echo '<p>' . esc_html__( 'Only after you\'ve imported the Settings should you try and update your Layouts.', 'option-tree' ) . '</p>';
		}

		echo '<p>' . esc_html__( 'To import your Layouts copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the "Import Layouts" button. Keep in mind that when you import your layouts, the active layout\'s saved data will write over the current data set for your Theme Options.', 'option-tree' ) . '</p>';

		echo '<button class="option-tree-ui-button button button-primary right hug-right">' . esc_html__( 'Import Layouts', 'option-tree' ) . '</button>';

		echo '</div>';

		echo '<div class="format-setting-inner">';

		echo '<textarea rows="10" cols="40" name="import_layouts" id="import_layouts" class="textarea"></textarea>';

		echo '</div>';

		echo '</div>';

		echo '</form>';
	}
}

if ( ! function_exists( 'ot_type_export_settings_file' ) ) {

	/**
	 * Export Settings File option type.
	 *
	 * @access public
	 * @since  2.0.8
	 */
	function ot_type_export_settings_file() {
		global $blog_id;

		echo '<form method="post" id="export-settings-file-form">';

		// Form nonce.
		wp_nonce_field( 'export_settings_file_form', 'export_settings_file_nonce' );

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea simple has-desc">';

		// Description.
		echo '<div class="description">';

		/* translators: %1$s: file name, %2$s: link to I18n docs, %3$s: link to internal docs */
		$string = esc_html__( 'Export your Settings into a fully functional %1$s file. If you want to add your own custom %2$s text domain to the file, enter it into the text field before exporting. For more information on how to use this file read the documentation on %3$s. Remember, you should always check the file for errors before including it in your theme.', 'option-tree' );
		echo '<p>' . sprintf( $string, '<code>theme-options.php</code>', '<a href="http://codex.wordpress.org/I18n_for_WordPress_Developers" target="_blank">I18n</a>', '<a href="' . get_admin_url( $blog_id, 'admin.php?page=ot-documentation#section_theme_mode' ) . '">' . esc_html__( 'Theme Mode', 'option-tree' ) . '</a>' ) . '</p>'; // phpcs:ignore

		echo '</div>';

		echo '<div class="format-setting-inner">';

		echo '<input type="text" name="domain" value="" class="widefat option-tree-ui-input" placeholder="text-domain" autocomplete="off" />';

		echo '<button class="option-tree-ui-button button button-primary hug-left">' . esc_html__( 'Export Settings File', 'option-tree' ) . '</button>';

		echo '</div>';

		echo '</div>';

		echo '</form>';
	}
}

if ( ! function_exists( 'ot_type_export_settings' ) ) {

	/**
	 * Export Settings option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_export_settings() {

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea simple has-desc">';

		// Description.
		echo '<div class="description">';

		/* translators: %1$s: visual path to import, %2$s: visual path to settings */
		$string = esc_html__( 'Export your Settings by highlighting this text and doing a copy/paste into a blank .txt file. Then save the file for importing into another install of WordPress later. Alternatively, you could just paste it into the %1$s %1$s textarea on another web site.', 'option-tree' );
		echo '<p>' . sprintf( $string, '<code>' . esc_html__( 'OptionTree->Settings->Import', 'option-tree' ) . '</code>', '<code>' . esc_html__( 'Settings', 'option-tree' ) . '</code>' ) . '</p>'; // phpcs:ignore

		echo '</div>';

		// Get theme options data.
		$settings = get_option( ot_settings_id(), array() );
		$settings = ! empty( $settings ) ? ot_encode( $settings ) : '';

		echo '<div class="format-setting-inner">';
		echo '<textarea rows="10" cols="40" name="export_settings" id="export_settings" class="textarea">' . $settings . '</textarea>'; // phpcs:ignore
		echo '</div>';

		echo '</div>';

	}
}

if ( ! function_exists( 'ot_type_export_data' ) ) {

	/**
	 * Export Data option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_export_data() {

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea simple has-desc">';

		// Description.
		echo '<div class="description">';

		/* translators: %1$s: visual path to import, %2$s: visual path to theme options */
		$string = esc_html__( 'Export your Theme Options data by highlighting this text and doing a copy/paste into a blank .txt file. Then save the file for importing into another install of WordPress later. Alternatively, you could just paste it into the %1$s %2$s textarea on another web site.', 'option-tree' );
		echo '<p>' . sprintf( $string, '<code>' . esc_html__( 'OptionTree->Settings->Import', 'option-tree' ) . '</code>', '<code>' . esc_html__( 'Theme Options', 'option-tree' ) . '</code>' ) . '</p>'; // phpcs:ignore

		echo '</div>';

		// Get theme options data.
		$data = get_option( ot_options_id(), array() );
		$data = ! empty( $data ) ? ot_encode( $data ) : '';

		echo '<div class="format-setting-inner">';
		echo '<textarea rows="10" cols="40" name="export_data" id="export_data" class="textarea">' . $data . '</textarea>'; // phpcs:ignore
		echo '</div>';

		echo '</div>';

	}
}

if ( ! function_exists( 'ot_type_export_layouts' ) ) {

	/**
	 * Export Layouts option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_export_layouts() {

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea simple has-desc">';

		// Description.
		echo '<div class="description">';

		/* translators: %1$s: visual path to import, %2$s: visual path to layouts */
		$string = esc_html__( 'Export your Layouts by highlighting this text and doing a copy/paste into a blank .txt file. Then save the file for importing into another install of WordPress later. Alternatively, you could just paste it into the %1$s %2$s textarea on another web site.', 'option-tree' );
		echo '<p>' . sprintf( $string, '<code>' . esc_html__( 'OptionTree->Settings->Import', 'option-tree' ) . '</code>', '<code>' . esc_html__( 'Layouts', 'option-tree' ) . '</code>' ) . '</p>'; // phpcs:ignore

		echo '</div>';

		// Get layout data.
		$layouts = get_option( ot_layouts_id(), array() );
		$layouts = ! empty( $layouts ) ? ot_encode( $layouts ) : '';

		echo '<div class="format-setting-inner">';
		echo '<textarea rows="10" cols="40" name="export_layouts" id="export_layouts" class="textarea">' . $layouts . '</textarea>'; // phpcs:ignore
		echo '</div>';

		echo '</div>';
	}
}

if ( ! function_exists( 'ot_type_modify_layouts' ) ) {

	/**
	 * Modify Layouts option type.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_type_modify_layouts() {

		echo '<form method="post" id="option-tree-settings-form">';

		// Form nonce.
		wp_nonce_field( 'option_tree_modify_layouts_form', 'option_tree_modify_layouts_nonce' );

		// Format setting outer wrapper.
		echo '<div class="format-setting type-textarea has-desc">';

		// Description.
		echo '<div class="description">';

		echo '<p>' . esc_html__( 'To add a new layout enter a unique lower case alphanumeric string (dashes allowed) in the text field and click "Save Layouts".', 'option-tree' ) . '</p>';
		echo '<p>' . esc_html__( 'As well, you can activate, remove, and drag & drop the order; all situations require you to click "Save Layouts" for the changes to be applied.', 'option-tree' ) . '</p>';
		echo '<p>' . esc_html__( 'When you create a new layout it will become active and any changes made to the Theme Options will be applied to it. If you switch back to a different layout immediately after creating a new layout that new layout will have a snapshot of the current Theme Options data attached to it.', 'option-tree' ) . '</p>';

		if ( OT_SHOW_DOCS ) {
			/* translators: %s: visual path to layouts overview */
			$string = esc_html__( 'Visit %s to see a more in-depth description of what layouts are and how to use them.', 'option-tree' );
			echo '<p>' . sprintf( $string, '<code>' . esc_html__( 'OptionTree->Documentation->Layouts Overview', 'option-tree' ) . '</code>' ) . '</p>'; // phpcs:ignore
		}

		echo '</div>';

		echo '<div class="format-setting-inner">';

		// Get the saved layouts.
		$layouts = get_option( ot_layouts_id() );

		// Set active layout.
		$active_layout = isset( $layouts['active_layout'] ) ? $layouts['active_layout'] : '';

		echo '<input type="hidden" name="' . esc_attr( ot_layouts_id() ) . '[active_layout]" value="' . esc_attr( $active_layout ) . '" class="active-layout-input" />';

		// Add new layout.
		echo '<input type="text" name="' . esc_attr( ot_layouts_id() ) . '[_add_new_layout_]" value="" class="widefat option-tree-ui-input" autocomplete="off" />';

		// Loop through each layout.
		echo '<ul class="option-tree-setting-wrap option-tree-sortable" id="option_tree_layouts">';

		if ( is_array( $layouts ) && ! empty( $layouts ) ) {

			foreach ( $layouts as $key => $data ) {

				// Skip active layout array.
				if ( 'active_layout' === $key ) {
					continue;
				}

				// Content.
				echo '<li class="ui-state-default list-layouts">' . ot_layout_view( $key, $data, $active_layout ) . '</li>'; // phpcs:ignore
			}
		}

		echo '</ul>';

		echo '<button class="option-tree-ui-button button button-primary right hug-right">' . esc_html__( 'Save Layouts', 'option-tree' ) . '</button>';

		echo '</div>';

		echo '</div>';

		echo '</form>';
	}
}
