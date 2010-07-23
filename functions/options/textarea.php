<?php
/**
 *
 * Textarea Option
 *
 */
function option_tree_textarea($value, $settings) { ?>
  <div class="option option-textarea">
    <h3><?php echo $value->item_title; ?></h3>
    <div class="section">
      <div class="element">
        <textarea name="<?php echo $value->item_id; ?>" rows="8"><?php 
          if ($settings[$value->item_id]) {
            echo stripslashes($settings[$value->item_id]);
          } else if ($value->item_std && $settings[$value->item_id] != '') {
            echo stripslashes($value->item_std);
          } ?></textarea>
      </div>
      <div class="description">
        <?php echo $value->item_desc; ?>
      </div>
    </div>
  </div>
<?php
}