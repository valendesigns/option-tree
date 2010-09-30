<?php if (!defined('OT_VERSION')) exit('No direct script access allowed'); ?>

<div id="framework_wrap" class="wrap">

	<div id="header">
    <h1>Option Tree</h1>
    <span class="icon">&nbsp;</span>
    <div class="version">
      <?php echo OT_VERSION; ?>
    </div>
	</div>
  
  <div id="content_wrap">
  
    <div class="info top-info"></div>

    <div id="content">
      <div id="options_tabs" class="docs">
      
        <ul class="options_tabs">
          <li><a href="#general">General</a><span></span></li>
          <li><a href="#theme_options">Theme Options</a><span></span></li>
          <li><a href="#settings">Settings</a><span></span></li>
        </ul>
        
        <div id="general" class="block">
          <h2>General</h2>
          <h3>Working with Data</h3>
          
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
          
          <p><code>get_option_tree( $item_id, $option_tree, $echo, $array, $array_id );</code></p>
          
          <ul class="doc_list">
            <li><code>$item_id</code> is the required option key you are requesting a value for.</li>
            <li><code>$option_tree</code> is a way to cut down on queries. Add <code>$option_tree = get_option( 'option_tree' );</code> to the top of your header to call the option_tree array once instead of each $item_id request. Defaults to false and is not required.</li>
            <li><code>$echo</code> is true or false and will either echo or return respectively. Default is false (return).</li>
            <li><code>$array</code> boolean (true/false) value. Default is false and the only option you would use this with is group of multiple checkboxes.</li>
            <li><code>$array_id</code> is numeric. Defaults to 0 and is only used if $array is true. This tells the function which array key you are after (remember an array starts at 0). Also, if you set to -1 and set $echo to false you can then set a variable to the value of the array and loop through it on your own.</li>
          </ul>

        </div>
        
        <div id="theme_options" class="block">
          <h2>Theme Options</h2>
          <h3>Blah</h3>
        </div>
        
        <div id="settings" class="block">
          <h2>Settings</h2>
          <h3>Option Types</h3>
        </div>
        
        <br class="clear" />
      </div>
    </div>
    <div class="info bottom"></div>   
  </div>

</div>
<!-- [END] framework_wrap -->