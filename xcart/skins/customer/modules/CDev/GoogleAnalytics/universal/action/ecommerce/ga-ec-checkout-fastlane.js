/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'googleAnalytics/checkout_fastlane/sections/payment/place_order', 
  ['checkout_fastlane/sections/payment/place_order',
   'googleAnalytics/eCommerceCheckoutFastlaneEvent',
   'checkout_fastlane/sections/section_change_button',
   'ready'],
  function(PlaceOrder, eCommerceCheckoutFastlaneEvent, SectionChangeButton){
    var oldPlaceOrder = PlaceOrder.options.methods.placeOrder;

    var PlaceOrder = PlaceOrder.extend({
      methods: {
        placeOrder: function() {
          var self = this;
          eCommerceCheckoutFastlaneEvent.instance.paymentSectionCompleted(function(){
            oldPlaceOrder.apply(self, arguments);
          });
        },
      }
    });

    Vue.registerComponent(SectionChangeButton, PlaceOrder);

    return PlaceOrder;
  }
);

define('googleAnalytics/eCommerceCheckoutFastlaneEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceCheckoutFastlaneEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'fastlane_section_switched':  this.sectionChanged,
            'checkout.common.ready':      this.registerPlaceOrder,
          };
        },

        registerPlaceOrder: function() {
          core.trigger('ga-ec-checkout-option', {
            step: 3,
            option: this.getOptionBySection('payment'),
          });

          var checkoutActionData = _.first(
            this.getActions('checkout')
          );

          if (checkoutActionData) {
            core.trigger('ga-ec-checkout', {
              products:     checkoutActionData.data.products,
              actionData:   { step: 4 },
              message:      'Checkout continue'
            });
          }
        },

        paymentSectionCompleted: function(callback) {

          this.registerCompletedSection(
            Checkout.instance.getState().sections.current
          );

          core.bind('ga-option-sent', _.once(callback));
        },

        sectionChanged: function (event, data) {
          var oldStep = 0;
          if (!_.isNull(data.oldSection) && !_.isUndefined(data.oldSection)) {
            this.registerCompletedSection(data.oldSection);
            oldStep = data.oldSection.index + 1;
          }

          if (!_.isNull(data.newSection) && !_.isUndefined(data.newSection)) {
            var newStep = data.newSection.index + 1;
            var checkoutActionData = _.first(
              this.getActions('checkout')
            );
            for (++oldStep; oldStep < newStep; oldStep++) {
              var actionData = { step: oldStep };
              var message = 'Checkout continue';

              if (oldStep === 1) {
                actionData.option = 'Address chosen';
                message = 'Checkout entered';
              }
              if (oldStep === 2 && !_.isUndefined(checkoutActionData.data.shipping_method)) {
                actionData.option = checkoutActionData.data.shipping_method;
              }

              core.trigger('ga-ec-checkout', {
                products:     checkoutActionData.data.products,
                actionData:   actionData,
                message:      message
              });
            }

            this.registerNewSection(data.newSection);
          }
        },

        registerCompletedSection: function (section) {
          var step = section.index + 1;

          core.trigger('ga-ec-checkout-option', {
            step: step,
            option: this.getOptionBySection(section.name),
          });
        },
        
        registerNewSection: function (section) {
          var step = section.index + 1;
          var checkoutActionData = _.first(
              this.getActions('checkout')
          );
          var message = 'Checkout continue';
          if (step === 1) {
            message = 'Checkout entered';
          }

          var data = {
            products:     checkoutActionData.data.products,
            actionData:   { step: step },
            message:      message
          };

          core.trigger('ga-ec-checkout', data);
        },

        getOptionBySection: function (sectionName) {
          var order = Checkout.instance.getState().order;

          if (sectionName === 'address') {
            return 'Address chosen';

          } else if (sectionName === 'shipping') {
            return this.getShippingMethodName(order.shipping_method);

          } else if (sectionName === 'payment') {
            return this.getPaymentMethodName(order.payment_method);
          }

          return sectionName + ' completed';
        },

        getPaymentMethodName: function (id) {
          return jQuery('#pmethod' + parseInt(id)).siblings('.payment-title').text();
        },

        getShippingMethodName: function (id) {
          return window.shippingMethodsList[parseInt(id)];
        },

      });

      eCommerceCheckoutFastlaneEvent.instance = new eCommerceCheckoutFastlaneEvent();

      return eCommerceCheckoutFastlaneEvent;
    }
);
