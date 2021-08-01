/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Banner rotation: customer zone controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('back_to_top/component', [ 'ready' ], function() {
  var BackToTop = Object.extend({
    base: null,
    scrollTrigger: 100,
    scrollDuration: 100,

    constructor: function(baseSelector) {
      baseSelector = baseSelector || '.back-to-top';
      
      if (jQuery(baseSelector).length) {
        this.base = jQuery(baseSelector);
      }

      this.init();
    },
    
    init: function () {
      var self = this;

      self.changeButtonState();

      jQuery(window).on('scroll', function () {
        self.changeButtonState();
      });

      self.base.on('click', function (e) {
        e.preventDefault();
        self.scrollToTop();
      });
    },

    scrollToTop: function() {
      jQuery('html,body').animate(
          {
            scrollTop: 0
          },
          this.scrollDuration
      );
    },
    
    changeButtonState: function () {
      var scrollTop = jQuery(window).scrollTop();

      if (scrollTop > this.scrollTrigger) {
        this.base.addClass('show');
      } else {
        this.base.removeClass('show');
      }
    }
  });

  BackToTop.instance = new BackToTop('.back-to-top');

  return BackToTop;
});
