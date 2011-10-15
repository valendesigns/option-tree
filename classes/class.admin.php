<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * OptionTree Admin
 *
 * @package     WordPress
 * @subpackage  OptionTree
 * @since       1.0.0
 * @author      Derek Herman
 */
class OT_Admin
{
  private $table_name;
  private $version;
  private $option_array;
  private $ot_file;
  private $ot_data;
  private $ot_layout;
  private $theme_options_xml;
  private $theme_options_data;
  private $theme_options_layout;
  private $has_xml;
  private $has_data;
  private $has_layout;
  private $show_docs;
  
  /**
   * PHP4 contructor
   *
   * @since 1.1.6
   */
  function OT_Admin()
  {
    $this->__construct();
  }
  
  /**
   * PHP5 contructor
   *
   * @since 1.0.0
   */
  function __construct() 
  {
    global $table_prefix;
    
    $this->version = OT_VERSION;
    $this->table_name = $table_prefix . 'option_tree';
    define( 'OT_TABLE_NAME', $this->table_name );
    $this->option_array = $this->option_tree_data();
    
    // file path & name without extention
    $this->ot_file   = '/option-tree/theme-options.xml';
    $this->ot_data   = '/option-tree/theme-options.txt';
    $this->ot_layout = '/option-tree/layouts.txt';
    
    // XML file path
    $this->theme_options_xml = get_stylesheet_directory() . $this->ot_file;
    if ( !is_readable( $this->theme_options_xml ) ) // no file try parent theme
      $this->theme_options_xml = get_template_directory() . $this->ot_file;
    
    // Data file path
    $this->theme_options_data = get_stylesheet_directory() . $this->ot_data;
    if ( !is_readable( $this->theme_options_data ) ) // no file try parent theme
      $this->theme_options_data = get_template_directory() . $this->ot_data;
    
    // Layout file path
    $this->theme_options_layout = get_stylesheet_directory() . $this->ot_layout;
    if ( !is_readable( $this->theme_options_layout ) ) // no file try parent theme
      $this->theme_options_layout = get_template_directory() . $this->ot_layout;
    
    // check for files
    $this->has_xml    = ( is_readable( $this->theme_options_xml ) ) ? true : false;
    $this->has_data   = ( is_readable( $this->theme_options_data ) ) ? true : false;
    $this->has_layout = ( is_readable( $this->theme_options_layout ) ) ? true : false;

  }
  
  /**
   * Initiate Plugin & setup main options
   *
   * @uses get_option()
   * @uses add_option()
   * @uses option_tree_activate()
   * @uses wp_redirect()
   * @uses admin_url()
   *
   * @access public
   * @since 1.0.0
   *
   * @return bool
   */
  function option_tree_init() 
  {
    // check for activation
    $check = get_option( 'option_tree_activation' );

    if ( $check != "set" ) 
    {
      add_option( 'option_tree_activation', 'set');
      
      // load DB activation function if updating plugin
      $this->option_tree_activate();
      
      if ( $this->has_xml == true && $this->show_docs == false )
      {
        // Redirect
        wp_redirect( admin_url().'themes.php?page=option_tree' );
      }
      else
      {
        // Redirect
        wp_redirect( admin_url().'admin.php?page=option_tree_settings#import_options' );
      }
    }
    return false;
  }
  
  /**
   * Plugin Table Structure
   *
   * @access public
   * @since 1.0.0
   *
   * @param string $type
   *
   * @return string
   */
  function option_tree_table( $type = '')
  {
    if ( $type == 'create' ) 
    {
      $sql = "CREATE TABLE {$this->table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        item_id VARCHAR(50) NOT NULL,
        item_title VARCHAR(100) NOT NULL,
        item_desc LONGTEXT,
        item_type VARCHAR(30) NOT NULL,
        item_options VARCHAR(250) DEFAULT NULL,
        item_sort mediumint(9) DEFAULT '0' NOT NULL,
        UNIQUE KEY (item_id)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
    }
    return $sql;
  }
  
