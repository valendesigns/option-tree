<?php
/**
 *
 * Input Option
 *
 */
function option_tree_input($value, $settings) { ?>
  <div class="option option-input">
    <h3><?php echo $value->item_title; ?></h3>
    <div class="section">
      <div class="element">
        <input type="text" name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" value="<?php if ($settings[$value->item_id]) { echo htmlspecialchars(stripslashes($settings[$value->item_id]), ENT_QUOTES); } else if ($value->item_std && $settings[$value->item_id] != '') { echo stripslashes($value->item_std); } ?>" />
      </div>
      <div class="description">
        <?php echo $value->item_desc; ?>
      </div>
    </div>
  </div>
<?php
}