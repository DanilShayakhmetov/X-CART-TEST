/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/checkoutCompleteEvent', ['googleAnalytics/eCommerceCoreEvent'], function(eCommerceCoreEvent) {
  GACheckoutCompleteEvent = eCommerceCoreEvent.extend({
    namespace: 'Checkout  ',

    getListeners: function() {
      return {
        'ga-pageview-sending':    this.registerInvoiceEnter,
      };
    },

    registerInvoiceEnter: function(event) {
      var checkoutActionData = _.first(
        this.getActions('checkout_complete')
      );
      if (checkoutActionData) {
        var data = {
          products: checkoutActionData.data.products,
          actionData: {step: 5},
          message: 'Checkout success'
        };

        core.trigger('ga-ec-checkout', data);
      }
    },
  });

  GACheckoutCompleteEvent.instance = new GACheckoutCompleteEvent();

  return GACheckoutCompleteEvent;
});