  /**
   * Plugin Activation
   *
   * @uses get_var()
   * @uses get_option()
   * @uses dbDelta()
   * @uses option_tree_table()
   * @uses option_tree_default_data()
   * @uses update_option()
   * @uses add_option()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_activate() 
  {
    global $wpdb;
    
    // check for table
    $new_installation = $wpdb->get_var( "show tables like '$this->table_name'" ) != $this->table_name;
    
    // check for installed version
  	$installed_ver = get_option( 'option_tree_version' );
    
    // add/update table
  	if ( $installed_ver != $this->version ) 
  	{	
      // run query
  		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
  		dbDelta( $this->option_tree_table( 'create' ) );
  		
  		// has xml file load defaults
  		if ( $this->has_xml == true )
  		  $this->option_tree_load_theme_files();
    }
    
    // new install
    if ( $new_installation ) 
      $this->option_tree_default_data();
    
    // New Version Update
    if ( $installed_ver != $this->version ) 
    {
      update_option( 'option_tree_version', $this->version );
    } 
    else if ( !$installed_ver ) 
    {
      add_option( 'option_tree_version', $this->version );
    }
  }
  
  /**
   * Plugin Deactivation delete options
   *
   * @uses delete_option()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_deactivate() 
  {
    // remove activation check & version
    delete_option( 'option_tree_activation' );
    delete_option( 'option_tree_version' );
  }
  
  /**
   * Load Default Data from theme included files
   *
   * @access public
   * @since 1.1.7
   *
   * @return void
   */
  function option_tree_load_theme_files() 
  {
    global $wpdb;

    $rawdata = file_get_contents( $this->theme_options_xml );
    
    if ( $rawdata )
    {
      $new_options = new SimpleXMLElement( $rawdata );
      
      // drop table
      $wpdb->query( "DROP TABLE $this->table_name" );
          
      // create table
      $wpdb->query( $this->option_tree_table( 'create' ) );
      	  
      foreach ( $new_options->row as $value )
      {
        $wpdb->insert( $this->table_name,
          array(
            'item_id' => $value->item_id,
            'item_title' => $value->item_title,
            'item_desc' => $value->item_desc,
            'item_type' => $value->item_type,
            'item_options' => $value->item_options
          )
        );
      }
    }
    
    // check for Data file and data not saved
    if ( $this->has_data == true && !get_option( 'option_tree' ) )
    {
      $rawdata = file_get_contents( $this->theme_options_data );
      $new_options = unserialize( base64_decode( $rawdata ) );
      
      // check if array()
      if ( is_array( $new_options ) )
      {
        // create new options
        add_option('option_tree', $new_options);
      }
    }
    
    // check for Layout file and layouts not saved
    if ( $this->has_layout == true && !get_option( 'option_tree_layouts' ) )
    {
      $rawdata = file_get_contents( $this->theme_options_layout );
      $new_layouts = unserialize( base64_decode( $rawdata ) );
      
      // check if array()
      if ( is_array( $new_layouts ) )
      {
        // create new layouts
        add_option('option_tree_layouts', $new_layouts);
      }
    }
  }
  
  /**
   * Plugin Activation Default Data
   *
   * @uses query()
   * @uses prepare()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_default_data() 
  {
    // load from files if they exist
    if ( $this->has_xml == true )
    {
      $this->option_tree_load_theme_files();
    }
    else
    {
      global $wpdb;
      
      // only run these queries if no xml file exist
    	$wpdb->query( $wpdb->prepare( "
        INSERT INTO {$this->table_name}
        ( item_id, item_title, item_type )
        VALUES ( %s, %s, %s ) ", 
        array('general_default','General','heading') ) );
        
      $wpdb->query( $wpdb->prepare( "
        INSERT INTO {$this->table_name}
        ( item_id, item_title, item_type )
        VALUES ( %s, %s, %s ) ", 
        array('test_input','Test Input','input') ) );
    }
  } 
  
  /**
   * Restore Table Data if empty
   *
   * @uses delete_option()
   * @uses option_tree_activate()
   * @uses wp_redirect()
   * @uses admin_url()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_restore_default_data() 
  {
    global $wpdb;
    
    // drop table
    $wpdb->query( "DROP TABLE $this->table_name" );
  
    // remove activation check
    delete_option( 'option_tree_version' );
    
    // load DB activation function
    $this->option_tree_activate();
    
    // Redirect
    if ( $this->has_xml == true && $this->show_docs == false )
    {
      wp_redirect( admin_url().'themes.php?page=option_tree' );
    }
    else
    {
      wp_redirect( admin_url().'admin.php?page=option_tree_settings' );
    }
  }
  
  /**
   * Add Admin Menu Items & Test Actions
   *
   * @uses option_tree_export_xml()
   * @uses option_tree_data()
   * @uses get_results()
   * @uses option_tree_restore_default_data()
   * @uses option_tree_activate()
   * @uses get_option()
   * @uses option_tree_import_xml()
   * @uses get_user_option()
   * @uses add_object_page()
   * @uses add_submenu_page()
   * @uses add_action()
   *
   * @access public
   * @since 1.0.0
   *
   * @param int $param
   *
   * @return void
   */
  function option_tree_admin() 
  {
    global $wpdb;
  	
  	// export XML - run before anything else
  	if ( isset($_GET['action']) && $_GET['action'] == 'ot-export-xml' )
      option_tree_export_xml( $this->option_tree_data(), $this->table_name );
    
    // grab saved table option
    $test_options = $wpdb->get_results( "SELECT * FROM {$this->table_name}" );
    
    // restore table if needed
  	if ( empty( $test_options ) )
      $this->option_tree_restore_default_data();
    
    // upgrade DB automatically
    $this->option_tree_activate();
    
    // load options array
  	$settings = get_option( 'option_tree' );
      
    // upload xml data
    $this->option_tree_import_xml();

    // if XML file came with the theme don't build the whole UI
    if ( $this->has_xml == true && $this->show_docs == false )
    {
      // create menu item
      $option_tree_options = add_submenu_page( 'themes.php', 'OptionTree Theme Options','Theme Options', 'edit_theme_options', 'option_tree', array( $this, 'option_tree_options_page' ) );
      
      // add menu item
      add_action( "admin_print_styles-$option_tree_options", array( $this, 'option_tree_load' ) );
    }
    else
    {
      // set admin color for icon
      $icon = ( get_user_option( 'admin_color' ) == 'classic' ) ? OT_PLUGIN_URL.'/assets/images/icon_classic.png' : OT_PLUGIN_URL.'/assets/images/icon_dark.png';
      
      // create menu items
      add_object_page( 'OptionTree', 'OptionTree', 'edit_theme_options', 'option_tree', array( $this, 'option_tree_options_page' ), $icon);
      $option_tree_options = add_submenu_page( 'option_tree', 'OptionTree', 'Theme Options', 'edit_theme_options', 'option_tree', array( $this, 'option_tree_options_page' ) );
      $option_tree_docs = add_submenu_page( 'option_tree', 'OptionTree', 'Documentation', 'edit_theme_options', 'option_tree_docs', array( $this, 'option_tree_docs_page' ) );
      $option_tree_settings = add_submenu_page( 'option_tree', 'OptionTree', 'Settings', 'edit_theme_options', 'option_tree_settings', array( $this, 'option_tree_settings_page' ) );
      
      // add menu items
      add_action( "admin_print_styles-$option_tree_options", array( $this, 'option_tree_load' ) );
      add_action( "admin_print_styles-$option_tree_docs", array( $this, 'option_tree_load' ) );
      add_action( "admin_print_styles-$option_tree_settings", array( $this, 'option_tree_load' ) );
    }
  }
  
