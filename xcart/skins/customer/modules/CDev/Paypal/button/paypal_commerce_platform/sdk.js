/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal SDK loader
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('payapl_sdk_loader', function () {
  function loadPayPalSdk(SDKParams, PartnerAttributionId, ClientToken, PayPal3Dsecure, callback) {
    var PAYPAL_SCRIPT = 'https://www.paypal.com/sdk/js?' + SDKParams;

    var container = document.body || document.head;
    callback = callback || function() {};

    var script = document.createElement('script');
    script.setAttribute('src', PAYPAL_SCRIPT);
    script.setAttribute('async', true);
    script.setAttribute('data-partner-attribution-id', PartnerAttributionId);

    if (ClientToken) {
      script.setAttribute('data-client-token', ClientToken);
    }

    if (PayPal3Dsecure) {
      script.setAttribute('data-enable-3ds', true);
    }

    script.onload = function() { callback() };
    script.onerror = function(err) { callback(err) };

    container.appendChild(script);
  }

  var env = core.getCommentedData(jQuery('body'), 'PayPalEnvironment');
  var SDKParams = core.getCommentedData(jQuery('body'), 'PayPalSDKParams');
  var PartnerAttributionId = core.getCommentedData(jQuery('body'), 'PayPalPartnerAttributionId');
  var PayPal3Dsecure = core.getCommentedData(jQuery('body'), 'PayPal3Dsecure');
  var ClientToken = core.getCommentedData(jQuery('body'), 'PayPalClientToken');

  loadPayPalSdk(SDKParams, PartnerAttributionId, ClientToken, PayPal3Dsecure, function (err) {
    if (typeof err !== "undefined") {
      console.error('Failed to load paypal script');
    } else {
      define('paypal_sdk', function () {
        return paypal;
      })
    }
  });
});

require('ready', function () {
  if (typeof (window['slidebar']) == 'function' && jQuery.mmenu) {
    core.bind('mm-menu.created', function () {
      setTimeout(function () {
        define('pcp_mmenu_loaded')
      }, 2000)
    })
  } else {
    setTimeout(function () {
      define('pcp_mmenu_loaded')
    }, 2000)
  }
})

