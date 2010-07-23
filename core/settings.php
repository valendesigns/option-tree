<?php include(THIS_PLUGIN_DIR.'/core/header.php'); ?>
  
  <div id="content_wrap">
  
    <div class="info top-info">
    </div>
    
    <div class="ajax-message<?php if (isset($_GET['saved']) || isset($_GET['reset']) || isset($_GET['error']) || isset($_GET['nofile']) || isset($_GET['empty'])) { echo ' show'; } ?>">
      <?php if(isset($_GET['saved'])) { echo '<div class="message">Settings Updated</div>'; } ?>
      <?php if(isset($_GET['reset'])) { echo '<div class="message">Settings Reset</div>'; } ?>
      <?php if(isset($_GET['error'])) { echo '<div class="message warning">Wrong File Type!</div>'; } ?>
      <?php if(isset($_GET['nofile'])) { echo '<div class="message warning">Please add a file.</div>'; } ?>
      <?php if(isset($_GET['empty'])) { echo '<div class="message warning">You have nothing to import!</div>'; } ?>
    </div>
    
    <div id="content">
      <div id="options_tabs">
        <ul class="options_tabs">
          <li><a href="#tree_settings">Developer Settings</a><span></span></li>
          <li><a href="#export_options">Export</a><span></span></li>
        </ul>
        
        <div id="tree_settings" class="block has-table">
          <form method="post" id="theme-options">
            <h2>Developer Settings</h2>
            <h3>Create Your Theme Options</h3>
            <p><strong>WARNING!</strong> If you're unsure or not completely positive that you should be editing these options, DONT!</p>
            <p><strong>Theme Options</strong> are the roots of OptionTree, it's what makes this plugin so unique. As a theme developer, you can create as many Theme Options as your project requires and use them how you see fit.</p>
            <p>When you add an option here, it will be available on the Theme Options page for use in your theme. To divide your Theme Options into sections, add a "<strong>heading</strong>" option type and a new navigation menu item will be created.</p>
            <p>All the Theme Options can be sorted and rearranged to your liking with <strong>Drag &amp; Drop</strong>. Don't worry in what order you create your options, you can always reorder them.</p>
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
                foreach ( $option_array as $value ) {
                $count++;
                $heading = ($value->item_type == 'heading') ? true : false; ?>
          			<tr id="option-<?php echo $value->id; ?>" class="<?php echo ($heading) ? 'col-heading ' : ''; ?><?php echo ($count==1) ? 'nodrag nodrop' : ''; ?>">
          		    <td class="col-title"<?php echo ($heading) ? ' colspan="3"' : ''; ?>><?php echo (!$heading) ? '&ndash; ' : ''; ?><?php echo htmlspecialchars(stripslashes($value->item_title)); ?></td>
          				<td class="col-key<?php echo ($heading) ? ' hide' : ''; ?>"><?php echo htmlspecialchars(stripslashes($value->item_id)); ?></td>
          				<td class="col-type<?php echo ($heading) ? ' hide' : ''; ?>"><?php echo $value->item_type; ?></td>
          				<td class="col-edit">
          				  <a href="javascript:;" class="edit-inline">Edit</a>
          				  <a href="javascript:;" class="delete-inline">Delete</a>
          				  <div class="hidden item-data" id="inline_<?php echo $value->id; ?>">
                      <div class="item_title"><?php echo htmlspecialchars(stripslashes($value->item_title)); ?></div>
                      <div class="item_id"><?php echo htmlspecialchars(stripslashes($value->item_id)); ?></div>
                      <div class="item_type"><?php echo $value->item_type; ?></div>
                      <div class="item_desc"><?php echo esc_html(stripslashes($value->item_desc)); ?></div>
                      <div class="item_std"><?php echo esc_html(stripslashes($value->item_std)); ?></div>
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
                          <strong>Title:</strong> Enter the option's title or name to be displayed on the Theme Option page.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-id">
          				    <div class="section">
                        <div class="element">
                          <input type="text" name="item_id" class="item_id" value="" />
                        </div>
                        <div class="description">
                          <strong>Option Key:</strong> Enter the option key in lowercase alphanumeric, underscores are expectable. For example 'test' or 'test_option' is a valid key. All option keys must be unique.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-type">
          				    <div class="section">
                        <div class="element">
                          <div class="select_wrapper">
                            <select name="item_type" class="select item_type">
                            <?php
                            $types = array("heading", "input", "checkbox", "radio", "select", "textarea", "upload", "colorpicker", "textblock");
                            foreach ($types as $type) {
                              echo '<option value="'.$type.'">'.$type.'</option>';
                            } 
                            ?>
                            </select>
                          </div>
                        </div>
                        <div class="description">
                          <strong>Option Type:</strong> Choose a supported option type from this list.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-desc">
          				    <div class="section">
                        <div class="element">
                          <textarea name="item_desc" class="item_desc" rows="8"></textarea>
                        </div>
                        <div class="description">
                          <strong>Description:</strong> Enter a detailed description that will display to the right of the theme option for end users. Exactly like this text display for you now. However, if the option type is a textblock, just enter the text you want to display.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-std">
          				    <div class="section">
                        <div class="element">
                          <input type="text" name="item_std" class="item_std" value="" />
                        </div>
                        <div class="description">
                          <strong>Default:</strong> Enter the option's default setting. Leave this blank if you're not sure how it works.
                        </div>
                      </div>
          				  </div>
          				  <div class="option option-options">
          				    <div class="section">
                        <div class="element">
                          <input type="text" name="item_options" class="item_options" value="" />
                        </div>
                        <div class="description">
                          <strong>Options:</strong> Enter a comma separated list of options. For example, if you have chosen a "<strong>Checkbox</strong>" as your option type you could have "Yes,No,Maybe" as your options.
                        </div>
                      </div>
          				  </div>
          				  <?php wp_nonce_field( 'inlineeditnonce', '_inline_edit', false ); ?>
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
                      <div class="item_std"></div>
                      <div class="item_options"></div>
                    </div>
          				</td>
          		  </tr>
              </tbody>
            </table>
          </form> 
        </div>
        
        <div id="export_options" class="block">
          <h2>Export</h2>
          <form method="post" action="admin.php?page=option_tree_settings&action=export">
            <div class="option option-input">
              <h3>Theme Options XML</h3>
              <div class="section desc-text">
                <p>Export Theme Options XML file - better directions coming.</p>
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
                Export Theme Options Data - better directions coming.
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

<?php include(THIS_PLUGIN_DIR.'/core/footer.php'); ?>