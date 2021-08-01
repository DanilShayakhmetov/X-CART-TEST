/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Facebook Pixel event script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('facebookPixel/viewContent', ['facebookPixel/event'], function (Event) {
  FacebookPixelViewContent = Event.extend({
    processReady: function() {
      this.registerView();
    },

    registerView: function() {
      var contentData = core.getCommentedData(jQuery('.fb-pixel-content-data'));
      if (contentData) {
        jQuery.each(jQuery('.list-container .items-list-products'), function (index, el) {
          _contentData = core.getCommentedData(el, 'fb_pixel_content_data');
          if (_contentData.content_ids) {
            _contentData.content_ids = "['"+_contentData.content_ids.join("','")+"']";
          }
          if (_contentData) {
            Object.assign(contentData.data, _contentData);
            return false;
          }
        })
      }

      if (contentData && contentData.type) {
        if ('category' === contentData.type) {
          this.sendCustomEvent('ViewCategory', contentData.data);
        } else {
          this.sendEvent('ViewContent', contentData.data);
        }

      } else {
        this.sendEvent('ViewContent');
      }
    }
  });

  FacebookPixelViewContent.instance = new FacebookPixelViewContent();

  return FacebookPixelViewContent;
});
