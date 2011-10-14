<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Select Option
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
function option_tree_select( $value, $settings, $int ) 
{ 
?>
  <div class="option option-select">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <?php $options_array = explode( ',', $value->item_options ); ?>
        <div class="select_wrapper">
          <select name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" class="select">
          <?php
          echo '<option value="">-- Choose One --</option>';
          foreach ( $options_array as $option ) {
            $selected = '';
            $value_pair = explode( '=', trim( $option ) );
            if ( isset( $value_pair[0] ) && isset( $value_pair[1] ) ) {
    	        echo '<option value="' . esc_attr( $value_pair[0] ) . '" ' . selected( $settings[$value->item_id], $value_pair[0], false ) . '>' . esc_html( $option ) . '</option>';
            } else {
    	        echo '<option value="' . esc_attr( trim( $option ) ) . '" ' . selected( $settings[$value->item_id], trim( $option ), false ) . '>' . esc_html( $option ) . '</option>';
            }
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