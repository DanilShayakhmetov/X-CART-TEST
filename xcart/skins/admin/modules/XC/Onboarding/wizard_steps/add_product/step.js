/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add product
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/add-product', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-add-product', {

    props: ['skipStep'],

    activate: function (done) {
      done();
    },

    ready: function() {
      this.$form = $(this.$el).find('form');

      $(this.$form).find('#form_default_price').on('click', function() {
        $(this).select();
      })
    },

    vuex: {
      getters: {
        demoCatalog: function(state) {
          return state.demoCatalog;
        },
        demoRemovalSkip: function(state) {
          return state.demoRemovalSkip;
        },
        product: function(state) {
          return state.product;
        },
        cloud: function() {
          return core.isCloud;
        }
      },

      actions: {
        updateProduct: function(state, id) {
          state.dispatch('UPDATE_PRODUCT', id);
        },
        updateProductName: function(state, name) {
          state.dispatch('UPDATE_PRODUCT_NAME', name);
        },
        updateProductImage: function(state, image) {
          state.dispatch('UPDATE_PRODUCT_IMAGE', image);
        },
      }
    },

    computed: {
    },

    watch: {
    },

    transitions: {
    },

    events: {
      'form-model-reset': function(form) {
        form.$children.forEach(function(field) {
          if ('undefined' !== typeof(field.reset)) {
            field.reset();
          }
        })
      },
      'form.submit.skip': function() {
        this.$dispatch('wizard.tracking.event', 'link', '(skip)');
        if (this.skipStep && ((this.demoCatalog === false || this.demoRemovalSkip === true) || (this.cloud && !this.product))) {
          this.$dispatch('wizard.step.switch', this.skipStep, true);
        } else {
          this.$dispatch('wizard.step.requestNext');
        }
      },
      'form.submit.success': function(data) {
        this.updateProduct(data.product);
        this.updateProductName(data.productName);
        this.updateProductImage(data.productImage);
        this.$dispatch('wizard.tracking.event', 'form');

        if (this.demoRemovalSkip === true && this.skipStep) {
          this.$dispatch('wizard.step.switch', this.skipStep, true);
        } else {
          this.$dispatch('wizard.step.requestNext');
        }

        this.$dispatch('wizard.landmark.pass', 'product');
        this.$dispatch('unblockBody');
      },
      'form.submit.fail': function() {
        this.$dispatch('unblockBody');
      },
      'currency-update': function (prefix, suffix) {
        this.getRootElement().find('.form-default-price .input-widget .input-group-addon').text(prefix || suffix);
      }
    },

    methods: {
      getRootElement: function () {
        return $(this.$el).closest('.onboarding-wizard-step');
      },
      hideWizard: function() {
        this.$dispatch('wizard.hide');
      },
    }
  });
});