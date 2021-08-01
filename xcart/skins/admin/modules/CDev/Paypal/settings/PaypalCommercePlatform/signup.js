/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * PayPal Commerce Platform onboarding
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
(function(d, s, id){
  var js, ref = d.getElementsByTagName(s)[0];

  if (!d.getElementById(id)){
    js = d.createElement(s); js.id = id; js.async = true;
    js.src = "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";

    ref.parentNode.insertBefore(js, ref);
  }
}(document, "script", "paypal-js"));

PaypalCommercePlatformOnboardedCallback = function (authCode, sharedId) {
  assignWaitOverlay($('body'))

  var data = {
    authCode: authCode,
    sharedId: sharedId
  };

  data[xliteConfig.form_id_name] = xliteConfig.form_id;

  core.post(
    {
      target: 'paypal_commerce_platform_settings',
      action: 'set_sign_up_flow_data'
    },
    null,
    data
  );
}