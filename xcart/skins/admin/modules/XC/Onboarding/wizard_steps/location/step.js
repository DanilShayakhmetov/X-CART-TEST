/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Location
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/location', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-location', {
    ready: function () {
      var self = this
      core.bind('location_map_ready', function () {
        self.updateLocationMap()
      })
      this.updateLocationMap()
    },

    data: function () {
      return {
        country: null,
        has_states: null,
        currency: null,
        weight_unit: null
      }
    },

    watch: {
      country: function (new_val, old_val) {
        this.updateLocationMap(new_val)
        this.updateCurrencyByCountry(new_val)
        this.updateWeightUnitByCountry(new_val)
      },
      currency: function (new_val, old_val) {
        this.updateCurrency(new_val)
      },
      weight_unit: function (new_val, old_val) {
        this.updateWeightUnit(new_val)
      }
    },

    events: {
      'currency-update': function (prefix, suffix) {
        this.getRootElement().find('.currency .example .prefix').text(prefix)
        this.getRootElement().find('.currency .example .suffix').text(suffix)
      },
      'weight-unit-update': function (unit) {
        this.getRootElement().find('.weight-unit .example .unit').text(unit)
      }
    },

    methods: {
      getRootElement: function () {
        return $(this.$el).closest('.onboarding-wizard-step')
      },
      updateAddress: function () {
        this.$dispatch('blockBody')
        var url = {
          target: 'onboarding_wizard',
          action: 'update_location'
        }

        var data = {
          country: this.country,
          currency: this.currency,
          weight_unit: this.weight_unit
        }

        var currenciesList = core.getCommentedData($('#location-currency').parents('.location-currency-value').get(0), 'currencies')

        this.$dispatch('wizard.tracking.event', 'form', '', {
          onboarding_country: this.country,
          onboarding_currency: currenciesList[this.currency],
          onboarding_weight_unit: this.weight_unit
        })

        data[xliteConfig.form_id_name] = xliteConfig.form_id

        core.post(
          url,
          this.handleResponse,
          data
        )
      },
      handleResponse: function (xhr, status, response) {
        this.$dispatch('unblockBody')
        if (status === 'success') {
          this.$dispatch('wizard.step.requestNext')
        }
      },
      updateLocationMap: function (code) {
        if (!code) {
          code = this.country
        }

        var option = this.getRootElement().find('.fields .country-selector select option[value="' + code + '"]')

        var name = option.data('name') || option.text()

        core.trigger('updateLocationMap', {
          'code': code,
          'name': name
        })

        this.$root.$broadcast('country-update', code)
      },
      updateCurrencyByCountry: function (id) {
        var option = this.getRootElement().find('.fields .country-selector select option[value="' + id + '"]')

        if (option.length) {
          var currencyId = option.data('currency')

          if (currencyId > 0) {
            this.currency = currencyId
          }
        }
      },
      updateWeightUnitByCountry: function (id) {
        var option = this.getRootElement().find('.fields .country-selector select option[value="' + id + '"]')

        if (option.length) {
          var weightUnit = option.data('weight-unit')

          if (weightUnit) {
            this.weight_unit = weightUnit
          }
        }
      },
      updateCurrency: function (id) {
        var option = this.getRootElement().find('.fields .currency select option[value="' + id + '"]')

        if (option.length) {
          var prefix = option.data('prefix')
          var suffix = option.data('suffix')

          this.$root.$broadcast('currency-update', prefix, suffix)
        }
      },
      updateWeightUnit: function (unit) {
        this.$root.$broadcast('weight-unit-update', unit)
      }
    }
  })
})
