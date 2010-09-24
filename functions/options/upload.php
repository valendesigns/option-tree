<?php
/**
 *
 * Upload Option
 *
 */
function option_tree_upload($value, $settings) { ?>
  <div class="option option-upload">
    <h3><?php echo $value->item_title; ?></h3>
    <div class="section">
      <div class="element">
        <input type="text" name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" value="<?php if ($settings[$value->item_id]) { echo $settings[$value->item_id]; } else if ($value->item_std && $settings[$value->item_id] != '') { echo $value->item_std; } ?>" class="upload<?php if ($settings[$value->item_id]) { echo ' has-file'; } ?>" />
        <input id="upload_<?php echo $value->item_id; ?>" class="upload_button" type="button" value="Upload" rel="<?php echo get_option_ID('option-tree'); ?>" />
        <div class="screenshot" id="<?php echo $value->item_id; ?>_image">
          <?php if ($settings[$value->item_id]) { 
            $item = preg_match('/(^.*\.jpg|jpeg|png|gif|ico*)/i', $settings[$value->item_id]);
            $remove = '<a href="javascript:(void);" class="remove">Remove Image</a>';
            if ($item) {
              echo '<img src="'.$settings[$value->item_id].'" alt="" />'.$remove.'';
            } else {
              $parts = explode("/", $settings[$value->item_id]);
              for($i = 0; $i < sizeof($parts); ++$i) {
                $title = $parts[$i];
              }
              echo '<div class="no_image"><a href="'.$settings[$value->item_id].'">'.$title.'</a>'.$remove.'</div>';
            }
          } ?>
        </div>
      </div>
      <div class="description">
        <?php echo $value->item_desc; ?>
      </div>

    </div>
  </div>
<?php
}