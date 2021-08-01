/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

decorate(
  'CartView',
  'handleUpdateCart',
  function (event, data) {
    arguments.callee.previousMethod.apply(this, arguments);

    var registerItemChange = function (productData, action, eventAction, eventLabel) {
      ga('ec:addProduct', productData);
      ga('ec:setAction', action);
      ga('send', 'event', 'Product', eventAction, eventLabel);
    };

    if (data.items) {
      for (var i = 0; i < data.items.length; i++) {
        var item = data.items[i];

        item['ga-data']['quantity'] = Math.abs(item.quantity_change);

        registerItemChange(
          item['ga-data'],
          item.quantity_change > 0 ? 'add' : 'remove',
          item.quantity_change > 0 ? 'AddToCart' : 'RemoveFromCart',
          item.quantity_change > 0 ? 'Add to cart' : 'Remove from cart'
        );
      }
    }
  }
);