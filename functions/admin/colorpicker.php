<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * ColorPicker Option
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
function option_tree_colorpicker( $value, $settings, $int ) 
{
?>
  <div class="option option-colorpicker">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <script type="text/javascript">
        jQuery(document).ready(function($) {  
          $('#<?php echo $value->item_id; ?>').ColorPicker({
            onSubmit: function(hsb, hex, rgb) {
            	$('#<?php echo $value->item_id; ?>').val('#'+hex);
            },
            onBeforeShow: function () {
            	$(this).ColorPickerSetColor(this.value);
            	return false;
            },
            onChange: function (hsb, hex, rgb) {
            	$('#cp_<?php echo $value->item_id; ?> div').css({'backgroundColor':'#'+hex, 'backgroundImage': 'none', 'borderColor':'#'+hex});
            	$('#cp_<?php echo $value->item_id; ?>').prev('input').attr('value', '#'+hex);
            }
          })	
          .bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
          });
        });
        </script>
        <input type="text" name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" value="<?php echo ( isset( $settings[$value->item_id] ) ) ? stripslashes( $settings[$value->item_id] ) : ''; ?>" class="cp_input" />
        <div id="cp_<?php echo $value->item_id; ?>" class="cp_box">
          <div style="background-color:<?php echo ( isset ($settings[$value->item_id] ) ) ? $settings[$value->item_id] : '#ffffff'; ?>;<?php if ( isset( $settings[$value->item_id] ) ) { echo 'background-image:none;border-color:' . $settings[$value->item_id] . ';'; } ?>"> 
          </div>
        </div> 
        <small>Click text box for color picker</small>
      </div>
      <div class="description">
        <?php echo htmlspecialchars_decode( $value->item_desc ); ?>
      </div>
    </div>
  </div>
<?php
}