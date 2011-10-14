/**
 *
 * Author: Derek Herman 
 * URL: http://valendesigns.com
 * Email: derek@valendesigns.com
 *
 */
 
/**
 *
 * Delay
 *
 * Creates a way to delay events
 * Dependencies: jQuery
 *
 */
(function ($) {
  $.fn.delay = function(time,func){
    return this.each(function(){
      setTimeout(func,time);
    });
  };
})(jQuery);

/**
 *
 * Center AJAX
 *
 * Creates a way to center the AJAX message
 * Dependencies: jQuery
 *
 */
(function ($) {
  $.fn.ajaxMessage = function(html){
    if (html) {
      return $(this).animate({"top":( $(window).height() - $(this).height() ) / 2  - 200 + $(window).scrollTop() + "px"},100).fadeIn('fast').html(html).delay(3000, function(){$('.ajax-message').fadeOut()});
    } else {
      return $(this).animate({"top":( $(window).height() - $(this).height() ) / 2 - 200 + $(window).scrollTop() + "px"},100).fadeIn('fast').delay(3000, function(){$('.ajax-message').fadeOut()});
    }
  };
})(jQuery);

/**
 *
 * Style File
 *
 * Creates a way to cover file input with a better styled version
 * Dependencies: jQuery
 *
 */
(function ($) {
  styleFile = {
    init: function () {
      $('input.file').each(function(){
        var uploadbutton = '<input class="upload_file_button" type="button" value="Upload" />';
        $(this).wrap('<div class="file_wrap" />');
        $(this).addClass('file').css('opacity', 0); //set to invisible
        $(this).parent().append($('<div class="fake_file" />').append($('<input type="text" class="upload" />').attr('id',$(this).attr('id')+'_file')).append(uploadbutton));
       
        $(this).bind('change', function() {
          $('#'+$(this).attr('id')+'_file').val($(this).val());;
        });
        $(this).bind('mouseout', function() {
          $('#'+$(this).attr('id')+'_file').val($(this).val());;
        });
      }); 
    }
  };
  $(document).ready(function () {
    styleFile.init()
  })
})(jQuery);

/**
 *
 * Style Select
 *
 * Replace Select text
 * Dependencies: jQuery
 *
 */
(function ($) {
  styleSelect = {
    init: function () {
      $('.select_wrapper').each(function () {
        $(this).prepend('<span>' + $(this).find('.select option:selected').text() + '</span>');
      });
      $('.select').live('change', function () {
        $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
      });
      $('.select').bind($.browser.msie ? 'click' : 'change', function(event) {
        $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
      }); 
    }
  };
  $(document).ready(function () {
    styleSelect.init()
  })
})(jQuery);

/**
 *
 * Activate Tabs
 *
 * Tab style UI toggle
 * Dependencies: jQuery, jQuery UI Core, jQuery UI Tabs
 *
 */
(function ($) {
  activateTabs = {
    init: function () {
      // Activate
      $("#options_tabs").tabs();
      // Append Toggle Button
      $('.top-info').prepend('<a href="" class="toggle_tabs">Tabs</a>');
      // Toggle Tabs
      $('.toggle_tabs').toggle(function() {
        $("#options_tabs").tabs('destroy');
        $(this).addClass('off');
      }, function() {
        $("#options_tabs").tabs();
        $(this).removeClass('off');
      }); 
    }
  };
  $(document).ready(function () {
    activateTabs.init()
  })
})(jQuery);

/**
 *
 * Upload Option
 *
 * Allows window.send_to_editor to function properly using a private post_id
 * Dependencies: jQuery, Media Upload, Thickbox
 *
 */