  /**
   * Load Scripts & Styles
   *
   * @uses wp_enqueue_style()
   * @uses get_user_option()
   * @uses add_thickbox()
   * @uses wp_enqueue_script()
   * @uses wp_deregister_style()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_load() 
  {
    // enqueue styles
    wp_enqueue_style( 'option-tree-style', OT_PLUGIN_URL.'/assets/css/style.css', false, $this->version, 'screen');
    
    // classic admin theme styles
    if ( get_user_option( 'admin_color') == 'classic' ) 
      wp_enqueue_style( 'option-tree-style-classic', OT_PLUGIN_URL.'/assets/css/style-classic.css', array( 'option-tree-style' ), $this->version, 'screen');
    
    // enqueue scripts
    add_thickbox();
    wp_enqueue_script( 'jquery-table-dnd', OT_PLUGIN_URL.'/assets/js/jquery.table.dnd.js', array('jquery'), $this->version );
    wp_enqueue_script( 'jquery-color-picker', OT_PLUGIN_URL.'/assets/js/jquery.color.picker.js', array('jquery'), $this->version );
    wp_enqueue_script( 'jquery-option-tree', OT_PLUGIN_URL.'/assets/js/jquery.option.tree.js', array('jquery','media-upload','thickbox','jquery-ui-core','jquery-ui-tabs','jquery-table-dnd','jquery-color-picker', 'jquery-ui-sortable'), $this->version );
    
    // remove GD star rating conflicts
    wp_deregister_style( 'gdsr-jquery-ui-core' );
    wp_deregister_style( 'gdsr-jquery-ui-theme' );
    
    // remove Cispm Mail Contact jQuery UI
    wp_deregister_script('jquery-ui-1.7.2.custom.min');
  }
  
  /**
   * Grab the wp_option_tree table options array
   *
   * @uses get_results()
   *
   * @access public
   * @since 1.0.0
   *
   * @return array
   */
  function option_tree_data() 
  {
    global $wpdb;
      
    // create an array of options
    $db_options = $wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY item_sort ASC" );
    return $db_options;
  }
  
  /**
   * Theme Options Page
   *
   * @uses get_option()
   * @uses get_option_page_ID()
   * @uses option_tree_check_post_lock()
   * @uses option_tree_check_post_lock()
   * @uses option_tree_notice_post_locked()
   *
   * @access public
   * @since 1.0.0
   *
   * @return string
   */
  function option_tree_options_page() 
  {
    // hook before page loads
    do_action( 'option_tree_admin_header' );
    
    // set 
    $ot_array = $this->option_array;
    
    // load saved option_tree
    $settings = get_option( 'option_tree' );
    
    // Load Saved Layouts
  	$layouts = get_option('option_tree_layouts');
    
    // private page ID
    $post_id = $this->get_option_page_ID( 'media' );
    
    // set post lock
    if ( $last = $this->option_tree_check_post_lock( $post_id ) ) 
    {
      $message = $this->option_tree_notice_post_locked( $post_id );
  	} 
  	else 
  	{
  		$this->option_tree_set_post_lock( $post_id );
  	}
    
    // Grab Options Page
    include( OT_PLUGIN_DIR. '/front-end/options.php' );
  }
  
  /**
   * Settings Page
   *
   * @uses get_option()
   * @uses get_option_page_ID()
   * @uses option_tree_check_post_lock()
   * @uses option_tree_check_post_lock()
   * @uses option_tree_notice_post_locked()
   *
   * @access public
   * @since 1.0.0
   *
   * @return string
   */
  function option_tree_settings_page() 
  {
    // hook before page loads
    do_action( 'option_tree_admin_header' );
    
    // set 
    $ot_array = $this->option_array;
    
    // Load Saved Options
  	$settings = get_option('option_tree');
  	
  	// Load Saved Layouts
  	$layouts = get_option('option_tree_layouts');
  	
  	// private page ID
    $post_id = $this->get_option_page_ID( 'options' );
    
    // set post lock
    if ( $last = $this->option_tree_check_post_lock( $post_id ) ) 
    {
      $message = $this->option_tree_notice_post_locked( $post_id );
  	} 
  	else 
  	{
  		$this->option_tree_set_post_lock( $post_id );
  	}
    
    // Get Settings Page
    include( OT_PLUGIN_DIR . '/front-end/settings.php' );
  }
  
