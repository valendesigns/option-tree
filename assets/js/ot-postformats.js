/*!
 * postformats.js v1.0.1
 */
!function ($) {

  "use strict"; // jshint ;_;

  /* POSTFORMATS CLASS DEFINITION
   * ====================== */
  var formats = "input.post-format"
    , metaboxes = [
          '#ot-post-format-gallery'
        , '#ot-post-format-link'
        , '#ot-post-format-image'
        , '#ot-post-format-quote'
        , '#ot-post-format-video'
        , '#ot-post-format-audio'
      ]
    , ids = metaboxes.join(',')
    , insertAfter = '#titlediv'
    , imageBox = '#postimagediv'
    , placeholder = 'postimagediv-placeholder'
    , Postformats = function (element, options) {
        this.$element = $(element)
          .on('click.postformats.data-api', $.proxy(this.toggle, this))
        this.$id = this.$element.attr('id')
        this.init()
      }

  Postformats.prototype = {

    constructor: Postformats
  
  , init: function () {

      // Moves the metaboxes into place
      $( '#ot-' + this.$id ).insertAfter( $( insertAfter ) ).hide()
      
      // Show the checked metabox
      if ( this.$element.is(':checked') ) {
      
        this.show()
        
      }
      
    }
    
  , toggle: function () {

      // Hides all the post format metaboxes
      $(ids).each(function() {
      
        $(this).hide()
        
      })
      
      // Shows the clicked post format metabox
      this.show()
      
    }
  
  , show: function () {
      
      // Featured image is never really hidden so it requires different code 
      if ( this.$id == 'post-format-image' ) {
        
        if ( $( '#' + placeholder ).length == 0 )
          $( imageBox ).after( '<div id="' + placeholder + '"></div>' ).insertAfter( insertAfter ).css({'marginTop':'20px','marginBottom':'0px'}).find('h3 span').text(option_tree.with)
        
      // Revert image
      } else {

        $( '#' + placeholder ).replaceWith( $( imageBox ).css({'marginTop':'0px','marginBottom':'20px'}) )
        $( imageBox ).find('h3 span').text(option_tree.replace)
        
      }
      
      // Show the metabox
      $( '#ot-' + this.$id ).css({'marginTop':'20px','marginBottom':'0px'}).show()
      
    }
  
  }
    
  /* POSTFORMATS PLUGIN DEFINITION
   * ======================= */
  var old = $.fn.postformats

  $.fn.postformats = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('postformats')
        , options = typeof option == 'object' && option
      if (!data) $this.data('postformats', (data = new Postformats(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.postformats.Constructor = Postformats
  
  /* POSTFORMATS NO CONFLICT
   * ================= */
  $.fn.postformats.noConflict = function () {
    $.fn.postformats = old
    return this
  }

  /* POSTFORMATS DATA-API
   * ============== */
  $(document).on('ready.postformats.data-api', function () {
    $(formats).each(function () {
      $(this).postformats()
    })
  })

}(window.jQuery);