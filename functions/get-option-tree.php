<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Displays or returns a value from the 'option_tree' array.
 *
 * @uses get_option()
 *
 * @access public
 * @since 1.0.0
 *
 * @param string $item_id
 * @param array $options
 * @param bool $echo
 * @param bool $is_array
 * @param int $offset
 *
 * @return mixed array or comma seperated lists of values
 */
function get_option_tree( $item_id = '', $options = '', $echo = false, $is_array = false, $offset = -1) {
  // load saved options
  if ( !$options )
    $options = get_option( 'option_tree' );
  
  // no value return
  if ( !isset( $options[$item_id] ) || empty( $options[$item_id] ) )
    return;
  
  // set content value & strip slashes
  $content = option_tree_stripslashes( $options[$item_id] );

  // is an array
  if ( $is_array == true ) {
    // saved as a comma seperated lists of values, explode into an array
    if ( !is_array( $content ) )
      $content = explode( ',', $content );

    // get an array value using an offset  
    if ( is_numeric( $offset ) && $offset >= 0 ) 
      $content = $content[$offset];
  
  // not an array
  } else if ( $is_array == false ) {
    // saved as array, implode and return a comma seperated lists of values
    if ( is_array( $content ) )
      $content = implode( ',', $content );
  }
  
  // echo content
  if ($echo)
    echo $content;
  
  return $content;
}

/**
 * Custom stripslashes from single value or array.
 *
 * @uses stripslashes()
 *
 * @access public
 * @since 1.1.3
 *
 * @param mixed $input
 *
 * @return mixed
 */
function option_tree_stripslashes( $input ) {
  if ( is_array( $input ) ) {
    foreach( $input as &$val ) {
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