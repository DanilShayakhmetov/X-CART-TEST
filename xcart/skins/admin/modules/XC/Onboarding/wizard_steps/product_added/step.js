/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product added
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/product-added', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-product-added', {
    props: {
      demoCatalog: Boolean,
      productUrlBase: String
    },
    ready: function() {
      this.updateDemoCatalogFlag(this.demoCatalog);
    },

    data: function () {
      return {
        isDeleted: false
      }
    },

    computed: {
      productUrl: function() {
        return this.productUrlBase.replace('PID', this.productId);
      },
      productNameLine: function() {
        return this.productName;
      },
      productImageLine: function() {
        return this.productImage;
      }
    },

    vuex: {
      getters: {
        productId: function(state) {
          return state.product;
        },
        productName: function(state) {
          return state.productName;
        },
        productImage: function(state) {
          return state.productImage;
        }
      },

      actions: {
        updateDemoCatalogFlag: function(state, value) {
          state.dispatch('UPDATE_DEMO_CATALOG_FLAG', value);
        },
        setDemoRemovalSkip: function(state, value) {
          state.dispatch('MARK_DEMO_REMOVAL_SKIP', value);
        }
      }
    },

    methods: {
      deleteDemoCatalog: function() {
        var self = this;
        this.$dispatch('blockBody');
        this.$dispatch('wizard.tracking.event', 'link', 'Demo catalog removal');

        var data = {};
        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            target: 'onboarding_wizard',
            action: 'remove_demo_catalog'
          },
          null,
          data,
          {
            rpc: true
          })
          .done(_.bind(this.onDeleteSuccess, this))
          .fail(_.bind(this.onDeleteFail, this));
      },
      skipStep: function() {
        this.setDemoRemovalSkip(true);
        this.$dispatch('wizard.tracking.event', 'link', 'Demo catalog removal (skipped)');
        this.$dispatch('wizard.step.requestNext');
      },
      goToNextStep: function() {
        this.$dispatch('wizard.step.requestNext');
      },
      onDeleteSuccess: function() {
        this.isDeleted = true;
        this.$dispatch('unblockBody');
      },
      onDeleteFail: function() {
        console.error('Demo catalog is not deleted');
        this.$dispatch('unblockBody');
      },
      hideWizard: function() {
        this.$dispatch('wizard.hide');
      },
    }
  });
});