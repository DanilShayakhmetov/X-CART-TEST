/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Facebook Pixel event script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('facebookPixel/search', ['facebookPixel/event'], function (Event) {
  FacebookPixelSearch = Event.extend({
    processReady: function() {
      var o = this;

      core.bind(
        'list.products.loaded',
        function (event, widget) {
          if (jQuery(widget.base).hasClass('products-search-result')) {
            o.registerSearchEvent();
          }
        }
      );

      o.registerSearchEvent();
    },

    registerSearchEvent: function() {
      var contentData = null;
      jQuery.each(jQuery('.list-container .items-list-products'), function(index, el) {
        var _contentData = core.getCommentedData(el, 'fb_pixel_content_data');
        if (_contentData && !_.isUndefined(_contentData.search_string)) {
          contentData = Object.assign({'content_type': 'product'}, _contentData);
          return false;
        }
      });

      if (contentData) {
        this.sendEvent('Search', contentData);
      } else {
        this.sendEvent('Search');
      }
    }
  });

  FacebookPixelSearch.instance = new FacebookPixelSearch();

  return FacebookPixelSearch;
});
