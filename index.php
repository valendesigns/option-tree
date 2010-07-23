<?php
/*
Plugin Name: OptionTree
Plugin URI: http://optiontree.themeforest.net
Description: An Insanely Customizable WordPress Theme Options Framework Built for ThemeForest.net
Version: 1.0.4
Author: Derek Herman
Author URI: http://valendesigns.com
*/

/**
 *
 * Define Globals
 *
 */
global $ver, $table_name, $option_array, $table_prefix;

// Set Version
$ver = '1.0.4';

// Define Table Name
$table_name = $table_prefix . 'option_tree'; 

// Load Options Array
$option_array = option_tree_data();


/**
 *
 * Define the URL of the plugin's folder
 *
 */
define('THIS_PLUGIN_URL', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)));
define('THIS_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.basename(dirname(__FILE__)));

/**
 *
 * Includes
 *
 */
include(THIS_PLUGIN_DIR.'/functions/functions.php' );

/*
 *
 * Wordpress Actions & Hooks
 *
 */
register_activation_hook(__FILE__, 'option_tree_activate');
register_deactivation_hook(__FILE__, 'option_tree_deactivate');
add_action('admin_init', 'option_tree_init');
add_action('admin_menu', 'option_tree_admin');

/**
 *
 * Plugin Activation
 *
 */
function option_tree_activate() {
  global $wpdb, $ver, $table_name;
  
  // Check for table
  $new_installation = $wpdb->get_var("show tables like '$table_name'") != $table_name;
  
  // Check for installed version
	$installed_ver = get_option('option_tree_version');
  
  // Check if Installed == Version
	if ( $installed_ver != $ver ) {
		$sql = "CREATE TABLE ".$table_name." (
  		  id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  		  item_id VARCHAR(30) NOT NULL,
  		  item_title VARCHAR(30) NOT NULL,
  		  item_desc LONGTEXT,
  		  item_type VARCHAR(30) NOT NULL,
  		  item_std VARCHAR(30),
  		  item_options VARCHAR(250),
  		  item_sort mediumint(9) DEFAULT '0' NOT NULL,
  		  UNIQUE KEY (item_id)
  	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
    
    // Run Query
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
  }
  
  // New Install Default Data
  if ($new_installation) {
    option_tree_default_data();
  }
  
  // New Version Update
  if ($installed_ver != $ver) {
    update_option('option_tree_version', $ver );
  } else if (!$installed_ver) {
    add_option('option_tree_version', $ver );
  }
  
}

/**
 *
 * Plugin Activation Default Data
 *
 */
function option_tree_default_data() {
	global $wpdb, $ver, $table_name;
	
	// Add Version
	add_option('option_tree_version', $ver );
	
	// Default Data
	//$insert = "INSERT INTO ".$table_name." (item_id, item_title, item_type) VALUES ('general_default','General','heading')";
	//$wpdb->query($insert);
	$wpdb->query( $wpdb->prepare( "
    INSERT INTO {$table_name}
    ( item_id, item_title, item_type )
    VALUES ( %s, %s, %s ) ", 
    array('general_default','General','heading') ) );
    
  $wpdb->query( $wpdb->prepare( "
    INSERT INTO {$table_name}
    ( item_id, item_title, item_type )
    VALUES ( %s, %s, %s ) ", 
    array('test_input','Test Input','input') ) );

} 

/**
 *
 * Restore Table Data if empty
 *
 */
function option_tree_restore_default_data() {
  global $wpdb, $table_name;
  
  // Drop Table
  $wpdb->query("DROP TABLE $table_name");

  // Remove Activation Check
  delete_option('option_tree_version');
  
  // Load DB Activation Function if no data
  option_tree_activate();
  
  // Redirect
  wp_redirect(admin_url().'admin.php?page=option_tree_settings');
}

/**
 *
 * Plugin Deactivation
 *
 */
function option_tree_deactivate() {
  
  // Remove Activation Check & Version
  delete_option('option_tree_activation');
  delete_option('option_tree_version');
  
}

/**
 *
 * Initiate Plugin & setup main options
 *
 */
function option_tree_init() {
  global $option_array, $ver;
  
  // Create Options Array & Redirect on Activation
  $check = get_option('option_tree_activation');
  if ($check != "set") {
  
    // Set Option Values
    foreach ($option_array as $value) {
    	$key = $value->item_id;
    	$val = $value->item_std;
      $new_options[$key] = $val;
    }
    
    // Add Theme Options
    add_option('option_tree', $new_options );
    add_option('option_tree_activation', 'set');
    
    // Load DB Activation Function if Updating Plugin
    option_tree_activate();
    
    // Redirect
    wp_redirect(admin_url().'admin.php?page=option_tree_setup');
  }
  
}

/**
 * 
 * Add Menu Items & Test Actions
 *
 * TODO AJAXify save & reset
 * TODO clean up and seperate
 *
 */
function option_tree_admin() {
  global $wpdb, $option_array, $table_name;
	
	// Export XML - run before anything else
	if($_GET['action'] == 'export'){
    option_tree_export_data();
  }
  
  // Grab Fresh Option Array
  $test_options = $wpdb->get_results("SELECT * FROM {$table_name}");
  
  // Restore Table if Empty
	if(empty($test_options)) {
    option_tree_restore_default_data();
  }
  
  // Upgrade DB automatically
  option_tree_activate();
  
  // Load options array
	$settings = get_option('option_tree');
	
	
	// Page == option_tree_setup
  if ($_GET['page'] == 'option_tree_setup') 
  {
    // Action == Upload
    if ('upload' == $_REQUEST['action']) 
    {
      // fail no file
      if ($_FILES["import"]['name'] == null)
      {
        header("Location: admin.php?page=option_tree_setup&nofile=true");
        die();
      }
      // fail errors
      else if ($_FILES["import"]["error"] > 0)
      {
        header("Location: admin.php?page=option_tree_setup&error=true");
        die();
      } 
      else 
      {
        // success - it's XML
        if (preg_match("/(.xml)$/i", $_FILES["import"]['name'])) 
        {
          // PHP 5
          if(version_compare(PHP_VERSION, '5.0.0', '>='))
          {
            $rawdata = file_get_contents($_FILES["import"]["tmp_name"]);
            $new_options = new SimpleXMLElement($rawdata);
          }
          // PHP 4
          else 
          {
            $new_options = simplexml_load_file($_FILES["import"]["tmp_name"]);
          }
          
          // Drop Table
          $wpdb->query("DROP TABLE $table_name");
          
          // Create Table
          $sql = "CREATE TABLE ".$table_name." (
        		  id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        		  item_id VARCHAR(30) NOT NULL,
        		  item_title VARCHAR(30) NOT NULL,
        		  item_desc LONGTEXT,
        		  item_type VARCHAR(30) NOT NULL,
        		  item_std VARCHAR(30),
        		  item_options VARCHAR(250),
        		  item_sort mediumint(9) DEFAULT '0' NOT NULL,
        		  UNIQUE KEY (item_id)
        	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
      	  $wpdb->query($sql);
      	  
      	  // Insert Data
          foreach ($new_options->row as $value) 
          {
            $wpdb->insert( $table_name, 
              array( 
                'item_id' => $value->item_id, 
                'item_title' => $value->item_title, 
                'item_desc' => $value->item_desc, 
                'item_type' => $value->item_type, 
                'item_std' => $value->item_std, 
                'item_options' => $value->item_options 
              )
            );
          }
          // success redirect
          header("Location: admin.php?page=option_tree_setup&xml=true");
          die();
        }
        // fail
        else
        {
          // redirect
          header("Location: admin.php?page=option_tree_setup&error=true");
          die();
        }
      }
    }
    // Action == Import
    else if ('import' == $_REQUEST['action'])
    {
      // Get Data
      $string = $_POST['import_options'];
  
      // Unserialize The Array
      $new_options = unserialize(base64_decode($string));
      
      // Is Array
      if (is_array($new_options)) 
      {
        // Delete Old Options
        delete_option('option_tree');
        
        // Add New Options
        add_option('option_tree', $new_options);
        
        // Redirect on Import
        header("Location: admin.php?page=option_tree&data=true");
        die();
      }
      // fail
      else 
      {
        header("Location: admin.php?page=option_tree_setup&empty=true");
        die();
      }
    }
  }
  
	// Page == option_tree
  if ($_GET['page'] == 'option_tree') {
    
    // If Save
    if ('save' == $_REQUEST['action'] && 'Reset Options' != $_REQUEST['reset']) {
      
      // Set Option Values
      foreach ($option_array as $value) {
        // Checkbox
        if ($value->item_type == "checkbox") { 
          foreach($_REQUEST['checkboxes'] as $key => $val){
            if ($key == $value->item_id) {
          		$values = implode(',',$val);
          		$new_settings[$key] = $values;
        		}
        	}
        	
        	// no checkbox values
        	if (!isset($_REQUEST['checkboxes'])) {
            $key = $value->item_id;
            $values = null;
            $new_settings[$key] = $values;
        	}

        // Radio
        } else if ($value->item_type == "radio") {
          // Grab Radio array
          foreach($_REQUEST['radios'] as $key => $val){
        		if ($key == $value->item_id) {
          		$values = implode(',',$val);
          		$new_settings[$key] = $values;
        		}
        	}

        // Everything Else
        } else {
          $key = trim($value->item_id); 
          $val = $_REQUEST[$key];
          $new_settings[$key] = $val;
        }
		  }
		  
		  // Update Theme Options
      update_option('option_tree', $new_settings);
      
      // Redirect            
      header("Location: admin.php?page=option_tree&saved=true");
      die();
    
    // If Reset  
    } else if ('Reset Options' == $_REQUEST['reset']) {
      
      // Set Option Values
      foreach ($option_array as $value) {
        $key = $value->item_id;
        $std = $value->item_std;
        $new_options[$key] = $std;
      }
      
      // Delete Theme Options
      delete_option('option_tree');
      
      // Add Theme Options
      add_option('option_tree', $new_options);
      
      // Redirect
      header("Location: admin.php?page=option_tree&reset=true");
      die();
      
    }
  }
  
  // Page == option_tree_settings
  if ($_GET['page'] == 'option_tree_settings') {
    
  }
  
  // Create Menu Items
  if (get_user_option('admin_color') == 'classic') {
    $icon = THIS_PLUGIN_URL.'/images/icon_classic.png';
  } else {
    $icon = THIS_PLUGIN_URL.'/images/icon_dark.png';
  }
  add_object_page('OptionTree', 'OptionTree', 10, 'option_tree', 'option_tree_options_page', $icon);
  $option_tree_options = add_submenu_page('option_tree', 'OptionTree', 'Theme Options', 10, 'option_tree','option_tree_options_page');
  $option_tree_setup = add_submenu_page('option_tree', 'OptionTree', 'Theme Setup', 10, 'option_tree_setup','option_tree_setup_page');
  $option_tree_settings = add_submenu_page('option_tree', 'OptionTree', 'Developer Settings', 10, 'option_tree_settings', 'option_tree_settings_page');
  //$option_tree_themes = add_submenu_page('option_tree', 'OptionTree', 'New Themes', 10, 'option_tree_themes', 'option_tree_themes_page');
  
  // Add Menu Items
  add_action("admin_print_styles-$option_tree_setup", 'option_tree_load');
  add_action("admin_print_styles-$option_tree_options", 'option_tree_load');
  add_action("admin_print_styles-$option_tree_settings", 'option_tree_load');
  //add_action("admin_print_styles-$option_tree_themes", 'option_tree_load');
  	
}

/**
 * 
 * Load Scripts & Styles
 *
 */
function option_tree_load() {
  global $ver;
  
  // Enqueue Styles
  wp_enqueue_style('option-tree-style', THIS_PLUGIN_URL.'/css/style.css', false, $ver, 'screen');
  if (get_user_option('admin_color') == 'classic') {
    wp_enqueue_style('option-tree-style-classic', THIS_PLUGIN_URL.'/css/style-classic.css', array('option-tree-style'), $ver, 'screen');
  }
  
  // Enqueue Scripts
  add_thickbox();
  wp_enqueue_script('jquery-table-dnd', THIS_PLUGIN_URL.'/js/jquery.table.dnd.js', array('jquery'), $ver);
  wp_enqueue_script('option-tree-application', THIS_PLUGIN_URL.'/js/application.js', array('jquery','media-upload','thickbox', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-table-dnd'), $ver);
  
  // Remove GD Star Rating conflicts
  wp_deregister_style('gdsr-jquery-ui-core');
  wp_deregister_style('gdsr-jquery-ui-theme');

}

/**
 * 
 * Grab the wp_options_config data
 *
 */
function option_tree_data() {
  global $wpdb, $table_name;
  
  // Create an Array of Options
  $db_options = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY item_sort ASC");
  return $db_options;
}

/**
 * 
 * Theme Setup Page
 *
 */
function option_tree_setup_page() {
  global $ver;
  
  // Grab Setup Page
  include(THIS_PLUGIN_DIR.'/core/setup.php');
}

/**
 * 
 * Theme Options Page
 *
 */
function option_tree_options_page() {
  global $option_array, $ver;
  
  // Load Saved Options
  $settings = get_option('option_tree');
  
  // Grab Options Page
  include(THIS_PLUGIN_DIR.'/core/options.php');
}

/**
 * 
 * Framework Settings Page
 *
 */
function option_tree_settings_page() {
  global $option_array, $ver;
  
  // Load Saved Options
	$settings = get_option('option_tree');
  
  // Get Settings Page
  include(THIS_PLUGIN_DIR.'/core/settings.php');
}

/**
 * 
 * Themes Page
 *
 */
function option_tree_themes_page() {
  global $ver;
  
  // Get Themes Page
  include(THIS_PLUGIN_DIR.'/core/themes.php');
}

/**
 * 
 * Insert Row into Option Config Table via AJAX
 *
 */
add_action('wp_ajax_option_tree_add', 'option_tree_add');
function option_tree_add() {
	global $wpdb, $table_name;
  
  // Check AJAX Referer
  check_ajax_referer( 'inlineeditnonce', '_inline_edit' );
  
  // Grab Fresh Options Array
  $option_array = $wpdb->get_results("SELECT * FROM {$table_name}");
  
  // Get Form Data
  $id = $_POST['id'];
	$item_id = esc_html(trim($_POST['item_id']));
	$item_title = esc_html(trim($_POST['item_title']));
	$item_desc = esc_html($_POST['item_desc']);
	$item_type = $_POST['item_type'];
	$item_std = esc_html($_POST['item_std']);
	$item_options = esc_html($_POST['item_options']);
	
	// Validate Item key
	foreach($option_array as $value) {
    if ($item_id == $value->item_id) {
      die("That option key is already in use.");
    }
	}
	
	// Verify Title
  if (strlen($item_title) < 1 ) {
    die("You must give your option a title.");
  }
    
  // Update Row
  $wpdb->insert( $table_name, 
    array( 
      'item_id' => $item_id, 
      'item_title' => $item_title, 
      'item_desc' => $item_desc, 
      'item_type' => $item_type, 
      'item_std' => $item_std, 
      'item_options' => $item_options,
      'item_sort' => $id
    )
  );
  
  // Verify values in the DB are updated
  $updated = $wpdb->get_var("
    SELECT id 
    FROM $table_name 
    WHERE item_id = '$item_id'
    AND item_title = '$item_title'
    AND item_desc = '$item_desc'
    AND item_type = '$item_type'
    AND item_std = '$item_std'
    AND item_options = '$item_options'
    ");
  
  // If Updated
  if ($updated) {
    die('updated');
  } else {
    die("There was an error, please try again.");
  }
}

/**
 * 
 * Update Option Config Table via AJAX
 *
 */
add_action('wp_ajax_option_tree_edit', 'option_tree_edit');
function option_tree_edit() {
	global $wpdb, $table_name;
  
  // Check AJAX Referer
  check_ajax_referer( 'inlineeditnonce', '_inline_edit' );
  
  // Grab Fresh Options Array
  $option_array = $wpdb->get_results("SELECT * FROM {$table_name}");
  
  // Get Form Data
	$id = $_POST['id'];
	$item_id = esc_html(trim($_POST['item_id']));
	$item_title = esc_html(trim($_POST['item_title']));
	$item_desc = esc_html($_POST['item_desc']);
	$item_type = $_POST['item_type'];
	$item_std = esc_html($_POST['item_std']);
	$item_options = esc_html($_POST['item_options']);
	
	// Validate Item Key
	foreach($option_array as $value) {
    if ($value->item_sort == $id) {
      if ($item_id == $value->item_id && $value->item_sort != $id) {
        die("That option key is already in use.");
      }
    } else if ($item_id == $value->item_id && $value->id != $id) {
      die("That option key is already in use.");
    }
  
	}
	
	// Verify Title
	if (strlen($item_title) < 1 ) {
    die("You must give your option a title.");
  }
  
  // Update Row
  $wpdb->update( $table_name, 
    array( 
      'item_id' => $item_id, 
      'item_title' => $item_title, 
      'item_desc' => $item_desc, 
      'item_type' => $item_type, 
      'item_std' => $item_std, 
      'item_options' => $item_options 
    ), 
    array( 
      'id' => $id 
    )
  );
  
  // Verify values in the DB are updated
  $updated = $wpdb->get_var("
    SELECT id 
    FROM $table_name 
    WHERE item_id = '$item_id'
    AND item_title = '$item_title'
    AND item_desc = '$item_desc'
    AND item_type = '$item_type'
    AND item_std = '$item_std'
    AND item_options = '$item_options'
    ");
  
  // If Updated
  if ($updated) {
    die('updated');
  } else {
    die("There was an error, please try again.");
  }
}

/**
 * 
 * Remove Option via AJAX
 *
 */
add_action('wp_ajax_option_tree_delete', 'option_tree_delete');
function option_tree_delete() {
  global $wpdb, $table_name;

  // Create an array of IDs
	$id =$_REQUEST['id'];
  
  // Delete Item
	$wpdb->query("
    DELETE FROM $table_name 
    WHERE id = '$id'
  ");
	die('removed');
}

/**
 * 
 * Get Option ID via AJAX
 *
 */
add_action('wp_ajax_option_tree_next_id', 'option_tree_next_id');
function option_tree_next_id() {
  global $wpdb, $table_name;

  // Get ID
  $test_options = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC LIMIT 1");
  $id = $test_options[0]->id;
	die($id);

}

/**
 * 
 * Update Sort Order via AJAX
 *
 */
add_action('wp_ajax_option_tree_sort', 'option_tree_sort');
function option_tree_sort() {
  global $wpdb, $table_name;

  // Create an array of IDs
	$fields = explode('&', $_REQUEST['id']);
	
	// Set Order
	$order  = 0;
  
  // Update the Sort Order
	foreach($fields as $field) {
		$order++;
		$key = explode('=', $field);
		$id = urldecode($key[1]);
		$wpdb->update( $table_name, 
      array(
        'item_sort' => $order 
      ), 
      array( 
        'id' => $id 
      )
    );
	}
	die();
}