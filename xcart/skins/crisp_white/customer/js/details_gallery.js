/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind('init-cycle-gallery', function () {
  var initGallery = function() {
      var count = $(this).data('cycle-carousel-visible');

      if (!$(this).find('.cycle-carousel-wrap').length) {
          if ($(this).find($(this).data('cycle-slides')).length <= count) {
              $($(this).data('cycle-next')).hide();
              $($(this).data('cycle-prev')).hide();
          } else {
              $($(this).data('cycle-next')).show();
              $($(this).data('cycle-prev')).show();
              $(this).cycle();
          }
      }
  };

  var changeVisibleElemsCount = function() {
      var elemCount = Math.floor($(this).closest('.slides').width() / $(this).find('li').first().outerWidth(true));
      elemCount = Math.min(elemCount, $(this).find('li').length);
      var changed = false;

      if ($(this).data('cycle-carousel-visible') != elemCount && !isNaN(elemCount)) {
          $(this).attr('data-cycle-carousel-visible', elemCount);
          $(this).data('cycle-carousel-visible', elemCount);
          if ($(this).data('cycle.opts') !== undefined) {
              $(this).cycle('destroy');
          }
          changed = true;
      }

      return changed;
  };

  $('.cycle-slideshow').each(function () {
    if ($(this).closest('.product-image-gallery').hasClass('mobile')) {
      changeVisibleElemsCount.apply(this);

      jQuery(window).resize(_.debounce(_.bind(function () {
        if (changeVisibleElemsCount.apply(this)) {
          initGallery.apply(this);
        }
      },this), 50));
    }

    initGallery.apply(this);
  });
});

jQuery(function () {
  core.trigger('init-cycle-gallery');

  core.bind('block.product.details.postprocess', function () {
    core.trigger('init-cycle-gallery');
  });

  $('.cycle-cloak.cycle-slideshow').on('cycle-initialized', function (event, opts) {
    $(this).removeClass('.cycle-cloak');
  });
});
