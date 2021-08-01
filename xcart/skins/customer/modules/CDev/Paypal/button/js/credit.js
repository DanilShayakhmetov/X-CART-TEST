/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('paypal_ec_credit_button_processor', ['paypal_ec_button_processors'], function (Processors) {
  Processors.push(function (element, state) {
    if (element.is('.pp-style-credit')) {
      state.label = 'credit';
      state.additionalUrlParams.paypalCredit = true;
    }
  })
});