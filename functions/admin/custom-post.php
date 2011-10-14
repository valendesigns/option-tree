<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Custom Post Option
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
function option_tree_custom_post( $value, $settings, $int ) 
{ 
?>
  <div class="option option-select">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <div class="select_wrapper">
          <select name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" class="select">
          <?php
       		$posts = &get_posts( array( 'post_type' => trim($value->item_options), 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
       		if ( $posts )
       		{
            echo '<option value="">-- Choose One --</option>';
            foreach ( $posts as $post ) 
            {
              $selected = '';
    	        if ( isset( $settings[$value->item_id] ) && $settings[$value->item_id] == $post->ID ) 
    	        { 
                $selected = ' selected="selected"'; 
              }
            	echo '<option value="'.$post->ID.'"'.$selected.'>'.$post->post_title.'</option>';
            }
          } 
          else 
          {
            echo '<option value="0">No Custom Posts Available</option>';
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
 * Custom Posts Option
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
function option_tree_custom_posts( $value, $settings, $int ) 
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
	      $posts = &get_posts( array( 'post_type' => $value->item_options, 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
       	if ( $posts )
       	{
       	  $count = 0;
  	      foreach ( $posts as $post ) 
  	      {
            $checked = '';
  	        if ( in_array( $post->ID, $ch_values ) ) 
  	        { 
              $checked = ' checked="checked"'; 
            }
  	        echo '<div class="input_wrap"><input name="'.$value->item_id.'['.$count.']" id="'.$value->item_id.'_'.$count.'" type="checkbox" value="'.$post->ID.'"'.$checked.' /><label for="'.$value->item_id.'_'.$count.'">'.$post->post_title.'</label></div>';
  	        $count++;
       		}
       	}
       	else
       	{
       	  echo '<p>No Custom Posts Available</p>';
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