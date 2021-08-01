/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Tooltip widget JS class
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery.fn.extend({
  startTooltip: function() {
    return this.each(function() {
      window.startTooltip(this);
    });
  },
  popoverKeepOnHover: function () {
    var keepOnHoverInit = function (element) {
      var debouncedToggle = _.debounce(function(element, option) {
        if (option === 'show'
            && $(element).data('bs.popover')
            && $(element).data('bs.popover').$tip
            && $(element).data('bs.popover').$tip.is(':visible')
        ) {
          return;
        }

        $(element).popover(option);
      }, 150);

      $(element).on("mouseenter", function () {
        var _this = this;
        debouncedToggle($(_this), 'show');
      }).on("mouseleave", function () {
        var _this = this;
        var hoveredPopover = $(".popover:hover");
        if (!hoveredPopover.length) {
          debouncedToggle($(_this), 'hide');
        } else {
          hoveredPopover.on("mouseleave", function() {
            debouncedToggle($(_this), 'hide');
          });
        }
      });
    };

    return this.each(function() {
      keepOnHoverInit(this);
    });
  }
});

(function ($) {

  // Tooltips in customer area
  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );

  window.startTooltip = function (element) {
    var helpId = $(element).data('help-id');
    var params = {};

    if ($(element).parents('.ui-widget-content:first').length) {
      params.viewport = ('#' + $(element).parents('.ui-widget-content:first').attr('id'));
    }

    if (helpId) {
      content = $('#' + helpId).html();
      if (content) {
        params.content = content;
        $(element).popover(params);
      }

    } else {
      params.container = element;
      $(element).popover(params);
    }

    if ($(element).data('keep-on-hover')) {
      $(element).popoverKeepOnHover();
    }
  };

  define('xlite-tooltip', ['vue/vue'], function (Vue) {
    Vue.directive('xlite-tooltip', {
      bind: function () {
        startTooltip(this.el);
      }
    });
  });

})(jQuery);

core.microhandlers.add(
  'Tooltip',
  '[data-toggle="popover"]',
  function () {
    startTooltip(this);
  }
);
