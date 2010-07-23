<?php
/**
 *
 * Includes Functions
 *
 */
if (is_admin()) {

  // Admin Functions
  include(THIS_PLUGIN_DIR.'/functions/export.php');
  include(THIS_PLUGIN_DIR.'/functions/simplexml.php');
  include(THIS_PLUGIN_DIR.'/functions/get-option-themes.php');
  
  include(THIS_PLUGIN_DIR.'/functions/options/heading.php');
  include(THIS_PLUGIN_DIR.'/functions/options/input.php');
  include(THIS_PLUGIN_DIR.'/functions/options/checkbox.php');
  include(THIS_PLUGIN_DIR.'/functions/options/radio.php');
  include(THIS_PLUGIN_DIR.'/functions/options/select.php');
  include(THIS_PLUGIN_DIR.'/functions/options/textarea.php');
  include(THIS_PLUGIN_DIR.'/functions/options/upload.php');
  include(THIS_PLUGIN_DIR.'/functions/options/colorpicker.php');
  include(THIS_PLUGIN_DIR.'/functions/options/textblock.php');
  
} else if (!is_admin()) {
  
  // Theme functions
  include(THIS_PLUGIN_DIR.'/functions/get-option-tree.php');
  
}