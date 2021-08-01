/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('paypal_ec_checkout_button_processor', ['paypal_ec_button_processors'], function (Processors) {
  Processors.push(function (element, state) {
    if (element.is('.paypal-ec-checkout') || element.is('.paypal-checkout-for-marketplaces')) {
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
            if (data.token) {
              dfr.resolve(data);
            } else {
              core.trigger('message', {'type': 'error', 'message': data.error});
              dfr.reject(data.error);
            }
          });
        }, this));

        return dfr.then(function (data) {
          return data.token;
        });
      };

      if (!state.funding) {
        state.funding = {};
      }

      state.funding.allowed = [];
      state.funding.disallowed = [];

      if (element.is('.pp-funding-credit')) {
        state.funding.allowed.push(paypal.FUNDING.CREDIT);
        state.tagline = true;
      } else {
        state.funding.disallowed.push(paypal.FUNDING.CREDIT);
      }

      if (element.data('fundingCard')) {
        state.funding.allowed.push(paypal.FUNDING.CARD);
      } else {
        state.funding.disallowed.push(paypal.FUNDING.CARD);
      }

      if (element.data('fundingElv')) {
        state.funding.allowed.push(paypal.FUNDING.ELV);
      } else {
        state.funding.disallowed.push(paypal.FUNDING.ELV);
      }

      if (element.data('fundingVenmo')) {
        state.funding.allowed.push(paypal.FUNDING.VENMO);
      } else {
      }

      state.commit = true;
    }
  })
});