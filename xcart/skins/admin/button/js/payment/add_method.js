/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add payment method JS controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonAddPaymentMethod()
{
  PopupButtonAddPaymentMethod.superclass.constructor.apply(this, arguments);

  const params = jQuery.getQueryParameters();
  if (params.show_add_payment_popup) {
    jQuery('button.add-online-method-button').click();
  }
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonAddPaymentMethod, PopupButton);

// New pattern is defined
PopupButtonAddPaymentMethod.prototype.pattern = '.add-payment-method-button';

decorate(
  'PopupButtonAddPaymentMethod',
  'callback',
  function (selector)
  {
    jQuery('.page-tabs .tab').click(function () {
      jQuery('.page-tabs .tab, .tab-content .body-item').removeClass('selected');
      jQuery('.page-tabs .tab').removeClass('tab-current');

      if (jQuery(this).hasClass('all-in-one-solutions')) {
        jQuery('.page-tabs .tab.all-in-one-solutions, .tab-content .body-item.all-in-one-solutions').addClass('selected');
        jQuery('.page-tabs .tab.all-in-one-solutions').addClass('tab-current');

      } else {
        jQuery('.page-tabs .tab.payment-gateways, .tab-content .body-item.payment-gateways').addClass('selected');
        jQuery('.page-tabs .tab.payment-gateways').addClass('tab-current');
      }

      jQuery('.ui-widget-overlay').css('height', jQuery(document).height());
    });

    jQuery(function () {
      var isChrome = /Chrome\//.test(navigator.userAgent);

      if (isChrome) {
        jQuery('.chosen-search input').attr('autocomplete', 'disable');
      }
    });

    core.autoload(TableItemsListQueue);
    SearchConditionBox();
  }
);

// Autoloading new POPUP widget
core.autoload(PopupButtonAddPaymentMethod);
