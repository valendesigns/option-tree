<?php
/**
 * OptionTree Meta Box.
 *
 * @package OptionTree
 */

if ( ! defined( 'OT_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! class_exists( 'OT_Meta_Box' ) ) {

	/**
	 * OptionTree Meta Box class.
	 *
	 * This class loads all the methods and helpers specific to build a meta box.
	 */
	class OT_Meta_Box {

		/**
		 * Stores the meta box config array.
		 *
		 * @var string
		 */
		private $meta_box;

		/**
		 * Class constructor.
		 *
		 * This method adds other methods of the class to specific hooks within WordPress.
		 *
		 * @uses add_action()
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param array $meta_box Meta box config array.
		 */
		public function __construct( $meta_box ) {
			if ( ! is_admin() ) {
				return;
			}

			global $ot_meta_boxes;

			if ( ! isset( $ot_meta_boxes ) ) {
				$ot_meta_boxes = array();
			}

			$ot_meta_boxes[] = $meta_box;

			$this->meta_box = $meta_box;

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

			add_action( 'save_post', array( $this, 'save_meta_box' ), 1, 2 );
		}

		/**
		 * Adds meta box to any post type
		 *
		 * @uses add_meta_box()
		 *
		 * @access public
		 * @since  1.0
		 */
		public function add_meta_boxes() {
			global $wp_version;

			$is_wp_5 = version_compare( $wp_version, '5.0', '>=' );

			foreach ( (array) $this->meta_box['pages'] as $page ) {
				add_meta_box( $this->meta_box['id'], $this->meta_box['title'], array( $this, 'build_meta_box' ), $page, $this->meta_box['context'], $this->meta_box['priority'], $this->meta_box['fields'] );

				if ( $is_wp_5 ) {
					add_filter(
						'postbox_classes_' . $page . '_' . $this->meta_box['id'],
						function( $classes ) {
							array_push( $classes, 'ot-meta-box' );
							return $classes;
						}
					);
				}
			}
		}

		/**
		 * Meta box view.
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param object $post   The WP_Post object.
		 * @param array  $fields The meta box fields.
		 */
		public function build_meta_box( $post, $fields ) {
			unset( $fields ); // @todo Check if the loop can use this param.

			echo '<div class="ot-metabox-wrapper">';

			// Use nonce for verification.
			echo '<input type="hidden" name="' . esc_attr( $this->meta_box['id'] ) . '_nonce" value="' . esc_attr( wp_create_nonce( $this->meta_box['id'] ) ) . '" />';

			// Meta box description.
			echo isset( $this->meta_box['desc'] ) && ! empty( $this->meta_box['desc'] ) ? '<div class="description" style="padding-top:10px;">' . htmlspecialchars_decode( $this->meta_box['desc'] ) . '</div>' : ''; // phpcs:ignore

			// Loop through meta box fields.
			foreach ( $this->meta_box['fields'] as $field ) {

				// Get current post meta data.
				$field_value = get_post_meta( $post->ID, $field['id'], true );

				// Set standard value.
				if ( isset( $field['std'] ) ) {
					$field_value = ot_filter_std_value( $field_value, $field['std'] );
				}

				// Build the arguments array.
				$_args = array(
					'type'               => $field['type'],
					'field_id'           => $field['id'],
					'field_name'         => $field['id'],
					'field_value'        => $field_value,
					'field_desc'         => isset( $field['desc'] ) ? $field['desc'] : '',
					'field_std'          => isset( $field['std'] ) ? $field['std'] : '',
					'field_rows'         => isset( $field['rows'] ) && ! empty( $field['rows'] ) ? $field['rows'] : 10,
					'field_post_type'    => isset( $field['post_type'] ) && ! empty( $field['post_type'] ) ? $field['post_type'] : 'post',
					'field_taxonomy'     => isset( $field['taxonomy'] ) && ! empty( $field['taxonomy'] ) ? $field['taxonomy'] : 'category',
					'field_min_max_step' => isset( $field['min_max_step'] ) && ! empty( $field['min_max_step'] ) ? $field['min_max_step'] : '0,100,1',
					'field_class'        => isset( $field['class'] ) ? $field['class'] : '',
					'field_condition'    => isset( $field['condition'] ) ? $field['condition'] : '',
					'field_operator'     => isset( $field['operator'] ) ? $field['operator'] : 'and',
					'field_choices'      => isset( $field['choices'] ) ? $field['choices'] : array(),
					'field_settings'     => isset( $field['settings'] ) && ! empty( $field['settings'] ) ? $field['settings'] : array(),
					'post_id'            => $post->ID,
					'meta'               => true,
				);

				$conditions = '';

				// Setup the conditions.
				if ( isset( $field['condition'] ) && ! empty( $field['condition'] ) ) {
					$conditions  = ' data-condition="' . esc_attr( $field['condition'] ) . '"';
					$conditions .= isset( $field['operator'] ) && in_array( $field['operator'], array( 'and', 'AND', 'or', 'OR' ), true ) ? ' data-operator="' . esc_attr( $field['operator'] ) . '"' : '';
				}

				// Only allow simple textarea due to DOM issues with wp_editor().
				if ( false === apply_filters( 'ot_override_forced_textarea_simple', false, $field['id'] ) && 'textarea' === $_args['type'] ) {
					$_args['type'] = 'textarea-simple';
				}

				// Build the setting CSS class.
				if ( ! empty( $_args['field_class'] ) ) {

					$classes = explode( ' ', $_args['field_class'] );

					foreach ( $classes as $key => $value ) {

						$classes[ $key ] = $value . '-wrap';

					}

					$class = 'format-settings ' . implode( ' ', $classes );
				} else {

					$class = 'format-settings';
				}

				// Option label.
				echo '<div id="setting_' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $class ) . '"' . $conditions . '>'; // phpcs:ignore

				echo '<div class="format-setting-wrap">';

				// Don't show title with textblocks.
				if ( 'textblock' !== $_args['type'] && ! empty( $field['label'] ) ) {
					echo '<div class="format-setting-label">';
					echo '<label for="' . esc_attr( $field['id'] ) . '" class="label">' . esc_html( $field['label'] ) . '</label>';
					echo '</div>';
				}

				// Get the option HTML.
				echo ot_display_by_type( $_args ); // phpcs:ignore

				echo '</div>';

				echo '</div>';

			}

			echo '<div class="clear"></div>';

			echo '</div>';
		}

		/**
		 * Saves the meta box values
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param  int    $post_id The post ID.
		 * @param  object $post_object The WP_Post object.
		 * @return int|void
		 */
		public function save_meta_box( $post_id, $post_object ) {
			global $pagenow;

			// Verify nonce.
			if ( isset( $_POST[ $this->meta_box['id'] . '_nonce' ] ) && ! wp_verify_nonce( $_POST[ $this->meta_box['id'] . '_nonce' ], $this->meta_box['id'] ) ) { // phpcs:ignore
				return $post_id;
			}

			// Store the post global for use later.
			$post_global = $_POST;

			// Don't save if $_POST is empty.
			if ( empty( $post_global ) || ( isset( $post_global['vc_inline'] ) && true === $post_global['vc_inline'] ) ) {
				return $post_id;
			}

			// Don't save during quick edit.
			if ( 'admin-ajax.php' === $pagenow ) {
				return $post_id;
			}

			// Don't save during autosave.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// Don't save if viewing a revision.
			if ( 'revision' === $post_object->post_type || 'revision.php' === $pagenow ) {
				return $post_id;
			}

			// Check permissions.
			if ( isset( $post_global['post_type'] ) && 'page' === $post_global['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}
			}

			foreach ( $this->meta_box['fields'] as $field ) {

				$old = get_post_meta( $post_id, $field['id'], true );
				$new = '';

				// There is data to validate.
				if ( isset( $post_global[ $field['id'] ] ) ) {

					// Slider and list item.
					if ( in_array( $field['type'], array( 'list-item', 'slider' ), true ) ) {

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
						$settings = isset( $post_global[ $field['id'] . '_settings_array' ] ) ? ot_decode( $post_global[ $field['id'] . '_settings_array' ] ) : array();

						// Settings are empty for some odd reason get the defaults.
						if ( empty( $settings ) ) {
							$settings = ( 'slider' === $field['type'] ) ? ot_slider_settings( $field['id'] ) : ot_list_item_settings( $field['id'] );
						}

						// Merge the two settings array.
						$settings = array_merge( $required_setting, $settings );

						foreach ( $post_global[ $field['id'] ] as $k => $setting_array ) {

							foreach ( $settings as $sub_setting ) {

								// Verify sub setting has a type & value.
								if ( isset( $sub_setting['type'] ) && isset( $post_global[ $field['id'] ][ $k ][ $sub_setting['id'] ] ) ) {

									$post_global[ $field['id'] ][ $k ][ $sub_setting['id'] ] = ot_validate_setting( $post_global[ $field['id'] ][ $k ][ $sub_setting['id'] ], $sub_setting['type'], $sub_setting['id'] );
								}
							}
						}

						// Set up new data with validated data.
						$new = $post_global[ $field['id'] ];

					} elseif ( 'social-links' === $field['type'] ) {

						// Convert the settings to an array.
						$settings = isset( $post_global[ $field['id'] . '_settings_array' ] ) ? ot_decode( $post_global[ $field['id'] . '_settings_array' ] ) : array();

						// Settings are empty get the defaults.
						if ( empty( $settings ) ) {
							$settings = ot_social_links_settings( $field['id'] );
						}

						foreach ( $post_global[ $field['id'] ] as $k => $setting_array ) {

							foreach ( $settings as $sub_setting ) {

								// Verify sub setting has a type & value.
								if ( isset( $sub_setting['type'] ) && isset( $post_global[ $field['id'] ][ $k ][ $sub_setting['id'] ] ) ) {
									$post_global[ $field['id'] ][ $k ][ $sub_setting['id'] ] = ot_validate_setting( $post_global[ $field['id'] ][ $k ][ $sub_setting['id'] ], $sub_setting['type'], $sub_setting['id'] );
								}
							}
						}

						// Set up new data with validated data.
						$new = $post_global[ $field['id'] ];
					} else {

						// Run through validation.
						$new = ot_validate_setting( $post_global[ $field['id'] ], $field['type'], $field['id'] );
					}

					// Insert CSS.
					if ( 'css' === $field['type'] ) {

						if ( '' !== $new ) {

							// insert CSS into dynamic.css.
							ot_insert_css_with_markers( $field['id'], $new, true );
						} else {

							// Remove old CSS from dynamic.css.
							ot_remove_old_css( $field['id'] );
						}
					}
				}

				if ( isset( $new ) && $new !== $old ) {
					update_post_meta( $post_id, $field['id'], $new );
				} elseif ( '' === $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				}
			}
		}

	}

}

if ( ! function_exists( 'ot_register_meta_box' ) ) {

	/**
	 * This method instantiates the meta box class & builds the UI.
	 *
	 * @uses OT_Meta_Box()
	 *
	 * @param array $args Meta box arguments.
	 *
	 * @access public
	 * @since  2.0
	 */
	function ot_register_meta_box( $args ) {
		if ( ! $args ) {
			return;
		}

		new OT_Meta_Box( $args );
	}
}
