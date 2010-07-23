<?php
/**
 *
 * Add SimpleXML Class if running PHP4
 *
 */
if(!function_exists("simplexml_load_file")){
  require_once(THIS_PLUGIN_DIR."/classes/simplexml.class.php");
  function simplexml_load_file($file){
    $sx = new simplexml;
    return $sx->xml_load_file($file);
  }
}