<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Typography Option
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
function option_tree_typography( $value, $settings, $int ) { ?>
  <div class="option option-font">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <script type="text/javascript">
        jQuery(document).ready(function($) {  
          $('#<?php echo $value->item_id; ?>-picker').ColorPicker({
            onSubmit: function(hsb, hex, rgb) {
            	$('#<?php echo $value->item_id; ?>-picker').val('#'+hex);
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
        <input type="text" name="<?php echo $value->item_id; ?>[font-color]" id="<?php echo $value->item_id; ?>-picker" value="<?php echo ( isset( $settings[$value->item_id]['font-color'] ) ) ? stripslashes( $settings[$value->item_id]['font-color'] ) : ''; ?>" class="cp_input" />
        <div id="cp_<?php echo $value->item_id; ?>" class="cp_box">
          <div style="background-color:<?php echo ( isset ( $settings[$value->item_id]['font-color'] ) ) ? $settings[$value->item_id]['font-color'] : '#ffffff'; ?>;<?php if ( isset( $settings[$value->item_id]['font-color'] ) ) { echo 'background-image:none;border-color:' . $settings[$value->item_id]['font-color'] . ';'; } ?>"> 
          </div>
        </div>
        <div class="select_wrapper typography-family">
          <select name="<?php echo $value->item_id; ?>[font-family]" class="select">
            <?php
            echo '<option value="">font-family</option>';
            foreach ( recognized_font_families() as $key => $family ) {
              echo '<option value="' . esc_attr( $key ) . '" ' . selected( $settings[$value->item_id]['font-family'], $key, false ) . '>' . esc_html( $family ) . '</option>';
            } 
            ?>
          </select>
        </div>
        <div class="select_wrapper typography-style" style="width:165px;">
          <select name="<?php echo $value->item_id; ?>[font-style]" class="select">
            <?php
            echo '<option value="">font-style</option>';
            foreach ( recognized_font_styles() as $key => $style ) {
              echo '<option value="' . esc_attr( $key ) . '" ' . selected( $settings[$value->item_id]['font-style'], $key, false ) . '>' . esc_html( $style ) . '</option>';
            } 
            ?>
          </select>
        </div>
        <div class="select_wrapper typography-variant" style="width:166px;margin-left:10px;">
          <select name="<?php echo $value->item_id; ?>[font-variant]" class="select">
            <?php
            echo '<option value="">font-variant</option>';
            foreach ( recognized_font_variants() as $key => $variant ) {
              echo '<option value="' . esc_attr( $key ) . '" ' . selected( $settings[$value->item_id]['font-variant'], $key, false ) . '>' . esc_html( $variant ) . '</option>';
            } 
            ?>
          </select>
        </div>
        <div class="select_wrapper typography-weight" style="width:165px;">
          <select name="<?php echo $value->item_id; ?>[font-weight]" class="select">
            <?php
            echo '<option value="">font-weight</option>';
            foreach ( recognized_font_weights() as $key => $weight ) {
              echo '<option value="' . esc_attr( $key ) . '" ' . selected( $settings[$value->item_id]['font-weight'], $key, false ) . '>' . esc_html( $weight ) . '</option>';
            } 
            ?>
          </select>
        </div>
        <div class="select_wrapper typography-size" style="width:166px;margin-left:10px;">
          <select name="<?php echo $value->item_id; ?>[font-size]" class="select">
            <?php
            echo '<option value="">font-size</option>';
            for ($i = 8; $i <= 72; $i++) { 
				      $size = $i . 'px';
              echo '<option value="' . esc_attr( $size ) . '" ' . selected( $settings[$value->item_id]['font-size'], $size, false ) . '>' . esc_html( $size ) . '</option>';
            } 
            ?>
          </select>
        </div>
      </div>
      <div class="description">
        <?php echo htmlspecialchars_decode( $value->item_desc ); ?>
      </div>
    </div>
  </div>
<?php
}