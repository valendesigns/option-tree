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
  
    <div class="info top-info"></div>

    <div id="content">
      <div id="options_tabs" class="docs">
      
        <ul class="options_tabs">
          <li><a href="#general">Usage &amp; Examples</a><span></span></li>
          <li><a href="#option_types">Option Types</a><span></span></li>
          <li><a href="#settings">Creating Options</a><span></span></li>
          <li><a href="#layouts">Adding Layouts</a><span></span></li>
          <li><a href="#integration">Theme Integration</a><span></span></li>
        </ul>
        
        <div id="general" class="block">
          <h2>Usage &amp; Examples</h2>
          <h3>Function Reference/get_option_tree</h3>
          
          <h3>Description</h3>
          <p>Displays or returns a value from the 'option_tree' array.</p>
          
          <h3>Usage</h3>
          <p><code>&lt;?php get_option_tree( $item_id, $options, $echo, $is_array, $offset ); ?&gt;</code></p>
          
          <h3>Default Usage</h3>
          <pre><code>get_option_tree( 
  'item_id'   => '',
  'options'   => '',
  'echo'      => 'false',
  'is_array'  => 'false',
  'offset'    => -1,
);</code></pre>
          
          <h3>Parameters</h3>
          <p>
            <code><strong>$item_id</strong></code> 
            <br />&nbsp;&nbsp;(<em>string</em>) (<em>required</em>) Enter a unique Option Key to get a returned value or array.
            <br />&nbsp;&nbsp;&nbsp;&nbsp;Default: <em>None</em>
          </p>
          <p>
            <code><strong>$options</strong></code> 
            <br />&nbsp;&nbsp;(<em>array</em>) (<em>optional</em>) Used to cut down on database queries in template files.
            <br />&nbsp;&nbsp;&nbsp;&nbsp;Default: <em>None</em>
          </p>
          <p>
            <code><strong>$echo</strong></code>
            <br />&nbsp;&nbsp;(<em>boolean</em>) (<em>optional</em>) Echo the output. 
            <br />&nbsp;&nbsp;&nbsp;&nbsp;Default: FALSE
          </p>
          <p>
            <code><strong>$is_array</strong></code> 
            <br />&nbsp;&nbsp;(<em>boolean</em>) (<em>optional</em>) Used to indicate the $item_id is an array of values.
            <br />&nbsp;&nbsp;&nbsp;&nbsp;Default: FALSE
          </p>
          <p>
            <code><strong>$offset</strong></code> 
            <br />&nbsp;&nbsp;(<em>integer</em>) (<em>optional</em>) Numeric offset key for the $item_id array, -1 will return all values (an array starts at 0).
            <br />&nbsp;&nbsp;&nbsp;&nbsp;Default: -1
          </p>

          
          <h3>Examples</h3>
          <p>
            This example assigns the value of the <code>get_option('option_tree')</code> array to the variable <code>$theme_options</code> for use in PHP. You would then add this code to the top of your header.php file and use that variable as the <code>$options</code> variable in the <code>get_option_tree()</code> function. This helps to reduce database queries anytime you want to request multiple theme options in a template files. This is optional, but may help speed up things up.
            <pre><code>&lt?php $theme_options = get_option('option_tree'); ?&gt;</code></pre>
          </p>
          
          <p>
            This example returns the $item_id value.
            <pre><code>&lt;?php 
if ( function_exists( 'get_option_tree' ) ) {
  get_option_tree( 'test_option' );
}
?&gt;</code></pre>
          </p>
          
          <p>
            These examples will echo the $item_id value.
            <pre><code>&lt;?php 
if ( function_exists( 'get_option_tree') ) {
  get_option_tree( 'test_option', '', true );
}
?&gt;

&lt?php 
if ( function_exists( 'get_option_tree') ) {
  echo get_option_tree( 'test_option' );
}
?&gt;</code></pre>
          </p>
          
          <p>
            This example will echo the value of $item_id by grabbing the first offset key in the array.
            <pre><code>&lt;?php 
