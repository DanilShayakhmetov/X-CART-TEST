/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal initialize Express Checkout on click 'Place order'
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind([
  'checkout.main.ready',
  'checkout.common.anyChange',
  'checkout.sections.payment.persist',
  'checkout.paymentTpl.loaded',
  'checkout.common.state.ready'
], function (event, controller) {
  if ($('.paypal-checkout-box').length > 0) {
    $('.review-step form.place .button-row').hide();
    $('form.place .paypal-ec-checkout').show();
    $('form.place .paypal-ec-checkout-credit').hide();
    $('form.place .paypal-checkout-for-marketplaces').hide();

    //fastlane
    $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').hide();
  } else if ($('.paypal-checkout-credit-box').length > 0) {
    $('.review-step form.place .button-row').hide();
    $('form.place .paypal-ec-checkout').hide();
    $('form.place .paypal-ec-checkout-credit').show();
    $('form.place .paypal-checkout-for-marketplaces').hide();

    //fastlane
    $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').hide();
  } else if ($('.paypal-checkout-for-marketplaces-box').length > 0) {
    $('.review-step form.place .button-row').hide();
    $('form.place .paypal-ec-checkout').hide();
    $('form.place .paypal-ec-checkout-credit').hide();

    if ($('.paypal-checkout-for-marketplaces').hasClass('unavailable')
      && $('.paypal-checkout-for-marketplaces').is(':hidden')
    ) {
      core.trigger('message', {type: 'warning', message: core.t('We are experiencing a problem with the "PayPal For Marketplaces" payment method.')});
    }

    $('form.place .paypal-checkout-for-marketplaces').show();

    //fastlane
    $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').hide();

  } else if ($('.pcp-checkout-box').length > 0) {
    $('.review-step form.place .button-row').hide();
    $('form.place .paypal-ec-checkout').hide();
    $('form.place .paypal-ec-checkout-credit').hide();

    $('form.place .pcp-button-container').show();
    $('form.place .pcp-hosted-fields-container').show();

    //fastlane
    $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').hide();

  } else {
    $('.review-step form.place .button-row').show();
    $('form.place .pp-express-checkout-button').hide();
    $('form.place .pcp-button-container').hide();
    $('form.place .pcp-hosted-fields-container').hide();

    //fastlane
    $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').show();
  }

  $(window).trigger('resize');
});

core.bind('checkout.common.state.nonready', function (state) {
  $('form.place .pp-express-checkout-button').addClass('nonready');
});

core.bind('checkout.common.state.ready', function (state) {
  $('form.place .pp-express-checkout-button').removeClass('nonready');
});

