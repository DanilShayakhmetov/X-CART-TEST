/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Business info
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/business-info', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-business-info', {
    ready: function () {
      const form = this.getRootElement().find('form').get(0)

      CommonForm.autoassign(this.getRootElement())
      form.commonController.enableBackgroundSubmit(
        this.beforeSubmit,
        this.handleResponse
      )

      jQuery(form).find('#experience').change(function () {
        const experience = $(this).val()
        const $revenueSelector = jQuery(form).find('#revenue-selector')

        if (experience !== '' && experience !== 'not_selling' && experience !== 'building_for_another') {
          $revenueSelector.removeClass('hidden')
        } else {
          $revenueSelector.addClass('hidden')
          $revenueSelector.find('select').val('')
        }
      }).change()
    },

    methods: {
      getRootElement: function () {
        return $(this.$el).closest('.onboarding-wizard-step')
      },
      beforeSubmit: function (event) {
        this.$dispatch('blockBody')
      },
      handleResponse: function (event, xhr) {
        const data = JSON.parse(xhr.data)

        if (data && data['full-filled']) {
          this.$dispatch('wizard.landmark.pass', 'business')
          this.$dispatch('wizard.tracking.event', 'form', '', data)
        } else {
          this.$dispatch('wizard.tracking.event', 'link', 'skip')
        }

        this.$dispatch('unblockBody')
        this.$dispatch('wizard.step.requestNext')
      },
      hideWizard: function() {
        this.$dispatch('wizard.hide');
      },
    }
  })
})