/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Popover
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('XC/VendorMessages/vendor_info/popover', ['XC/MultiVendor/vendor_info/popover'], function (MultivendorPopover) {
  decorate(
    MultivendorPopover,
    'onShown',
    function()
    {
      arguments.callee.previousMethod.apply(this, arguments);

      core.autoload(PopupButtonLogin);
    }
  );

  core.autoload(PopupButtonLogin);
});
