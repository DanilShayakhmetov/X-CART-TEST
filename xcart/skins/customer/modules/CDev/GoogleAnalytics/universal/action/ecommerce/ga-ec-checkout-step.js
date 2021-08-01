/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceCheckoutStepEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceCheckoutStepEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'ga-pageview-sending':    this.registerCheckoutEnter,
            'ga-pageview-sent':       this.registerInitialCheckoutOptions,
            'ga-ec-checkout':         this.registerCheckoutExternal,
            'ga-ec-checkout-option':  this.registerCheckoutOptionExternal,
            'checkout.common.ready':  this.registerPlaceOrder,
          };
        },

        registerCheckoutEnter: function (event, data) {
          var actionData = _.first(
            this.getActions('checkout')
          );

          if (actionData) {
            if (this.isForceLoginPage()) {
              this._registerCheckoutEnter(
                actionData['data']['products'],
                { step: 1 }
              );
              ga('send', 'event', 'Checkout', 'Checkout entered');

            } else if (this.isOnePageCheckout()) {
              this._registerCheckoutEnter(
                actionData['data']['products'],
                { step: 1 }
              );
              ga('send', 'event', 'Checkout', 'Checkout entered');

              this._registerCheckoutEnter(
                actionData['data']['products'],
                { step: 2 }
              );
              ga('send', 'event', 'Checkout', 'Checkout continue');

              this._registerCheckoutEnter(
                actionData['data']['products'],
                { step: 3 }
              );
              ga('send', 'event', 'Checkout', 'Checkout continue');
            }
          }
        },

        registerPlaceOrder: function() {
          if (this.isOnePageCheckout()) {
            core.trigger('ga-ec-checkout-option', {
              step: 1,
              option: 'Address chosen',
            });
            core.trigger('ga-ec-checkout-option', {
              step: 2,
              option: this.getOPCShippingMethodName(),
            });
            core.trigger('ga-ec-checkout-option', {
              step: 3,
              option: this.getOPCPaymentMethodName(),
            });

            var checkoutActionData = _.first(
              this.getActions('checkout')
            );

            if (checkoutActionData) {
              core.trigger('ga-ec-checkout', {
                products: checkoutActionData.data.products,
                actionData: {step: 4},
                message: 'Checkout continue'
              });
            }
          }
        },

        registerCheckoutExternal: function(event, data) {
          if (!data
            || _.isUndefined(data['products'])
            || _.isUndefined(data['actionData'])
          ) {
            return;
          }

          this._registerCheckoutEnter(
              data['products'],
              data['actionData']
          );
          ga('send', 'event', 'Checkout', data.message || 'Checkout entered');
        },

        registerInitialCheckoutOptions: function(event, data) {
          var self = this;

          _.each(
              this.getActions('checkout-option'),
              function (action, index) {
                self._registerCheckoutOption(action.data);
                ga('send', 'event', 'Checkout', 'Option');
              }
          );
        },

        registerCheckoutOptionExternal: function(event, data) {
          this._registerCheckoutOption(data);

          if (!_.isUndefined(ga.loaded) && ga.loaded) {
            ga('send', 'event', 'Checkout', 'Option', {
              hitCallback: function() {
                core.trigger('ga-option-sent', data);
              }
            });
          } else {
            core.trigger('ga-option-sent', data);
          }
        },

        _registerCheckoutOption: function(data) {
          if (!data || _.isUndefined(data.option)) {
            return;
          }

          ga('ec:setAction', 'checkout_option', data);
        },

        _registerCheckoutEnter: function (productsData, actionData) {
          _.each(productsData, function(product) {
            ga('ec:addProduct', product);
          });
          ga('ec:setAction', 'checkout', actionData || {});
        },

        isOnePageCheckout: function () {
          return jQuery('.checkout_fastlane_container').length === 0
        },

        isForceLoginPage: function () {
          return jQuery('.signin-anonymous-wrapper').length !== 0;
        },

        getOPCPaymentMethodName: function () {
          return jQuery('.step-payment-methods input[name="methodId"]:checked').siblings('.payment-title').text();
        },

        getOPCShippingMethodName: function () {
          return jQuery('.step-shipping-methods input[name="methodId"]:checked').siblings('.rate-title').text();
        },

      });

      eCommerceCheckoutStepEvent.instance = new eCommerceCheckoutStepEvent();

      return eCommerceCheckoutStepEvent;
    }
);
