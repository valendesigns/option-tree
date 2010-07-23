<?php
/**
 * 
 * Get Options Tree Data
 *
 */
function get_option_tree($item_id = false, $option_tree = false, $echo = false, $array = false, $array_id = 0) {
  
  // Load Saved Options
  if (!$option_tree) {
    $option_tree = get_option('option_tree');
  }
  
  // Set the item
  $content = $option_tree[$item_id];
  
  // Create an Array
  if ($array) {
    $content = explode(',', $content);
    if ($array_id >= 0) {
      $content = trim($content[$array_id]);
    }
  }
  
  // Return Content
  if ($echo) {
    echo $content;
  } else {
    return $content;
  }
  
}