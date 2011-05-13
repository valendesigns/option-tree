<?php if (!defined('OT_VERSION')) exit('No direct script access allowed'); ?>

<div id="framework_wrap" class="wrap">
	
	<div id="header">
    <h1>OptionTree</h1>
    <span class="icon">&nbsp;</span>
    <div class="version">
      <?php echo OT_VERSION; ?>
    </div>
	</div>

  <div id="content_wrap">
  
    <form method="post" id="the-theme-options">
      
      <div class="info top-info">
        
        <input type="submit" value="<?php _e('Save All Changes') ?>" class="button-framework save-options" name="submit" />
        
        <?php if ( $this->has_xml && $this->show_docs == false ) { ?>
        <input type="submit" value="<?php _e('Reload XML') ?>" class="button-framework reload-options" name="reload" style="margin-right:10px;" />
        <?php } ?>
        <?php
        if ( is_array( $layouts ) && !empty($layouts) ) 
        {
          echo '<div class="select-layout">';
          echo '<select name="active_theme_layout" id="active_theme_layout">';
          echo '<option value="">-- Choose One --</option>';

          $active_layout = $layouts['active_layout'];
          foreach( $layouts as $key => $v ) 
          { 
            if ( $key == 'active_layout')
              continue;
              
            $selected = '';
  	        if ( $active_layout == trim( $key ) ) 
              $selected = ' selected="selected"'; 

  	        echo '<option'.$selected.'>'.trim( $key ).'</option>';
       		}
       		echo '</select>';
       		?>
       		<input type="submit" value="<?php _e('Activate Layout') ?>" class="button-framework user-activate-layout" name="user-activate-layout" style="margin-right:10px;" />
       		<?php
       		echo '</div>';
     		}
        ?>
        
      </div>
      
      <div class="ajax-message<?php if ( isset( $message ) || isset($_GET['updated']) || isset($_GET['layout']) ) { echo ' show'; } ?>">
        <?php if (isset($_GET['updated'])) { echo '<div class="message"><span>&nbsp;</span>Theme Options were updated.</div>'; } ?>
        <?php if (isset($_GET['layout'])) { echo '<div class="message"><span>&nbsp;</span>Your Layout has been activated.</div>'; } ?>
        <?php if ( isset( $message ) ) { echo $message; } ?>
      </div>
      
      <div id="content">
      
        <div id="options_tabs">
        
          <ul class="options_tabs">
            <?php 
            foreach ( $ot_array as $value ) 
            { 
              if ( $value->item_type == 'heading' ) 
              {
                echo '<li><a href="#option_'.$value->item_id.'">' . htmlspecialchars_decode( $value->item_title ).'</a><span></span></li>';
              } 
            } 
            ?>
          </ul>
          
            <?php
            // set count        
            $count = 0;
            // loop options & load corresponding function   
            foreach ( $ot_array as $value ) 
            {
              $count++;
              if ( $value->item_type == 'upload' || $value->item_type == 'slider' ) 
              {
                $int = $post_id;
              }
              else if ( $value->item_type == 'textarea' )
              {
                $int = ( is_numeric( trim( $value->item_options ) ) ) ? trim( $value->item_options ) : 8;
              }
              else
              {
                $int = $count;
              }
              call_user_func_array( 'option_tree_' . $value->item_type, array( $value, $settings, $int ) );
            }
            // close heading
            echo '</div>';
            ?>
            
          <br class="clear" />
          
        </div>
        
      </div>
      
      <div class="info bottom">
      
        <input type="submit" value="<?php _e('Reset Options') ?>" class="button-framework reset" name="reset"/>
        <input type="submit" value="<?php _e('Save All Changes') ?>" class="button-framework save-options" name="submit"/>
        
      </div>
      
      <?php wp_nonce_field( '_theme_options', '_ajax_nonce', false ); ?>
      
    </form>
    
  </div>

</div>
<!-- [END] framework_wrap -->