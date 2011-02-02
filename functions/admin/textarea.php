<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Textarea Option
 *
 * @access public
 * @since 1.0.0
 *
 * @param array $value
 * @param array $settings
 * @param int $int
 *
 * @return string
 */
function option_tree_textarea( $value, $settings, $int ) 
{ 
?>
  <div class="option option-textarea">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <textarea name="<?php echo $value->item_id; ?>" rows="<?php echo $int; ?>"><?php 
          if ( isset( $settings[$value->item_id] ) ) 
            echo stripslashes($settings[$value->item_id]);
          ?></textarea>
      </div>
      <div class="description">
        <?php echo htmlspecialchars_decode( $value->item_desc ); ?>
      </div>
    </div>
  </div>
<?php
}