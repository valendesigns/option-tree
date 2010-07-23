<?php
/**
 *
 * Checkbox Option
 *
 */
function option_tree_checkbox($value, $settings) { ?>
  <div class="option option-checbox">
    <h3><?php echo $value->item_title; ?></h3>
    <div class="section">
      <div class="element">
        <?php
        $checked = '';
        $options_array = explode(',', $value->item_options); 
	      $ch_values = explode(',',$settings[$value->item_id]);
	      foreach ($options_array as $option) { 
	        if (in_array(trim($option), $ch_values)) { 
            $checked = ' checked="checked"'; 
          } else if ($value->item_std == trim($option) && (!$options_array || !$settings[$value->item_id])) {
            $checked = ' checked="checked"';
          } else {
            $checked = '';
          }
	        echo '<div class="input_wrap"><input name="checkboxes['.$value->item_id.'][]" type="checkbox" value="'.trim($option).'"'.$checked.' /><label>'.trim($option).'</label></div>';
     		}
        ?>
      </div>
      <div class="description">
        <?php echo $value->item_desc; ?>
      </div>
    </div>
  </div>
<?php
}