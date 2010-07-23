<?php include(THIS_PLUGIN_DIR.'/core/header.php'); ?>
  
  <div id="content_wrap">
  
    <form method="post" id="theme-options">
      
      <div class="info top-info">
        <input type="submit" value="<?php _e('Save All Changes') ?>" class="button-framework" name="submit"/>
      </div>
      
      <div class="ajax-message<?php if (isset($_GET['saved']) || isset($_GET['reset']) || isset($_GET['data'])) { echo ' show'; } ?>">
        <?php if(isset($_GET['saved'])) { echo '<div class="message">Theme Options Updated</div>'; } ?>
        <?php if(isset($_GET['reset'])) { echo '<div class="message">Theme Options Reset</div>'; } ?>
        <?php if(isset($_GET['data'])) { echo '<div class="message">Theme Options Imported</div>'; } ?>
      </div>
      
      <div id="content">
        <div id="options_tabs">
          <ul class="options_tabs"><?php 
          foreach ($option_array as $value) { 
            if ($value->item_type == 'heading') {
              echo '<li><a href="#option_'.$value->item_id.'">'.htmlspecialchars(stripslashes($value->item_title)).'</a><span></span></li>';
            } 
          } ?>
          </ul>
          <?php
          // Set Count        
          $count = 0;
          // Loop Options         
          foreach ($option_array as $value) {
            // Set Type
            $type = $value->item_type;
            // Increment Count
            $count++;       
            if ($type == 'heading') {
              option_tree_heading($value, $settings, $count);
              $heading = true;
            } else if ($type == 'input') {
              option_tree_input($value, $settings);
            } else if ($type == 'checkbox') {
              option_tree_checkbox($value, $settings);
            } else if ($type == 'radio') {
              option_tree_radio($value, $settings);
            } else if ($type == 'select') {
              option_tree_select($value, $settings);
            } else if ($type == 'textarea') {
              option_tree_textarea($value, $settings);
            } else if ($type == 'upload') {
              option_tree_upload($value, $settings);
            } else if ($type == 'colorpicker') {
              option_tree_colorpicker($value, $settings);
            } else if ($type == 'textblock') {
              option_tree_textblock($value, $settings);
            }  
          }
          // Close First Heading
          if ($heading) {
            echo '</div>';
          }
          ?>
          <br class="clear" />
        </div>
      </div>
      
      <div class="info bottom">
        <input type="submit" value="<?php _e('Reset Options') ?>" class="button-framework reset" name="reset"/>
        <input type="submit" value="<?php _e('Save All Changes') ?>" class="button-framework" name="submit"/>
      </div>
      
      <input type="hidden" name="action" value="save" />
      
    </form>
    
  </div>

<?php include(THIS_PLUGIN_DIR.'/core/footer.php'); ?>