/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal script loader
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function() {
  function loadPayPalCheckout(callback) {
    var PAYPAL_SCRIPT = 'https://www.paypalobjects.com/api/checkout.min.js';

    var container = document.body || document.head;
    callback = callback || function() {};

    var script = document.createElement('script');
    script.setAttribute('src', PAYPAL_SCRIPT);
    script.setAttribute('data-version-4', "");

    script.onload = function() { callback() };
    script.onerror = function(err) { callback(err) };

    container.appendChild(script);
  }

  loadPayPalCheckout(function (err) {
    if (typeof err !== "undefined") {
      console.error('Failed to load paypal script');
    }
  });
})();