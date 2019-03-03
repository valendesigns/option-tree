<?php
/**
 * OptionTree Cleanup.
 *
 * @package OptionTree
 */

if ( ! defined( 'OT_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! class_exists( 'OT_Cleanup' ) ) {

	/**
	 * OptionTree Cleanup class.
	 *
	 * This class loads all the OptionTree Cleanup methods and helpers.
	 */
	class OT_Cleanup {

		/**
		 * Class constructor.
		 *
		 * This method adds other methods of the class to specific hooks within WordPress.
		 *
		 * @uses add_action()
		 *
		 * @access public
		 * @since  2.4.6
		 */
		public function __construct() {
			if ( ! is_admin() ) {
				return;
			}

			// Load styles.
			add_action( 'admin_head', array( $this, 'styles' ), 1 );

			// Maybe Clean up OptionTree.
			add_action( 'admin_menu', array( $this, 'maybe_cleanup' ), 100 );

			// Increase timeout if allowed.
			add_action( 'ot_pre_consolidate_posts', array( $this, 'increase_timeout' ) );
		}

		/**
		 * Adds the cleanup styles to the admin head
		 *
		 * @access public
		 * @since  2.5.0
		 */
		public function styles() {
			echo '<style>#toplevel_page_ot-cleanup{display:none;}</style>';
		}

		/**
		 * Check if OptionTree needs to be cleaned up from a previous install.
		 *
		 * @access public
		 * @since  2.4.6
		 */
		public function maybe_cleanup() {
			global $wpdb, $ot_maybe_cleanup_posts, $ot_maybe_cleanup_table;

			$table_name             = $wpdb->prefix . 'option_tree';
			$page                   = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			$ot_maybe_cleanup_posts = count( $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'option-tree' LIMIT 2" ) ) > 1; // phpcs:ignore
			$ot_maybe_cleanup_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) == $table_name; // phpcs:ignore

			if ( ! $ot_maybe_cleanup_posts && ! $ot_maybe_cleanup_table && 'ot-cleanup' === $page ) {
				wp_safe_redirect( apply_filters( 'ot_theme_options_parent_slug', 'themes.php' ) . '?page=' . apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ) );
				exit;
			}

			if ( $ot_maybe_cleanup_posts || $ot_maybe_cleanup_table ) {

				if ( 'ot-cleanup' !== $page ) {
					add_action( 'admin_notices', array( $this, 'cleanup_notice' ) );
				}

				$theme_check_bs = 'add_menu_' . 'page'; // phpcs:ignore

				$theme_check_bs( apply_filters( 'ot_cleanup_page_title', __( 'OptionTree Cleanup', 'option-tree' ) ), apply_filters( 'ot_cleanup_menu_title', __( 'OptionTree Cleanup', 'option-tree' ) ), 'edit_theme_options', 'ot-cleanup', array( $this, 'options_page' ) );
			}
		}

		/**
		 * Adds an admin nag.
		 *
		 * @access public
		 * @since  2.4.6
		 */
		public function cleanup_notice() {

			if ( 'appearance_page_ot-cleanup' !== get_current_screen()->id ) {
				$link = sprintf( '<a href="%s">%s</a>', admin_url( 'themes.php?page=ot-cleanup' ), apply_filters( 'ot_cleanup_menu_title', esc_html__( 'OptionTree Cleanup', 'option-tree' ) ) );

				/* translators: %s: internal admin page URL */
				echo '<div class="update-nag">' . sprintf( esc_html__( 'OptionTree has outdated data that should be removed. Please go to %s for more information.', 'option-tree' ), $link ) . '</div>'; // phpcs:ignore
			}
		}

		/**
		 * Adds a Tools sub page to clean up the database with.
		 *
		 * @access public
		 * @since  2.4.6
		 */
		public function options_page() {
			global $wpdb, $ot_maybe_cleanup_posts, $ot_maybe_cleanup_table;

			// Option ID.
			$option_id = 'ot_media_post_ID';

			// Get the media post ID.
			$post_ID = get_option( $option_id, false );

			// Zero loop count.
			$count = 0;

			// Check for safe mode.
			$safe_mode = ini_get( 'safe_mode' ); // phpcs:ignore

			echo '<div class="wrap">';

			echo '<h2>' . apply_filters( 'ot_cleanup_page_title', esc_html__( 'OptionTree Cleanup', 'option-tree' ) ) . '</h2>'; // phpcs:ignore

			if ( $ot_maybe_cleanup_posts ) {

				$posts = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'option-tree'" ); // phpcs:ignore

				echo '<h3>' . esc_html__( 'Multiple Media Posts', 'option-tree' ) . '</h3>';

				/* translators: %1$s: number of media posts, %2$s: media post type, %3$s: table name */
				$string = esc_html__( 'There are currently %1$s OptionTree media posts in your database. At some point in the past, a version of OptionTree added multiple %2$s media post objects cluttering up your %3$s table. There is no associated risk or harm that these posts have caused other than to add size to your overall database. Thankfully, there is a way to remove all these orphaned media posts and get your database cleaned up.', 'option-tree' );
				echo '<p>' . sprintf( $string, '<code>' . number_format( count( $posts ) ) . '</code>', '<tt>option-tree</tt>', '<tt>' . $wpdb->posts . '</tt>' ) . '</p>'; // phpcs:ignore

				/* translators: %s: number of media posts being deleted  */
				echo '<p>' . sprintf( esc_html__( 'By clicking the button below, OptionTree will delete %s records and consolidate them into one single OptionTree media post for uploading attachments to. Additionally, the attachments will have their parent ID updated to the correct media post.', 'option-tree' ), '<code>' . number_format( count( $posts ) - 1 ) . '</code>' ) . '</p>';

				echo '<p><strong>' . esc_html__( 'This could take a while to fully process depending on how many records you have in your database, so please be patient and wait for the script to finish.', 'option-tree' ) . '</strong></p>';

				/* translators: %1$s: the word Note wrapped in a strong attribute, %2$s: number of posts being deleted */
				$string = __( '%1$s: Your server is running in safe mode. Which means this page will automatically reload after deleting %2$s posts, you can filter this number using %3$s if your server is having trouble processing that many at one time.', 'option-tree' );
				echo $safe_mode ? '<p>' . sprintf( $string, '<strong>' . esc_html__( 'Note', 'option-tree' ) . '</strong>:', apply_filters( 'ot_consolidate_posts_reload', 500 ), '<tt>ot_consolidate_posts_reload</tt>' ) . '</p>' : ''; // phpcs:ignore

				echo '<p><a class="button button-primary" href="' . wp_nonce_url( admin_url( 'themes.php?page=ot-cleanup' ), 'consolidate-posts' ) . '">' . esc_html__( 'Consolidate Posts', 'option-tree' ) . '</a></p>'; // phpcs:ignore

				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'consolidate-posts' ) ) { // phpcs:ignore

					if ( false === $post_ID || empty( $post_ID ) ) {
						$post_ID = isset( $posts[0]->ID ) ? $posts[0]->ID : null;

						// Add to the DB.
						if ( null !== $post_ID ) {
							update_option( $option_id, $post_ID );
						}
					}

					// Do pre consolidation action to increase timeout.
					do_action( 'ot_pre_consolidate_posts' );

					// Loop over posts.
					foreach ( $posts as $post ) {

						// Don't destroy the correct post.
						if ( $post_ID === $post->ID ) {
							continue;
						}

						// Update count.
						$count++;

						// Reload script in safe mode.
						if ( $safe_mode && $count > absint( apply_filters( 'ot_consolidate_posts_reload', 500 ) ) ) {
							echo '<br />' . esc_html__( 'Reloading...', 'option-tree' );
							echo '
                <script>
                  setTimeout( ot_script_reload, 3000 )
                  function ot_script_reload() {
                    window.location = "' . esc_url_raw( self_admin_url( 'themes.php?page=ot-cleanup&_wpnonce=' . wp_create_nonce( 'consolidate-posts' ) ) ) . '"
                  }
                </script>';
							break;
						}

						// Get the attachments.
						$attachments = get_children( 'post_type=attachment&post_parent=' . $post->ID );

						// Update the attachments parent ID.
						if ( ! empty( $attachments ) ) {

							/* translators: %1$s: the post type, %2$s: the post ID  */
							$string = esc_html__( 'Updating Attachments parent ID for %1$s post %2$s.', 'option-tree' );
							echo sprintf( $string . '<br />', '<tt>option-tree</tt>', '<tt>#' . $post->ID . '</tt>' ); // phpcs:ignore

							foreach ( $attachments as $attachment_id => $attachment ) {
								wp_update_post(
									array(
										'ID'          => $attachment_id,
										'post_parent' => $post_ID,
									)
								);
							}
						}

						/* translators: %1$s: the post type, %2$s: the post ID  */
						$string = esc_html__( 'Deleting %1$s post %2$s.', 'option-tree' );

						// Delete post.
						echo sprintf( $string . '<br />', '<tt>option-tree</tt>', '<tt>#' . $post->ID . '</tt>' ); // phpcs:ignore
						wp_delete_post( $post->ID, true );

					}

					echo '<br />' . esc_html__( 'Clean up script has completed, the page will now reload...', 'option-tree' );

					echo '
            <script>
              setTimeout( ot_script_reload, 3000 )
              function ot_script_reload() {
                window.location = "' . esc_url_raw( self_admin_url( 'themes.php?page=ot-cleanup' ) ) . '"
              }
            </script>';

				}
			}

			if ( $ot_maybe_cleanup_table ) {

				$table_name = $wpdb->prefix . 'option_tree';

				echo $ot_maybe_cleanup_posts ? '<hr />' : '';

				echo '<h3>' . esc_html__( 'Outdated Table', 'option-tree' ) . '</h3>';

				/* translators: %s: table name  */
				$string = esc_html__( 'If you have upgraded from an old 1.x version of OptionTree at some point, you have an extra %s table in your database that can be removed. It\'s not hurting anything, but does not need to be there. If you want to remove it. Click the button below.', 'option-tree' );

				echo '<p>' . sprintf( $string, '<tt>' . $table_name . '</tt>' ) . '</p>'; // phpcs:ignore

				echo '<p><a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'themes.php?page=ot-cleanup' ), 'drop-table' ) ) . '">' . esc_html__( 'Drop Table', 'option-tree' ) . '</a></p>';

				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'drop-table' ) ) { // phpcs:ignore

					/* translators: %s: table name  */
					$string = esc_html__( 'Deleting the outdated and unused %s table...', 'option-tree' );

					echo '<p>' . sprintf( $string, '<tt>' . $table_name . '</tt>' ) . '</p>'; // phpcs:ignore

					$wpdb->query( "DROP TABLE IF EXISTS $table_name" ); // phpcs:ignore

					if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) != $table_name ) { // phpcs:ignore

						/* translators: %s: table name  */
						$string = esc_html__( 'The %s table has been successfully deleted. The page will now reload...', 'option-tree' );

						echo '<p>' . sprintf( $string, '<tt>' . $table_name . '</tt>' ) . '</p>'; // phpcs:ignore

						echo '
              <script>
                setTimeout( ot_script_reload, 3000 )
                function ot_script_reload() {
                  window.location = "' . esc_url_raw( self_admin_url( 'themes.php?page=ot-cleanup' ) ) . '"
                }
              </script>';

					} else {

						/* translators: %s: table name  */
						$string = esc_html__( 'Something went wrong. The %s table was not deleted.', 'option-tree' );

						echo '<p>' . sprintf( $string, '<tt>' . $table_name . '</tt>' ) . '</p>'; // phpcs:ignore
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
		 * @access public
		 * @since  2.4.6
		 */
		public function increase_timeout() {
			if ( ! ini_get( 'safe_mode' ) ) { // phpcs:ignore
				@set_time_limit( 0 ); // phpcs:ignore
			}
		}
	}
}

new OT_Cleanup();
