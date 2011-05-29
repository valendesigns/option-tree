<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Measurement Option
 *
 * @access public
 * @since 1.1.2
 * @contributors valendesigns & youngmicroserf
 *
 * @param array $value
 * @param array $settings
 * @param int $int
 *
 * @return string
 */
function option_tree_measurement( $value, $settings, $int ) { ?>
  <div class="option option-measurement">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <?php        
        if ( isset( $settings[$value->item_id] ) )
          $measurement = $settings[$value->item_id]; ?>
        <input type="text" name="<?php echo $value->item_id; ?>[0]" value="<?php if ( isset( $measurement[0] ) ) { echo htmlspecialchars( stripslashes( $measurement[0] ), ENT_QUOTES); } ?>" class="measurement" />

        <div class="select_wrapper measurement">
          <select name="<?php echo $value->item_id; ?>[1]" class="select">
            <?php
            echo '<option value="">&nbsp;-- </option>';
            foreach ( measurement_unit_types() as $unit ) {
              echo '<option value="' . esc_attr( trim( $unit ) ) . '" ' . selected( $measurement[1], trim( $unit ), false ) . '>' . esc_html( $unit ) . '</option>';
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