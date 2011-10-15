<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * General Functions
 *
 * @package     WordPress
 * @subpackage  OptionTree
 * @since       1.1.8
 * @author      Derek Herman
 */

/**
 * Recognized font styles
 *
 * Returns an array of all recognized font styles.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function recognized_font_styles() {
  return apply_filters( 'recognized_font_styles', array(
    'normal'  => 'Normal',
    'italic'  => 'Italic',
    'oblique' => 'Oblique',
    'inherit' => 'Inherit'
    ) );
}

/**
 * Recognized font weights
 *
 * Returns an array of all recognized font weights.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function recognized_font_weights() {
  return apply_filters( 'recognized_font_weights', array(
    'normal'    => 'Normal',
    'bold'      => 'Bold',
    'bolder'    => 'Bolder',
    'lighter'   => 'Lighter',
    '100'       => '100',
    '200'       => '200',
    '300'       => '300',
    '400'       => '400',
    '500'       => '500',
    '600'       => '600',
    '700'       => '700',
    '800'       => '800',
    '900'       => '900',
    'inherit'   => 'Inherit'
    ) );
}

/**
 * Recognized font variants
 *
 * Returns an array of all recognized font variants.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function recognized_font_variants() {
  return apply_filters( 'recognized_font_variants', array(
    'normal'      => 'Normal',
    'small-caps'  => 'Small Caps',
    'inherit'     => 'Inherit'
    ) );
}

/**
 * Recognized font families
 *
 * Returns an array of all recognized font families.
 * Keys are intended to be stored in the database
 * while values are ready for display in html.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function recognized_font_families() {
  return apply_filters( 'recognized_font_families', array(
    'arial'     => 'Arial',
    'georgia'   => 'Georgia',
    'helvetica' => 'Helvetica',
    'palatino'  => 'Palatino',
    'tahoma'    => 'Tahoma',
    'times'     => '"Times New Roman", sans-serif',
    'trebuchet' => 'Trebuchet',
    'verdana'   => 'Verdana'
    ) );
}

/**
 * Recognized background repeat
 *
 * Returns an array of all recognized background repeat values.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function recognized_background_repeat() {
  return apply_filters( 'recognized_background_repeat', array(
    'no-repeat' => 'No Repeat',
    'repeat' 		=> 'Repeat All',
    'repeat-x'  => 'Repeat Horizontally',
    'repeat-y' 	=> 'Repeat Vertically',
    'inherit'   => 'Inherit'
    ) );
}

/**
 * Recognized background attachment
 *
 * Returns an array of all recognized background attachment values.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function recognized_background_attachment() {
  return apply_filters( 'recognized_background_attachment', array(
    "fixed"   => "Fixed",
    "scroll"  => "Scroll",
    "inherit" => "Inherit"
    ) );
}

/**
 * Recognized background position
 *
 * Returns an array of all recognized background position values.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function recognized_background_position() {
  return apply_filters( 'recognized_background_position', array(
    "left top"      => "Left Top",
    "left center"   => "Left Center",
    "left bottom"   => "Left Bottom",
    "center top"    => "Center Top",
    "center center" => "Center Center",
    "center bottom" => "Center Bottom",
    "right top"     => "Right Top",
    "right center"  => "Right Center",
    "right bottom"  => "Right Bottom"
    ) );
}

/**
 * Measurement Units
 *
 * Returns an array of all available unit types.
 *
 * @uses      apply_filters()
 *
 * @access    public
 * @since     1.1.8
 *
 * @return    array
 */
function measurement_unit_types() {
  return apply_filters( 'measurement_unit_types', array(
  'px' => 'px',
  '%'  => '%',
  'em' => 'em',
  'pt' => 'pt'
  ) );
}

/**
 * Find CSS option type and add to style.css
 *
 * @since 1.1.8
 *
 * @return bool True on write success, false on failure.
 */
function option_tree_css_save() {
  global $wpdb;
  
  $options = $wpdb->get_results( "SELECT item_id FROM " . OT_TABLE_NAME . " WHERE `item_type` = 'css' ORDER BY `item_sort` ASC" );
  foreach ( $options as $option )
    option_tree_insert_css_with_markers( $option->item_id );
  
  return false;
}

/**
 * Inserts CSS with Markers
 *
 * Inserts CSS into a dynamic.css file, placing it between
 * BEGIN and END markers. Replaces existing marked info. Retains surrounding
 * data.
 *
 * @since 1.1.8
 *
 * @return bool True on write success, false on failure.
 */
