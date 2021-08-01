/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sales channels popup
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonSalesChannels() {
  PopupButtonSalesChannels.superclass.constructor.apply(this, arguments);

  var self = this;
  jQuery('.menu-item.sales-channels:not(:has(.box))>.line>span.link').click(function () {
    self.base.click();
  });
}

extend(PopupButtonSalesChannels, PopupButton);

PopupButtonSalesChannels.prototype.pattern = '.action-widget .sales-channels';

core.autoload(PopupButtonSalesChannels);
