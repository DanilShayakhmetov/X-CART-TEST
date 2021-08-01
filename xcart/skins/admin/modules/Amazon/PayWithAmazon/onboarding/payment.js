/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Payment
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/paypal/payment-signup', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-onboarding-amazon-pay', {
    mixins: [VueLoadableMixin],

    ready: function () {
    },

    loadable: {
      loader: function() {
        return core.get({
            target: 'onboarding_wizard',
            widget: 'XLite\\Module\\Amazon\\PayWithAmazon\\View\\Onboarding\\Payment',
          }, undefined, undefined, { timeout: 45000 }
        );
      },
    },

    data: function () {
      return {
      }
    },

    computed: {
      classes: function() {
        return {
          'reloading': this.$reloading
        }
      }
    },

    events: {},

    methods: {
      addMethod: function (method_id, link) {

        var data = {
          id: method_id
        };

        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            target: 'payment_settings',
            action: 'add'
          },
          '',
          data
        );
        window.open(link);
      },
      switchAmazonMethod: function (method_id, event) {
        var switcher = $(event.currentTarget).closest('.switcher');

        switcher.addClass('loading');

        var url = {
          target: 'payment_settings',
          action: 'enable'
        };

        if (switcher.hasClass('enabled')) {
          url['action'] = 'disable';
        } else {
          url['action'] = 'enable';
        }

        switcher.toggleClass('enabled');

        var data = {
          id: method_id
        };

        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          url,
          _.partial(this.handleResponse, switcher),
          data
        );

      },
      handleResponse: function (switcher, xhr, status, response) {
        switcher.removeClass('loading');

        if (status !== 'success') {
          switcher.toggleClass('enabled');
        }

        var newState = switcher.hasClass('enabled') ? 'enabled' : 'disabled';
        this.$dispatch('wizard.tracking.event', 'link', 'Amazon Pay ' + newState);
      }
    }
  });
});