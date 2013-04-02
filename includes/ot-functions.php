<?php if ( ! defined( 'OT_VERSION' ) ) exit( 'No direct script access allowed' );
/**
 * OptionTree functions
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2012, Derek Herman
 * @since     2.0
 */
      
/**
 * Get Option.
 *
 * Helper function to return the option value.
 * If no value has been saved, it returns $default.
 *
 * @param     string    The option ID.
 * @param     string    The default option value.
 * @return    mixed
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_get_option' ) ) {

  function ot_get_option( $option_id, $default = '' ) {
    
    /* get the saved options */ 
    $options = get_option( 'option_tree' );
    
    /* look for the saved value */
    if ( isset( $options[$option_id] ) && '' != $options[$option_id] ) {
        
      return $options[$option_id];
      
    }
    
    return $default;
    
  }
  
}

/**
 * Enqueue the dynamic CSS.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_load_dynamic_css' ) ) {

  function ot_load_dynamic_css() {
    
    /* don't load in the admin */
    if ( is_admin() )
      return;
    
    /* grab a copy of the paths */
    $ot_css_file_paths = get_option( 'ot_css_file_paths', array() );
    
    if ( ! empty( $ot_css_file_paths ) ) {
      
      $last_css = '';
      
      /* loop through paths */
      foreach( $ot_css_file_paths as $key => $path ) {
        
        if ( '' != $path && file_exists( $path ) ) {
        
          $parts = explode( '/wp-content', $path );
          
          if ( isset( $parts[1] ) ) {
            
            $css = home_url( '/wp-content' . $parts[1] );
            
            if ( $last_css !== $css ) {
              
              /* enqueue filtered file */
              wp_enqueue_style( 'ot-dynamic-' . $key, $css, false, OT_VERSION );
              
              $last_css = $css;
              
            }
            
          }
      
        }
        
      }
    
    }
    
  }
  
}

/* End of file ot-functions.php */
/* Location: ./includes/ot-functions.php */