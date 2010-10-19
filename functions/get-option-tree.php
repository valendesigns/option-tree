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
 * @return mixed
 */
function get_option_tree( $item_id = '', $options = '', $echo = false, $is_array = false, $offset = -1) 
{  
  // load saved options
  if ( !$options )
    $options = get_option( 'option_tree' );
  
  // set the item
  if ( !isset( $options[$item_id] ) )
    return;
  
  // single item value  
  $content = $options[$item_id];
  
  // create an array of values
  if ( $is_array ) 
  {
    $content = explode( ',', $content );
    if ( is_numeric( $offset ) && $offset >= 0) 
    {
      $content = htmlspecialchars_decode( trim( $content[$offset] ) );
    }
  }
  
  // echo content
  if ($echo)
    echo $content;
  
  return $content;
}