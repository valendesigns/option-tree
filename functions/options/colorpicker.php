<?php
/**
 *
 * ColorPicker Option
 *
 */
function option_tree_colorpicker($value, $settings) { ?>
  <div class="option option-colorpicker">
    <h3><?php echo $value->item_title; ?></h3>
    <div class="section">
      <div class="element">
        <script type="text/javascript">
        jQuery(document).ready(function() {  
          jQuery('#<?php echo $value->item_id; ?>').ColorPicker({
            onSubmit: function(hsb, hex, rgb) {
            	jQuery('#<?php echo $value->item_id; ?>').val('#'+hex);
            },
            onBeforeShow: function () {
            	jQuery(this).ColorPickerSetColor(this.value);
            	return false;
            },
            onChange: function (hsb, hex, rgb) {
            	jQuery('#cp_<?php echo $value->item_id; ?> div').css({'backgroundColor':'#'+hex, 'backgroundImage': 'none', 'borderColor':'#'+hex});
            	jQuery('#cp_<?php echo $value->item_id; ?>').prev('input').attr('value', '#'+hex);
            }
          })	
          .bind('keyup', function(){
            jQuery(this).ColorPickerSetColor(this.value);
          });
        });
        </script>
        <input type="text" name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" value="<?php echo ($settings[$value->item_id]) ? stripslashes($settings[$value->item_id]) : stripslashes($value->item_std); ?>" class="cp_input" />
        <div id="cp_<?php echo $value->item_id; ?>" class="cp_box">
          <div style="background-color:<?php echo ($settings[$value->item_id]) ? $settings[$value->item_id] : (($value->item_std) ? $value->item_std : '#ffffff'); ?>;<?php if ($settings[$value->item_id]) { echo 'background-image:none;border-color:'.$settings[$value->item_id].';'; } ?>"> 
          </div>
        </div> 
        <small>Click text box for color picker</small>
      </div>
      <div class="description">
        <?php echo $value->item_desc; ?>
      </div>
    </div>
  </div>
<?php
}