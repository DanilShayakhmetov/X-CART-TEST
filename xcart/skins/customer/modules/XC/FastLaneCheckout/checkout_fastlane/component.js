/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout = {
  loadableCache: {}
};

define('checkout_fastlane/deps', function () { return {} });

define('checkout_fastlane/loader', ['vue/vue', 'ready'], function(Vue){

  if (core.isDeveloperMode) {
    Vue.config.debug = true;
    Vue.config.devtools = true;
  }

  define('checkout_fastlane/app', ['vue/vue', 'checkout_fastlane/sections', 'checkout_fastlane/store', 'checkout_fastlane/navigation', 'checkout_fastlane/deps'], function(Vue, Sections, Store, Navigation, Deps) {
    var App = Vue.extend({
      name: 'checkout',
      replace: false,

      data: function () {
        return {
          uid: null
        }
      },

      created: function() {
        core.trigger('checkout.main.initialize');
        core.bind('fastlane_section_switched', _.bind(this.updateSectionHandler, this));
      },

      ready: function() {
        core.trigger('checkout.main.postprocess');
        this.$broadcast('checkStartSection');
        this.assignGlobalListeners();
        core.trigger('checkout.main.ready');
        $(this.$el).removeClass('immediate');

        this.$nextTick(function() {
          $(this.$el).removeClass('reloading reloading-animated');
        });
      },

      components: {
        Sections: Sections,
        Navigation: Navigation,
      },

      methods: {
        getState: function() {
          return this.$store.state;
        },
        startLoadAnimation: function(message) {
          message = message || '';
          var msgBox = document.createElement('div');
          $(msgBox).text(message).addClass('reloading-message');
          $('body').children().remove('.reloading-message').remove('.reloading-element');
          $('body').addClass('reloading reloading-animated').append('<div class="reloading-element"></div>').append(msgBox);
        },
        finishLoadAnimation: function() {
          $('body').children().remove('.reloading-message').remove('.reloading-element');
          $('body').removeClass('reloading reloading-animated');
        },
        reloadBlock: function(blockName) {
          if (jQuery(blockName).length) {
            jQuery(blockName).get(0).__vue__.$reload();
          } else {
            console.error('Trying to reload undefined checkout block ' + blockName);
          };
        },
        updateSectionHandler: function(event, data) {
          if (!_.isUndefined(data['oldSection']) && data['oldSection'] !== null) {
            jQuery('body').removeClass('fastlane-step-' + data['oldSection']['name']);

            if (data['oldSection']['name'] == 'address') {
              history.pushState(null, null, null);
            }
          };
          if (!_.isUndefined(data['newSection']) && data['newSection'] !== null) {
            jQuery('body').addClass('fastlane-step-' + data['newSection']['name']);
          };
        },
        assignGlobalListeners: function() {
          core.bind('updatecart', _.bind(this.broadcastCoreEvent('global_updatecart'), this));
          core.bind('loginexists', _.bind(this.broadcastCoreEvent('global_loginexists'), this));
          core.bind('invalidElement', _.bind(this.broadcastCoreEvent('global_invalidelement'), this));
          core.bind('selectcartaddress', _.bind(this.broadcastCoreEvent('global_selectcartaddress'), this));
          core.bind('createShippingAddress', _.bind(this.broadcastCoreEvent('global_createshippingaddress'), this));
        },
        broadcastCoreEvent: function(name) {
          return function(event, data) {
            this.$broadcast(name, data);
          }
        },
      },

      store: Store
    });

    Checkout.instance = new App({ el: '.checkout_fastlane_container' });

    return Checkout.instance;
  });
});  
