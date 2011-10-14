<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Upload Option
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
function option_tree_upload( $value, $settings, $int ) { ?>
  <div class="option option-upload">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <input type="text" name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" value="<?php if ( isset( $settings[$value->item_id] ) ) { echo $settings[$value->item_id]; } ?>" class="upload<?php if ( isset( $settings[$value->item_id] ) ) { echo ' has-file'; } ?>" />
        <input id="upload_<?php echo $value->item_id; ?>" class="upload_button" type="button" value="Upload" rel="<?php echo $int; ?>" />
        <?php if ( is_array( @getimagesize( $settings[$value->item_id] ) ) ) { ?>
        <div class="screenshot" id="<?php echo $value->item_id; ?>_image">
          <?php 
          if ( isset( $settings[$value->item_id] ) && $settings[$value->item_id] != '' ) {
            $remove = '<a href="javascript:(void);" class="remove">Remove</a>';
            $image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $settings[$value->item_id] );
            if ( $image ) {
              echo '<img src="'.$settings[$value->item_id].'" alt="" />'.$remove.'';
            } else {
              $parts = explode( "/", $settings[$value->item_id] );
              for( $i = 0; $i < sizeof($parts); ++$i ) {
                $title = $parts[$i];
              }
              echo '<div class="no_image"><a href="'.$settings[$value->item_id].'">'.$title.'</a>'.$remove.'</div>';
            }
          }
          ?>
        </div>
        <?php } ?>
      </div>
      <div class="description">
        <?php echo htmlspecialchars_decode( $value->item_desc ); ?>
      </div>
    </div>
  </div>
<?php
}