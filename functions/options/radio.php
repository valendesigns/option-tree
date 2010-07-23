<?php
/**
 *
 * Radio Option
 *
 */
function option_tree_radio($value, $settings) { ?>
  <div class="option option-radio">
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
          } else if ($value->item_std == trim($option)) {
            $checked = ' checked="checked"';
          } else {
            $checked = '';
          }
	        echo '<div class="input_wrap"><input name="radios['.$value->item_id.'][]" type="radio" value="'.trim($option).'"'.$checked.' /><label>'.trim($option).'</label></div>';
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