/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Facebook Pixel event script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('facebookPixel/addToCart', ['facebookPixel/event'], function (Event) {
  FacebookPixelAddToCart = Event.extend({
    processReady: function () {
      var o = this;

      core.bind('productAddedToCart', function (event, data) {
        o.registerAddedToCart(data.fbPixelProductData);
      });
    },

    registerAddedToCart: function (productData) {
      if (productData) {
        this.sendEvent('AddToCart', productData);
      } else {
        this.sendEvent('AddToCart');
      }
    }
  });

  FacebookPixelAddToCart.instance = new FacebookPixelAddToCart();

  return FacebookPixelAddToCart;
});