  /**
   * Documentation Page
   *
   * @access public
   * @since 1.0.0
   *
   * @return string
   */
  function option_tree_docs_page() 
  {
    // hook before page loads
    do_action( 'option_tree_admin_header' );
    
    // Get Settings Page
    include( OT_PLUGIN_DIR . '/front-end/docs.php' );
  }
  
  /**
   * Save Theme Option via AJAX
   *
   * @uses check_ajax_referer()
   * @uses update_option()
   * @uses option_tree_set_post_lock()
   * @uses get_option_page_ID()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_array_save() 
  {
    // Check AJAX Referer
    check_ajax_referer( '_theme_options', '_ajax_nonce' );
  	
    // set option values
    foreach ( $this->option_array as $value ) 
    {
      $key = trim( $value->item_id );
      if ( isset( $_REQUEST[$key] ) )
      { 
        $val = $_REQUEST[$key];
        $new_settings[$key] = $val;
      }
	  }
	  
	  // Update Theme Options
    update_option( 'option_tree', $new_settings );
    
    // update active layout content
    $options_layouts = get_option( 'option_tree_layouts' );
    if ( isset( $options_layouts['active_layout'] ) ) {
      $options_layouts[$options_layouts['active_layout']] = base64_encode( serialize( $new_settings ) );
      update_option( 'option_tree_layouts', $options_layouts );
    }
    
    // lock post editing
    $this->option_tree_set_post_lock( $this->get_option_page_ID( 'media' ) );
    
    // hook before AJAX is returned
    do_action( 'option_tree_array_save' );

  	die();
  }
  
  /**
   * Update XML Theme Option via AJAX
   *
   * @uses check_ajax_referer()
   * @uses update_option()
   * @uses option_tree_set_post_lock()
   * @uses get_option_page_ID()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_array_reload()
  {
    // Check AJAX Referer
    check_ajax_referer( '_theme_options', '_ajax_nonce' );
    
    global $wpdb;

    $rawdata = file_get_contents( $this->theme_options_xml );
    
    if ( $rawdata )
    {
      $new_options = new SimpleXMLElement( $rawdata );
      
      // drop table
      $wpdb->query( "DROP TABLE $this->table_name" );
          
      // create table
      $wpdb->query( $this->option_tree_table( 'create' ) );
      	  
      foreach ( $new_options->row as $value )
      {
        $wpdb->insert( $this->table_name,
          array(
            'item_id' => $value->item_id,
            'item_title' => $value->item_title,
            'item_desc' => $value->item_desc,
            'item_type' => $value->item_type,
            'item_options' => $value->item_options
          )
        );
      }
      die('themes.php?page=option_tree&updated=true&cache=buster_'.mt_rand(5, 100));
    }
    else
    {
      die('-1');
    }
  }
  
  /**
   * Reset Theme Option via AJAX
   *
   * @uses check_ajax_referer()
   * @uses update_option()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_array_reset() 
  {
    // Check AJAX Referer
    check_ajax_referer( '_theme_options', '_ajax_nonce' );
    
    // clear option values
    foreach ( $this->option_array as $value ) 
    {
      $key = $value->item_id;
      $new_options[$key] = '';
    }
    
    // update theme Options
    update_option( 'option_tree', $new_options );
    
    // update active layout content
    $options_layouts = get_option( 'option_tree_layouts' );
    if ( isset( $options_layouts['active_layout'] ) ) {
      $options_layouts[$options_layouts['active_layout']] = base64_encode( serialize( $new_options ) );
      update_option( 'option_tree_layouts', $options_layouts );
    }
    
  	die();
  }
  
  /**
   * Insert Row into Option Setting Table via AJAX
   *
   * @uses check_ajax_referer()
   * @uses get_results()
   * @uses insert()
   * @uses get_var()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_add() 
  {
  	global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    // grab fresh options array
    $ot_array = $wpdb->get_results( "SELECT * FROM {$this->table_name}" );
    
    // get form data
    $id = $_POST['id'];
  	$item_id       = htmlspecialchars(stripslashes(trim($_POST['item_id'])), ENT_QUOTES,'UTF-8',true);
  	$item_title    = htmlspecialchars(stripslashes(trim($_POST['item_title'])), ENT_QUOTES,'UTF-8',true);
  	$item_desc     = htmlspecialchars(stripslashes(trim($_POST['item_desc'])), ENT_QUOTES,'UTF-8',true);
  	$item_type     = htmlspecialchars(stripslashes(trim($_POST['item_type'])), ENT_QUOTES,'UTF-8',true);
  	$item_options  = htmlspecialchars(stripslashes(trim($_POST['item_options'])), ENT_QUOTES,'UTF-8',true);
  	
  	// validate item key
  	foreach( $ot_array as $value ) 
  	{
      if ( $item_id == $value->item_id ) 
      {
        die( "That option key is already in use." );
      }
  	}
  	
  	// verify key is alphanumeric
    if ( preg_match( '/[^a-z0-9_]/', $item_id ) ) 
      die("You must enter a valid option key.");
  	
  	// verify title
    if (strlen($item_title) < 1 ) 
      die("You must give your option a title.");
    
    if ( $item_type == 'textarea' && !is_numeric( $item_options ) )
      die("The row value must be numeric.");
      
    // update row
    $wpdb->insert( $this->table_name, 
      array( 
        'item_id' => $item_id,
        'item_title' => $item_title,
        'item_desc' => $item_desc,
        'item_type' => $item_type,
        'item_options' => $item_options,
        'item_sort' => $id
      )
    );
    
    // verify values in the DB are updated
    $updated = $wpdb->get_var("
      SELECT id 
      FROM {$this->table_name}
      WHERE item_id = '$item_id'
      AND item_title = '$item_title'
      AND item_type = '$item_type'
      AND item_options = '$item_options'
    ");
    
    // if updated
    if ( $updated )
    {
      die('updated');
    } 
    else
    {
      die("There was an error, please try again.");
    }
  }
  
  /**
   * Update Option Setting Table via AJAX
   *
   * @uses check_ajax_referer()
   * @uses get_results()
   * @uses update()
   * @uses get_var()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_edit() 
  {
  	global $wpdb;
    
    // Check AJAX Referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    // grab fresh options array
    $ot_array = $wpdb->get_results( "SELECT * FROM {$this->table_name}" );
    
    // get form data
  	$id = $_POST['id'];
  	$item_id       = htmlspecialchars(stripslashes(trim($_POST['item_id'])), ENT_QUOTES,'UTF-8',true);
  	$item_title    = htmlspecialchars(stripslashes(trim($_POST['item_title'])), ENT_QUOTES,'UTF-8',true);
  	$item_desc     = htmlspecialchars(stripslashes(trim($_POST['item_desc'])), ENT_QUOTES,'UTF-8',true);
  	$item_type     = htmlspecialchars(stripslashes(trim($_POST['item_type'])), ENT_QUOTES,'UTF-8',true);
  	$item_options  = htmlspecialchars(stripslashes(trim($_POST['item_options'])), ENT_QUOTES,'UTF-8',true);
  	
  	// validate item key
  	foreach($ot_array as $value) 
  	{
      if ( $value->item_sort == $id ) 
      {
        if ($item_id == $value->item_id && $value->item_sort != $id) 
        {
          die("That option key is already in use.");
        }
      } 
      else if ($item_id == $value->item_id && $value->id != $id) 
      {
        die("That option key is already in use.");
      }
  	}
  	
  	// verify key is alphanumeric
    if ( preg_match( '/[^a-z0-9_]/', $item_id ) ) 
      die("You must enter a valid option key.");
      
  	// verify title
  	if ( strlen( $item_title ) < 1 ) 
      die("You must give your option a title.");
    
    if ( $item_type == 'textarea' && !is_numeric( $item_options ) )
      die("The row value must be numeric.");
    
    // update row
    $wpdb->update( $this->table_name, 
      array( 
        'item_id' => $item_id, 
        'item_title' => $item_title, 
        'item_desc' => $item_desc, 
        'item_type' => $item_type, 
        'item_options' => $item_options 
      ), 
      array( 
        'id' => $id 
      )
    );
    
    // verify values in the DB are updated
    $updated = $wpdb->get_var("
      SELECT id 
      FROM {$this->table_name}
      WHERE item_id = '$item_id'
      AND item_title = '$item_title'
      AND item_type = '$item_type'
      AND item_options = '$item_options'
      ");
    
    // if updated
    if ( $updated ) 
    {
      die('updated');
    } 
    else 
    {
      die("There was an error, please try again.");
    }
  }

  /**
   * Remove Option via AJAX
   *
   * @uses check_ajax_referer()
   * @uses query()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_delete() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
  
    // grab ID
  	$id = $_REQUEST['id'];
    
    // delete item
  	$wpdb->query("
      DELETE FROM $this->table_name 
      WHERE id = '$id'
    ");
    
  	die('removed');
  }
  
  /**
   * Get Option ID via AJAX
   *
   * @uses check_ajax_referer()
   * @uses delete_post_meta()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_next_id() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    // get ID
    $id = $wpdb->get_var( "SELECT id FROM {$this->table_name} ORDER BY id DESC LIMIT 1" );
    
    // return ID
  	die($id);
  }
  
  /**
   * Update Sort Order via AJAX
   *
   * @uses check_ajax_referer()
   * @uses update()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_sort() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
  
    // create an array of IDs
  	$fields = explode('&', $_REQUEST['id']);
  	
  	// set order
  	$order = 0;
    
    // update the sort order
  	foreach( $fields as $field ) {
  		$order++;
  		$key = explode('=', $field);
  		$id = urldecode($key[1]);
  		$wpdb->update( $this->table_name, 
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
  
  /**
   * Upload XML Option Data
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_import_xml() 
  {
    global $wpdb;
    
    // Check for multisite and add xml mime type if needed
    if ( is_multisite() )
    {
      $xml_ext = false;
      
      // build ext array
      $site_exts = explode( ' ', get_site_option( 'upload_filetypes' ) );
      
      // check for xml ext 
      foreach ( $site_exts as $ext )
      { 
        if ( $ext == 'xml' )
          $xml_ext = true;
      }
          
    	// add xml to mime types
      if ( $xml_ext == false )
      {
        $new_site_exts = implode( ' ', $site_exts );
        update_site_option( 'upload_filetypes', $new_site_exts.' xml' );
      }
    }

    // action == upload
    if ( isset($_GET['action']) && $_GET['action'] == 'ot-upload-xml' ) 
    {
      // fail no file
      if ( $_FILES["import"]['name'] == null )
      {
        header("Location: admin.php?page=option_tree_settings&nofile=true#import_options");
        die();
      }
      // fail errors
      else if ( $_FILES["import"]["error"] > 0 )
      {
        header("Location: admin.php?page=option_tree_settings&error=true#import_options");
        die();
      } 
      else 
      {
        // success - it's XML
        if ( preg_match( "/(.xml)$/i", $_FILES["import"]['name'] ) ) 
        {
        
          $mimes = apply_filters( 'upload_mimes', array(
            'xml' => 'text/xml'
          ));
         
          $overrides = array('test_form' => false, 'mimes' => $mimes);
          $import = wp_handle_upload($_FILES['import'], $overrides);
 
          if (!empty($import['error'])) 
          {
            header("Location: admin.php?page=option_tree_settings&error=true#import_options");
            die();
          }
         
          $rawdata = file_get_contents( $import['file'] );
          $new_options = new SimpleXMLElement( $rawdata );
          
          // drop table
          $wpdb->query( "DROP TABLE $this->table_name" );
          
          // create table
      	  $wpdb->query( $this->option_tree_table( 'create' ) );
      	  
      	  // insert data
          foreach ( $new_options->row as $value ) 
          {
            $wpdb->insert( $this->table_name, 
              array( 
                'item_id' => $value->item_id,
                'item_title' => $value->item_title,
                'item_desc' => $value->item_desc,
                'item_type' => $value->item_type,
                'item_options' => $value->item_options
              )
            );
          }
          // success redirect
          header("Location: admin.php?page=option_tree_settings&xml=true#import_options");
          die();
        }
        // fail
        else
        {
          // redirect
          header("Location: admin.php?page=option_tree_settings&error=true#import_options");
          die();
        }
      }
    }
  }
  
  /**
   * Import Option Data via AJAX
   *
   * @uses check_ajax_referer()
   * @uses update()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function option_tree_import_data() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( '_import_data', '_ajax_nonce' );
    
    // Get Data
    $string = $_REQUEST['import_options_data'];

    // Unserialize The Array
    $new_options = unserialize( base64_decode( $string ) );
      
    // check if array()
    if ( is_array( $new_options ) ) 
    {
      // delete old options
      delete_option( 'option_tree' );
      
      // create new options
      add_option('option_tree', $new_options);
      
      // update active layout content
      $options_layouts = get_option( 'option_tree_layouts' );
      if ( isset( $options_layouts['active_layout'] ) ) {
        $options_layouts[$options_layouts['active_layout']] = base64_encode( serialize( $new_options ) );
        update_option( 'option_tree_layouts', $options_layouts );
      }
      
      // hook after import, before AJAX is returned
      do_action( 'option_tree_import_data' );
    
      // redirect
      die();
    }
    // failed
    die('-1');
  }
  
  /**
   * Update Layouts data via AJAX
   *
   * @uses check_ajax_referer()
   * @uses get_option()
   *
   * @access public
   * @since 1.1.7
   *
   * @return void
   */
  function option_tree_update_export_data() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    $saved = $_REQUEST['saved'];
    $updated = base64_encode( serialize( get_option( 'option_tree' ) ) );
    
