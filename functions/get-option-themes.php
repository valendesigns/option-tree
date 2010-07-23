<?php
/**
 *
 * Get Themes
 *
 */
function get_option_themes($category='') {
  $json_url = "http://marketplace.envato.com/api/edge/new-files:themeforest,{$category}.json";
  $json_contents = @file_get_contents($json_url);
  if ($json_contents) {
    $json_data = json_decode($json_contents, true);
    return $json_data['new-files'];
  } else {
    return 'There was an error establishing a conection to the Marketplace API';
  }
}

/**
 *
 * Test for json_decode()
 *
 */
if (!function_exists('json_decode')) {
  include(THIS_PLUGIN_DIR.'/classes/JSON.php');
  function json_decode($data, $output_mode=false) {
    $param = $output_mode ? 16 : null;
    $json = new Services_JSON($param);
    return($json->decode($data));
  }
}