/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Languages items list javascript controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

ItemsList.prototype.listeners.currency = function(handler) {

  function hideCurrencyDefaultValue(switcher) {
    switcher
        .parents('div.cell')
        .find('input[name="defaultValue"][type="radio"]:not(:checked)')
        .parents('div.default-value-checkbox')
        .css('visibility', 'hidden');
  }

  jQuery('.input-checkbox-switcher.disabled').each(function() {
    hideCurrencyDefaultValue(jQuery(this));
  });

  jQuery('.input-checkbox-switcher').click (
      function() {
        defaultCurency = jQuery(this).parents('div.cell').find('div.default-value-checkbox');

        if (jQuery(this).hasClass('enabled') && defaultCurency.find('input[name="defaultValue"][type="radio"]:checked').length > 0) {
          return false;
        }

        if (jQuery(this).hasClass('disabled')) {
          hideCurrencyDefaultValue(jQuery(this));
        } else {
          defaultCurency.css('visibility', 'visible');
        }
      }
  );

  jQuery('input[name="defaultValue"][type="radio"]').click (
      function() {
        hideCurrencyDefaultValue(jQuery('.input-checkbox-switcher.disabled'));
      }
  );
}