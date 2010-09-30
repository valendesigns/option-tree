<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Get & return and Options Tree data array or variable
 *
 * @uses get_option()
 *
 * @access public
 * @since 1.0.0
 *
 * @param string $item_id
 * @param array $option_tree
 * @param bool $echo
 * @param bool $array
 * @param int $array_id
 *
 * @return mixed
 */
function get_option_tree( $item_id = false, $option_tree = false, $echo = false, $array = false, $array_id = 0) 
{  
  // load saved options
  if ( !$option_tree )
    $option_tree = get_option( 'option_tree' );
  
  // set the item
  if ( !isset( $option_tree[$item_id] ) )
    return;
  
  // single item value  
  $content = $option_tree[$item_id];
  
  // create an array of values
  if ( $array ) 
  {
    $content = explode( ',', $content );
    if ( is_numeric( $array_id ) && $array_id >= 0) 
    {
      $content = htmlspecialchars_decode( trim( $content[$array_id] ) );
    }
  }
  
  // echo content
  if ($echo)
    echo $content;
  
  return $content;
}