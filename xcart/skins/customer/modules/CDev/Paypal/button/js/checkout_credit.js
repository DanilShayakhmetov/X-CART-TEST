/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('paypal_ec_checkout_credit_button_processor', ['paypal_ec_button_processors'], function (Processors) {
  Processors.push(function (element, state) {
    if (element.is('.paypal-ec-checkout-credit')) {
      state.additionalUrlParams.ignoreCheckout = true;

      state.payment = function () {
        var dfr = $.Deferred();

        var form = element.closest('form').get(0);
        var actionElement = jQuery(form).find('input[name="action"]');
        var oldAction = actionElement.val();
        actionElement.val('setOrderNote');

        var notes = $('textarea[name="notes"]').clone();
        notes.appendTo(form);

        form.submitBackground(_.bind(function () {
          actionElement.val(oldAction);
          notes.remove();
          paypal.request.post(getInitiateTokenUrl(state.additionalUrlParams)).then(function (data) {
            dfr.resolve(data);
          });
        }, this));

        return dfr.then(function (data) {
          return data.token;
        });
      };
    }
  })
});