if ( function_exists( 'get_option_tree' ) ) {
  get_option_tree( 'test_option', '', true, true, 0 );
}
?&gt;</code></pre>
          </p>

          <p>
            This example assigns the value of $item_id to the variable $ids for use in PHP. As well, it uses the <code>$theme_options</code> variable I set in the first example above in my header.php file to reduce database queries. It then loops through all the array items and echos an unordered list of page links (navigation). 
            <pre><code>&lt?php
if ( function_exists( 'get_option_tree' ) ) {
  // set an array of page id's
  $ids = get_option_tree( 'list_of_page_ids', $theme_options, false, true, -1 );

  // loop through id's & echo custom navigation
  echo '&lt;ul&gt;';
  foreach( $ids as $id ) {
    echo '&lt;li&gt;&lt;a href="'.get_permalink($id).'"&gt;'.get_the_title($id).'&lt;/a&gt;&lt;/li&gt;';
  }
  echo '&lt;/ul&gt;';
}
?&gt;</code></pre>
            OR a more WordPress version would be:<br /><br />
            <pre><code>&lt;?php
if ( function_exists( 'get_option_tree' ) ) {
  // set an array of page id's
  $ids = get_option_tree( 'list_of_page_ids', $theme_options, false, true, -1 );

  // echo custom navigation using wp_list_pages()
  echo '&lt;ul&gt;';
  wp_list_pages(
    array(
      'include' => $ids,
      'title_li' => ''
    )
  );
  echo '&lt;/ul&gt;';
}
?&gt;</code></pre>
          </p>
          
          <p>
            This example explains how to use the Measurement post type in your PHP files. The Measurement post type is an array of key/value pairs where the first key's value is the value of the measurement and the second key's value is the unit of measurement.
          </p>
          <pre><code>&lt;?php
if ( function_exists( 'get_option_tree' ) ) {
  $measurement = get_option_tree( 'measurement_type_id', $theme_options, false, true );
  echo $measurement[0].$measurement[1];
}
?&gt;</code></pre>
          
          <p>
            <strong>OR:</strong>
          </p>
          <pre><code>&lt;?php
if ( function_exists( 'get_option_tree' ) ) {
  $measurement = get_option_tree( 'measurement_type_id', $theme_options, false, true, 0 );
  $unit        = get_option_tree( 'measurement_type_id', $theme_options, false, true, 1 );
  echo $measurement.$unit;
}
?&gt;</code></pre>
          
          <p>
            <strong>OR:</strong>
          </p>
          <pre><code>&lt;?php
if ( function_exists( 'get_option_tree' ) ) {
  $measurement = get_option_tree( 'measurement_type_id', $theme_options, false, true, -1 );
  echo implode( '', $measurement );
}
?&gt;</code></pre>
          
          
          <p>
          This example displays a very basic slider loop.
          <pre><code>&lt;?php
