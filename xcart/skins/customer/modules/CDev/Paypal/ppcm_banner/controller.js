/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('ppcm_banner', ['paypal_sdk', 'js/jquery', 'js/underscore', 'pcp_mmenu_loaded'],
  function (paypal, $, _) {
    var renderBanner = function () {
      if (jQuery('.ppcm-banner-amount').length) {
        renderBannerWithAmount();
      } else {
        renderDefault();
      }
    }

    var renderBannerWithAmount = function () {
      var amountElem = jQuery('.ppcm-banner-amount');
      var amount = amountElem.data('amount');

      if (typeof amount !== 'undefined') {
        jQuery('.ppcm-banner').attr('data-pp-amount', parseFloat(amount));
        paypal.Messages().render('.ppcm-banner');
      }
    }

    var renderDefault = function () {
      paypal.Messages({'data-pp-offer': 'NI'}).render('.ppcm-banner');
    }

    if (
      paypal.isFundingEligible(paypal.FUNDING.CREDIT)
      || paypal.isFundingEligible(paypal.FUNDING.PAYLATER)
    ) {
      renderBanner();

      core.bind(
        [
          'cart.main.loaded',
          'block.product.details.loaded',
          'checkout.cart_items.ready',
          'checkout.cartItems.postprocess'
        ],
        function () {
          renderBanner();
        }
      );

      core.registerTriggersBind('update-product-page', function(){
        renderBanner();
      });

    } else {
      $('.ppcm-banner').remove();
    }
  }
)

