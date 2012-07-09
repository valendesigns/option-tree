<?php if ( ! defined( 'OT_VERSION' ) ) exit( 'No direct script access allowed' );
/**
 * Compatibility Functions.
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2012, Derek Herman
 * @since     2.0
 */

/* add all the old filters */
add_filter( 'ot_option_types_array',              'compat_ot_option_types_array', 10, 1 );
add_filter( 'ot_recognized_font_styles',          'compat_ot_recognized_font_styles', 10, 2 );
add_filter( 'ot_recognized_font_weights',         'compat_ot_recognized_font_weights', 10, 2 );
add_filter( 'ot_recognized_font_variants',        'compat_ot_recognized_font_variants', 10, 2 );
add_filter( 'ot_recognized_font_families',        'compat_ot_recognized_font_families', 10, 2 );
add_filter( 'ot_recognized_background_repeat',    'compat_ot_recognized_background_repeat', 10, 2 );
add_filter( 'ot_recognized_background_position',  'compat_ot_recognized_background_position', 10, 2 );
add_filter( 'ot_measurement_unit_types',          'compat_ot_measurement_unit_types', 10, 2 );

/**
 * Filters the option types array.
 *
 * Allows the old 'option_tree_option_types' filter to 
 * change the new 'ot_option_types_array' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_option_types_array' ) ) {

  function compat_ot_option_types_array( $array ) {
  
    return apply_filters( 'option_tree_option_types', $array );
    
  }

}

/**
 * Filters the recognized font styles array.
 *
 * Allows the old 'recognized_font_styles' filter to 
 * change the new 'ot_recognized_font_styles' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_recognized_font_styles' ) ) {

  function compat_ot_recognized_font_styles( $array, $id ) {
  
    return apply_filters( 'recognized_font_styles', $array, $id );
    
  }
  
}

/**
 * Filters the recognized font weights array.
 *
 * Allows the old 'recognized_font_weights' filter to 
 * change the new 'ot_recognized_font_weights' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_recognized_font_weights' ) ) {

  function compat_ot_recognized_font_weights( $array, $id ) {
  
    return apply_filters( 'recognized_font_weights', $array, $id );
    
  }
  
}

/**
 * Filters the recognized font variants array.
 *
 * Allows the old 'recognized_font_variants' filter to 
 * change the new 'ot_recognized_font_variants' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_recognized_font_variants' ) ) {

  function compat_ot_recognized_font_variants( $array, $id ) {
  
    return apply_filters( 'recognized_font_variants', $array, $id );
    
  }
  
}

/**
 * Filters the recognized font families array.
 *
 * Allows the old 'recognized_font_families' filter to 
 * change the new 'ot_recognized_font_families' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_recognized_font_families' ) ) {

  function compat_ot_recognized_font_families( $array, $id ) {
  
    return apply_filters( 'recognized_font_families', $array, $id );
    
  }
  
}

/**
 * Filters the recognized background repeat array.
 *
 * Allows the old 'recognized_background_repeat' filter to 
 * change the new 'ot_recognized_background_repeat' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_recognized_background_repeat' ) ) {

  function compat_ot_recognized_background_repeat( $array, $id ) {
  
    return apply_filters( 'recognized_background_repeat', $array, $id );
    
  }
  
}

/**
 * Filters the recognized background position array.
 *
 * Allows the old 'recognized_background_position' filter to 
 * change the new 'ot_recognized_background_position' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_recognized_background_position' ) ) {

  function compat_ot_recognized_background_position( $array, $id ) {
  
    return apply_filters( 'recognized_background_position', $array, $id );
    
  }
  
}

/**
 * Filters the measurement unit types array.
 *
 * Allows the old 'measurement_unit_types' filter to 
 * change the new 'ot_measurement_unit_types' return value.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'compat_ot_measurement_unit_types' ) ) {

  function compat_ot_measurement_unit_types( $array, $id ) {
  
    return apply_filters( 'measurement_unit_types', $array, $id );
    
  }
  
}


/* End of file ot-functions-compat.php */
/* Location: ./includes/ot-functions-compat.php */