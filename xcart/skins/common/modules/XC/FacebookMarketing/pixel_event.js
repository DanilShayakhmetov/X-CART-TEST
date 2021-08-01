/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Facebook Pixel core script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('facebookPixel/event', ['facebookPixel/core'], function (Core) {
  FacebookPixelEvent = Object.extend({
    constructor: function() {
      jQuery().ready(_.bind(this.processReady, this));

      this.bindListeners();
    },

    bindListeners: function() {
      _.each(
        this.getListeners(),
        _.bind(
          function (handler, eventName) {
            core.bind(eventName, _.bind(handler, this));
          },
          this
        )
      );
    },

    getListeners: function() {
      return {};
    },

    processReady: function() {},

    sendEvent: function(name, data) {
      Core.instance.registerEvent('track', name, data);
    },

    sendCustomEvent: function(name, data) {
      Core.instance.registerEvent('trackCustom', name, data);
    },

    retrieveCartData: function (callback) {
      Core.instance.retrieveCartData(callback);
    }
  });

  return FacebookPixelEvent;
});