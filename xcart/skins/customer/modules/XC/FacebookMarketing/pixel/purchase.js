/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Facebook Pixel event script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('facebookPixel/purchase', ['facebookPixel/event'], function (Event) {
  FacebookPixelPurchase = Event.extend({
    processReady: function () {
      this.registerPurchase();
    },

    registerPurchase: function () {
      var self = this;

      this.retrieveOrderData(function (xhr, state, response) {
        if (state === 'success') {
          var data = jQuery.parseJSON(response);

          if (data && !_.isUndefined(data.order_total) && !_.isUndefined(data.order_currency_code)) {
            self.sendEvent('Purchase', {
              value: data.order_total,
              currency: data.order_currency_code
            });
          } else if (core.isDeveloperMode) {
            console.log('Error on order data retrieving.');
          }
        }
      });
    },

    retrieveOrderData: function (callback) {
      var params = {
        target: 'checkoutSuccess',
        action: 'PixelRetrieveOrderData',
      };

      if (core.getURLParam('order_id')) {
        params.order_id = core.getURLParam('order_id');
      }

      if (core.getURLParam('order_number')) {
        params.order_number = core.getURLParam('order_number');
      }

      var url = URLHandler.buildURL(params);
      core.get(url, callback);
    }
  });

  FacebookPixelPurchase.instance = new FacebookPixelPurchase();

  return FacebookPixelPurchase;
});
