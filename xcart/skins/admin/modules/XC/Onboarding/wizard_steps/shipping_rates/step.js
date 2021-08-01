/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping rates
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/location', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-shipping-rates', {
    props: ['methods'],

    ready: function () {
      var self = this;
      this.$methodName = $(this.$el).find('.method-label .input-display');
      this.$methodInput = $(this.$el).find('.method-label .input-wrapper input');
      this.$methodName.on('focus', function() {
        self.focusedName = true;

        self.$nextTick(function() {
          self.$methodInput.focus();
          self.$methodInput.select();
        });
      });

      this.$methodInput.on('blur', function() {
        self.focusedName = false;
      });

      $(this.$el).find('.input-text-price input').on('click', function() {
        $(this).select();
      })
    },

    data: function () {
      return {
        country: null,
        name: null,
        focusedName: false,
        methodsActual: this.methods
      }
    },

    computed: {
      methodClasses: function () {
        var self = this;
        var methods = this.methodsActual || this.methods;
        return _.reduce(methods, function(result, item) {
          result[item.processor] = {};

          if (item.is_added) {
            result[item.processor]['added'] = true;
          }

          return result;
        }, {})
      }
    },

    events: {
      'country-update': function(code) {
        this.country = code;
      },
      'currency-update': function (prefix, suffix) {
        this.getRootElement().find('.flat-rate .input-text-price .input-group-addon').text(prefix || suffix);
      }
    },

    methods: {
      getRootElement: function () {
        return $(this.$el).closest('.onboarding-wizard-step');
      },
      goToNextStep: function () {
        this.$dispatch('wizard.step.requestNext');
        this.$dispatch('wizard.landmark.pass', 'shipping');
      },
      isMethodAvailable: function(processor) {
        if (processor === 'capost') {
          return this.country === 'CA';
        }

        if (processor === 'aupost') {
          return this.country === 'AU';
        }

        if (processor === 'usps') {
          return _.contains(['US', 'CA'], this.country);
        }

        if (processor === 'ems') {
          return this.country === 'RU';
        }

        return true;
      },
      createMethod: function () {
        this.$dispatch('blockBody');

        var data = {
          method_label: this.getRootElement().find('.method-label input').val(),
          zone_id: this.getRootElement().find('.zones select').val(),
          flat_rate: this.getRootElement().find('.flat-rate input[type=text]').val()
        };

        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            target: 'onboarding_wizard',
            action: 'create_shipping_method'
          },
          this.handleResponseCreateMethod,
          data
        );
      },
      handleResponseCreateMethod: function (xhr, status, response) {
        this.$dispatch('unblockBody');

        if (status === 'success') {
          this.$dispatch('wizard.tracking.event', 'form', 'Shipping rate');
          this.goToNextStep();
        }
      },
      addShippingMethod: function (url, event) {
        var elem = $(event.currentTarget).parent();
        if (elem.hasClass('added')) {
          return;
        }

        this.$dispatch('blockBody');

        core.get(
          url,
          _.partial(this.handleResponseAddMethod, url, elem)
        );

        return false;
      },
      handleResponseAddMethod: function (url, elem, xhr, status, response) {
        this.$dispatch('unblockBody');
        var self = this;

        if (status === 'success') {
          popup.open(this.renderPopup(
            url,
            elem.find('img').attr('src'),
            elem.find('img').attr('alt')
          ), {
            close: _.wrap(this.goToNextStep, function(func) {
              func.apply(self, arguments);
            })
          });

          var processor = elem.data('processor');
          self.$dispatch('wizard.tracking.event', 'link', 'Add shipping method', {onboarding_shippingmethod: processor});

          this.methodsActual[processor].is_added = true;

          var carriers = elem.closest('.shipping-carriers');
        }
      },
      renderPopup: function (url, image_url, method_name) {
        var popup = this.getRootElement().find('.popup-template').clone();
        popup.find('img').attr('src', image_url);
        popup.find('img').attr('alt', method_name);
        popup.find('.note').text(popup.find('.note').text().replace('[[carrier]]', method_name));
        popup.find('.settings-link').attr('href', url);

        popup.find('button').click(this.closePopupAndGoToNextStep);

        return popup;
      },
      closePopupAndGoToNextStep: function () {
        popup.close();
        this.goToNextStep();
      }
    }
  });
});