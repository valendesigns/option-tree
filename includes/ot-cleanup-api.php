<?php if ( ! defined( 'OT_VERSION' ) ) exit( 'No direct script access allowed' );
/**
 * OptionTree Cleanup API
 *
 * This class loads all the OptionTree Cleanup methods and helpers.
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2014, Derek Herman
 */
if ( ! class_exists( 'OT_Cleanup' ) ) {

  class OT_Cleanup {
  
    /**
     * PHP5 constructor method.
     *
     * This method adds other methods of the class to specific hooks within WordPress.
     *
     * @uses      add_action()
     *
     * @return    void
     *
     * @access    public
     * @since     2.4.6
     */
    function __construct() {
      if ( ! is_admin() )
        return;

      // Load styles
      add_action( 'admin_head', array( $this, 'styles' ), 1 );
      
      // Maybe Clean up OptionTree
      add_action( 'admin_menu', array( $this, 'maybe_cleanup' ), 100 );
      
      // Increase timeout if allowed
      add_action( 'ot_pre_consolidate_posts', array( $this, 'increase_timeout' ) );
      
    }
    
    /**
     * Adds the cleanup styles to the admin head
     *
     * @return    string
     *
     * @access    public
     * @since     2.5.0
     */
    function styles() {

      echo '<style>#toplevel_page_ot-cleanup{display:none;}</style>';

    }
    
    /**
     * Check if OptionTree needs to be cleaned up from a previous install.
     *
     * @return    void
     *
     * @access    public
     * @since     2.4.6
     */
    public function maybe_cleanup() {
      global $wpdb, $ot_maybe_cleanup_posts, $ot_maybe_cleanup_table;

      $table_name = $wpdb->prefix . 'option_tree';
      $page = isset( $_GET['page'] ) ? $_GET['page'] : '';
      $ot_maybe_cleanup_posts = count( $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'option-tree' LIMIT 2" ) ) > 1;
      $ot_maybe_cleanup_table = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) == $table_name;

      if ( ! $ot_maybe_cleanup_posts && ! $ot_maybe_cleanup_table && $page == 'ot-cleanup' ) {
        wp_redirect( apply_filters( 'ot_theme_options_parent_slug', 'themes.php' ) . '?page=' . apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ) );
        exit;
      }

      if ( $ot_maybe_cleanup_posts || $ot_maybe_cleanup_table ) {
        
        if ( $page != 'ot-cleanup' )
          add_action( 'admin_notices', array( $this, 'cleanup_notice' ) );

        $theme_check_bs = 'add_menu_page';

        $theme_check_bs( apply_filters( 'ot_cleanup_page_title', __( 'OptionTree Cleanup', 'option-tree' ) ), apply_filters( 'ot_cleanup_menu_title', __( 'OptionTree Cleanup', 'option-tree' ) ), 'edit_theme_options', 'ot-cleanup', array( $this, 'options_page' ) );

      }
      
    }
    
    /**
     * Adds an admin nag.
     *
     * @return    string
     *
     * @access    public
     * @since     2.4.6
     */
    public function cleanup_notice() {

      if ( get_current_screen()->id != 'appearance_page_ot-cleanup' )
        echo '<div class="update-nag">' . sprintf( __( 'OptionTree has outdated data that should be removed. Please go to %s for more information.', 'option-tree' ), sprintf( '<a href="%s">%s</a>', admin_url( 'themes.php?page=ot-cleanup' ), apply_filters( 'ot_cleanup_menu_title', __( 'OptionTree Cleanup', 'option-tree' ) ) ) ) . '</div>';
    
    }

    /**
     * Adds a Tools sub page to clean up the database with.
     *
     * @return    string
     *
     * @access    public
     * @since     2.4.6
     */
    public function options_page() {
      global $wpdb, $ot_maybe_cleanup_posts, $ot_maybe_cleanup_table;

      // Option ID
      $option_id = 'ot_media_post_ID';

      // Get the media post ID
      $post_ID = get_option( $option_id, false );

      // Zero loop count
      $count = 0;

      // Check for safe mode
      $safe_mode = ini_get( 'safe_mode' );

      echo '<div class="wrap">';

        echo '<h2>' . apply_filters( 'ot_cleanup_page_title', __( 'OptionTree Cleanup', 'option-tree' ) ) . '</h2>';

        if ( $ot_maybe_cleanup_posts ) {

          $posts = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'option-tree'" );

          echo '<h3>' . __( 'Multiple Media Posts', 'option-tree' ) . '</h3>';

          echo '<p>' . sprintf( __( 'There are currently %s OptionTree media posts in your database. At some point in the past, a version of OptionTree added multiple %s media post objects cluttering up your %s table. There is no associated risk or harm that these posts have caused other than to add size to your overall database. Thankfully, there is a way to remove all these orphaned media posts and get your database cleaned up.', 'option-tree' ), '<code>' . number_format( count( $posts ) ) . '</code>', '<tt>option-tree</tt>', '<tt>' . $wpdb->posts . '</tt>' ) . '</p>';

          echo '<p>' . sprintf( __( 'By clicking the button below, OptionTree will delete %s records and consolidate them into one single OptionTree media post for uploading attachments to. Additionally, the attachments will have their parent ID updated to the correct media post.', 'option-tree' ), '<code>' . number_format( count( $posts ) - 1 ) . '</code>' ) . '</p>';

          echo '<p><strong>' . __( 'This could take a while to fully process depending on how many records you have in your database, so please be patient and wait for the script to finish.', 'option-tree' ) . '</strong></p>';

          echo $safe_mode ?  '<p>' . sprintf( __( '%s Your server is running in safe mode. Which means this page will automatically reload after deleting %s posts, you can filter this number using %s if your server is having trouble processing that many at one time.', 'option-tree' ), '<strong>Note</strong>:', apply_filters( 'ot_consolidate_posts_reload', 500 ), '<tt>ot_consolidate_posts_reload</tt>' ) . '</p>' : '';

          echo '<p><a class="button button-primary" href="' . wp_nonce_url( admin_url( 'themes.php?page=ot-cleanup' ), 'consolidate-posts' ) . '">' . __( 'Consolidate Posts', 'option-tree' ) . '</a></p>';

          if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'consolidate-posts' ) ) {

            if ( $post_ID === false || empty( $post_ID ) ) {
              $post_ID = isset( $posts[0]->ID ) ? $posts[0]->ID : null;

              // Add to the DB
              if ( $post_ID !== null )
                update_option( $option_id, $post_ID );

            }

            // Do pre consolidation action to increase timeout.
            do_action( 'ot_pre_consolidate_posts' );

            // Loop over posts
            foreach( $posts as $post ) {

              // Don't destroy the correct post.
              if ( $post_ID == $post->ID )
                continue;

              // Update count
              $count++;

              // Reload script in safe mode
              if ( $safe_mode && $count > apply_filters( 'ot_consolidate_posts_reload', 500 ) ) {
                echo '<br />' . __( 'Reloading...', 'option-tree' );
                echo '
                <script>
                  setTimeout( ot_script_reload, 3000 )
                  function ot_script_reload() {
                    window.location = "' . self_admin_url( 'themes.php?page=ot-cleanup&_wpnonce=' . wp_create_nonce( 'consolidate-posts' ) ) . '"
                  }
                </script>';
                break;
              }
 
              // Get the attachements
              $attachments = get_children( 'post_type=attachment&post_parent=' . $post->ID );

              // Update the attachments parent ID
              if ( ! empty( $attachments ) ) {

                echo 'Updating Attachments parent ID for <tt>option-tree</tt> post <tt>#' . $post->ID . '</tt>.<br />';

                foreach( $attachments as $attachment_id => $attachment ) {
                  wp_update_post( 
                    array(
                      'ID' => $attachment_id,
                      'post_parent' => $post_ID
                    )
                  );
                }

              }

              // Delete post
              echo 'Deleting <tt>option-tree</tt> post <tt>#' . $post->ID . '</tt><br />';
              wp_delete_post( $post->ID, true );

            }

            echo '<br />' . __( 'Clean up script has completed, the page will now reload...', 'option-tree' );

            echo '
            <script>
              setTimeout( ot_script_reload, 3000 )
              function ot_script_reload() {
                window.location = "' . self_admin_url( 'themes.php?page=ot-cleanup' ) . '"
              }
            </script>';

          }

        }

        if ( $ot_maybe_cleanup_table ) {

          $table_name = $wpdb->prefix . 'option_tree';

          echo $ot_maybe_cleanup_posts ? '<hr />' : '';

          echo '<h3>' . __( 'Outdated Table', 'option-tree' ) . '</h3>';

          echo '<p>' . sprintf( __( 'If you have upgraded from an old 1.x version of OptionTree at some point, you have an extra %s table in your database that can be removed. It\'s not hurting anything, but does not need to be there. If you want to remove it. Click the button below.', 'option-tree' ), '<tt>' . $table_name . '</tt>' ) . '</p>';

          echo '<p><a class="button button-primary" href="' . wp_nonce_url( admin_url( 'themes.php?page=ot-cleanup' ), 'drop-table' ) . '">' . __( 'Drop Table', 'option-tree' ) . '</a></p>';

          if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'drop-table' ) ) {

            echo '<p>' . sprintf( __( 'Deleting the outdated and unused %s table...', 'option-tree' ), '<tt>' . $table_name . '</tt>' ) . '</p>';

            $wpdb->query( "DROP TABLE IF EXISTS $table_name" );

            if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name ) {

              echo '<p>' . sprintf( __( 'The %s table has been successfully deleted. The page will now reload...', 'option-tree' ), '<tt>' . $table_name . '</tt>' ) . '</p>';

              echo '
              <script>
                setTimeout( ot_script_reload, 3000 )
                function ot_script_reload() {
                  window.location = "' . self_admin_url( 'themes.php?page=ot-cleanup' ) . '"
                }
              </script>';

            } else {

              echo '<p>' . sprintf( __( 'Something went wrong. The %s table was not deleted.', 'option-tree' ), '<tt>' . $table_name . '</tt>' ) . '</p>';

            }

          }

        }
 
      echo '</div>';

    }

    /**
     * Increase PHP timeout.
     *
     * This is to prevent bulk operations from timing out
     *
     * @return    void
     *
     * @access    public
     * @since     2.4.6
     */
    public function increase_timeout() {
      
      if ( ! ini_get( 'safe_mode' ) ) {
      
        @set_time_limit( 0 );
        
      }
      
    }

  }

}

new OT_Cleanup();

/* End of file ot-cleanup-api.php */
/* Location: ./includes/ot-cleanup-api.php */