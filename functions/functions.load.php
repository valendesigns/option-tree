<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');
/**
 * Functions Load
 *
 * @package     WordPress
 * @subpackage  OptionTree
 * @since       1.0.0
 * @author      Derek Herman
 */
if ( is_admin() )
{
  include( 'admin/export.php' );
  include( 'admin/heading.php' );
  include( 'admin/input.php' );
  include( 'admin/checkbox.php' );
  include( 'admin/radio.php' );
  include( 'admin/select.php' );
  include( 'admin/textarea.php' );
  include( 'admin/upload.php' );
  include( 'admin/colorpicker.php' );
  include( 'admin/textblock.php' );
  include( 'admin/post.php' );
  include( 'admin/page.php' );
  include( 'admin/category.php' );
  include( 'admin/tag.php' );
  include( 'admin/custom-post.php' );
  include( 'admin/measurement.php' );
  include( 'admin/slider.php' );
}
else if ( !is_admin() )
{
  include( 'get-option-tree.php' );
}