function option_tree_insert_css_with_markers( $option = '' ) {
  /* No option defined */
  if ( !$option )
    return;
  
  /* path to the dynamic.css file */
  $filepath = get_stylesheet_directory().'/dynamic.css';
  
  /* allow filter on path */
  $filepath = apply_filters( 'css_option_file_path', $filepath, $option );
   
  /* Insert CSS into file */
  if ( ! file_exists( $filepath ) || is_writeable( $filepath ) ) {
    
    /* Get options & set CSS value */
    $options     = get_option('option_tree');
    $insertion   = option_tree_normalize_css( stripslashes( $options[$option] ) );
    $regex       = "/{{([a-zA-Z0-9\_\-\#\|\=]+)}}/";
    $marker      = $option;
  
    /* Match custom CSS */
    preg_match_all( $regex, $insertion, $matches );
    
    /* Loop through CSS */
    foreach( $matches[0] as $option ) {
      $the_option = str_replace( array('{{', '}}'), '', $option );
      $option_array = explode("|", $the_option );
      /* get array by key from key|value explode */
      if ( is_array( $option_array ) ) {
        $value = $options[$option_array[0]];
      /* get the whole array from $option param */
      } else {
        $value = $options[$option];
      }
      if ( is_array( $value ) ) {
        /* key|value explode didn't return a second value */
        if ( !isset($option_array[1]) ) {
          /* Measurement */
          if ( isset( $value[0] ) && isset( $value[1] ) ) {
            $value = $value[0].$value[1];
          /* typography */
          } else if ( isset( $value['font-color'] ) || isset( $value['font-style'] ) || isset( $value['font-variant'] ) || isset( $value['font-weight'] ) || isset( $value['font-size'] ) || isset( $value['font-family'] ) ) {
            $font = array();
            
            if ( ! empty( $value['font-color'] ) )
              $font[] = "font-color: " . $value['font-color'] . ";";

            foreach ( recognized_font_families() as $key => $v ) {
              if ( ! empty( $value['font-family'] ) && $key == $value['font-family'] )
                $font[] = "font-family: " . $v . ";";
            }
            
            if ( ! empty( $value['font-size'] ) )
              $font[] = "font-size: " . $value['font-size'] . ";";
            
            if ( ! empty( $value['font-style'] ) )
              $font[] = "font-style: " . $value['font-style'] . ";";
            
            if ( ! empty( $value['font-variant'] ) )
              $font[] = "font-variant: " . $value['font-variant'] . ";";
            
            if ( ! empty( $value['font-weight'] ) )
              $font[] = "font-weight: " . $value['font-weight'] . ";";
            
            if ( ! empty( $font ) )
                $value = implode( "\n", $font );
          /* background */
          } else if ( isset( $value['background-color'] ) || isset( $value['background-image'] ) ) {
            $bg = array();
            
            if ( ! empty( $value['background-color'] ) )
              $bg[] = $value['background-color'];
              
            if ( ! empty( $value['background-image'] ) )
              $bg[] = 'url("' . $value['background-image'] . '")';
              
            if ( ! empty( $value['background-repeat'] ) )
              $bg[] = $value['background-repeat'];
              
            if ( ! empty( $value['background-attachment'] ) )
              $bg[] = $value['background-attachment'];
              
            if ( ! empty( $value['background-position'] ) )
              $bg[] = $value['background-position'];

            if ( ! empty( $bg ) )
              $value = 'background: ' . implode( " ", $bg ) . ';';
          }
        /* key|value explode return a second value */
        } else {
          $value = $value[$option_array[1]];
        }
     	}
      $insertion = stripslashes( str_replace( $option, $value, $insertion ) );
    }
	
    /* file doesn't exist */
    if ( ! file_exists( $filepath ) ) {
      $markerdata = '';
    /* file exist, create array from the lines of code */
    } else {
      $markerdata = explode( "\n", implode( '', file( $filepath ) ) );
    }
    
    /* can't write to the file return false */
    if ( !$f = @fopen( $filepath, 'w' ) )
      return false;
    
    $foundit = false;
    
    /* has array of lines */
    if ( $markerdata ) {
      $state = true;
      /* foreach line of code */
      foreach ( $markerdata as $n => $markerline ) {
        /* found begining of marker, set state to false  */
        if ( strpos( $markerline, '/* BEGIN ' . $marker . ' */' ) !== false )
          $state = false;
        /* state is true, rebuild css  */
        if ( $state ) {
          if ( $n + 1 < count( $markerdata ) )
            fwrite( $f, "{$markerline}\n" );
          else
            fwrite( $f, "{$markerline}" );
        }
        /* found end marker write code */
        if ( strpos( $markerline, '/* END ' . $marker . ' */' ) !== false ) {
          fwrite( $f, "/* BEGIN {$marker} */\n" );
          fwrite( $f, "{$insertion}\n" );
          fwrite( $f, "/* END {$marker} */\n" );
          $state = true;
          $foundit = true;
        }
      }
    }
    /* nothing inserted, write code. DO IT, DO IT! */
    if ( ! $foundit ) {
      fwrite( $f, "\n\n/* BEGIN {$marker} */\n" );
      fwrite( $f, "{$insertion}\n" );
      fwrite( $f, "/* END {$marker} */\n" );
    }
    /* close file */
    fclose( $f );
    return true;
  } else {
    return false;
  }
}

function option_tree_normalize_css( $s ) {
  // Normalize line endings
  // Convert all line-endings to UNIX format
  $s = str_replace( "\r\n", "\n", $s );
  $s = str_replace( "\r", "\n", $s );
  // Don't allow out-of-control blank lines
  $s = preg_replace( "/\n{2,}/", "\n\n", $s );
  return $s;
}