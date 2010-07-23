<?php include(THIS_PLUGIN_DIR.'/core/header.php'); ?>
  
  <div id="content_wrap">
  
    <div class="info top-info">
    </div>
    
    <div id="content">
      
      <div id="options_tabs">
        <ul class="options_tabs">
          <li><a href="#wp_themes">WordPress Themes</a><span></span></li>
          <li><a href="#html_templates">HTML Templates</a><span></span></li>
          <li><a href="#psd_templates">PSD Templates</a><span></span></li>
        </ul>
        
        <div id="wp_themes" class="block">
        
          <h2>WordPress Themes</h2>
          <ul class="themes">
            <?php
            $itemsArray = get_option_themes('wordpress');
      			$count = 1;
      			foreach($itemsArray as $value) {
      				if($count <= 20) {
      					echo '<li><a href="'.$value['url'].'" class="api_item"><img src="'.$value['thumbnail'].'" alt="'.$value['item'].'" /><span>'.$value['item'].' <strong>$'.$value['cost'].'</strong></span></a></li>';
      					$count++;
      				} else {
      					break;
      				}
      			}
        		?>
      		</ul>
      		
  		  </div>
  		  
  		  <div id="html_templates" class="block">
        
          <h2>HTML Templates</h2>
          <ul class="themes">
            <?php
            $itemsArray = get_option_themes('site-templates');
      			$count = 1;
      			foreach($itemsArray as $value) {
      				if($count <= 20) {
      					echo '<li><a href="'.$value['url'].'" class="api_item"><img src="'.$value['thumbnail'].'" alt="'.$value['item'].'" /><span>'.$value['item'].' <strong>$'.$value['cost'].'</strong></span></a></li>';
      					$count++;
      				} else {
      					break;
      				}
      			}
        		?>
      		</ul>
      		
  		  </div>
  		  
  		  <div id="psd_templates" class="block">
        
          <h2>PSD Templates</h2>
          <ul class="themes">
            <?php
            $itemsArray = get_option_themes('psd-templates');
      			$count = 1;
      			foreach($itemsArray as $value) {
      				if($count <= 20) {
      					echo '<li><a href="'.$value['url'].'" class="api_item"><img src="'.$value['thumbnail'].'" alt="'.$value['item'].'" /><span>'.$value['item'].' <strong>$'.$value['cost'].'</strong></span></a></li>';
      					$count++;
      				} else {
      					break;
      				}
      			}
        		?>
      		</ul>
      		
  		  </div>
  		  
  		  <br class="clear" />
  		</div>
  		
    </div>
    <div class="info bottom">
    </div>   
  </div>

<?php include(THIS_PLUGIN_DIR.'/core/footer.php'); ?>