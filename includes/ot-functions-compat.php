<?php
/**
 * OptionTree Compatibility Functions.
 *
 * @package OptionTree
 */

if ( ! defined( 'OT_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

// Run the actions & filters.
add_action( 'admin_init', 'compat_ot_import_from_files', 1 );
add_filter( 'ot_option_types_array', 'compat_ot_option_types_array', 10, 1 );
add_filter( 'ot_recognized_font_styles', 'compat_ot_recognized_font_styles', 10, 2 );
add_filter( 'ot_recognized_font_weights', 'compat_ot_recognized_font_weights', 10, 2 );
add_filter( 'ot_recognized_font_variants', 'compat_ot_recognized_font_variants', 10, 2 );
add_filter( 'ot_recognized_font_families', 'compat_ot_recognized_font_families', 10, 2 );
add_filter( 'ot_recognized_background_repeat', 'compat_ot_recognized_background_repeat', 10, 2 );
add_filter( 'ot_recognized_background_position', 'compat_ot_recognized_background_position', 10, 2 );
add_filter( 'ot_measurement_unit_types', 'compat_ot_measurement_unit_types', 10, 2 );

if ( ! function_exists( 'compat_ot_import_from_files' ) ) {

	/**
	 * Import from the old 1.x files for backwards compatibility.
	 *
	 * @access private
	 * @since  2.0.8
	 */
	function compat_ot_import_from_files() {

		// File path & name.
		$ot_data   = '/option-tree/theme-options.txt';
		$ot_layout = '/option-tree/layouts.txt';

		// Data file path - child theme first then parent.
		if ( is_readable( get_stylesheet_directory() . $ot_data ) ) {

			$data_file = get_stylesheet_directory_uri() . $ot_data;

		} elseif ( is_readable( get_template_directory() . $ot_data ) ) {

			$data_file = get_template_directory_uri() . $ot_data;

		}

		// Layout file path - child theme first then parent.
		if ( is_readable( get_stylesheet_directory() . $ot_layout ) ) {

			$layout_file = get_stylesheet_directory_uri() . $ot_layout;

		} elseif ( is_readable( get_template_directory() . $ot_layout ) ) {

			$layout_file = get_template_directory_uri() . $ot_layout;

		}

		// Check for files.
		$has_data   = isset( $data_file ) ? true : false;
		$has_layout = isset( $layout_file ) ? true : false;

		// Auto import Data file.
		if ( true === $has_data && ! get_option( ot_options_id() ) ) {

			$get_data = wp_remote_get( $data_file );

			if ( is_wp_error( $get_data ) ) {
				return false;
			}

			$options      = isset( $get_data['body'] ) ? ot_decode( $get_data['body'] ) : array();
			$options_safe = array();

			// Get settings array.
			$settings = get_option( ot_settings_id() );

			// Has options.
			if ( is_array( $options ) ) {

				// Validate options.
				if ( is_array( $settings ) ) {

					foreach ( $settings['settings'] as $setting ) {
						if ( isset( $options[ $setting['id'] ] ) ) {
							$options_safe[ $setting['id'] ] = ot_validate_setting( wp_unslash( $options[ $setting['id'] ] ), $setting['type'], $setting['id'] );
						}
					}
				}

				// Update the option tree array.
				update_option( ot_options_id(), $options_safe );
			}
		}

		// Auto import Layout file.
		if ( true === $has_layout && ! get_option( ot_layouts_id() ) ) {

			$get_data = wp_remote_get( $layout_file );

			if ( is_wp_error( $get_data ) ) {
				return false;
			}

			$layouts      = isset( $get_data['body'] ) ? ot_decode( $get_data['body'] ) : array();
			$layouts_safe = array();

			// Get settings array.
			$settings = get_option( ot_settings_id() );

			// Has layouts.
			if ( is_array( $layouts ) ) {

				// Validate options.
				if ( is_array( $settings ) ) {

					foreach ( $layouts as $key => $value ) {

						if ( 'active_layout' === $key ) {
							$layouts_safe['active_layout'] = $key;
							continue;
						}

						$options      = ot_decode( $value );
						$options_safe = array();

						foreach ( $settings['settings'] as $setting ) {
							if ( isset( $options[ $setting['id'] ] ) ) {
								$options_safe[ $setting['id'] ] = ot_validate_setting( wp_unslash( $options[ $setting['id'] ] ), $setting['type'], $setting['id'] );
							}
						}

						if ( $key === $layouts['active_layout'] ) {
							$new_options_safe = $options_safe;
						}

						$layouts_safe[ $key ] = ot_encode( $options_safe );
					}
				}

				// Update the option tree array.
				if ( isset( $new_options_safe ) ) {
					update_option( ot_options_id(), $new_options_safe );
				}

				// Update the option tree layouts array.
				update_option( ot_layouts_id(), $layouts_safe );
			}
		}
	}
}

if ( ! function_exists( 'compat_ot_option_types_array' ) ) {

	/**
	 * Filters the option types array.
	 *
	 * Allows the old 'option_tree_option_types' filter to
	 * change the new 'ot_option_types_array' return value.
	 *
	 * @param  array $array The option types in key:value format.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_option_types_array( $array ) {

		return apply_filters( 'option_tree_option_types', $array );

	}
}

if ( ! function_exists( 'compat_ot_recognized_font_styles' ) ) {

	/**
	 * Filters the recognized font styles array.
	 *
	 * Allows the old 'recognized_font_styles' filter to
	 * change the new 'ot_recognized_font_styles' return value.
	 *
	 * @param  array  $array The option types in key:value format.
	 * @param  string $id    The field ID.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_recognized_font_styles( $array, $id ) {

		return apply_filters( 'recognized_font_styles', $array, $id );

	}
}

if ( ! function_exists( 'compat_ot_recognized_font_weights' ) ) {

	/**
	 * Filters the recognized font weights array.
	 *
	 * Allows the old 'recognized_font_weights' filter to
	 * change the new 'ot_recognized_font_weights' return value.
	 *
	 * @param  array  $array The option types in key:value format.
	 * @param  string $id    The field ID.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_recognized_font_weights( $array, $id ) {

		return apply_filters( 'recognized_font_weights', $array, $id );

	}
}

if ( ! function_exists( 'compat_ot_recognized_font_variants' ) ) {

	/**
	 * Filters the recognized font variants array.
	 *
	 * Allows the old 'recognized_font_variants' filter to
	 * change the new 'ot_recognized_font_variants' return value.
	 *
	 * @param  array  $array The option types in key:value format.
	 * @param  string $id    The field ID.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_recognized_font_variants( $array, $id ) {

		return apply_filters( 'recognized_font_variants', $array, $id );

	}
}

if ( ! function_exists( 'compat_ot_recognized_font_families' ) ) {

	/**
	 * Filters the recognized font families array.
	 *
	 * Allows the old 'recognized_font_families' filter to
	 * change the new 'ot_recognized_font_families' return value.
	 *
	 * @param  array  $array The option types in key:value format.
	 * @param  string $id    The field ID.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_recognized_font_families( $array, $id ) {

		return apply_filters( 'recognized_font_families', $array, $id );

	}
}

if ( ! function_exists( 'compat_ot_recognized_background_repeat' ) ) {

	/**
	 * Filters the recognized background repeat array.
	 *
	 * Allows the old 'recognized_background_repeat' filter to
	 * change the new 'ot_recognized_background_repeat' return value.
	 *
	 * @param  array  $array The option types in key:value format.
	 * @param  string $id    The field ID.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_recognized_background_repeat( $array, $id ) {

		return apply_filters( 'recognized_background_repeat', $array, $id );

	}
}

if ( ! function_exists( 'compat_ot_recognized_background_position' ) ) {

	/**
	 * Filters the recognized background position array.
	 *
	 * Allows the old 'recognized_background_position' filter to
	 * change the new 'ot_recognized_background_position' return value.
	 *
	 * @param  array  $array The option types in key:value format.
	 * @param  string $id    The field ID.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_recognized_background_position( $array, $id ) {

		return apply_filters( 'recognized_background_position', $array, $id );

	}
}

if ( ! function_exists( 'compat_ot_measurement_unit_types' ) ) {

	/**
	 * Filters the measurement unit types array.
	 *
	 * Allows the old 'measurement_unit_types' filter to
	 * change the new 'ot_measurement_unit_types' return value.
	 *
	 * @param  array  $array The option types in key:value format.
	 * @param  string $id    The field ID.
	 * @return array
	 *
	 * @access public
	 * @since  2.0
	 */
	function compat_ot_measurement_unit_types( $array, $id ) {

		return apply_filters( 'measurement_unit_types', $array, $id );

	}
}
