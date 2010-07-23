<?php include(THIS_PLUGIN_DIR.'/core/header.php'); ?>
  
  <div id="content_wrap">
    
    <div class="info top-info">
    </div>
    
    <div class="ajax-message<?php if (isset($_GET['xml']) || isset($_GET['data']) || isset($_GET['error']) || isset($_GET['nofile']) || isset($_GET['empty'])) { echo ' show'; } ?>">
      <?php if(isset($_GET['xml'])) { echo '<div class="message">Theme Options XML Updated</div>'; } ?>
      <?php if(isset($_GET['data'])) { echo '<div class="message">Theme Options Data Updated</div>'; } ?>
      <?php if(isset($_GET['error'])) { echo '<div class="message warning">Wrong File Type!</div>'; } ?>
      <?php if(isset($_GET['nofile'])) { echo '<div class="message warning">Please add a file.</div>'; } ?>
      <?php if(isset($_GET['empty'])) { echo '<div class="message warning">You have nothing to import!</div>'; } ?>
      <?php if(isset($_GET['xml'])) { echo '<script type="text/javascript">jQuery(document).ready(function ($) { $(".import_options").focus(); });</script>'; } ?>
    </div>
    
    <div id="content">
      <div id="options_tabs">
        <ul class="options_tabs">
          <li><a href="#theme_setup">Theme Setup</a><span></span></li>
        </ul>
        
        <div id="theme_setup" class="block">
          <h2>Theme Setup</h2>
          <form method="post" enctype="multipart/form-data" id="upload-xml">
            <input type="hidden" name="action" value="upload" />
            <div class="option option-upload">
              <h3>Theme Options XML</h3>
              <div class="section desc-text">
                <p>If you were given an Theme Options XML file with a premium or free theme, locate it on your hard drive and upload that file here. It's also possible that you did all your development on a local server and just need to get your live site in working condition from your own exported settings file. Either way, once you have the proper file in the upload input below, press the "Import XML" button.</p>
                  <p>However, if you're a theme developer activating the plugin for the first time, you can get started on the <a href="admin.php?page=option_tree_settings">Developers Settings</a> page.</p>
                <input name="import" type="file" class="file" />
                <input type="submit" value="<?php _e('Import XML') ?>" class="ob_button right" />
              </div>
            </div>
          </form>
          <form method="post">
            <input type="hidden" name="action" value="import" />
            <div class="option option-input">
              <h3>Theme Options Data</h3>
              <div class="section">
                <div class="element">
                  <textarea name="import_options" rows="8" class="import_options"></textarea>
                </div>
                <div class="description">
                  <p>Only after you've imported the Theme Options XML file should you try and update your Theme Options Data.</p>
                  <p>To import the values of your theme options copy and paste what appears to be a random string of alpha numeric characters into this textarea and press the "Import Data" button below.</p>
                </div>
              </div>
              <input type="submit" value="<?php _e('Import Data') ?>" class="ob_button right" />
            </div>
          </form>
        </div>
  
        <br class="clear" />
      </div>
    </div>
    <div class="info bottom">
    </div>
      
  </div>

<?php include(THIS_PLUGIN_DIR.'/core/footer.php'); ?>