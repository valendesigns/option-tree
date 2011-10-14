<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Export Table Data
 *
 * @access public
 * @since 1.0.0
 *
 * @param array $options
 * @param string $table_name
 *
 * @return file
 */
function option_tree_export_xml( $options, $table_name ) 
{
  global $wpdb;
  
  // create doctype
  $dom = new DomDocument("1.0");
  $dom->formatOutput = true;
  header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
  header("Pragma: no-cache ");
  header("Content-Type: text/plain");
  header('Content-Disposition: attachment; filename="theme-options-'.date("Y-m-d").'.xml"');
  // create root element
  $root = $dom->createElement($table_name);
  $root = $dom->appendChild($root);
  foreach ($options as $value) {
    // create root element
    $child = $dom->createElement('row');
    $child = $root->appendChild($child);
      // ID
      $item = $dom->createElement('id');
      $item = $child->appendChild($item);
      $text = $dom->createTextNode($value->id);
      $text = $item->appendChild($text);
      // Item ID
      $item = $dom->createElement('item_id');
      $item = $child->appendChild($item);
      $text = $dom->createTextNode($value->item_id);
      $text = $item->appendChild($text);
      // Item Title
      $item = $dom->createElement('item_title');
      $item = $child->appendChild($item);
      $text = $dom->createTextNode($value->item_title);
      $text = $item->appendChild($text);
      // Item Description
      $item = $dom->createElement('item_desc');
      $item = $child->appendChild($item);
      $text = $dom->createTextNode($value->item_desc);
      $text = $item->appendChild($text);
      // Item Type
      $item = $dom->createElement('item_type');
      $item = $child->appendChild($item);
      $text = $dom->createTextNode($value->item_type);
      $text = $item->appendChild($text);
      // Item Options
      $item = $dom->createElement('item_options');
      $item = $child->appendChild($item);
      $text = $dom->createTextNode($value->item_options);
      $text = $item->appendChild($text);
      // Item Sort
      $item = $dom->createElement('item_sort');
      $item = $child->appendChild($item);
      $text = $dom->createTextNode($value->item_sort);
      $text = $item->appendChild($text);
  }
  // save and display tree
  echo $dom->saveXML();
  die();
}