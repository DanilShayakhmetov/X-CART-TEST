/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * List item
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($) {
  var undefined;

  var options = {
    prefix: 'list-item__'
  };

  var methods = {
    focus: function () {
      var element = $(this);
      if (!element.attr('tabindex')) {
        element.attr('tabindex', -1);
      }

      // focus to element
      $('html, body').animate({
        scrollTop: Math.max(element.offset().top - 100, 0)
      }, 300, 'swing', function () {
        element.focus();
      });
    },
    show: function () {
      this.each(function () {
        $(this).on('animationend', _.once(function () {
          $(this).removeClass(options.prefix + 'showing');
          $(this).css('max-height', '');
        }));
        $(this).css('max-height', $(this).data('height'));
        $(this).removeClass(options.prefix + 'hidden');
        $(this).removeClass(options.prefix + 'sidebar-hidden');
        $(this).addClass(options.prefix + 'showing');
      });
    },
    hide: function () {
      this.each(function () {
        $(this).data('height', $(this).outerHeight());
        $(this).css('max-height', $(this).outerHeight());
        $(this).on('animationend', _.once(function () {
          $(this).removeClass(options.prefix + 'hiding');
          $(this).addClass(options.prefix + 'hidden');
          $(this).css('max-height', 0);
        }));
        $(this).addClass(options.prefix + 'hiding');
      });
    },
    isHidden: function () {
      return this.hasClass(options.prefix + 'hidden') || this.hasClass(options.prefix + 'temporary');
    },
    isSidebarHidden: function() {
      return this.hasClass(options.prefix + 'sidebar-hidden');
    },
  };

  $.fn.listItem = function (method) {
    if (methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method) {
      return methods.init.apply(this, arguments);
    } else {
      $.error('Метод с именем ' + method + ' не определен для jQuery.listItem');
    }
  };
})(jQuery);
