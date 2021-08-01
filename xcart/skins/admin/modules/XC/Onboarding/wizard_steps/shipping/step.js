/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping rates
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/location', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-shipping', {
    ready: function () {
    },

    data: function () {
      return {
        shipping_enabled: null
      }
    },

    events: {},

    methods: {
      getRootElement: function () {
        return $(this.$el).closest('.onboarding-wizard-step');
      },
      goToShipping: function () {
        this.$dispatch('wizard.tracking.event', 'link', 'Enable shipping');
        if (this.shipping_enabled) {
          this.$dispatch('wizard.step.requestNext');
        } else {
          this.enableShipping();
        }
      },
      enableShipping: function () {
        this.$dispatch('blockBody');

        var data = {};

        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            target: 'onboarding_wizard',
            action: 'enable_shipping'
          },
          this.handleResponseEnableShipping,
          data
        );
      },
      handleResponseEnableShipping: function (xhr, status, response) {
        this.$dispatch('unblockBody');

        if (status === 'success') {
          this.shipping_enabled = true;
          this.$dispatch('wizard.step.requestNext');
        }
      },
      skipShipping: function () {
        this.$dispatch('wizard.tracking.event', 'link', 'Disable shipping');
        if (this.shipping_enabled) {
          this.disableShipping();
        } else {
          this.$dispatch('wizard.landmark.pass', 'shipping');
          this.$dispatch('wizard.step.switch', 'payment', true);
        }
      },
      disableShipping: function () {
        this.$dispatch('blockBody');

        var data = {};

        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            target: 'onboarding_wizard',
            action: 'disable_shipping'
          },
          this.handleResponseDisableShipping,
          data
        );
      },
      handleResponseDisableShipping: function (xhr, status, response) {
        this.$dispatch('unblockBody');

        if (status === 'success') {
          this.shipping_enabled = false;
          this.$dispatch('wizard.landmark.pass', 'shipping');
          this.$dispatch('wizard.step.switch', 'payment', true);
        }
      }
    }
  });
});