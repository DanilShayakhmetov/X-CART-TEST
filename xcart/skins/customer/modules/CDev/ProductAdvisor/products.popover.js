/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Popup-singleton
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function(){
  jQuery('.recently-viewed-products .product-cell > .product').each(function(index, elem) {
    var content = jQuery(elem).find('.recently-viewed-product-details').html();

    jQuery(elem).popover({
      content:    content,
      container:  'body',
      placement:  'auto top',
      trigger:    'manual',
      html:       true,
    }).on('mouseenter', function () {
      var _this = this;
      jQuery(this).popover('show');
      jQuery(this).siblings('.popover').on('mouseleave', function () {
        jQuery(_this).popover('hide');
      });
    }).on('mouseleave', function () {
      var _this = this;
      var recheck = function() {
        setTimeout(function () {
          if (!jQuery('.popover:hover').length) {
            jQuery(_this).popover('hide')
          } else {
            recheck();
          }
        }, 100);
      };
      recheck();
    });
  });
});
