/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Facebook Pixel event script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('facebookPixel/initiateCheckout', ['facebookPixel/event'], function (Event) {
  FacebookPixelInitiateCheckout = Event.extend({
    processReady: function () {
      this.registerInitiateCheckout();
    },

    registerInitiateCheckout: function () {
      var data = core.getCommentedData(jQuery('.fb-pixel-init-checkout-data'));

      if (data) {
        this.sendEvent('InitiateCheckout', data);
      } else {
        this.sendEvent('InitiateCheckout');
      }
      this.sentCheckoutInitiated();
    },

    sentCheckoutInitiated: function () {
      var url = URLHandler.buildURL({target: 'facebook_pixel', action: 'initiate_checkout'});
      core.post(url);
    }
  });

  FacebookPixelInitiateCheckout.instance = new FacebookPixelInitiateCheckout();

  return FacebookPixelInitiateCheckout;
});
