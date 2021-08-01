/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define(["ProductDetails"], function () {
  decorate(
    'ProductDetailsView',
    'postprocessAdd2Cart',
    function (event, data) {
      if (!paypalAdd2CartAwaiting) {
        arguments.callee.previousMethod.apply(this, arguments);
      }

      core.trigger('activateExpressCheckout');
    }
  );
});