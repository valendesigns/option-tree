<?php
/**
 *
 * Select Option
 *
 */
function option_tree_select($value, $settings) { ?>
  <div class="option option-select">
    <h3><?php echo $value->item_title; ?></h3>
    <div class="section">
      <div class="element">
        <?php $options_array = explode(',', $value->item_options); ?>
        <div class="select_wrapper">
          <select name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" class="select">
          <?php foreach ($options_array as $option) { ?>
            <option<?php if ($settings[$value->item_id] == trim($option)) { echo ' selected="selected"'; } else if ($value->item_std == trim($option) && !$settings[$value->item_id]) { echo ' selected="selected"'; } ?>><?php echo trim($option); ?></option>
          <?php } ?>
          </select>
        </div>
      </div>
      <div class="description">
        <?php echo $value->item_desc; ?>
      </div>
    </div>
  </div>
<?php
}