<?php
/**
 * OptionTree Deprecated Functions.
 *
 * @package OptionTree
 */

if ( ! defined( 'OT_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'get_option_tree' ) ) {

	/**
	 * Displays or returns a value from the 'option_tree' array.
	 *
	 * @param      string $item_id  The item ID.
	 * @param      array  $options  Options array.
	 * @param      bool   $echo     Whether to echo or return value.
	 * @param      bool   $is_array Whether the value option is an array or string.
	 * @param      int    $offset   The array key.
	 * @return     mixed  Array or comma separated lists of values.
	 *
	 * @access     public
	 * @since      1.0.0
	 * @updated    2.0
	 * @deprecated 2.0
	 */
	function get_option_tree( $item_id = '', $options = array(), $echo = false, $is_array = false, $offset = -1 ) {

		// Load saved options.
		if ( ! $options ) {
			$options = get_option( ot_options_id() );
		}

		// No value return.
		if ( ! isset( $options[ $item_id ] ) || empty( $options[ $item_id ] ) ) {
			return;
		}

		// Set content value & strip slashes.
		$content = option_tree_stripslashes( $options[ $item_id ] );

		if ( true === $is_array ) {
			if ( ! is_array( $content ) ) {
				$content = explode( ',', $content );
			}

			if ( is_numeric( $offset ) && 0 <= $offset ) {
				$content = $content[ $offset ];
			} elseif ( ! is_numeric( $offset ) && isset( $content[ $offset ] ) ) {
				$content = $content[ $offset ];
			}
		} else {
			if ( is_array( $content ) ) {
				$content = implode( ',', $content );
			}
		}

		if ( $echo ) {
			echo $content; // phpcs:ignore
		}

		return $content;
	}
}

if ( ! function_exists( 'option_tree_stripslashes' ) ) {

	/**
	 * Custom stripslashes from single value or array.
	 *
	 * @param      mixed $input Input string or array.
	 * @return     mixed
	 *
	 * @access     public
	 * @since      1.1.3
	 * @deprecated 2.0
	 */
	function option_tree_stripslashes( $input ) {
		if ( is_array( $input ) ) {
			foreach ( $input as &$val ) {
				if ( is_array( $val ) ) {
					$val = option_tree_stripslashes( $val );
				} else {
					$val = stripslashes( $val );
				}
			}
		} else {
			$input = stripslashes( $input );
		}
		return $input;
	}
}
