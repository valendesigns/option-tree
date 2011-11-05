<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Functions Load
 *
 * @package     WordPress
 * @subpackage  OptionTree
 * @since       1.0.0
 * @author      Derek Herman
 */
include( OT_PLUGIN_DIR . '/functions/functions.php' );

if ( is_admin() && isset( $_GET['page'] ) && strpos( '_' . $_GET['page'], 'option_tree' ) ) {
  include( OT_PLUGIN_DIR . '/functions/admin/export.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/heading.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/input.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/checkbox.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/radio.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/select.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/textarea.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/upload.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/colorpicker.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/textblock.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/post.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/page.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/category.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/tag.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/custom-post.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/measurement.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/slider.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/background.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/typography.php' );
  include( OT_PLUGIN_DIR . '/functions/admin/css.php' );
} else if ( ! is_admin() ) {
  include( OT_PLUGIN_DIR . '/functions/get-option-tree.php' );
}