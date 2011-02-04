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
  
    <div class="info top-info">
    </div>
    
    <div class="ajax-message<?php if ( isset($_GET['xml']) || isset($_GET['error']) || isset($_GET['nofile']) || isset($_GET['empty']) || isset( $message ) ) { echo ' show'; } ?>">
      <?php if(isset($_GET['xml'])) { echo '<div class="message"><span>&nbsp;</span>Theme Options Created</div>'; } ?>
      <?php if(isset($_GET['error'])) { echo '<div class="message warning"><span>&nbsp;</span>Wrong File Type!</div>'; } ?>
      <?php if(isset($_GET['nofile'])) { echo '<div class="message warning"><span>&nbsp;</span>Please add a file.</div>'; } ?>
      <?php if(isset($_GET['empty'])) { echo '<div class="message warning"><span>&nbsp;</span>An error occurred while importing your data.</div>'; } ?>
      <?php if ( isset( $message ) ) { echo $message; } ?>
    </div>
    
    <div id="content">
      <div id="options_tabs">
        <ul class="options_tabs">
          <li><a href="#tree_settings">Create</a><span></span></li>
          <li><a href="#import_options">Import</a><span></span></li>
          <li><a href="#export_options">Export</a><span></span></li>
        </ul>
        
        <div id="tree_settings" class="block has-table">
          <form method="post" id="theme-options" class="option-tree-settings">
            <h2>Create</h2>
            <h3>Create the Theme Options</h3>
            <p><strong style="color:red;">WARNING!</strong> If you're unsure or not completely positive that you should be editing these options, you should read the <a href="<?php echo admin_url().'admin.php?page=option_tree_docs'; ?>"><strong>Documentation</strong></a> first.</p>
            <p>You can create as many Theme Options as your project requires and use them how you see fit. When you add an option here, it will be available on the <a href="<?php echo admin_url().'admin.php?page=option_tree'; ?>"><strong>Theme Options</strong></a> page for use in your theme. To break your Theme Options into sections, add a "<strong>heading</strong>" option type and a new navigation menu item will be created.</p>
            <p>All of the Theme Options can be sorted and rearranged to your liking with <strong>Drag &amp; Drop</strong>. Don't worry about the order in which you create your options, you can always reorder them.</p>
            <table cellspacing="0">
              <thead>
                <tr>
                  <th class="col-title">Title</th>
                  <th class="col-key">Key</th>
                  <th class="col-type">Type</th>
                  <th class="col-edit"><a href="javascript:;" class="add-option">Add Option</a></th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th class="col-title">Title</th>
                  <th class="col-key">Key</th>
                  <th class="col-type">Type</th>
                  <th class="col-edit"><a href="javascript:;" class="add-option">Add Option</a></th>
                </tr>
              </tfoot>
              <tbody id="framework-settings" class="dragable">
              <?php 
                $count = 0;
                foreach ( $ot_array as $value ) {
                $count++;
                $heading = ($value->item_type == 'heading') ? true : false; ?>
          			<tr id="option-<?php echo $value->id; ?>" class="<?php echo ($heading) ? 'col-heading ' : ''; ?><?php echo ($count==1) ? 'nodrag nodrop' : ''; ?>">
          		    <td class="col-title"<?php echo ($heading) ? ' colspan="3"' : ''; ?>><?php echo (!$heading) ? '&ndash; ' : ''; ?><?php echo htmlspecialchars_decode( $value->item_title ); ?></td>
          				<td class="col-key<?php echo ($heading) ? ' hide' : ''; ?>"><?php echo htmlspecialchars(stripslashes($value->item_id)); ?></td>
          				<td class="col-type<?php echo ($heading) ? ' hide' : ''; ?>"><?php echo $value->item_type; ?></td>
          				<td class="col-edit">
          				  <a href="javascript:;" class="edit-inline">Edit</a>
          				  <a href="javascript:;" class="delete-inline">Delete</a>
          				  <div class="hidden item-data" id="inline_<?php echo $value->id; ?>">
                      <div class="item_title"><?php echo htmlspecialchars_decode( $value->item_title ); ?></div>
                      <div class="item_id"><?php echo $value->item_id; ?></div>
                      <div class="item_type"><?php echo $value->item_type; ?></div>
                      <div class="item_desc"><?php echo esc_html(stripslashes($value->item_desc)); ?></div>
                      <div class="item_options"><?php echo esc_html(stripslashes($value->item_options)); ?></div>
                    </div>
          				</td>
          			</tr>
              <?php } ?>
              </tbody>
            </table>
            <table style="display:none">
              <tbody id="framework-settings-edit">
          			<tr id="inline-edit" class="inline-edit-option nodrop nodrag">
          				<td colspan="4">
          				  <div class="option option-title">
          				    <div class="section">
                        <div class="element">
                          <input type="text" name="item_title" class="item_title" value="" />
                        </div>
                        <div class="description">
                          <strong>Title:</strong> Displayed on the Theme Options page.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-id">
          				    <div class="section">
                        <div class="element">
                          <input type="text" name="item_id" class="item_id" value="" />
                        </div>
                        <div class="description">
                          <strong>Option Key:</strong> Unique alphanumeric key, underscores are acceptable.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-type">
          				    <div class="section">
                        <div class="element">
                          <div class="select_wrapper">
                            <select name="item_type" class="select item_type">
                            <?php
                            $types = array(
                              'heading'       => 'Heading',
                              'textblock'     => 'Textblock',
                              'input'         => 'Input',
                              'checkbox'      => 'Checkbox',
                              'radio'         => 'Radio',
                              'select'        => 'Select',
                              'textarea'      => 'Textarea',
                              'upload'        => 'Upload',
                              'colorpicker'   => 'Colorpicker',
                              'post'          => 'Post',
                              'posts'         => 'Posts',
                              'page'          => 'Page',
                              'pages'         => 'Pages',
                              'category'      => 'Category',
                              'categories'    => 'Categories',
                              'tag'           => 'Tag',
                              'tags'          => 'Tags',
                              'custom_post'   => 'Custom Post',
                              'custom_posts'  => 'Custom Posts',
                              'measurement'   => 'Measurement',
                              'slider'        => 'Slider'
                            );
                            foreach ( $types as $key => $value ) 
                            {
                              echo '<option value="'.$key.'">'.$value.'</option>';
                            } 
                            ?>
                            </select>
                          </div>
                        </div>
                        <div class="description">
                          <strong>Option Type:</strong> Choose one of the supported option types.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-desc">
          				    <div class="section">
                        <div class="element">
                          <textarea name="item_desc" class="item_desc" rows="8"></textarea>
                        </div>
                        <div class="description">
                          <strong>Description:</strong> Enter a detailed description of the option for end users to read. However, if the option type is a <strong>Textblock</strong>, enter the text you want to display (HTML is allowed).
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-options">
          				    <div class="section">
                        <div class="element">
                          <input type="text" name="item_options" class="item_options" value="" />
                        </div>
                        <div class="description">
                          <span class="regular"><strong>Options:</strong> Enter a comma separated list of options. For example, you could have "One,Two,Three" or just a single value like "Yes" for a checkbox.</span>
                          <span class="alternative" style="display:none;">&nbsp;</span>
                        </div>
                      </div>
          				  </div>
          				  <?php wp_nonce_field( 'inlineeditnonce', '_ajax_nonce', false ); ?>
          				  <div class="inline-edit-save">
          				    <a href="#" class="cancel button-framework reset">Cancel</a> 
          				    <a href="#" class="save button-framework">Save</a>
          				  </div>
          				</td>
          		  </tr>
          		  <tr id="inline-add">
          		    <td class="col-title"></td>
          				<td class="col-key"></td>
          				<td class="col-type"></td>
          				<td class="col-edit">
          				  <a href="#" class="edit-inline">Edit</a>
          				  <a href="#" class="delete-inline">Delete</a>
          				  <div class="hidden item-data">
                      <div class="item_title"></div>
                      <div class="item_id"></div>
                      <div class="item_type"></div>
                      <div class="item_desc"></div>
                      <div class="item_options"></div>
                    </div>
          				</td>
          		  </tr>
              </tbody>
            </table>
          </form> 
        </div>
        
        <div id="import_options" class="block">
          <h2>Import</h2>
          
          <form method="post" action="admin.php?page=option_tree_settings&action=upload" enctype="multipart/form-data" id="upload-xml">
            <input type="hidden" name="action" value="upload" />
            <div class="option option-upload">
              <h3>Theme Options XML</h3>
              <div class="section desc-text">
                <p>If you were given a Theme Options XML file with a premium or free theme, locate it on your hard drive and upload that file here. It's also possible that you did all your development on a local server and just need to get your live site in working condition from your own exported settings file. Either way, once you have the proper file in the input field below, click the "Import XML" button.</p>
                  <p>However, if you're a theme developer activating the plugin for the first time, you can get started by clicking the Developers Settings link to the left.</p>
                <input name="import" type="file" class="file" />
                <input type="submit" value="<?php _e('Import XML') ?>" class="ob_button right" />
              </div>
            </div>
          </form>
          
          <form method="post" id="import-data">
            <div class="option option-input">
              <h3>Theme Options Data</h3>
              <div class="section">
                <div class="element">
                  <textarea name="import_options" rows="8" id="import_options" class="import_options"></textarea>
                </div>
                <div class="description">
                  <p>Only after you've imported the Theme Options XML file should you try and update your Theme Options Data.</p>
                  <p>To import the values of your theme options copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the "Import Data" button below.</p>
                </div>
              </div>
              <input type="submit" value="<?php _e('Import Data') ?>" class="ob_button right import-data" />
            </div>
            <?php wp_nonce_field( '_import_data', '_ajax_nonce', false ); ?>
          </form>
          
        </div>
        
        <div id="export_options" class="block">
          <h2>Export</h2>
          <form method="post" action="admin.php?page=option_tree_settings&action=export">
            <div class="option option-input">
              <h3>Theme Options XML</h3>
              <div class="section desc-text">
                <p>Click the Export XML button to export your Theme Options. A dialogue box will open and ask you what you want to do with the XML file, save it somewhere you can easily find it later.</p>
                <input type="submit" value="Export XML" class="ob_button right" />
              </div>
            </div>
          </form>
          <div class="option option-input">
            <h3>Theme Options Data</h3>
            <div class="section">
              <div class="element">
                <textarea name="export_options" rows="8"><?php echo base64_encode(serialize($settings)); ?></textarea>
              </div>
              <div class="description">
                Export your saved Theme Options data by highlighting this text and doing a copy/paste into a blank .txt file. Then save the file for importing into another install of WordPress later. Alternatively, you could just paste it into the <code>OptionTree->Settings->Import</code> on another web site.
              </div>
            </div>
          </div>
        </div>
        
        <br class="clear" />
      </div>
    </div>
    <div class="info bottom">
      <input type="hidden" name="action" value="save" />
    </div>   
  </div>

</div>
<!-- [END] framework_wrap -->