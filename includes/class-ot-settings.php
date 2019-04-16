<?php
/**
 * OptionTree Settings.
 *
 * @package OptionTree
 */

if ( ! defined( 'OT_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! class_exists( 'OT_Settings' ) ) {

	/**
	 * OptionTree Settings class.
	 *
	 * This class loads all the methods and helpers specific to a Settings page.
	 */
	class OT_Settings {

		/**
		 * An array of options.
		 *
		 * @var array
		 */
		private $options;

		/**
		 * Page hook for targeting admin page.
		 *
		 * @var string
		 */
		private $page_hook;

		/**
		 * Constructor
		 *
		 * @param array $args An array of options.
		 *
		 * @access public
		 * @since  2.0
		 */
		public function __construct( $args ) {

			$this->options = $args;

			// Return early if not viewing an admin page or no options.
			if ( ! is_admin() || ! is_array( $this->options ) ) {
				return false;
			}

			// Load everything.
			$this->hooks();
		}

		/**
		 * Execute the WordPress Hooks
		 *
		 * @access public
		 * @since  2.0
		 */
		public function hooks() {

			/**
			 * Filter the `admin_menu` action hook priority.
			 *
			 * @since 2.5.0
			 *
			 * @param int $priority The priority. Default '10'.
			 */
			$priority = apply_filters( 'ot_admin_menu_priority', 10 );

			// Add pages & menu items.
			add_action( 'admin_menu', array( $this, 'add_page' ), $priority );

			// Register sections.
			add_action( 'admin_init', array( $this, 'add_sections' ) );

			// Register settings.
			add_action( 'admin_init', array( $this, 'add_settings' ) );

			// Reset options.
			add_action( 'admin_init', array( $this, 'reset_options' ), 10 );

			// Initialize settings.
			add_action( 'admin_init', array( $this, 'initialize_settings' ), 11 );
		}

		/**
		 * Loads each admin page
		 *
		 * @return bool
		 *
		 * @access public
		 * @since  2.0
		 */
		public function add_page() {

			// Loop through options.
			foreach ( (array) $this->options as $option ) {

				// Loop through pages.
				foreach ( (array) $this->get_pages( $option ) as $page ) {

					/**
					 * Theme Check... stop nagging me about this kind of stuff.
					 * The damn admin pages are required for OT to function, duh!
					 */
					$theme_check_bs  = 'add_menu_' . 'page'; // phpcs:ignore
					$theme_check_bs2 = 'add_submenu_' . 'page'; // phpcs:ignore

					// Load page in WP top level menu.
					if ( ! isset( $page['parent_slug'] ) || empty( $page['parent_slug'] ) ) {
						$page_hook = $theme_check_bs(
							$page['page_title'],
							$page['menu_title'],
							$page['capability'],
							$page['menu_slug'],
							array( $this, 'display_page' ),
							$page['icon_url'],
							$page['position']
						);

						// Load page in WP sub menu.
					} else {
						$page_hook = $theme_check_bs2(
							$page['parent_slug'],
							$page['page_title'],
							$page['menu_title'],
							$page['capability'],
							$page['menu_slug'],
							array( $this, 'display_page' )
						);
					}

					// Only load if not a hidden page.
					if ( ! isset( $page['hidden_page'] ) ) {

						// Associate $page_hook with page id.
						$this->page_hook[ $page['id'] ] = $page_hook;

						// Add scripts.
						add_action( 'admin_print_scripts-' . $page_hook, array( $this, 'scripts' ) );

						// Add styles.
						add_action( 'admin_print_styles-' . $page_hook, array( $this, 'styles' ) );

						// Add contextual help.
						add_action( 'load-' . $page_hook, array( $this, 'help' ) );
					}
				}
			}

			return false;
		}

		/**
		 * Loads the scripts
		 *
		 * @access public
		 * @since  2.0
		 */
		public function scripts() {
			ot_admin_scripts();
		}

		/**
		 * Loads the styles
		 *
		 * @access public
		 * @since  2.0
		 */
		public function styles() {
			ot_admin_styles();
		}

		/**
		 * Loads the contextual help for each page
		 *
		 * @return bool
		 *
		 * @access public
		 * @since  2.0
		 */
		public function help() {
			$screen = get_current_screen();

			// Loop through options.
			foreach ( (array) $this->options as $option ) {

				// Loop through pages.
				foreach ( (array) $this->get_pages( $option ) as $page ) {

					// Verify page.
					if ( ! isset( $page['hidden_page'] ) && $screen->id === $this->page_hook[ $page['id'] ] ) {

						// Set up the help tabs.
						if ( ! empty( $page['contextual_help']['content'] ) ) {
							foreach ( $page['contextual_help']['content'] as $contextual_help ) {
								$screen->add_help_tab(
									array(
										'id'      => esc_attr( $contextual_help['id'] ),
										'title'   => esc_attr( $contextual_help['title'] ),
										'content' => htmlspecialchars_decode( $contextual_help['content'] ),
									)
								);
							}
						}

						// Set up the help sidebar.
						if ( ! empty( $page['contextual_help']['sidebar'] ) ) {
							$screen->set_help_sidebar( htmlspecialchars_decode( $page['contextual_help']['sidebar'] ) );
						}
					}
				}
			}

			return false;
		}

		/**
		 * Loads the content for each page
		 *
		 * @access public
		 * @since  2.0
		 */
		public function display_page() {
			$screen = get_current_screen();

			// Loop through settings.
			foreach ( (array) $this->options as $option ) {

				// Loop through pages.
				foreach ( (array) $this->get_pages( $option ) as $page ) {

					// Verify page.
					if ( ! isset( $page['hidden_page'] ) && $screen->id === $this->page_hook[ $page['id'] ] ) {

						$show_buttons = isset( $page['show_buttons'] ) && false === $page['show_buttons'] ? false : true;

						// Update active layout content.
						if ( isset( $_REQUEST['settings-updated'] ) && true === filter_var( wp_unslash( $_REQUEST['settings-updated'] ), FILTER_VALIDATE_BOOLEAN ) ) { // phpcs:ignore

							$layouts = get_option( ot_layouts_id() );

							// Has active layout.
							if ( isset( $layouts['active_layout'] ) ) {
								$option_tree                          = get_option( $option['id'], array() );
								$layouts[ $layouts['active_layout'] ] = ot_encode( $option_tree );
								update_option( ot_layouts_id(), $layouts );
							}
						}

						echo '<div class="wrap settings-wrap" id="page-' . esc_attr( $page['id'] ) . '">';

						echo '<h2>' . wp_kses_post( $page['page_title'] ) . '</h2>';

						echo ot_alert_message( $page ); // phpcs:ignore

						settings_errors( 'option-tree' );

						// Header.
						echo '<div id="option-tree-header-wrap">';

						echo '<ul id="option-tree-header">';

						$link = '<a href="https://wordpress.org/plugins/option-tree/" target="_blank">' . esc_html__( 'OptionTree', 'option-tree' ) . '</a>';
						echo '<li id="option-tree-logo">' . wp_kses_post( apply_filters( 'ot_header_logo_link', $link, $page['id'] ) ) . '</li>';

						echo '<li id="option-tree-version"><span>' . esc_html( apply_filters( 'ot_header_version_text', 'OptionTree ' . OT_VERSION, $page['id'] ) ) . '</span></li>';

						// Add additional theme specific links here.
						do_action( 'ot_header_list', $page['id'] );

						echo '</ul>';

						// Layouts form.
						if ( 'ot_theme_options' === $page['id'] && true === OT_SHOW_NEW_LAYOUT ) {
							ot_theme_options_layouts_form();
						}

						echo '</div>';

						// Remove forms on the custom settings pages.
						if ( $show_buttons ) {

							echo '<form action="options.php" method="post" id="option-tree-settings-api">';

							settings_fields( $option['id'] );
						} else {

							echo '<div id="option-tree-settings-api">';
						}

						// Sub Header.
						echo '<div id="option-tree-sub-header">';

						if ( $show_buttons ) {
							echo '<button class="option-tree-ui-button button button-primary right">' . esc_html( $page['button_text'] ) . '</button>';
						}

						echo '</div>';

						// Navigation.
						echo '<div class="ui-tabs">';

						// Check for sections.
						if ( isset( $page['sections'] ) && 0 < count( $page['sections'] ) ) {

							echo '<ul class="ui-tabs-nav">';

							// Loop through page sections.
							foreach ( (array) $page['sections'] as $section ) {
								echo '<li id="tab_' . esc_attr( $section['id'] ) . '"><a href="#section_' . esc_attr( $section['id'] ) . '">' . wp_kses_post( $section['title'] ) . '</a></li>';
							}

							echo '</ul>';
						}

						// Sections.
						echo '<div id="poststuff" class="metabox-holder">';

						echo '<div id="post-body">';

						echo '<div id="post-body-content">';

						$this->do_settings_sections( isset( $_GET['page'] ) ? $_GET['page'] : '' ); // phpcs:ignore

						echo '</div>';

						echo '</div>';

						echo '</div>';

						echo '<div class="clear"></div>';

						echo '</div>';

						// Buttons.
						if ( $show_buttons ) {

							echo '<div class="option-tree-ui-buttons">';

							echo '<button class="option-tree-ui-button button button-primary right">' . esc_html( $page['button_text'] ) . '</button>';

							echo '</div>';
						}

						echo $show_buttons ? '</form>' : '</div>';

						// Reset button.
						if ( $show_buttons ) {

							echo '<form method="post" action="' . esc_url_raw( str_replace( '&settings-updated=true', '', $_SERVER['REQUEST_URI'] ) ) . '">'; // phpcs:ignore

							// Form nonce.
							wp_nonce_field( 'option_tree_reset_form', 'option_tree_reset_nonce' );

							echo '<input type="hidden" name="action" value="reset" />';

							echo '<button type="submit" class="option-tree-ui-button button button-secondary left reset-settings" title="' . esc_html__( 'Reset Options', 'option-tree' ) . '">' . esc_html__( 'Reset Options', 'option-tree' ) . '</button>';

							echo '</form>';
						}

						echo '</div>';
					}
				}
			}

			return false;
		}

		/**
		 * Adds sections to the page
		 *
		 * @return bool
		 *
		 * @access public
		 * @since  2.0
		 */
		public function add_sections() {

			// Loop through options.
			foreach ( (array) $this->options as $option ) {

				// Loop through pages.
				foreach ( (array) $this->get_pages( $option ) as $page ) {

					// Loop through page sections.
					foreach ( (array) $this->get_sections( $page ) as $section ) {

						// Add each section.
						add_settings_section(
							$section['id'],
							$section['title'],
							array( $this, 'display_section' ),
							$page['menu_slug']
						);

					}
				}
			}

			return false;
		}

		/**
		 * Callback for add_settings_section()
		 *
		 * @access public
		 * @since  2.0
		 */
		public function display_section() {
			/* currently pointless */
		}

		/**
		 * Add settings the the page
		 *
		 * @return bool
		 *
		 * @access public
		 * @since  2.0
		 */
		public function add_settings() {

			// Loop through options.
			foreach ( (array) $this->options as $option ) {

				register_setting( $option['id'], $option['id'], array( $this, 'sanitize_callback' ) );

				// Loop through pages.
				foreach ( (array) $this->get_pages( $option ) as $page ) {

					// Loop through page settings.
					foreach ( (array) $this->get_the_settings( $page ) as $setting ) {

						// Skip if missing setting ID, label, or section.
						if ( ! isset( $setting['id'] ) || ! isset( $setting['label'] ) || ! isset( $setting['section'] ) ) {
							continue;
						}

						// Add get_option param to the array.
						$setting['get_option'] = $option['id'];

						// Add each setting.
						add_settings_field(
							$setting['id'],
							$setting['label'],
							array( $this, 'display_setting' ),
							$page['menu_slug'],
							$setting['section'],
							$setting
						);
					}
				}
			}

			return false;
		}

		/**
		 * Callback for add_settings_field() to build each setting by type
		 *
		 * @param  array $args Setting object array.
		 *
		 * @access public
		 * @since  2.0
		 */
		public function display_setting( $args = array() ) {
			extract( $args ); // phpcs:ignore

			// Get current saved data.
			$options = get_option( $get_option, false );

			// Set field value.
			$field_value = isset( $options[ $id ] ) ? $options[ $id ] : '';

			// Set standard value.
			if ( isset( $std ) ) {
				$field_value = ot_filter_std_value( $field_value, $std );
			}

			// Allow the descriptions to be filtered before being displayed.
			$desc = apply_filters( 'ot_filter_description', ( isset( $desc ) ? $desc : '' ), $id );

			// Build the arguments array.
			$_args = array(
				'type'               => $type,
				'field_id'           => $id,
				'field_name'         => $get_option . '[' . $id . ']',
				'field_value'        => $field_value,
				'field_desc'         => $desc,
				'field_std'          => isset( $std ) ? $std : '',
				'field_rows'         => isset( $rows ) && ! empty( $rows ) ? $rows : 15,
				'field_post_type'    => isset( $post_type ) && ! empty( $post_type ) ? $post_type : 'post',
				'field_taxonomy'     => isset( $taxonomy ) && ! empty( $taxonomy ) ? $taxonomy : 'category',
				'field_min_max_step' => isset( $min_max_step ) && ! empty( $min_max_step ) ? $min_max_step : '0,100,1',
				'field_condition'    => isset( $condition ) && ! empty( $condition ) ? $condition : '',
				'field_operator'     => isset( $operator ) && ! empty( $operator ) ? $operator : 'and',
				'field_class'        => isset( $class ) ? $class : '',
				'field_choices'      => isset( $choices ) && ! empty( $choices ) ? $choices : array(),
				'field_settings'     => isset( $settings ) && ! empty( $settings ) ? $settings : array(),
				'post_id'            => ot_get_media_post_ID(),
				'get_option'         => $get_option,
			);

			// Limit DB queries for Google Fonts.
			if ( 'google-fonts' === $type ) {
				ot_fetch_google_fonts();
				ot_set_google_fonts( $id, $field_value );
			}

			// Get the option HTML.
			echo ot_display_by_type( $_args ); // phpcs:ignore
		}

		/**
		 * Sets the option standards if nothing yet exists.
		 *
		 * @access public
		 * @since  2.0
		 */
		public function initialize_settings() {

			// Loop through options.
			foreach ( (array) $this->options as $option ) {

				// Skip if option is already set.
				if ( isset( $option['id'] ) && get_option( $option['id'], false ) ) {
					return false;
				}

				$defaults = array();

				// Loop through pages.
				foreach ( (array) $this->get_pages( $option ) as $page ) {

					// Loop through page settings.
					foreach ( (array) $this->get_the_settings( $page ) as $setting ) {

						if ( isset( $setting['std'] ) ) {

							$defaults[ $setting['id'] ] = ot_validate_setting( $setting['std'], $setting['type'], $setting['id'] );
						}
					}
				}

				update_option( $option['id'], $defaults );
			}

			return false;
		}

		/**
		 * Sanitize callback for register_setting()
		 *
		 * @param mixed $input The setting input.
		 * @return string
		 *
		 * @access public
		 * @since  2.0
		 */
		public function sanitize_callback( $input ) {

			// Store the post global for use later.
			$post_global = $_POST; // phpcs:ignore

			// Loop through options.
			foreach ( (array) $this->options as $option ) {

				// Loop through pages.
				foreach ( (array) $this->get_pages( $option ) as $page ) {

					// Loop through page settings.
					foreach ( (array) $this->get_the_settings( $page ) as $setting ) {

						// Verify setting has a type & value.
						if ( isset( $setting['type'] ) && isset( $input[ $setting['id'] ] ) ) {

							// Get the defaults.
							$current_settings = get_option( ot_settings_id() );
							$current_options  = get_option( $option['id'] );

							// Validate setting.
							if ( is_array( $input[ $setting['id'] ] ) && in_array( $setting['type'], array( 'list-item', 'slider' ), true ) ) {

								// Required title setting.
								$required_setting = array(
									array(
										'id'        => 'title',
										'label'     => __( 'Title', 'option-tree' ),
										'desc'      => '',
										'std'       => '',
										'type'      => 'text',
										'rows'      => '',
										'class'     => 'option-tree-setting-title',
										'post_type' => '',
										'choices'   => array(),
									),
								);

								// Convert the settings to an array.
								$settings = isset( $post_global[ $setting['id'] . '_settings_array' ] ) ? ot_decode( $post_global[ $setting['id'] . '_settings_array' ] ) : array();

								// Settings are empty for some odd reason get the defaults.
								if ( empty( $settings ) ) {
									$settings = 'slider' === $setting['type'] ? ot_slider_settings( $setting['id'] ) : ot_list_item_settings( $setting['id'] );
								}

								// Merge the two settings arrays.
								$settings = array_merge( $required_setting, $settings );

								// Create an empty WPML id array.
								$wpml_ids = array();

								foreach ( $input[ $setting['id'] ] as $k => $setting_array ) {

									$has_value = false;
									foreach ( $settings as $sub_setting ) {

										// Setup the WPML ID.
										$wpml_id = $setting['id'] . '_' . $sub_setting['id'] . '_' . $k;

										// Add id to array.
										$wpml_ids[] = $wpml_id;

										/* verify sub setting has a type & value */
										if ( isset( $sub_setting['type'] ) && isset( $input[ $setting['id'] ][ $k ][ $sub_setting['id'] ] ) ) {

											// Validate setting.
											$input[ $setting['id'] ][ $k ][ $sub_setting['id'] ] = ot_validate_setting( $input[ $setting['id'] ][ $k ][ $sub_setting['id'] ], $sub_setting['type'], $sub_setting['id'], $wpml_id );
											$has_value = true;
										}
									}

									if ( ! $has_value ) {
										unset( $input[ $setting['id'] ][ $k ] );
									}
								}
							} elseif ( is_array( $input[ $setting['id'] ] ) && 'social-links' === $setting['type'] ) {

								// Convert the settings to an array.
								$settings = isset( $post_global[ $setting['id'] . '_settings_array' ] ) ? ot_decode( $post_global[ $setting['id'] . '_settings_array' ] ) : array();

								// Settings are empty get the defaults.
								if ( empty( $settings ) ) {
									$settings = ot_social_links_settings( $setting['id'] );
								}

								// Create an empty WPML id array.
								$wpml_ids = array();

								foreach ( $input[ $setting['id'] ] as $k => $setting_array ) {

									$has_value = false;
									foreach ( $settings as $sub_setting ) {

										// Setup the WPML ID.
										$wpml_id = $setting['id'] . '_' . $sub_setting['id'] . '_' . $k;

										// Add id to array.
										$wpml_ids[] = $wpml_id;

										// Verify sub setting has a type & value.
										if ( isset( $sub_setting['type'] ) && isset( $input[ $setting['id'] ][ $k ][ $sub_setting['id'] ] ) ) {

											if ( 'href' === $sub_setting['id'] ) {
												$sub_setting['type'] = 'url';
											}

											// Validate setting.
											$input_safe = ot_validate_setting( $input[ $setting['id'] ][ $k ][ $sub_setting['id'] ], $sub_setting['type'], $sub_setting['id'], $wpml_id );

											if ( ! empty( $input_safe ) ) {
												$input[ $setting['id'] ][ $k ][ $sub_setting['id'] ] = $input_safe;
												$has_value = true;
											}
										}
									}

									if ( ! $has_value ) {
										unset( $input[ $setting['id'] ][ $k ] );
									}
								}
							} else {
								$input[ $setting['id'] ] = ot_validate_setting( $input[ $setting['id'] ], $setting['type'], $setting['id'], $setting['id'] );
							}
						}

						// Unregister WPML strings that were deleted from lists and sliders.
						if ( isset( $current_settings['settings'] ) && isset( $setting['type'] ) && in_array( $setting['type'], array( 'list-item', 'slider' ), true ) ) {

							if ( ! isset( $wpml_ids ) ) {
								$wpml_ids = array();
							}

							foreach ( $current_settings['settings'] as $check_setting ) {

								if ( $setting['id'] === $check_setting['id'] && ! empty( $current_options[ $setting['id'] ] ) ) {

									foreach ( $current_options[ $setting['id'] ] as $key => $value ) {

										foreach ( $value as $ckey => $cvalue ) {

											$id = $setting['id'] . '_' . $ckey . '_' . $key;

											if ( ! in_array( $id, $wpml_ids, true ) ) {
												ot_wpml_unregister_string( $id );
											}
										}
									}
								}
							}
						}

						/* unregister WPML strings that were deleted from social links */
						if ( isset( $current_settings['settings'] ) && isset( $setting['type'] ) && 'social-links' === $setting['type'] ) {

							if ( ! isset( $wpml_ids ) ) {
								$wpml_ids = array();
							}

							foreach ( $current_settings['settings'] as $check_setting ) {

								if ( $setting['id'] === $check_setting['id'] && ! empty( $current_options[ $setting['id'] ] ) ) {

									foreach ( $current_options[ $setting['id'] ] as $key => $value ) {

										foreach ( $value as $ckey => $cvalue ) {

											$id = $setting['id'] . '_' . $ckey . '_' . $key;

											if ( ! in_array( $id, $wpml_ids, true ) ) {
												ot_wpml_unregister_string( $id );
											}
										}
									}
								}
							}
						}
					}
				}
			}

			return $input;
		}

		/**
		 * Helper function to get the pages array for an option
		 *
		 * @param  array $option Option array.
		 * @return mixed
		 *
		 * @access public
		 * @since  2.0
		 */
		public function get_pages( $option = array() ) {

			if ( empty( $option ) ) {
				return false;
			}

			// Check for pages.
			if ( isset( $option['pages'] ) && ! empty( $option['pages'] ) ) {

				// Return pages array.
				return $option['pages'];

			}

			return false;
		}

		/**
		 * Helper function to get the sections array for a page
		 *
		 * @param  array $page Page array.
		 * @return mixed
		 *
		 * @access public
		 * @since  2.0
		 */
		public function get_sections( $page = array() ) {

			if ( empty( $page ) ) {
				return false;
			}

			// Check for sections.
			if ( isset( $page['sections'] ) && ! empty( $page['sections'] ) ) {

				// Return sections array.
				return $page['sections'];

			}

			return false;
		}

		/**
		 * Helper function to get the settings array for a page
		 *
		 * @param  array $page Page array.
		 * @return mixed
		 *
		 * @access public
		 * @since  2.0
		 */
		public function get_the_settings( $page = array() ) {

			if ( empty( $page ) ) {
				return false;
			}

			/* check for settings */
			if ( isset( $page['settings'] ) && ! empty( $page['settings'] ) ) {

				/* return settings array */
				return $page['settings'];

			}

			return false;
		}

		/**
		 * Prints out all settings sections added to a particular settings page
		 *
		 * @global $wp_settings_sections Storage array of all settings sections added to admin pages.
		 * @global $wp_settings_fields   Storage array of settings fields and info about their pages/sections.
		 *
		 * @param  string $page The slug name of the page whos settings sections you want to output.
		 * @return string
		 *
		 * @access public
		 * @since  2.0
		 */
		public function do_settings_sections( $page ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[ $page ] ) ) {
				return false;
			}

			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {

				if ( ! isset( $section['id'] ) ) {
					continue;
				}

				$section_id = $section['id'];

				echo '<div id="section_' . esc_attr( $section_id ) . '" class="postbox ui-tabs-panel">';

				call_user_func( $section['callback'], $section );

				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section_id ] ) ) {
					continue;
				}

				echo '<div class="inside">';

				/**
				 * Hook to insert arbitrary markup before the `do_settings_fields` method.
				 *
				 * @since 2.6.0
				 *
				 * @param string $page       The page slug.
				 * @param string $section_id The section ID.
				 */
				do_action( 'ot_do_settings_fields_before', $page, $section_id );

				$this->do_settings_fields( $page, $section_id );

				/**
				 * Hook to insert arbitrary markup after the `do_settings_fields` method.
				 *
				 * @since 2.6.0
				 *
				 * @param string $page       The page slug.
				 * @param string $section_id The section ID.
				 */
				do_action( 'ot_do_settings_fields_after', $page, $section_id );

				echo '</div>';

				echo '</div>';
			}

		}

		/**
		 * Print out the settings fields for a particular settings section
		 *
		 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
		 *
		 * @param  string $page    Slug title of the admin page who's settings fields you want to show.
		 * @param  string $section Slug title of the settings section who's fields you want to show.
		 * @return string
		 *
		 * @access public
		 * @since  2.0
		 */
		public function do_settings_fields( $page, $section ) {
			global $wp_settings_fields;

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {

				$conditions = '';

				if ( isset( $field['args']['condition'] ) && ! empty( $field['args']['condition'] ) ) {

					$conditions  = ' data-condition="' . esc_attr( $field['args']['condition'] ) . '"';
					$conditions .= isset( $field['args']['operator'] ) && in_array( $field['args']['operator'], array( 'and', 'AND', 'or', 'OR' ), true ) ? ' data-operator="' . esc_attr( $field['args']['operator'] ) . '"' : '';
				}

				// Build the setting CSS class.
				if ( isset( $field['args']['class'] ) && ! empty( $field['args']['class'] ) ) {

					$classes = explode( ' ', $field['args']['class'] );

					foreach ( $classes as $key => $value ) {
						$classes[ $key ] = $value . '-wrap';
					}

					$class = 'format-settings ' . implode( ' ', $classes );
				} else {

					$class = 'format-settings';
				}

				echo '<div id="setting_' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $class ) . '"' . $conditions . '>'; // phpcs:ignore

				echo '<div class="format-setting-wrap">';

				if ( 'textblock' !== $field['args']['type'] && ! empty( $field['title'] ) ) {

					echo '<div class="format-setting-label">';

					echo '<h3 class="label">' . wp_kses_post( $field['title'] ) . '</h3>';

					echo '</div>';
				}

				call_user_func( $field['callback'], $field['args'] );

				echo '</div>';

				echo '</div>';
			}
		}

		/**
		 * Resets page options before the screen is displayed
		 *
		 * @access public
		 * @since  2.0
		 *
		 * @return bool
		 */
		public function reset_options() {

			// Check for reset action.
			if ( isset( $_POST['option_tree_reset_nonce'] ) && wp_verify_nonce( $_POST['option_tree_reset_nonce'], 'option_tree_reset_form' ) ) { // phpcs:ignore

				// Loop through options.
				foreach ( (array) $this->options as $option ) {

					// Loop through pages.
					foreach ( (array) $this->get_pages( $option ) as $page ) {

						// Verify page.
						if ( isset( $_GET['page'] ) && $_GET['page'] === $page['menu_slug'] ) {

							// Reset options.
							delete_option( $option['id'] );
						}
					}
				}
			}
			return false;
		}
	}

}

if ( ! function_exists( 'ot_register_settings' ) ) {

	/**
	 * This method instantiates the settings class & builds the UI.
	 *
	 * @uses OT_Settings()
	 *
	 * @param array $args Array of arguments to create settings.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_register_settings( $args ) {
		if ( ! $args ) {
			return;
		}

		new OT_Settings( $args );
	}
}