(function ($) {
  uploadOption = {
    init: function () {
      var formfield,
          formID,
          btnContent = '',
          tbframe_interval;
      // On Click
      $('.upload_button').live("click", function () {
        formfield = $(this).prev('input').attr('id');
        formID = $(this).attr('rel');
        tb_show('', 'media-upload.php?post_id='+formID+'&type=image&amp;TB_iframe=1');
        tbframe_interval = setInterval(function() { jQuery('#TB_iframeContent').contents().find('.savesend .button').val('Add to OptionTree'); }, 2000);
        return false;
      });
      window.original_send_to_editor = window.send_to_editor;
      window.send_to_editor = function(html) {
        if (formfield) {
          clearInterval(tbframe_interval);
          itemurl = $(html).attr('href');
          var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
          var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
          var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
          var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;
          if ( UrlExists(itemurl) ) {
            if (itemurl.match(image)) {
              btnContent = '<img src="'+itemurl+'" alt="" /><a href="" class="remove">Remove Image</a>';
            } else {
              btnContent = '<div class="no_image">'+html+'<a href="" class="remove">Remove</a></div>';
            }
          }
          $('#' + formfield).val(itemurl);
          $('#' + formfield + '_image').remove();
          $('#' + formfield).parent('div').append('<div class="screenshot" id="'+formfield+'_image" />');
          $('#' + formfield + '_image').append(btnContent).slideDown();
          tb_remove();
        } else {
          window.original_send_to_editor(html);
        }
      }
    }
  };
  $(document).ready(function () {
    uploadOption.init()
  })
})(jQuery);

function UrlExists(url) {
  var http = new XMLHttpRequest();
  http.open('HEAD', url, false);
  http.send();
  return http.status!=404;
}

/**
 *
 * Inline Edit Options
 * 
 * Creates & Updates Options via Ajax
 * Dependencies: jQuery
 *
 */
