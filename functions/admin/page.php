<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Page Option
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
function option_tree_page( $value, $settings, $int ) 
{ 
?>
  <div class="option option-select">
    <h3><?php echo htmlspecialchars_decode( $value->item_title ); ?></h3>
    <div class="section">
      <div class="element">
        <div class="select_wrapper">
          <select name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" class="select">
          <?php
       		$pages = &get_pages();
       		if ( $pages )
       		{
       	    echo '<option value="">-- Choose One --</option>';
            foreach ( $pages as $page ) 
            {
              $selected = '';
    	        if ( isset( $settings[$value->item_id] ) && $settings[$value->item_id] == $page->ID ) 
    	        { 
                $selected = ' selected="selected"'; 
              }
            	echo '<option value="'.$page->ID.'"'.$selected.'>'.$page->post_title.'</option>';
            }
          } 
          else 
          {
            echo '<option value="0">No Pages Available</option>';
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
 * Pages Option
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
function option_tree_pages( $value, $settings, $int ) 
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
	      $pages = &get_pages();
       	if ( $pages )
       	{
       	  $count = 0;
  	      foreach ( $pages as $page ) 
  	      {
            $checked = '';
  	        if ( in_array( $page->ID, $ch_values ) ) 
  	        { 
              $checked = ' checked="checked"'; 
            }
  	        echo '<div class="input_wrap"><input name="'.$value->item_id.'['.$count.']" id="'.$value->item_id.'_'.$count.'" type="checkbox" value="'.$page->ID.'"'.$checked.' /><label for="'.$value->item_id.'_'.$count.'">'.$page->post_title.'</label></div>';
  	        $count++;
       		}
       	}
       	else
       	{
       	  echo '<p>No Pages Available</p>';
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