if ( function_exists( 'get_option_tree' ) ) {
  $slides = get_option_tree( 'my_slider', $option_tree, false, true, -1 );
  foreach( $slides as $slide ) {
    echo '
    &lt;li&gt;
      &lt;a href="'.$slide['link'].'"&gt;&lt;img src="'.$slide['image'].'" alt="'.$slide['title'].'" /&gt;&lt;/a&gt;
      &lt;div class="description">'.$slide['description'].'&lt;/div&gt;
    &lt;/li&gt;';
  }
}
?&gt;</code></pre>
          </p>

        </div>
        
        <div id="option_types" class="block">
          <h2>Option Types</h2>
          <h3>Overview of available Option Types.</h3>
          
          <p>
            <strong>Heading</strong>:<br />
            Used only in the WordPress Admin area to logical separate Theme Options into sections for easy editing. A Heading will create a navigation menu item on the <a href="<?php echo admin_url().'admin.php?page=option_tree'; ?>"><strong>Theme Options</strong></a> page. You would NEVER use this in your themes template files.
          </p>
          
          <p>
            <strong>Textblock</strong>:<br />
            Used only in the WordPress Admin area. A Textblock will allow you to create &amp; display HTML on your <a href="<?php echo admin_url().'admin.php?page=option_tree'; ?>"><strong>Theme Options</strong></a> page. You can then use the Textblock to add a more detailed set of instruction on how the options are used in your theme. You would NEVER use this in your themes template files.
          </p>
          
          <p>
            <strong>Input</strong>:<br />
            The Input option type would be used to save a simple string value. Maybe a link to feedburner, your Twitter username, or Google Analytics ID. Any optional or required text that is of reasonably short character length.
          </p>
          
          <p>
            <strong>Checkbox</strong>:<br />
            A Checkbox option type could ask a question. For example, "Do you want to activate asynchronous Google analytics?" would be a simple one checkbox question. You could have more complex usages but the idea is that you can easily grab the value of the checkbox and use it in you theme. In this situation you would test if the checkbox has a value and execute a block of code if it does and do nothing if it doesn't.
          </p>
          
          <p>
            <strong>Radio</strong>:<br />
            A Radio option type could ask a question. For example, "Do you want to activate the custom navigation?" could require a yes or no answer with a radio option. In this situation you would test if the radio has a value of 'yes' and execute a block of code, or if it's 'no' execute a different block of code. Since a radio has to be one or the other nothing will execute if you have not saved the options yet.
          </p>
          
          <p>
            <strong>Select</strong>:<br />
            Could use the Select option type to list different theme styles or choose any other setting that would be chosen from a select option list.
          </p>
          
          <p>
            <strong>Textarea</strong>:<br />
            With the Textarea option type users can add custom code or text for use in the theme.
          </p>
          
          <p>
            <strong>Upload</strong>:<br />
            The Upload option type is used to upload any WordPress supported media. After uploading, users are required to press the "<strong style="color:red;">Insert into Post</strong>" or "<strong style="color:red;">Add to OptionTree</strong>" button in order to populate the input with the URI of that media. There is one caveat of this feature. If you import the theme options and have uploaded media on one site the old URI will not reflect the URI of your new site. You'll have to re-upload or FTP any media to your new server and change the URIs if necessary.
          </p>
          
          <p>
            <strong>Colorpicker</strong>:<br />
            A Colorpicker is a very self explanatory feature that saves hex HTML color codes. Use it to set/change the color of something in your theme.
          </p>
          
          <p>
            <strong>Post</strong>:<br />
            The Post option type is an option select list of post IDs. It will return a single post ID for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Posts</strong>:<br />
            The Posts option type is a checkbox list of post IDs. It will return an array of multiple post IDs for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Page</strong>:<br />
            The Page option type is an option select list of page IDs. It will return a single page ID for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Pages</strong>:<br />
            The Pages option type is a checkbox list of page IDs. It will return an array of multiple page IDs for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Category</strong>:<br />
            The Category type is an option select list of category IDs. It will return a single category ID for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Categories</strong>:<br />
            The Categories option type is a checkbox list of category IDs. It will return an array of multiple category IDs for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Tag</strong>:<br />
            The Tag option type is an option select list of tag IDs. It will return a single tag ID for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Tags</strong>:<br />
            The Tags option type is a checkbox list of tag IDs. It will return an array of multiple tag IDs for use in a custom function or loop.
          </p>
          
          <p>
            <strong>Custom Post</strong>:<br />
            The Custom Post option type is an option select list of IDs from any available wordpress post type or custom post type. It will return a single ID for use in a custom function or loop. Custom Post requires the post_type you are querying when created.
          </p>
          
          <p>
            <strong>Custom Posts</strong>:<br />
            The Custom Posts option type is a checkbox list of IDs from any available wordpress post type or custom post type. It will return an array of multiple IDs for use in a custom function or loop. Custom Posts requires the post_type you are querying when created.
          </p>
          
          <p>
            <strong>Measurement</strong>:<br />
            The Measurement option type is a mix of input and select fields. The text input excepts a value and the select list lets you choose the unit of measurement to add to that value. Currently the default units are px, %, em, pt. However, you can change them with the<code>measurement_unit_types</code> filter.
          </p>
          
          <p>
            <strong>Filter to completely change the units in the Measurement option type</strong><br />
            Added to functions.php
          </p>
          
          <pre><code>add_filter( 'measurement_unit_types', 'custom_unit_types' );