(function ($) {
  inlineEditOption = {
    init: function () {
      var c = this,
          d = $("tr.inline-edit-option");
      $('.save-options', '#the-theme-options').live("click", function () {
        inlineEditOption.save_options(this);
        return false;
      });
      $('.reload-options', '#the-theme-options').live("click", function () {
        var agree = confirm("Are you sure you want to reload your options from the theme-options.xml file?");
        if (agree) {
          inlineEditOption.reload_options(this);
          return false;
        } else {
          return false;
        }
      });
      $('.reset', '#the-theme-options').live("click", function () {
        var agree = confirm("Are you absolutely sure you wish to delete all of your saved Theme Option?");
        if (agree) {
          inlineEditOption.reset_options(this);
          return false;
        } else {
          return false;
        }
      });
      $('.import-data', '#import-data').live("click", function () {
        inlineEditOption.import_data(this);
        return false;
      });
      $('.import-layout', '#import-layout').live("click", function () {
        inlineEditOption.import_layout(this);
        return false;
      });
      $('.save-layout', '#save-layout').live("click", function (e) {
        inlineEditOption.save_layout(this);
        e.preventDefault();
        return false;
      });
      $('.user-save-layout', '#the-theme-options').live("click", function (e) {
        var agree = confirm("Are you sure you want to save this layout?");
        if (agree) {
          inlineEditOption.save_layout_user_side(this);
          return false;
        }
        return false;
      });
      $('a.delete-saved').live("click", function () {
        if ($("a.delete-saved").hasClass('disable')) {
          event.preventDefault();
          return false;
        } else {
          var agree = confirm("Are you sure you want to delete this saved layout?");
          if (agree) {
            inlineEditOption.delete_layout(this);
            return false;
          }
        }
        return false;
      });
      $("a.activate-saved").live("click", function(){
        var agree = confirm("Are you sure you want to activate this layout?");
        if (agree) {
          inlineEditOption.activate_layout(this);
          return false;
        }
        return false;
      });
      $('.user-activate-layout', '#the-theme-options').live("click", function () {
        var agree = confirm("Are you sure you want to activate this layout?");
        if (agree) {
          inlineEditOption.activate_layout_user_side(this);
          return false;
        }
        return false;
      });
      $("a.edit-inline").live("click", function (event) {
        if ($("a.edit-inline").hasClass('disable')) {
          event.preventDefault();
          return false;
        } else {
          inlineEditOption.edit(this);
          return false;
        }                
      });
      $("a.save").live("click", function () {
        if ($("a.save").hasClass('add-save')) {
          inlineEditOption.addSave(this);
          return false;
        } else {
          inlineEditOption.editSave(this);
          return false;
        }
      });
      $("a.cancel").live("click", function () {
        if ($("a.cancel").hasClass('undo-add')) {
          inlineEditOption.undoAdd();
          return false;
        } else {
          inlineEditOption.revert();
          return false;
        }
      });
      $("a.add-option").live("click", function (event) {
        if ($(this).hasClass('disable')) {
          event.preventDefault();
          return false;
        } else {
          $.post( 
            ajaxurl,  
            { action:'option_tree_next_id', _ajax_nonce: $("#_ajax_nonce").val() },
            function (response) {
              c = parseInt(response) + 1;
              inlineEditOption.add(c);
            }
          );
          return false;
        }
      });
      $('#framework-settings').tableDnD({
        onDragClass: "dragging",
        onDrop: function(table, row) {
          d = {
            action: "option_tree_sort",
            id: $.tableDnD.serialize(),
            _ajax_nonce: $("#_ajax_nonce").val()
          };
          $.post(ajaxurl, d, function (response) {
        
          }, "html");
        }
      });
      $('#upload-xml').submit(function() {
        var agree = confirm("Are you sure you want to import these new settings?");
        if (agree) {
          return true;
        }
        return false;
      });
      $('.delete-inline').live("click", function (event) {
        if ($("a.delete-inline").hasClass('disable')) {
          event.preventDefault();
          return false;
        } else {
          var agree = confirm("Are you sure you want to delete this option?");
          if (agree) {
            inlineEditOption.remove(this);
            return false;
          } else {
            return false;
          }
        }
      });
      // Fade out message div
      if ($('.ajax-message').hasClass('show')) {
        $('.ajax-message').ajaxMessage();
      }
      // Remove Uploaded Image
      $('.remove').live('click', function(event) { 
        $(this).hide();
        $(this).parents().prev().prev('.upload').attr('value', '');
        //$(this).parents('.screenshot').slideUp();
        $(this).parents('.screenshot').find('img').remove();
        $(this).parents('.screenshot').find('.remove').remove();
        event.preventDefault();
      });
      // Hide the delete button on the first row 
      $('a.delete-inline', "#option-1").hide();
      // change upload input
      $('.upload').live('blur', function() {
        var id = $(this).attr('id'),
            val = $(this).val(),
            img = $(this).parent().find('img'),
            btn = $(this).parent().find('.remove'),
            src = img.attr('src');
        
        // don't match update             
        if ( val != src ) {
          img.attr('src', val);
        }
        // no image to change add it
        if ( val !== '' && ( typeof src == 'undefined' || src == false ) && UrlExists(val) ) {
          btnContent = '<img src="'+val+'" alt="" /><a href="" class="remove">Remove Image</a>';
          $(this).parent('div').append('<div class="screenshot" id="'+id+'_image" />');
          $('#' + id + '_image').append(btnContent).slideDown();
        } else if ( val == '' || ! UrlExists(val) ) {
          img.remove();
          btn.remove();
        }  
      });
      // add # to hex if missing
      $('.cp_input').live('blur', function() {
        $('.cp_input').each( function(index, domEle) {
          var val = $(domEle).val();
          var reg = /^[A-Fa-f0-9]{6}$/;
          if( reg.test(val) && val != '' ) { 
            $(domEle).attr('value', '#'+val )
          }
        });
      });
    },
    save_options: function (e) {
      var d = {
        action: "option_tree_array_save"
      };
      b = $(':input', '#the-theme-options').serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Theme Options were saved</div>');
          $(".option-tree-slider-body").hide();
          $('.option-tree-slider .edit').removeClass('down');
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Theme Options could not be saved</div>');
        }
      });
      return false;
    },
    reload_options: function (e) {
      var d = {
        action: "option_tree_array_reload",
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          window.location.href = r;
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Theme Options could not be updated.</div>');
        }
      });
      return false;
    },
    reset_options: function () {
      var d = {
        action: "option_tree_array_reset",
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $('.screenshot').hide();
          $(':input','#the-theme-options')
          .not(':button, :submit, :reset, :hidden, #active_theme_layout')
          .val('')
          .removeAttr('checked')
          .removeAttr('selected');
          $('.select').each(function () {
            var parent = $(this).parents('div');
            var new_text = '-- Choose One --';
            if ( parent.hasClass('measurement') )
              new_text = '&nbsp;--';
            if ( parent.hasClass('background-repeat') )
              new_text = 'background-repeat';
            if ( parent.hasClass('background-attachment') )
              new_text = 'background-attachment';
            if ( parent.hasClass('background-position') )
              new_text = 'background-position';
            if ( parent.hasClass('typography-family') )
              new_text = 'font-family';
            if ( parent.hasClass('typography-style') )
              new_text = 'font-style';
            if ( parent.hasClass('typography-variant') )
              new_text = 'font-variant';
            if ( parent.hasClass('typography-weight') )
              new_text = 'font-weight';
            if ( parent.hasClass('typography-size') )
              new_text = 'font-size';
            $(this).prev('span').html(new_text);
          });
          $('ul.option-tree-slider-wrap li').each(function () {
            $(this).remove();
          });
          $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Theme Options were deleted</div>');
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Theme Options could not be deleted</div>');
        }
      });
      return false;
    },
    save_layout: function(e){  
      var d = {
        action: "option_tree_save_layout"
      };
      var aa = $(':input', '#save-layout').val();
      if ( !aa ) {
        aa = 'default';
      }
      var ab = aa.replace(' ', '-');
      ab = ab.toLowerCase();
  	  
      b = $(':input', '#save-layout').serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Layout Saved Successfully.</div>');
          $("#saved-options > tbody").prepend("<tr id='saved-"+ab+"'><td class='col-title'>"+aa+"</td><td class='col-key>'><textarea>"+r+"</textarea></td><td class='col-edit' style='padding-left:10px !important; width: 55px;'><a href='#' class='activate-saved' alt='Activate'>Activate</a><a href='#' class='delete-saved' alt='Delete'>Delete</a></td></tr>");
          inlineEditOption.update_export_layout();
          $('tr').removeClass('active-layout');
          $('#layout-settings tr:first').addClass('active-layout');
          $('.empty-layouts').remove();
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Your Layout could not be saved.</div>');
        }
      });
      return false;
    },
    save_layout_user_side: function(b) {
      d = {
        action: "option_tree_save_layout",
        options_name: $("#save_theme_layout").val(),
        _ajax_nonce: $("#_ajax_nonce").val(),
        themes: true
      };
      if ( $("#save_theme_layout").val() == '' ) {
        return false;  
      }
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          window.location.href = r;
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Your Layout could not be saved.</div>');
        }
      });
    },
  	activate_layout: function (b) {
      var c = true;
        
      // Set ID
      c = $(b).parents("tr:first").attr('id');
      c = c.replace("saved-", "");
  
      d = {
        action: "option_tree_activate_layout",
        id: c,
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Your Layout has been activated.</div>');
          inlineEditOption.update_export_data();
          inlineEditOption.update_export_layout();
          $('tr').removeClass('active-layout');
          $('#'+$(b).parents("tr:first").attr('id')).addClass('active-layout');
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
      return false;
    },
    activate_layout_user_side: function(b) {
      d = {
        action: "option_tree_activate_layout",
        id: $("#active_theme_layout").val(),
        _ajax_nonce: $("#_ajax_nonce").val(),
        themes: true
      };
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          window.location.href = r;
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Theme Options could not be saved</div>');
        }
      });
    },
    delete_layout: function (b) {
      var c = true;
        
      // Set ID
      c = $(b).parents("tr:first").attr('id');
      c = c.replace("saved-", "");
        
      d = {
        action: "option_tree_delete_layout",
        id: c,
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $("#saved-" + c).remove();
          $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Your Layout has been deleted.</div>');
          inlineEditOption.update_export_layout();
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
      return false;
    },
    import_layout: function (e) {
      var d = {
        action: "option_tree_import_layout"
      };
      b = $(':input', '#import-layout').serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          window.location.href = r;
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Your Layouts could not be imported.</div>');
        }
      });
      return false;
    },
    update_export_layout: function () {
      var d = {
        action: "option_tree_update_export_layout",
        saved: $("textarea#export_layouts").val(),
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $("textarea#export_layouts").val(r);
        }
      });
      return false;
    },
    import_data: function (e) {
      var d = {
        action: "option_tree_import_data"
      };
      b = $(':input', '#import-data').serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $("textarea#import_options_data").val('');
          $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Your Theme Options data was successfully imported.</div>');
          inlineEditOption.update_export_data();
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Your Theme Options data could not be imported.</div>');
        }
      });
      return false;
    },
    update_export_data: function () {
      var d = {
        action: "option_tree_update_export_data",
        saved: $("textarea#export_data").val(),
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $("textarea#export_data").val(r);
          $('.active-layout textarea').val(r);
        }
      });
      return false;
    },
    remove: function (b) {
      var c = true;
      
      // Set ID
      c = $(b).parents("tr:first").attr('id');
      c = c.substr(c.lastIndexOf("-") + 1);
      
      d = {
        action: "option_tree_delete",
        id: c,
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r) {
          if (r == 'removed') {
            $("#option-" + c).remove();
            $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Option Deleted.</div>');
          } else {
            $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
          }
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
      return false;
    },
    add: function (c) {
      var e = this, 
          addRow, editRow = true, temp_select;
      e.revert();
      
      // Clone the blank main row
      addRow = $('#inline-add').clone(true);
      addRow = $(addRow).attr('id', 'option-'+c);
      
      // Clone the blank edit row
      editRow = $('#inline-edit').clone(true);
      
      $('a.cancel', editRow).addClass('undo-add');
      $('a.save', editRow).addClass('add-save');
      $('a.edit-inline').addClass('disable');
      $('a.delete-inline').addClass('disable');
      $('a.add-option').addClass('disable');
      
      // Set Colspan to 4
      $('td', editRow).attr('colspan', 4);
      
      // Add Row
      $("#framework-settings tr:last").after(addRow);
      
      // Add Row and hide
      $(addRow).hide().after(editRow);
      
      $('.item-data', addRow).attr('id', 'inline_'+c);
      
      // Show The Editor
      $(editRow).attr('id', 'edit-'+c).addClass('inline-editor').show();
      
      $('.item_title', '#edit-'+c).focus();
      
      $('.select').each(function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', '#edit-'+c).hide();
          $('.option-options', '#edit-'+c).hide();
        } 
      });
      
      $('.select').live('change', function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', '#edit-'+c).hide();
          $('.option-options', '#edit-'+c).hide();
        } else if ( 
            temp_select == 'Checkbox' || 
            temp_select == 'Radio' || 
            temp_select == 'Select'
          ) {
          $('.alternative').hide();
          $('.regular').show();
          $('.option-desc', '#edit-'+c).show();
          $('.option-options', '#edit-'+c).show();
        } else {
          if (temp_select == 'Textarea' || temp_select == 'CSS') {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Row Count:</strong> Enter a numeric value for the number of rows in your textarea.');
            $('.option-desc', '#edit-'+c).show();
            $('.option-options', '#edit-'+c).show();
          } else if (
              temp_select == 'Custom Post' ||
              temp_select == 'Custom Posts'
            ) {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Post Type:</strong> Enter your custom post_type.');
            $('.option-desc', '#edit-'+c).show();
            $('.option-options', '#edit-'+c).show();
          } else {
            $('.option-desc', '#edit-'+c).show();
            $('.option-options', '#edit-'+c).hide();
          }
        }
      });
      
      // Scroll
      var $elem = $('#framework_wrap');
      $('html, body').animate({ scrollTop: $elem.height() }, 500);

      return false;
    },
    undoAdd: function (b) {
      var e = this,
          c = true;
      e.revert();
      c = $("#framework-settings tr:last").attr('id');
      c = c.substr(c.lastIndexOf("-") + 1);

      $("a.edit-inline").removeClass('disable');
      $("a.delete-inline").removeClass('disable');
      $("a.add-option").removeClass('disable');
      $("#option-" + c).remove();
      
      return false;
    },
    addSave: function (e) {
      var d, b, c, f, g, itemId;
      e = $("tr.inline-editor").attr("id");
      e = e.substr(e.lastIndexOf("-") + 1);
      f = $("#edit-" + e);
      g = $("#inline_" + e);
      itemId = $.trim($("input.item_id", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      if (!itemId) {
        itemId = $.trim($("input.item_title", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      }
      d = {
        action: "option_tree_add",
        id: e,
        item_id: itemId,
        item_title: $("input.item_title", f).val(),
        item_desc: $("textarea.item_desc", f).val(),
        item_type: $("select.item_type", f).val(),
        item_options: $("input.item_options", f).val()
      };
      b = $("#edit-" + e + " :input").serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r) {
          if (r == 'updated') {
            inlineEditOption.afterSave(e);
            $("#edit-" + e).remove();
            $("#option-" + e).show();
            $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Option Added.</div>');
            $('#framework-settings').tableDnD({
              onDragClass: "dragging",
              onDrop: function(table, row) {
                d = {
                  action: "option_tree_sort",
                  id: $.tableDnD.serialize(),
                  _ajax_nonce: $("#_ajax_nonce").val()
                };
                $.post(ajaxurl, d, function (response) {

                }, "html");
              }
            });
          } else {
            $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
          }
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
      return false;
    },
    edit: function (b) {
      var e = this, 
          c, editRow, rowData, item_title, item_id, item_type, item_desc, item_options = true, temp_select;
      e.revert();
    
      c = $(b).parents("tr:first").attr('id');
      c = c.substr(c.lastIndexOf("-") + 1);
      
      // Clone the blank row
      editRow = $('#inline-edit').clone(true);
      $('td', editRow).attr('colspan', 4);
      $("#option-" + c).hide().after(editRow);
      
      // First Option Settings 
      if ("#option-" + c == '#option-1') {
        $('.option').hide();
        $('.option-title').show().css({"paddingBottom":"1px"});
        $('.description', editRow).html('First item must be a heading.');
      }
      
      // Populate the option data
      rowData = $('#inline_' + c);
      
      // Item Title
      item_title = $('.item_title', rowData).text();
      $('.item_title', editRow).attr('value', item_title);
      
      // Item ID
      item_id = $('.item_id', rowData).text();
      $('.item_id', editRow).attr('value', item_id);
      
      // Item Type
      item_type = $('.item_type', rowData).text();
      $('select[name=item_type] option[value='+item_type+']', editRow).attr('selected', true);
      var temp_item_type = $('select[name=item_type] option[value='+item_type+']', editRow).text();
      $('.select_wrapper span', editRow).text(temp_item_type);
  		
  		// Item Description
      item_desc = $('.item_desc', rowData).text();
      $('.item_desc', editRow).attr('value', item_desc);
      
      // Item Options
      item_options = $('.item_options', rowData).text();
      $('.item_options', editRow).attr('value', item_options);
      
      
      $('.select', editRow).each(function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', editRow).hide();
          $('.option-options', editRow).hide();
        } else if ( 
            temp_select == 'Checkbox' || 
            temp_select == 'Radio' || 
            temp_select == 'Select'
          ) {
          $('.option-desc', editRow).show();
          $('.option-options', editRow).show();
        } else {
          if (temp_select == 'Textarea' || temp_select == 'CSS') {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Row Count:</strong> Enter a numeric value for the number of rows in your textarea.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else if (
              temp_select == 'Custom Post' ||
              temp_select == 'Custom Posts'
            ) {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Post Type:</strong> Enter your custom post_type.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else {
            $('.option-desc', editRow).show();
            $('.option-options', editRow).hide();
          }
        }
      });
      
      $('.select').live('change', function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', editRow).hide();
          $('.option-options', editRow).hide();
        } else if ( 
            temp_select == 'Checkbox' || 
            temp_select == 'Radio' || 
            temp_select == 'Select'
          ) {
          $('.alternative').hide();
          $('.regular').show();
          $('.option-desc', editRow).show();
          $('.option-options', editRow).show();
        } else {
          if (temp_select == 'Textarea' || temp_select == 'CSS') {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Row Count:</strong> Enter a numeric value for the number of rows in your textarea.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else if (
              temp_select == 'Custom Post' ||
              temp_select == 'Custom Posts'
            ) {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Post Type:</strong> Enter your custom post_type.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else {
            $('.option-desc', editRow).show();
            $('.option-options', editRow).hide();
          }
        }
      });
  		
      // Show The Editor
      $(editRow).attr('id', 'edit-'+c).addClass('inline-editor').show();
      
      // Scroll
      var target = $('#edit-'+c);
      if (c > 1) {
          var top = target.offset().top;
          $('html,body').animate({scrollTop: top}, 500);
          return false;
      }
      
      return false;
    },
    editSave: function (e) {
      var d, b, c, f, g, itemId;
      e = $("tr.inline-editor").attr("id");
      e = e.substr(e.lastIndexOf("-") + 1);
      f = $("#edit-" + e);
      g = $("#inline_" + e);
      itemId = $.trim($("input.item_id", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      if (!itemId) {
        itemId = $.trim($("input.item_title", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      }
      d = {
        action: "option_tree_edit",
        id: e,
        item_id: itemId,
        item_title: $("input.item_title", f).val(),
        item_desc: $("textarea.item_desc", f).val(),
        item_type: $("select.item_type", f).val(),
        item_options: $("input.item_options", f).val()
      };
      b = $("#edit-" + e + " :input").serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r) {
          if (r == 'updated') {
            inlineEditOption.afterSave(e);
            $("#edit-" + e).remove();
            $("#option-" + e).show();
            $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Option Saved.</div>');
          } else {
            $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
          }
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
      return false;
    },
    afterSave: function (e) {
      var x, y, z,
          n, m, o, p, q, r = true;
      x = $("#edit-" + e);
      y = $("#option-" + e);
      z = $("#inline_" + e);
      $('.option').show();
      $('a.cancel', x).removeClass('undo-add');
      $('a.save', x).removeClass('add-save');
      $("a.add-option").removeClass('disable');
      $('a.edit-inline').removeClass('disable');
      $('a.delete-inline').removeClass('disable');
      if (n = $("input.item_title", x).val()) {
        if ($("select.item_type", x).val() != 'heading') {
          $(y).removeClass('col-heading');
          $('.col-title', y).attr('colspan', 1);
          $(".col-key", y).show();
          $(".col-type", y).show();
          $(".col-title", y).text('- ' + n);
        } else {
          $(y).addClass('col-heading');
          $('.col-title', y).attr('colspan', 3);
          $(".col-key", y).hide();
          $(".col-type", y).hide();
          $(".col-title", y).text(n);
        }
        $(".item_title", z).text(n);
      }
      if (m = $.trim($("input.item_id", x).val().toLowerCase()).replace(/(\s+)/g,'_')) {
        $(".col-key", y).text(m);
        $(".item_id", z).text(m);
      } else {
        m = $.trim($("input.item_title", x).val().toLowerCase()).replace(/(\s+)/g,'_');
        $(".col-key", y).text(m);
        $(".item_id", z).text(m);
      }
      if (o = $("select.item_type option:selected", x).val()) {
        $(".col-type", y).text(o);
        $(".item_type", z).text(o);
      }
      if (p = $("textarea.item_desc", x).val()) {
        $(".item_desc", z).text(p);
      }
      if (r = $("input.item_options", x).val()) {
        $(".item_options", z).text(r);
      }
    },
    revert: function () {
      var b, 
          n, m, o, p, q, r = true;
      if (b = $(".inline-editor").attr("id")) {
        $('#'+ b).remove();
        b = b.substr(b.lastIndexOf("-") + 1);
        $('.option').show();
        $("#option-" + b).show();
      }
      return false;
    }
  };
  $(document).ready(function () {
    inlineEditOption.init();
  })
})(jQuery);

/**
 *
 * Image Slider
 * 
 * Creates & Updates Image Slider
 * Dependencies: jQuery, jQuery UI
 *
 */
(function ($) {
  ImageSlider = {
    processing: false,
    init: function () {
      $(".option-tree-slider-body").hide();
      $('.option-tree-slider .edit').live('click', function(event){
        event.preventDefault();
        $('.option-tree-slider .edit').not($(this)).removeClass('down');
        $('.option-tree-slider-body').not($(this).parent().find('.option-tree-slider-body')).hide();
        $(this).toggleClass('down');
        $(this).parent().find('.option-tree-slider-body').toggle();
      });
      $('.option-tree-slider-title').live('keyup', function(){
        ImageSlider.update_slider_title(this);
      });
      $('.remove-slide').live('click', function(event){
        event.preventDefault();
        var agree = confirm("Are you sure you wish to delete this slide?");
        if (agree) {
          ImageSlider.delete_slider_image(this);
          return false;
        } else {
          return false;
        }
      });
      $('.add-slide').live('click', function(event){
        event.preventDefault();
        ImageSlider.add_slider($(this).attr('id'));
      });
      $('.option-tree-slider-wrap').each( function() {
        var id = $(this).attr('id');
        if ( $('#'+id).length ) {
          $('#'+id).sortable({
            update: function(event,ui){
              $('#'+id).find('li:not(.ui-sortable-helper)').each(function(inc){
                var target = $(this).find('a.open').attr('href').split("#")[1];
                $('#' + target).find('input.option-tree-slider-order').val(inc + 1);
              });
            }
          });
        }
      });
    },
    update_slider_title: function(e) {
      var element = e;
      if ( this.timer ) {
        clearTimeout( element.timer );
      }
      this.timer = setTimeout( function() {
        $(element).parents('.option-tree-slider').find('.open').text( element.value );
      }, 100);
      return true;
    },
    add_slider: function(id) {
      var self = this;
      if ( this.processing === false ) {
        this.processing = true;
        var image_count = parseInt($( '#'+id+'_list li' ).length) - 1;
        $.ajax({
          url: ajaxurl,
          type: 'get',
          data: {
            action: 'option_tree_add_slider',
            slide_id: id,
            count: image_count,
            page: 'option_tree'
          },
          complete: function( data ) {
            $('.option-tree-slider .edit').removeClass('down');
            $('.option-tree-slider-body').hide();
            $('#'+id+'_list').append( '<li>' + data.responseText + '</li>' );
            $('#'+id+'_list li:last .option-tree-slider .edit').toggleClass('down');
            self.processing = false;
          }
        });
      }
    },
    delete_slider_image: function(e) {
      $(e).parents('li').remove();
    }
  };
  $(document).ready(function () {
    ImageSlider.init();
  })
})(jQuery);