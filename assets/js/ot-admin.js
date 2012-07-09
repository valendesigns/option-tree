/**
 * Option Tree UI
 * 
 * Dependencies: jQuery, jQuery UI, ColorPicker
 *
 * @author Derek Herman (derek@valendesigns.com)
 */
;(function($) {
  OT_UI = {
    processing: false,
    init: function() {
      this.init_hide_body();
      this.init_sortable();
      this.init_add();
      this.init_edit();
      this.init_remove();
      this.init_edit_title()
      this.init_edit_id();
      this.init_activate_layout();
      this.init_upload();
      this.init_upload_remove();
      this.init_tabs();
      this.fix_upload_parent();
      this.fix_colorpicker();
      this.replicate_ajax();
      this.reset_settings();
    },
    init_hide_body: function(elm,type) {
      var css = '.option-tree-setting-body';
      if ( type == 'parent' ) {
        $(css).not( elm.parent().parent().children(css) ).hide();
      } else if ( type == 'child' ) {
        elm.closest('ul').find(css).not( elm.parent().parent().children(css) ).hide();
      } else if ( type == 'child-add' ) {
        elm.children().find(css).hide();
      } else if ( type == 'toggle' ) {
        elm.parent().parent().children(css).toggle();
      } else {
        $(css).hide();
      }
    },
    init_remove_active: function(elm,type) {
      var css = '.option-tree-setting-edit';
      if ( type == 'parent' ) {
        $(css).not(elm).removeClass('active');
      } else if ( type == 'child' ) {
        elm.closest('ul').find(css).not(elm).removeClass('active');
      } else if ( type == 'child-add' ) {
        elm.children().find(css).removeClass('active');
      } else {
        $(css).removeClass('active');
      }
    },
    init_sortable: function() {
      $('.option-tree-sortable').each( function() {
        if ( $(this).children('li').length ) {
          var elm = $(this);
          elm.show();
          $(this).sortable({
            items: 'li:not(.ui-state-disabled)',
            placeholder: 'ui-state-highlight',
            stop: function(evt, ui) {
              setTimeout(
                function(){
                  OT_UI.update_ids(elm);
                },
                200
              )
            }
          });
          $(this).children('li').disableSelection();	
        }
      });
    },
    init_add: function() {
      $('.option-tree-section-add').live('click', function(e){
        e.preventDefault();
        OT_UI.add(this,'section');
      });
      $('.option-tree-setting-add').live('click', function(e){
        e.preventDefault();
        OT_UI.add(this,'setting');
      });
      $('.option-tree-help-add').live('click', function(e){
        e.preventDefault();
        OT_UI.add(this,'contextual_help');
        OT_UI.init_sortable();
      });
      $('.option-tree-choice-add').live('click', function(e){
        e.preventDefault();
        OT_UI.add(this,'choice');
      });
      $('.option-tree-list-item-add').live('click', function(e){
        e.preventDefault();
        OT_UI.add(this,'list_item');
      });
      $('.option-tree-list-item-setting-add').live('click', function(e){
        e.preventDefault();
        OT_UI.add(this,'list_item_setting');
      });
    },
    init_edit: function() {
      $('.option-tree-setting-edit').live('click', function(e){
        e.preventDefault();
        if ( $(this).parents().hasClass('option-tree-setting-body') ) {
          OT_UI.init_remove_active($(this),'child');
          OT_UI.init_hide_body($(this),'child');
        } else {
          OT_UI.init_remove_active($(this),'parent');
          OT_UI.init_hide_body($(this), 'parent');
        }
        $(this).toggleClass('active');
        OT_UI.init_hide_body($(this), 'toggle');
      });
    },
    init_remove: function() {
      $('.option-tree-setting-remove').live('click', function(event){
        event.preventDefault();
        if ( $(this).parents('li').hasClass('ui-state-disabled') ) {
          alert(option_tree.remove_no);
          return false;
        }
        var agree = confirm(option_tree.remove_agree);
        if (agree) {
          var list = $(this).parents('ul');
          OT_UI.remove(this);
          setTimeout( function() { 
            OT_UI.update_ids(list); 
          }, 200 );
        }
        return false;
      });
    },
    init_edit_title: function() {
      $('.option-tree-setting-title').live('keyup', function(){
        OT_UI.edit_title(this);
      });
    },
    init_edit_id: function() {
      $('.section-id').live('keyup', function(){
        OT_UI.update_id(this);
      });
    },
    init_activate_layout: function() {
      $('.option-tree-layout-activate').live('click', function(){ 
        var active = $(this).parents('.option-tree-setting').find('.open').text();
        $('.option-tree-layout-activate').removeClass('active');
        $(this).toggleClass('active');
        $('.active-layout-input').attr({'value':active});
      });
      $('#option-tree-options-layouts-form select').live('change', function(){
        var agree = confirm(option_tree.activate_layout_agree);
        if (agree) {
          $('#option-tree-options-layouts-form').submit();
        } else {
          var active = $('#the_current_layout').attr('value');
          $('#option-tree-options-layouts-form select option[value="' + active + '"]').attr({'selected':'selected'}) ;
        }
      });
    },
    add: function(elm,type) {
      var self = this, list, list_class = '', name = '';
      if ( type == 'contextual_help' ) {
        list = $(elm).parent().find('ul:last');
        list_class = 'list-contextual-help';
      } else if ( type == 'choice' ) {
        list = $(elm).parent().children('ul');
        list_class = 'list-choice';
      } else if ( type == 'list_item' ) {
        list = $(elm).parent().children('ul');
        list_class = 'list-sub-setting';
      } else if ( type == 'list_item_setting' ) {
        list = $(elm).parent().children('ul');
        list_class = 'list-sub-setting';
      } else {
        list = $(elm).parent().find('ul:first');
        list_class = ( type == 'section' ) ? 'list-section' : 'list-setting';
      }
      name = list.attr('rel');
      if ( this.processing === false ) {
        this.processing = true;
        var count = parseInt(list.children('li').length);
        $.ajax({
          url: option_tree.ajax,
          type: 'get',
          data: {
            action: 'add_' + type,
            count: count,
            name: name
          },
          complete: function( data ) {
            if ( type == 'choice' || type == 'list_item_setting' ) {
              OT_UI.init_remove_active(list,'child-add');
              OT_UI.init_hide_body(list,'child-add');
            } else {
              OT_UI.init_remove_active();
              OT_UI.init_hide_body();
            }
            list.append('<li class="ui-state-default ' + list_class + '">' + data.responseText + '</li>');
            list.children().last().find('.option-tree-setting-edit').toggleClass('active');
            list.children().last().find('.option-tree-setting-body').toggle();
            list.children().last().find('.option-tree-setting-title').focus();
            if ( type != 'contextual_help' ) {
              OT_UI.update_ids(list);
            }
            self.processing = false;
          }
        });
      }
    },
    remove: function(e) {
      $(e).parent().parent().parent('li').remove();
    },
    edit_title: function(e) {
      if ( this.timer ) {
        clearTimeout(e.timer);
      }
      this.timer = setTimeout( function() {
        $(e).parent().parent().parent().parent().parent().children('.open').text(e.value);
      }, 100);
      return true;
    },
    update_id: function(e) {
      if ( this.timer ) {
        clearTimeout(e.timer);
      }
      this.timer = setTimeout( function() {
        OT_UI.update_ids($(e).parents('ul'));
      }, 100);
      return true;
    },
    update_ids: function(list) {
      var last_section, section, list_items = list.children('li');
      list_items.each(function(index) {
        if ( $(this).hasClass('list-section') ) {
          section = $(this).find('.section-id').val().trim().toLowerCase().replace(/[^a-z0-9]/gi,'_');
          if (!section) {
            section = $(this).find('.section-title').val().trim().toLowerCase().replace(/[^a-z0-9]/gi,'_');
          }
          if (!section) {
            section = last_section;
          }
        }
        if ($(this).hasClass('list-setting') ) {
          $(this).find('.hidden-section').attr({'value':section});
        }
        last_section = section;
      });
    },
    init_upload: function() {
      $('.ot_upload_media').live('click', function() {
        var field_id    = $(this).parent('.option-tree-ui-upload-parent').find('input').attr('id'),
            post_id     = $(this).attr('rel'),
            intval      = window.setInterval(function() { $('#TB_iframeContent').contents().find('.savesend .button').val(option_tree.upload_text); }, 500),
            backup      = window.send_to_editor,
            btnContent  = '';
        tb_show('', 'media-upload.php?post_id='+post_id+'&field_id='+field_id+'&type=image&amp;TB_iframe=1');
        window.send_to_editor = function(html) {
          var href = $(html).attr('href');
          var image = /\.(?:jpe?g|png|gif|ico)$/i;
          if (OT_UI.url_exists(href)) {
            if (href.match(image)) {
              btnContent += '<div class="option-tree-ui-image-wrap"><img src="'+href+'" alt="" /></div>';
            }
            btnContent += '<a href="javascript:(void);" class="option-tree-ui-remove-media option-tree-ui-button" title="'+option_tree.remove_media_text+'"><span class="icon trash-can">'+option_tree.remove_media_text+'</span></a>';
          }
          $('#'+field_id).val(href);
          $('#'+field_id+'_media').remove();
          $('#'+field_id).parent().parent('div').append('<div class="option-tree-ui-media-wrap" id="'+field_id+'_media" />');
          $('#'+field_id+'_media').append(btnContent).slideDown();
          OT_UI.fix_upload_parent();
          tb_remove();
          window.clearInterval(intval);
          window.send_to_editor = backup;
  	    };
        return false;
      });
    },
    init_upload_remove: function() {
      $('.option-tree-ui-remove-media').live('click', function(event) {
        event.preventDefault();
        var agree = confirm(option_tree.remove_agree);
        if (agree) {
          OT_UI.remove_image(this);
          return false;
        }
        return false;
      });
    },
    init_upload_fix: function(elm) {
      var id  = $(elm).attr('id'),
          val = $(elm).val(),
          img = $(elm).parent().next('option-tree-ui-media-wrap').find('img'),
          src = img.attr('src'),
          btnContent = '';
      if ( val != src ) {
        img.attr('src', val);
      }
      if ( val !== '' && ( typeof src == 'undefined' || src == false ) && OT_UI.url_exists(val) ) {
        var image = /\.(?:jpe?g|png|gif|ico)$/i;
        if (val.match(image)) {
          btnContent += '<div class="option-tree-ui-image-wrap"><img src="'+val+'" alt="" /></div>';
        }
        btnContent += '<a href="javascript:(void);" class="option-tree-ui-remove-media option-tree-ui-button" title="'+option_tree.remove_media_text+'"><span class="icon trash-can">'+option_tree.remove_media_text+'</span></a>';
        $('#'+id).val(val);
        $('#'+id+'_media').remove();
        $('#'+id).parent().parent('div').append('<div class="option-tree-ui-media-wrap" id="'+id+'_media" />');
        $('#'+id+'_media').append(btnContent).slideDown();
      } else if ( val == '' || ! OT_UI.url_exists(val) ) {
        $(elm).parent().next('.option-tree-ui-media-wrap').remove();
      }
    },
    init_tabs: function() {
      $(".wrap.settings-wrap .ui-tabs").tabs({ 
        fx: { 
          opacity: "toggle", 
          duration: "fast" 
        }
      }).bind("tabsselect", function(event, ui) {
        $("input[name=\'_wp_http_referer\']").val(ui.tab);
      });
    },
    bind_colorpicker: function(field_id) {
      $('#'+field_id).ColorPicker({
        onSubmit: function(hsb, hex, rgb) {
          $('#'+field_id).val('#'+hex);
        },
        onBeforeShow: function () {
          $(this).ColorPickerSetColor(this.value);
          return false;
        },
        onChange: function (hsb, hex, rgb) {
          var bc = $.inArray(hex, [ 'FFFFFF', 'FFF', 'ffffff', 'fff' ]) != -1 ? 'ccc' : hex;
          $('#cp_'+field_id).css({'backgroundColor':'#'+hex,'borderColor':'#'+bc});
          $('#cp_'+field_id).prev('input').attr('value', '#'+hex);
        }
      })	
      .bind('keyup', function(){
        $(this).ColorPickerSetColor(this.value);
      });
    },
    fix_upload_parent: function() {
      $('.option-tree-ui-upload-input').live('focus blur', function(){
        $(this).parent('.option-tree-ui-upload-parent').toggleClass('focus');
        OT_UI.init_upload_fix(this);
      });
    },
    remove_image: function(e) {
      $(e).parent().parent().find('.option-tree-ui-upload-input').attr('value','');
      $(e).parent('.option-tree-ui-media-wrap').remove();
    },
    fix_colorpicker: function() {
      $('.cp_input').live('blur', function() {
        $('.cp_input').each( function(index, el) {
          var val = $(el).val();
          var reg = /^[A-Fa-f0-9]{6}$/;
          if( reg.test(val) && val != '' ) { 
            $(el).attr('value', '#'+val)
          } else if ( val == '' ) {
            $(this).parent().next().css({'background':'#f1f1f1','border-color':'#ccc'});
          }
        });
      });
    },
    replicate_ajax: function() {
      if (location.href.indexOf("#") != -1) {
        var url = $("input[name=\'_wp_http_referer\']").val(),
            hash = location.href.substr(location.href.indexOf("#"));
        $("input[name=\'_wp_http_referer\']").val( url + hash );
        this.scroll_to_top();
      }
      setTimeout( function() {
        $(".wrap.settings-wrap .fade").fadeOut("fast");
      }, 3000 );
    },
    reset_settings: function() {
      $(".reset-settings").live("click", function(event){
        var agree = confirm(option_tree.reset_agree);
        if (agree) {
          return true;
        } else {
          return false;
        }
        event.preventDefault();
      });
    },
    url_exists: function(url) {
      var http = new XMLHttpRequest();
      http.open('HEAD', url, false);
      http.send();
      return http.status!=404;
    },
    scroll_to_top: function() {
      setTimeout( function() {
        $(this).scrollTop(0);
      }, 50 );
    }
  };
  $(document).ready( function() {
    OT_UI.init();
  });
})(jQuery);