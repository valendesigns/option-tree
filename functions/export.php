<?php
/**
 *
 * Export Table Data
 *
 */
function option_tree_export_data() {
  global $wpdb, $table_name;

  $options = option_tree_data();
  
  // PHP5
  if (version_compare(PHP_VERSION, '5.0.0', '>=')) {
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
        // Item Standard
        $item = $dom->createElement('item_std');
        $item = $child->appendChild($item);
        $text = $dom->createTextNode($value->item_std);
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
  // PHP4
  } else {
    // create doctype
    $dom = domxml_new_doc("1.0");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache ");
    header("Content-Type: text/plain");
    header('Content-Disposition: attachment; filename="export.'.date("Y-m-d").'.xml"');
    // create root element
    $root = $dom->create_element($table_name);
    $root = $dom->append_child($root);
    foreach ($options as $value) {
      // create root element
      $child = $dom->create_element('row');
      $child = $root->append_child($child);
        // ID
        $item = $dom->create_element('id');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->id);
        $text = $item->append_child($text);
        // Item ID
        $item = $dom->create_element('item_id');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->item_id);
        $text = $item->append_child($text);
        // Item Title
        $item = $dom->create_element('item_title');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->item_title);
        $text = $item->append_child($text);
        // Item Description
        $item = $dom->create_element('item_desc');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->item_desc);
        $text = $item->append_child($text);
        // Item Type
        $item = $dom->create_element('item_type');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->item_type);
        $text = $item->append_child($text);
        // Item Standard
        $item = $dom->create_element('item_std');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->item_std);
        $text = $item->append_child($text);
        // Item Options
        $item = $dom->create_element('item_options');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->item_options);
        $text = $item->append_child($text);
        // Item Sort
        $item = $dom->create_element('item_sort');
        $item = $child->append_child($item);
        $text = $dom->create_text_node($value->item_sort);
        $text = $item->append_child($text);
    }
    // save and display tree
    $xml_string = $dom->dump_mem(true);
    echo $xml_string;
  }
  exit();
}