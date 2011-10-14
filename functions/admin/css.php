<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * CSS Option
 *
 * @access public
 * @since 1.1.8
 *
 * @param array $value
 * @param array $settings
 * @param int $int
 *
 * @return string
 */
function option_tree_css( $value, $settings, $int ) { 
?>
  <div class="option option-css">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="css_block">
        <textarea name="<?php echo $value->item_id; ?>" rows="<?php echo $int; ?>"><?php 
          if ( isset( $settings[$value->item_id] ) ) 
            echo stripslashes($settings[$value->item_id]);
          ?></textarea>
      </div>
      <div class="text_block">
        <?php echo htmlspecialchars_decode( $value->item_desc ); ?>
      </div>
    </div>
  </div>
<?php
}