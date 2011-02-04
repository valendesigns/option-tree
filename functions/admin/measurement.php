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
            $units = array(
              'px' => 'px',
              '%'  => '%',
              'em' => 'em',
              'pt' => 'pt'
            );
            // filter the unit types
            $units = apply_filters( 'measurement_unit_types', $units );
            foreach ( $units as $unit ) {
              if ( isset( $measurement[1] ) && $measurement[1] == trim( $unit ) ) { 
                $selected = ' selected="selected"'; 
              } else {
                $selected = '';
              }
              echo '<option'.$selected.' value="'.trim( $unit ).'">&nbsp;'.trim( $unit ).'</option>';
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