function custom_unit_types() {
  $array = array(
    'in' => 'inches',
    'ft' => 'feet'
  );
  return $array;
}</code></pre>

          <p>
            <strong>Filter to add new units in the Measurement option type</strong><br />
            Added to functions.php
          </p>
          
          <pre><code>add_filter( 'measurement_unit_types', 'custom_unit_types' );

function custom_unit_types($array) {
  $array['in'] = 'inches';
  $array['ft'] = 'feet';
  return $array;
}</code></pre>
          
          <p>
            <strong>Slider</strong>:<br />
            The Slider option type is a mix of elements that you can change with the<code>image_slider_fields</code> filter. The currently supported element types are text, textarea, & hidden. In the future there will be more input types. As well, the current inputs are order, title, image, link, & description. Order & title are required fields. However, the other three can be altered using the filter above.<br />
          
          <p>
            <strong>Filter to completely change the input fields in the Slider option type</strong><br />
            Added to functions.php
          </p>
          <pre><code>add_filter( 'image_slider_fields', 'new_slider_fields', 10, 2 );

function new_slider_fields( $image_slider_fields, $id ) {
  if ( $id == 'my_slider_id' ) {
    $image_slider_fields = array(
      array(
        'name'  => 'image',
        'type'  => 'text',
        'label' => 'Post Image URL',
        'class' => ''
      ),
      array(
        'name'  => 'link',
        'type'  => 'text',
        'label' => 'Post URL',
        'class' => ''
      ),
      array(
        'name'  => 'description',
        'type'  => 'textarea',
        'label' => 'Post Description',
        'class' => ''
      )
    );
  }
  return $image_slider_fields;
}</code></pre>

          <p>
            <strong>Filter to add a new field to the Slider option type</strong><br />
            Added to functions.php
          </p>
          <pre><code>add_filter( 'image_slider_fields', 'new_slider_fields', 10, 2 );

