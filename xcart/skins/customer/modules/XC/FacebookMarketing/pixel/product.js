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
      if ('product' === core.getTarget()) {
        this.registerView();
      }

      var o = this;
      core.bind('afterPopupPlace', function(event, data){
        if (popup.currentPopup.box.hasClass('ctrl-customer-quicklook')) {
          o.registerView(popup.currentPopup.box);
        }
      })

      var currentId = null;
      core.registerTriggersBind('update-product-page', function(){
        var pixelContentIdElem = jQuery('input[name="facebook_pixel_content_id"]');
        if (pixelContentIdElem.length > 0) {
          var newId = pixelContentIdElem.eq(0).val();
          if (currentId && currentId !== newId) {
            o.registerView();
          }
          currentId = newId;
        }
      });
    },

    registerView: function(base) {
      if (_.isUndefined(base)) {
        base = jQuery('body');
      }

      var contentData = {
        'content_type': 'product'
      }

      var pixelContentIdElem = jQuery('input[name="facebook_pixel_content_id"]', base);
      if (pixelContentIdElem.length > 0) {
        contentData['content_ids'] = "['"+pixelContentIdElem.eq(0).val()+"']";
      }

      var pixelValueElem = jQuery('input[name="facebook_pixel_value"]', base);
      if (pixelValueElem.length > 0) {
        contentData['value'] = pixelValueElem.eq(0).val();
      }

      var pixelCurrencyElem = jQuery('input[name="facebook_pixel_value_currency"]', base);
      if (pixelCurrencyElem.length > 0) {
        contentData['currency'] = pixelCurrencyElem.eq(0).val();
      }

      this.sendEvent('ViewContent', contentData);
    }
  });

  FacebookPixelViewContent.instance = new FacebookPixelViewContent();

  return FacebookPixelViewContent;
});
