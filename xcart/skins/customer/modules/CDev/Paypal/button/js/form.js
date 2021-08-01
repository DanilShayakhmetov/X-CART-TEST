/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal In-Context checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('paypal_ec_form_button_processor', ['paypal_ec_button_processors'], function (Processors) {
  Processors.push(function (element, state) {
    if (element.is('.pp-ec-form')) {
      state.payment = function () {
        paypalAdd2CartAwaiting = true;
        var form = element.closest('form').get(0);

        var dfr = $.Deferred();

        if (form) {
          showAdd2CartPopup = false;
          form.commonController.backgroundSubmit = true;
          form['expressCheckout'].value = 1;

          $(form).submit();

          core.bind('activateExpressCheckout', function () {
            paypal.request.post(getInitiateTokenUrl(state.additionalUrlParams)).then(function (data) {
              dfr.resolve(data);
            });
          });
        }

        return dfr.then(function (data) {
          return data.token;
        });
      };
    }
  })
});

