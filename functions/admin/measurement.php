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
  <div class="option option-valueunit">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <?php 
        if ( isset( $settings[$value->item_id] ) )
	      {
          $measurement = explode(',', $settings[$value->item_id] );
        }
        else
        {
          $measurement = array();
        }
        ?>
        <input type="text" name="measurement[<?php echo $value->item_id; ?>][]" value="<?php if ( isset( $measurement[0] ) ) { echo htmlspecialchars( stripslashes( $measurement[0] ), ENT_QUOTES); } ?>" class="measurement" />

        <div class="select_wrapper measurement">
          <select name="measurement[<?php echo $value->item_id; ?>][]" class="select">
            <?php
            echo '<option value=""></option>';
            $units = array(
              'px' => 'px',
              '%'  => '%',
              'em' => 'em',
              'pt' => 'pt'
            );

            foreach ( $units as $unit ) {
              $selected = '';
              if ( isset( $measurement[1] ) && $measurement[1] == trim( $unit ) ) { 
                $selected = ' selected="selected"'; 
              }
              echo '<option'.$selected.'>'.trim( $unit ).'</option>';
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