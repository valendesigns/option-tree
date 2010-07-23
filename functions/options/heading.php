<?php
/**
 *
 * Heading Option
 *
 */
function option_tree_heading($value, $settings, $count) {
  echo ($count > 1) ? '</div>' : '';
  echo '<div id="option_'.$value->item_id.'" class="block">';
  echo '<h2>'.$value->item_title.'</h2>';
  echo '<input type="hidden" name="'.$value->item_id.'" value="'.$value->item_title.'" />';
}