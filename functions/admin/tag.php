<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Tag Option
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
function option_tree_tag( $value, $settings, $int ) 
{ 
?>
  <div class="option option-select">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <div class="select_wrapper">
          <select name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" class="select">
          <?php
       		$tags = &get_tags( array( 'hide_empty' => false ) );
       		if ( $tags )
       		{
            echo '<option value="">-- Choose One --</option>';
            foreach ( $tags as $tag ) 
            {
              $selected = '';
    	        if ( isset( $settings[$value->item_id] ) && $settings[$value->item_id] == $tag->term_id ) 
    	        { 
                $selected = ' selected="selected"'; 
              }
            	echo '<option value="'.$tag->term_id.'"'.$selected.'>'.$tag->name.'</option>';
            }
          } 
          else 
          {
            echo '<option value="0">No Tags Available</option>';
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

/**
 * Tags Option
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
function option_tree_tags( $value, $settings, $int ) 
{ 
?>
  <div class="option option-checbox">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <?php
        // check for settings item value 
	      if ( isset( $settings[$value->item_id] ) ) {
          $ch_values = (array) $settings[$value->item_id];
        } else {
          $ch_values = array();
        }
        // loop through tags
	      $tags = &get_tags( array( 'hide_empty' => false ) );
       	if ( $tags )
       	{
       	  $count = 0;
  	      foreach ( $tags as $tag ) 
  	      {
            $checked = '';
  	        if ( in_array( $tag->term_id, $ch_values ) ) 
  	        { 
              $checked = ' checked="checked"'; 
            }
  	        echo '<div class="input_wrap"><input name="'.$value->item_id.'['.$count.']" id="'.$value->item_id.'_'.$count.'" type="checkbox" value="'.$tag->term_id.'"'.$checked.' /><label for="'.$value->item_id.'_'.$count.'">'.$tag->name.'</label></div>';
  	        $count++;
       		}
       	}
       	else
       	{
       	  echo '<p>No Tags Available</p>';
       	}
        ?>
      </div>
      <div class="description">
        <?php echo htmlspecialchars_decode( $value->item_desc ); ?>
      </div>
    </div>
  </div>
<?php
}