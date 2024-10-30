var zendeskInbox = (function( $ ) {
  var _self = this;
  var $container = null;

  return {
    init: function() {
      $container = $('#inbox-zendesk-container');

      $('.reload').click(function () {
        window.location.reload();
      });

      // Check if there's an element before the iframe container and add a margin
      if ($container.prevAll().filter(':visible').length) {
        $container.toggleClass('with-top');
      }

      // Sets the iframe size to fit between the nav and footer
      $(window).resize(function() {
        zendeskInbox.onWindowResize();
      }).trigger('resize');
    },

    /**
     * Resizes the iframe container to fit the whole page
     */
    onWindowResize: function() {
      var $footer = $('#wpfooter'),
          containerOffset = $container.offset(),
          wpBodyPadding = 0;

      // Footer is hidden on mobile view.
      if ($footer.is(':visible')) {
        footerOffset = $footer.offset().top;
        // get only the numeric part
        wpBodyPadding = parseInt($('#wpbody-content').css('padding-bottom'), 10);
      } else {
        footerOffset = $(window).height();
      }

      // get diff between container position and footer
      $('iframe', $container).css('height', (footerOffset - containerOffset.top - wpBodyPadding) + 'px');
    }
  }
})( jQuery );

jQuery(document).ready(function ($) {
  // pagenow is declared in wp-admin
  if ('toplevel_page_inbox-by-zendesk' !== pagenow) {
    return;
  }

  zendeskInbox.init();
});
