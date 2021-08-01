/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context cart
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('paypal_ec_checkout_cart_button_processor', ['paypal_ec_button_processors'], function (Processors) {
  Processors.push(function (element, state) {
    if (!state.funding) {
      state.funding = {};
    }

    if (element.is('.pp-funding-credit')) {
      state.funding.allowed = [ paypal.FUNDING.CREDIT ];
      state.funding.disallowed = [];
      state.tagline = true;
    } else {
      state.funding.allowed = [ ];
      state.funding.disallowed = [ paypal.FUNDING.CREDIT ];
    }

    state.funding.disallowed.push(paypal.FUNDING.CARD);
  })
});