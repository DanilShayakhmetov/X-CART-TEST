/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * shipping-methods.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/blocks/shipping_methods', 
 ['vue/vue',
  'vue/vue.loadable',
  'checkout_fastlane/sections/shipping'],
  function(Vue, VueLoadableMixin, ShippingSection) {

  var shippingMethodInitialLoaded = false;

  var ShippingMethods = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'shipping-methods',
    replace: false,
    props: ['deferred'],

    loadable: {
      transferState: false,
      loader: function() {
        this.watcherIsBlocked = true;
        this.$root.$broadcast('reloadingBlock', 1);
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\ShippingMethods'
        }, undefined, undefined, { timeout: 45000 });
      },
      resolve: function() {
        var self = this;
        // updates window.shippingMethodsList
        $.globalEval($('#ShippingMethodsWidgetData').text());
        this.$root.$broadcast('reloadingUnblock', 1);
        this.$nextTick(function () {
          self.watcherIsBlocked = false;
        })
      },
      reject: function() {
        this.$root.$broadcast('reloadingUnblock', 1);
        this.watcherIsBlocked = false;
      }
    },

    ready: function() {
      if (this.deferred && !shippingMethodInitialLoaded) {
        shippingMethodInitialLoaded = true;
        this.$reload();
      } else {
        this.assignChosen();
      }
    },

    data: function() {
      return {
        watcherIsBlocked: false,
        selector: null,
        methodId: null,
      };
    },

    computed: {
      classes: function () {
        return {
          'reloading': this.$reloading,
          'reloading-animated': this.$reloading
        }
      },

      isValid: {
        cache: false,
        get: function() {
          return this.methodId;
        }
      }
    },

    watch: {
      methodId: function(value, oldValue){
        var silent = (oldValue === null || this.watcherIsBlocked);

        if (!silent) {
          this.$reloading = true;
          this.$root.$broadcast('reloadingBlock', 1);
        }

        this.triggerUpdate({
          silent: silent
        });
      }
    },

    events: {
      sectionPersist: function(data) {
        this.$reloading = false;
        this.$root.$broadcast('reloadingUnblock', 1);
      },
      global_createshippingaddress: function(data) {
        this.$reload();
      },
      global_updatecart: function(data) {
        var shippingKeys = ['shippingMethodsHash', 'shippingMethodId'];
        var needsUpdate = _.some(shippingKeys, function(key) {
          return _.has(data, key);
        });

        if (needsUpdate) {
          this.$reload();
        }
      },
    },

    vuex: {
      actions: {
        updateMethod: function(state, value) {
          state.dispatch('UPDATE_SHIPPING_METHOD', value);
        },
      }
    },

    methods: {
      triggerUpdate: function(options) {
        options = options || {};
        var eventArgs = _.extend({
          sender: this,
          isValid: this.isValid,
          fields: this.toDataObject()
        }, options);

        this.$dispatch('update', eventArgs);
        this.updateMethod(this.methodId);
      },
      toDataObject: function() {
        return {
          methodId: this.methodId
        };
      },
      onChosenChange: function(select) {
        this.methodId = select.val();
      },
      assignChosen: function () {
        var self = this;
        var select = $(this.$el).find('.selector-view.rich select.rich');
        if (select.length > 0) {
          var options = {};
          if (select.data('disable-search') == 1) {
            options.disable_search = true;
          }
          select.chosen(options).on('change', function(e) {
            self.onChosenChange($(this));
          });
          select.next('.chosen-container').css({
            'width':     'auto',
            'min-width': select.parent().width()
          });
        }
      }
    }
  });

  Vue.registerComponent(ShippingSection, ShippingMethods);

  return ShippingMethods;
});
