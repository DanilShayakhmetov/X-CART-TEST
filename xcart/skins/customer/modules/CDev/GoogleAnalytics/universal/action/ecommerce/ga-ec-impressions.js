/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceCoreEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'], function (eCommerceCoreEvent, _) {
  eCommerceImpressionEvent = eCommerceCoreEvent.extend({
    processsedImpressions: [],

    getListeners: function () {
      return {
        'ga-pageview-sending':      this.registerAllImpressionsInitial,
        'list.products.loaded':     this.registerAllImpressionsInList,
        'ga-ec-addImpression':      this.addImpressionExternalHandler,
      };
    },

    registerAllImpressionsInitial: function (event, data) {
      var self = this;
      var length = 0;

      _.each(
          this.getActions('impression'),
          function (action, index) {
            if (self.addImpression(action.data)) {
              length += JSON.stringify(action.data).length;
            }
            if (length > 6000) {
              ga('send', 'pageview');
              length = 0;
            }
          }
      );
      if (length) {
        ga('send', 'pageview');
      }
    },

    registerAllImpressionsInList: function (event, widget) {
      var self = this;

      var shouldSend = false;

      this.processsedImpressions = [];

      _.each(
          this.getActions('impression', widget['base']),
          function (action, index) {
            shouldSend = self.addImpression(action.data) || shouldSend;
          }
      );

      if (shouldSend) {
        ga('send', 'pageview');
      }
    },

    addImpression: function (impressionData) {
      var result = false;
      var hash = window.core.utils.hash(
          _.omit(impressionData, 'position')
      );

      if (!this.processsedImpressions[hash]) {
        ga('ec:addImpression', impressionData);
        result = true;

        this.processsedImpressions[hash] = true;
      }

      return result;
    },

    addImpressionExternalHandler: function (event, data) {
      this.addImpression(data);
    },

  });

  eCommerceImpressionEvent.instance = new eCommerceImpressionEvent();

  return eCommerceImpressionEvent;
});