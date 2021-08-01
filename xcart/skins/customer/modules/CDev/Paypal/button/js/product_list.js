/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('paypal_ec_list_product_button_processor', ['paypal_ec_button_processors'], function (Processors) {
  Processors.push(function (element, state) {
    if (element.is('.pp-ec-product')) {
      state.payment = function () {
        var dfr = $.Deferred();

        showAdd2CartPopup = false;

        core.post({
          'target': 'cart',
          'action': 'add'
        }, function () {
          paypal.request.post(getInitiateTokenUrl(state.additionalUrlParams)).then(function (data) {
            dfr.resolve(data);
          });
        }, {
          'target': 'cart',
          'action': 'add',
          product_id: element.data('product-id'),
          expressCheckout: 1
        });

        return dfr.then(function (data) {
          return data.token;
        });
      };
    }
  })
});