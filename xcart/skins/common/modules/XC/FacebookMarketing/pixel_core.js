/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Facebook Pixel core script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('facebookPixel/core', [], function () {
  FacebookPixelCore = Object.extend({
    constructor: function () {
    },

    registerEvent: function (type, name, data) {
      if (core.isDeveloperMode) {
        if (data) {
          console.log('Facebook Pixel event-' + type + '-' + name, data);
        } else {
          console.log('Facebook Pixel event-' + type + '-' + name);
        }
      }

      if (!_.isUndefined(fbq)) {
        if (data) {
          fbq(type, name, data);
        } else {
          fbq(type, name);
        }
      } else if (core.isDeveloperMode) {
        console.log('Facebook Pixel "fbq" is undefined');
      }
    },

    retrieveCartData: function (callback) {
      var url = URLHandler.buildURL({target: 'facebook_pixel', action: 'retrieve_current_cart_data'});
      core.get(url, callback);
    }
  });

  FacebookPixelCore.instance = new FacebookPixelCore();

  return FacebookPixelCore;
});