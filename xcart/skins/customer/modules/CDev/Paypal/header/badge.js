/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal header bar badge
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
  var headerBar = $('.desktop-header');
  headerBar.on('affix-top.bs.affix', function () {
    $(this).find('.header_paypal-icon span').fadeIn();
  });
  headerBar.on('affixed.bs.affix', function () {
    $(this).find('.header_paypal-icon span').fadeOut();
  });
});