    // check if array()
    if ( $saved != $updated ) 
    {
      die($updated);
    }
    // failed
    die('-1');
  }
  
  /**
   * Save Layout via AJAX
   *
   * @uses check_ajax_referer()
   * @uses get_option()
   * @uses update_option()
   * @uses add_option()
   *
   * @access public
   * @since 1.1.7
   *
   * @return void
   */
  function option_tree_save_layout() 
  {
    global $wpdb;
    
    // check AJAX referer
    if ( isset($_REQUEST['themes']) && $_REQUEST['themes'] == true ) 
    {
      // Check AJAX Referer
      check_ajax_referer( '_theme_options', '_ajax_nonce' );
    }
    else
    {
      // check AJAX referer
      check_ajax_referer( '_save_layout', '_ajax_nonce' );
    }
    
    // Get Data
    $string = $_REQUEST['options_name'];
    
    // set default layout name
    if ( !$string ) 
      $string = 'default';
    
    // replace whitespace and set to lower case
    $string = str_replace(' ', '-', strtolower( $string ) );

    // get options and encode
    $options = get_option( 'option_tree' );
    $options = base64_encode( serialize( $options ) );
    
    // get saved layouts
    $options_layouts = get_option( 'option_tree_layouts' );
	
    if ( is_array( $options_layouts ) )
    {
      $options_layouts['active_layout'] = $string;
      $options_layouts[$string] = $options;
      update_option( 'option_tree_layouts', $options_layouts );
    } 
    else
    {
      delete_option( 'option_tree_layouts' );
      add_option( 'option_tree_layouts', array( 'active_layout' => $string, $string => $options ) );
    }
    
    // hook after save, before AJAX is returned
    do_action( 'option_tree_save_layout' );
    
    // redirect
    if ( isset($_REQUEST['themes']) && $_REQUEST['themes'] == true )
    {
      die('admin.php?page=option_tree&layout_saved=true');
    }
    else 
    {
      die( $options );
    }
  }
  
  /**
   * Delete Layout via AJAX
   *
   * @uses check_ajax_referer()
   * @uses get_option()
   * @uses update_option()
   * @uses add_option()
   *
   * @access public
   * @since 1.1.7
   *
   * @return void
   */
  function option_tree_delete_layout() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    // grab ID
    $id = $_REQUEST['id'];
	
    $options_layouts = get_option( 'option_tree_layouts' );
    
    // remove the item
    unset( $options_layouts[$id] );
    
    // check active layout and unset if deleted
    if ( $options_layouts['active_layout'] == $id )
    {
      unset( $options_layouts['active_layout'] );
    }
    
    update_option( 'option_tree_layouts', $options_layouts );
    
    // hook after delete, before AJAX is returned
    do_action( 'option_tree_delete_layout' );
    
    die( 'removed' );
  }
  
  /**
   * Activate Layout via AJAX
   *
   * @uses check_ajax_referer()
   * @uses get_option()
   * @uses update_option()
   * @uses add_option()
   *
   * @access public
   * @since 1.1.7
   *
   * @return void
   */
  function option_tree_activate_layout() 
  {
    global $wpdb;
    
    if ( isset($_REQUEST['themes']) && $_REQUEST['themes'] == true ) 
    {
      // Check AJAX Referer
      check_ajax_referer( '_theme_options', '_ajax_nonce' );
    }
    else
    {
      // check AJAX referer
      check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    }
    
    // grab ID
    $id = $_REQUEST['id'];
	
    // Get Saved Options
    $options_layouts = get_option('option_tree_layouts');
	
    // Unserialize The Array
    $new_options = unserialize( base64_decode( $options_layouts[$id] ) );
    
    // check if array()
    if ( is_array( $new_options ) ) 
    {
      // delete old options
      delete_option( 'option_tree' );
      
      // set active layout
      $options_layouts['active_layout'] = $id;
      update_option('option_tree_layouts', $options_layouts);
      
      // create new options
      add_option( 'option_tree', $new_options );
      
      // hook after activate, before AJAX is returned
      do_action( 'option_tree_activate_layout' );
    
      // redirect
      if ( $this->has_xml == true && $this->show_docs == false )
      {
        die('themes.php?page=option_tree&layout=true');
      }
      else if ( isset($_REQUEST['themes']) && $_REQUEST['themes'] == true )
      {
        die('admin.php?page=option_tree&layout=true');
      }
      else 
      {
        die('activated');
      }
      
    }
    
    // failed
    die('-1');
  }
  
  /**
   * Import Layouts via AJAX
   *
   * @uses check_ajax_referer()
   * @uses delete_option()
   * @uses add_option()
   *
   * @access public
   * @since 1.1.7
   *
   * @return void
   */
  function option_tree_import_layout() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( '_import_layout', '_ajax_nonce' );
    
    // Get Data
    $string = $_REQUEST['import_option_layouts'];

    // Unserialize The Array
    $new_options = unserialize( base64_decode( $string ) );
    
    // check if array()
    if ( is_array( $new_options ) ) 
    {
      // delete old layouts
      delete_option( 'option_tree_layouts' );
      
      // create new layouts
      add_option('option_tree_layouts', $new_options);
      
      // hook after import, before redirect
      do_action( 'option_tree_import_layout' );
      
      // redirect
      die('admin.php?page=option_tree_settings&layout=true&cache=buster_'.mt_rand(5, 100).'#layout_options');
    }
    // failed
    die('-1');
  }
  
  /**
   * Update Layouts data via AJAX
   *
   * @uses check_ajax_referer()
   * @uses get_option()
   *
   * @access public
   * @since 1.1.7
   *
   * @return void
   */
  function option_tree_update_export_layout() 
  {
    global $wpdb;
    
    // check AJAX referer
    check_ajax_referer( 'inlineeditnonce', '_ajax_nonce' );
    
    $saved = $_REQUEST['saved'];
    $updated = base64_encode( serialize( get_option( 'option_tree_layouts' ) ) );
    
    // check if array()
    if ( $saved != $updated ) 
    {
      die($updated);
    }
    // failed
    die('-1');
  }
  
  function option_tree_add_slider() 
  {
    $count = $_GET['count'] + 1;
    $id = $_GET['slide_id'];
    $image = array(
      'order'       => $count,
      'title'       => '',
      'image'       => '',
      'link'        => '',
      'description' => ''
    );
    option_tree_slider_view( $id, $image, $this->get_option_page_ID('media'), $count );
    die();
  }
  
  /**
   * Returns the ID of a cutom post tpye
   *
   * @uses get_results()
   *
   * @access public
   * @since 1.0.0
   *
   * @param string $page_title
   *
   * @return int
   */
  function get_option_page_ID( $page_title = '' ) 
  {
    global $wpdb;
    
    return $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE `post_name` = '{$page_title}' AND `post_type` = 'option-tree' AND `post_status` = 'private'");
  }
  
  /**
   * Register custom post type & create two posts
   *
   * @uses get_results()
   *
   * @access public
   * @since 1.0.0
   *
   * @return void
   */
  function create_option_post() 
  {
    global $current_user;
    
    // profile show docs & settings checkbox
    $this->show_docs = ( get_the_author_meta( 'show_docs', $current_user->ID ) == "Yes" ) ? true : false;
    
    register_post_type( 'option-tree', array(
    	'labels' => array(
    		'name' => __( 'Options' ),
    	),
    	'public' => true,
    	'show_ui' => false,
    	'capability_type' => 'post',
    	'exclude_from_search' => true,
    	'hierarchical' => false,
    	'rewrite' => false,
    	'supports' => array( 'title', 'editor' ),
    	'can_export' => true,
    	'show_in_nav_menus' => false,
    ) );
    
    // create a private page to attach media to
    if ( isset($_GET['page']) && $_GET['page'] == 'option_tree' ) 
    {  
      // look for custom page
      $page_id = $this->get_option_page_ID( 'media' );
      
      // no page create it
      if ( ! $page_id ) 
      {
        // create post object
        $_p = array();
        $_p['post_title'] = 'Media';
        $_p['post_status'] = 'private';
        $_p['post_type'] = 'option-tree';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        
        // insert the post into the database
        $page_id = wp_insert_post( $_p );
      }
    }
    
    // create a private page for settings page
    if ( isset($_GET['page']) && $_GET['page'] == 'option_tree_settings' ) 
    {  
      // look for custom page
      $page_id = $this->get_option_page_ID( 'options' );
      
      // no page create it
      if ( ! $page_id ) 
      {
        // create post object
        $_p = array();
        $_p['post_title'] = 'Options';
        $_p['post_status'] = 'private';
        $_p['post_type'] = 'option-tree';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        
        // insert the post into the database
        $page_id = wp_insert_post( $_p );
      }
    }
  }
  
  /**
   * Outputs the notice message to say that someone else is editing this post at the moment.
   *
   * @uses get_userdata()
   * @uses get_post_meta()
   * @uses esc_html()
   *
   * @access public
   * @since 1.0.0
   *
   * @param int $post_id
   *
   * @return string
   */
  function option_tree_notice_post_locked( $post_id ) 
  {
    if ( !$post_id )
  		return false;
      
  	$last_user = get_userdata( get_post_meta( $post_id, '_edit_last', true ) );
    $last_user_name = $last_user ? $last_user->display_name : __('Somebody');
    $the_page = ( $_GET['page'] == 'option_tree' ) ? __('Theme Options') : __('Settings');
    
    $message = sprintf( __( 'Warning: %s is currently editing the %s.' ), esc_html( $last_user_name ), $the_page );
    return '<div class="message warning"><span>&nbsp;</span>'.$message.'</div>';
  }
  
  /**
   * Check to see if the post is currently being edited by another user.
   *
   * @uses get_post_meta()
   * @uses apply_filters()
   * @uses get_current_user_id()
   *
   * @access public
   * @since 1.0.0
   *
   * @param int $post_id
   *
   * @return bool
   */
  function option_tree_check_post_lock( $post_id ) 
  { 
  	if ( !$post_id )
  		return false;
  
  	$lock = get_post_meta( $post_id, '_edit_lock', true );
  	$last = get_post_meta( $post_id, '_edit_last', true );
  
  	$time_window = apply_filters( 'wp_check_post_lock_window', 30 );
  
  	if ( $lock && $lock > time() - $time_window && $last != get_current_user_id() )
  		return $last;
  		
  	return false;
  }
  
  /**
   * Mark the post as currently being edited by the current user
   *
   * @uses update_post_meta()
   * @uses get_current_user_id()
   *
   * @access public
   * @since 1.0.0
   *
   * @param int $post_id
   *
   * @return bool
   */
  function option_tree_set_post_lock( $post_id ) 
  {
  	if ( !$post_id )
  		return false;
  		
  	if ( 0 == get_current_user_id() )
  		return false;
  
  	$now = time();
  
  	update_post_meta( $post_id, '_edit_lock', $now );
  	update_post_meta( $post_id, '_edit_last', get_current_user_id() );
  }
  
  /**
   * Remove the post lock
   *
   * @uses delete_post_meta()
   *
   * @access public
   * @since 1.0.0
   *
   * @param int $post_id
   *
   * @return bool
   */
  function option_tree_remove_post_lock( $post_id ) 
  {
  	if ( !$post_id )
  		return false;
  
  	delete_post_meta( $post_id, '_edit_lock' );
  	delete_post_meta( $post_id, '_edit_last' );
  }
  
  /**
   * Extra Profile Fields
   *
   * @uses get_the_author_meta()
   *
   * @access public
   * @since 1.8
   *
   * @param option_tree
   *
   * @return void
   */
  function option_tree_extra_profile_fields( $user ) 
  { 
  ?>
  <h3>Option Tree</h3>
  <table class="form-table">
    <tr>
      <th scope="row"><?php _e( 'Show Settings &amp; Docs', 'option-tree' ); ?></th>
      <td>
        <input type="checkbox" name="show_docs" value="<?php echo esc_attr( get_the_author_meta( 'show_docs', $user->ID ) ); ?>"<?php if(esc_attr( get_the_author_meta( 'show_docs', $user->ID ) ) == "Yes"){ echo ' checked="checked"'; } ?> />
        <label for="show_docs"><?php _e( 'Yes', 'option-tree' ); ?></label>
      </td>
    </tr>
  </table>
  <?php 
  }
	
	  
  /**
   * Extra Profile Fields Save
   *
   * @uses current_user_can()
   *
   * @access public
   * @since 1.8
   *
   * @param option_tree
   *
   * @return void
   */
  function option_tree_save_extra_profile_fields( $user_id ) 
  {
    if ( !current_user_can( 'edit_user', $user_id ) )
      return false;
    
    $ot_view = isset( $_POST['show_docs'] ) ? 'Yes' : 'No';
    update_user_meta( $user_id, 'show_docs', $ot_view );
  }

}