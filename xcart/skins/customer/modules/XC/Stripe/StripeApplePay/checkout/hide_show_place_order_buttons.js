/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Stripe Apple Pay initialize
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Must Be The First Bind To Work If ($('.Stripe-Method-Is-Selected').Length > 0) Cond Properly
core.bind('checkout.common.state.ready', function (state) {
  $('#payment-request-button').removeClass('nonready');
});

core.bind([
  'checkout.main.ready',
  'checkout.common.anyChange',
  'checkout.sections.payment.persist',
  'checkout.paymentTpl.loaded',
  'checkout.common.state.ready'
], function (event, controller) {
  if ($('.stripe-method-is-selected').length > 0) {

    $('.review-step form.place .button-row').hide();
    $('#payment-request-button').show();

    //fastlane
    $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').hide();

  } else {
    $('.review-step form.place .button-row').show();
    $('#payment-request-button').hide();

    //fastlane
    $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').show();
  }
});

core.bind('checkout.common.state.nonready', function (state) {
  $('#payment-request-button').addClass('nonready');
});
