/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceChangeItemEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceChangeItemEvent = eCommerceCoreEvent.extend({
        getListeners: function () {
          return {
              'productAddedToCart': this.registerAddToCart,
          };
        },

        registerAddToCart: function (event, data) {
          var gaProductData = data.gaProductData;

          if (gaProductData) {
            ga('ec:addProduct', gaProductData);

            if (!_.isUndefined(gaProductData.list)) {
              ga('ec:setAction', 'add', {list: gaProductData.list});
            } else {
              ga('ec:setAction', 'add');
            }

            ga('send', 'event', 'Product', 'AddToCart', 'Add to cart');
          }
        },

        processReady: function () {
          var self = this;

          _.each(
              this.getActions('items-changed'),
              function (action, index) {
                self.registerItemChangedByAdmin(action.data);
              }
          );

          var orderChangedData = _.first(
              this.getActions('order-changed')
          );

          if (orderChangedData) {
            this.registerOrderChangedByAdmin(
                orderChangedData['data']
            );
          }
        },

        registerOrderChangedByAdmin: function (data) {
          data.actionData = data.actionData || {};

          ga('ec:setAction', data.actionName, data.actionData);
          ga('send', 'event', 'AOM', data.actionName);
        },

        registerItemChangedByAdmin: function (data) {
          var message = 'Item change';
          data.actionData = data.actionData || {};
          if (data.actionName === 'purchase') {
            message = 'Add to cart'
          } else if (data.actionName === 'refund') {
            message = 'Remove from cart'
          }

          ga('ec:addProduct', data.productData);
          ga('ec:setAction', data.actionName, data.actionData);
          ga('send', 'event', 'AOM', data.actionName, message);
        },

      });

      eCommerceChangeItemEvent.instance = new eCommerceChangeItemEvent();

      return eCommerceChangeItemEvent;
    }
);