function new_slider_fields( $image_slider_fields, $id ) {
  if ( $id == 'my_slider_id' ) {
    $image_slider_fields[] =
      array(
        'name'  => 'awesome_field',
        'type'  => 'text',
        'label' => 'Write Something Awesome',
        'class' => ''
      );
  }
  return $image_slider_fields;
}</code></pre>

          <p>
            <strong>CSS</strong>:<br />
            The CSS option type is a simple easy way to add dynamic CSS to your theme from within OptionTree. It will create a file named <code>dynamic.css</code> at the root level of your theme (if it doesn't exist) and update the CSS in that file every time you save your theme options.
          </p>
          
          <p>
            <strong>An few examples of the CSS option type</strong><br />
            This assumes you have an option with the ID of <code>custom_background_css</code> which will display the saved values for that option.
          </p>
          
          <p><strong>Input:</strong></p>
          <pre><code>body {
  {{custom_background_css}}
  background-color: {{custom_background_css|background-color}};
}
</code></pre>
          
          <p>
            <strong>Output:</strong><br />
            The values saved in the database will replace the text placeholders below.
          </p>
          <pre><code>body {
  background: color image repeat attachment position;
  background-color: color;
}
</code></pre>
          
          <p>
            <strong>Background</strong>:<br />
            The Background option type is for adding background styles to your theme dynamically via the CSS option type above or manually with <code>get_option_tree()</code>.
          </p>
          
          <p>
            <strong>Typography</strong>:<br />
            The Typography option type is for adding typography styles to your theme dynamically via the CSS option type above or manually with <code>get_option_tree()</code>.
          </p>
                                     
        </div>
        
        <div id="settings" class="block">
          <h2>Creating Options</h2>
          <h3>Overview of available Theme Option fields.</h3>
          
          <p>
            <strong>Title</strong>:<br />
            The Title field should be a short but descriptive block of text 100 characters or less with no HTML.
          </p>
          
          <p>
            <strong>Option Key</strong>:<br />
            The Option Key field is a unique alphanumeric key used to differentiate each theme option (underscores are acceptable). Also, the plugin will lowercase any text you write in this field and bridge all spaces with an underscore automatically.
          </p>
          
          <p style="padding-bottom:5px">
            <strong>Option Type</strong>:<br />
            You are required to choose one of the supported option types. They are:
          </p>
          <ul class="doc_list">
            <?php
            $types = apply_filters( 'option_tree_option_types', array(
              'heading'           => 'Heading',
              'background'	      => 'Background',
              'category'          => 'Category',
              'categories'        => 'Categories',
              'checkbox'          => 'Checkbox',
              'colorpicker'       => 'Colorpicker',
              'css'	              => 'CSS',
              'custom_post'       => 'Custom Post',
              'custom_posts'      => 'Custom Posts',                     
              'input'             => 'Input',
              'measurement'       => 'Measurement',
              'page'              => 'Page',
              'pages'             => 'Pages',
              'post'              => 'Post',
              'posts'             => 'Posts',
              'radio'             => 'Radio',
              'select'            => 'Select',
              'slider'            => 'Slider',
              'tag'               => 'Tag',
              'tags'              => 'Tags',
              'textarea'          => 'Textarea',
              'textblock'         => 'Textblock',
              'typography'	      => 'Typography',
              'upload'            => 'Upload'
              ) );
            foreach ( $types as $key => $value ) {
              echo '<li>'.$value.'</li>';
            } 
            ?>
          </ul>
          
          <p>
            <strong>Description</strong>:<br />
            Enter a detailed description of the option for end users to read. However, if the option type is a Textblock, enter the text you want to display (HTML is allowed).
          </p>

          <p>
            <strong>Options</strong>:<br />
            Enter a comma separated list of options in this field. For example, you could have "One,Two,Three" or just a single value like "Yes" for a checkbox.
          </p>
          
          <p>
            <strong>Row Count</strong>:<br />
            Enter a numeric value for the number of rows in your textarea.
          </p>
          
          <p style="padding-bottom:5px">
            <strong>Post Type</strong>:<br />
            Enter your custom post_type. These are the default post types available with WordPress. You can also add your own custom post_type.
          </p>
          <ul class="doc_list">
            <li>post</li>
            <li>page</li>
            <li>attachment</li>
            <li>any</li>
          </ul>
          
        </div>
        
        <div id="layouts" class="block">
          <h2>Adding Layouts</h2>
          <h3>Overview on how Layouts work.</h3>
          
          <p>
            <strong>It's Super Simple</strong>:<br />
            Layouts make your theme awesome! Package them with different theme variations, with very little effort. I made adding a layout ridiculously easy, just follow these steps and youl'll be on your way to having a WordPress super theme.
          </p>
          
          <h3>For Developers</h3>
          
          <p style="padding-bottom:5px">
            <strong>Creating a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>Go to the <a href="admin.php?page=option_tree_settings#layout_options">Layouts</a> page.</li>
            <li>Enter a name for your layout in the text field and hit "Save Layout", you've created your first layout.</li>
            <li>Adding new layout is as easy as repeating the steps above.</li>
          </ul>
          
          <p style="padding-bottom:5px">
            <strong>Activating a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>Go to the <a href="admin.php?page=option_tree_settings#layout_options">Layouts</a> page.</li>
            <li>Click on the activate layout button in the actions list.</li>
          </ul>
          
          <p style="padding-bottom:5px">
            <strong>Deleting a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>Go to the <a href="admin.php?page=option_tree_settings#layout_options">Layouts</a> page.</li>
            <li>Click on the delete layout button in the actions list.</li>
          </ul>
          
          <p style="padding-bottom:5px">
            <strong>Editing the data of a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>Go to the <a href="admin.php?page=option_tree">Theme Options</a> page.</li>
            <li>Modify and save your theme options and the layout will be updated automatically.</li>
            <li><span style="color:red;">NOTE:</span> Saving theme options data will update the currently active layout, so before you start saving make sure you want to modify the current layout.</li>
            <li>If you want to edit a new layout, first create it then save your theme options.</li>
          </ul>
          
          <h3>End-Users Mode</h3>
          
          <p style="padding-bottom:5px">
            <strong>Creating a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>End-Users mode does not allow creating new layouts.</li>
          </ul>
          
          <p style="padding-bottom:5px">
            <strong>Activating a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>Go to the <a href="admin.php?page=option_tree">Theme Options</a> page.</li>
            <li>Choose a layout from the select list and click the "Activate Layout" button.</li>
          </ul>
          
          <p style="padding-bottom:5px">
            <strong>Deleting a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>End-Users mode does not allow deleting layouts.</li>
          </ul>
          
          <p style="padding-bottom:5px">
            <strong>Editing the data of a Layout</strong>:<br />
          </p>
          <ul class="doc_list">
            <li>Go to the <a href="admin.php?page=option_tree">Theme Options</a> page.</li>
            <li>Modify and save your theme options and the layout will be updated automatically.</li>
            <li><span style="color:red;">NOTE:</span> Saving theme options data will update the currently active layout, so before you start saving make sure you want to modify the current layout.</li>
          </ul>

        
        </div>
        
        <div id="integration" class="block">
          <h2>Theme Integration</h2>
          <h3>Overview on how Theme Integration works.</h3>
          
          <p>
            <strong>For Developers</strong>:<br />
            If you have a theme that you want to be OptionTree aware, keep reading.
          </p>
          
          <p>
            After you've built your theme and created all your options on the 'Settings' page and populated your options with default data on the 'Theme Options' page, you can now seamlessly integrate with OptionTree by including two files with your theme (only one is required). It's that simple!
          </p>
          
          <p style="padding-bottom:5px">
            <strong>Getting OtionTree aware</strong>:<br />
            <span style="color:red;">Follow these instructions.</span>
          </p>
          <ul class="doc_list">
            <li>Export your <a href="admin.php?page=option_tree_settings#export_options">Theme Options XML</a> file and save it somewhere you can get to it and once it's saved, rename it to '<strong>theme-options.xml</strong>'.</li>
            <li>Copy the <a href="admin.php?page=option_tree_settings#export_options">Theme Options Data</a> string to a new .txt file and name it '<strong>theme-options.txt</strong>' and save it where you can get to it.</li>
            <li>Copy the <a href="admin.php?page=option_tree_settings#export_options">Layouts</a> string to a new .txt file and name it '<strong>layouts.txt</strong>' and save it where you can get to it.</li>
            <li>Create a new directory in the root of your themes directory and name it '<strong>option-tree</strong>'.</li>
            <li>Move your '<strong>layouts.txt</strong>', '<strong>theme-options.txt</strong>', and '<strong>theme-options.xml</strong>' into the '<strong>option-tree</strong>' directory and when the plugin is activated it will populate the defaults from those files.</li>
          </ul>
          
          <p>
            <span style="color:red;">NOTE</span>: the .txt filee are optional and will only be used to populate the data on activation.<br />
            <span style="color:red;">NOTE</span>: The file names are important, so be sure you have named them correctly.<br />
            <span style="color:red;">NOTE</span>: If the theme options are not created properly or you can still see the OptionTree menu the .xml file is not readable to the server or you have the files in the wrong place.<br />
            <span style="color:red;">NOTE</span>: The plugin looks for the '<strong>option-tree</strong>' directory in the child themes root directory first, then the parent if you use a child/parent theme relationship. 
          </p>
        
        </div>
        
        <br class="clear" />
      </div>
    </div>
    <div class="info bottom"></div>   
  </div>

</div>
<!-- [END] framework_wrap -->