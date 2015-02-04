<?php if ( ! defined( 'OT_VERSION' ) ) exit( 'No direct script access allowed' );
/**
 * OptionTree functions
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2013, Derek Herman
 * @since     2.0
 */

/**
 * Theme Options ID
 *
 * @return    string
 *
 * @access    public
 * @since     2.3.0
 */
if ( ! function_exists( 'ot_options_id' ) ) {

  function ot_options_id() {
    
    return apply_filters( 'ot_options_id', 'option_tree' );
    
  }
  
}

/**
 * Theme Settings ID
 *
 * @return    string
 *
 * @access    public
 * @since     2.3.0
 */
if ( ! function_exists( 'ot_settings_id' ) ) {

  function ot_settings_id() {
    
    return apply_filters( 'ot_settings_id', 'option_tree_settings' );
    
  }
  
}

/**
 * Theme Layouts ID
 *
 * @return    string
 *
 * @access    public
 * @since     2.3.0
 */
if ( ! function_exists( 'ot_layouts_id' ) ) {

  function ot_layouts_id() {
    
    return apply_filters( 'ot_layouts_id', 'option_tree_layouts' );
    
  }
  
}

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
    $options = get_option( ot_options_id() );
    
    /* look for the saved value */
    if ( isset( $options[$option_id] ) && '' != $options[$option_id] ) {
        
      return ot_wpml_filter( $options, $option_id );
      
    }
    
    return $default;
    
  }
  
}

/**
 * Echo Option. (via Github @joshlevinson)
 *
 * Helper function to echo the option value.
 * If no value has been saved, it echos $default.
 *
 * @param     string    The option ID.
 * @param     string    The default option value.
 * @return    mixed
 *
 * @access    public
 * @since     2.2.0
 */
if ( ! function_exists( 'ot_echo_option' ) ) {
  
  function ot_echo_option( $option_id, $default = '' ) {
    
    echo ot_get_option( $option_id, $default );
  
  }
  
}

/**
 * Filter the return values through WPML
 *
 * @param     array     $options The current options    
 * @param     string    $option_id The option ID
 * @return    mixed
 *
 * @access    public
 * @since     2.1
 */
if ( ! function_exists( 'ot_wpml_filter' ) ) {

  function ot_wpml_filter( $options, $option_id ) {
      
    // Return translated strings using WMPL
    if ( function_exists('icl_t') ) {
      
      $settings = get_option( ot_settings_id() );
      
      if ( isset( $settings['settings'] ) ) {
      
        foreach( $settings['settings'] as $setting ) {
          
          // List Item & Slider
          if ( $option_id == $setting['id'] && in_array( $setting['type'], array( 'list-item', 'slider' ) ) ) {
          
            foreach( $options[$option_id] as $key => $value ) {
          
              foreach( $value as $ckey => $cvalue ) {
                
                $id = $option_id . '_' . $ckey . '_' . $key;
                $_string = icl_t( 'Theme Options', $id, $cvalue );
                
                if ( ! empty( $_string ) ) {
                
                  $options[$option_id][$key][$ckey] = $_string;
                  
                }
                
              }
            
            }
          
          // List Item & Slider
          } else if ( $option_id == $setting['id'] && $setting['type'] == 'social-links' ) {
          
            foreach( $options[$option_id] as $key => $value ) {
          
              foreach( $value as $ckey => $cvalue ) {
                
                $id = $option_id . '_' . $ckey . '_' . $key;
                $_string = icl_t( 'Theme Options', $id, $cvalue );
                
                if ( ! empty( $_string ) ) {
                
                  $options[$option_id][$key][$ckey] = $_string;
                  
                }
                
              }
            
            }
          
          // All other acceptable option types
          } else if ( $option_id == $setting['id'] && in_array( $setting['type'], apply_filters( 'ot_wpml_option_types', array( 'text', 'textarea', 'textarea-simple' ) ) ) ) {
          
            $_string = icl_t( 'Theme Options', $option_id, $options[$option_id] );
            
            if ( ! empty( $_string ) ) {
            
              $options[$option_id] = $_string;
              
            }
            
          }
          
        }
      
      }
    
    }
    
    return $options[$option_id];
  
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
            
            $css = set_url_scheme( WP_CONTENT_URL ) . $parts[1];
            
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

/**
 * Enqueue the Google Fonts CSS.
 *
 * @return    void
 *
 * @access    public
 * @since     2.5.0
 */
if ( ! function_exists( 'ot_load_google_fonts_css' ) ) {

  function ot_load_google_fonts_css() {

    /* don't load in the admin */
    if ( is_admin() )
      return;

    $ot_google_fonts      = get_theme_mod( 'ot_google_fonts', array() );
    $ot_set_google_fonts  = get_theme_mod( 'ot_set_google_fonts', array() );
    $paths = array();

    if ( ! empty( $ot_set_google_fonts ) ) {

      foreach( $ot_set_google_fonts as $id => $fonts ) {

        foreach( $fonts as $font ) {

          $path = '';

          if ( ! isset( $ot_google_fonts[$font['family']]['family'] ) ) {
            continue;
          }

          // Add variants
          if ( ! empty( $font['variants'] ) && is_array( $font['variants'] ) ) {
            $path.= implode( ',', $font['variants'] );
          }

          // Add subsets
          if ( ! empty( $font['subsets'] ) && is_array( $font['subsets'] ) ) {
            $add_subsets = false;
            foreach( $font['subsets'] as $subset ) {
              if ( $subset !== 'latin' ) {
                $add_subsets = true;
              }
            }
            if ( $add_subsets === true ) {
              $path.= '&subset=' . implode( ',', $font['subsets'] );
            }
          }

          // Build actual path
          if ( $path ) {
            $paths[] = str_replace( ' ', '+', $ot_google_fonts[$font['family']]['family'] ) . ':' . $path;
          }

        }

      }

    }

    if ( ! empty( $paths ) ) {
      wp_enqueue_style( 'ot-google-fonts', esc_url( '//fonts.googleapis.com/css?family=' . implode( '|', $paths ) ), false, null );
    }

  }

}

/**
 * Registers the Theme Option page link for the admin bar.
 *
 * @uses      ot_register_settings()
 *
 * @return    void
 *
 * @access    public
 * @since     2.1
 */
if ( ! function_exists( 'ot_register_theme_options_admin_bar_menu' ) ) {

  function ot_register_theme_options_admin_bar_menu( $wp_admin_bar ) {
    
    if ( ! current_user_can( apply_filters( 'ot_theme_options_capability', 'edit_theme_options' ) ) || ! is_admin_bar_showing() )
      return;
    
    $wp_admin_bar->add_node( array(
      'parent'  => 'appearance',
      'id'      => apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ),
      'title'   => apply_filters( 'ot_theme_options_page_title', __( 'Theme Options', 'option-tree' ) ),
      'href'    => admin_url( apply_filters( 'ot_theme_options_parent_slug', 'themes.php' ) . '?page=' . apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ) )
    ) );
    
  }
  
}

/* End of file ot-functions.php */
/* Location: ./includes/ot-functions.php */