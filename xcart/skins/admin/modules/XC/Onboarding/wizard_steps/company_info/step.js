/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Company info
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/company-info', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-company-info', {
    ready: function () {
      CommonForm.autoassign(this.getRootElement())
      this.getRootElement().find('form').get(0).commonController.enableBackgroundSubmit(
        this.beforeSubmit,
        this.handleResponse
      )
    },

    methods: {
      getRootElement: function () {
        return $(this.$el).closest('.onboarding-wizard-step')
      },
      beforeSubmit: function (event) {
        this.$dispatch('blockBody')
        this.$dispatch('wizard.tracking.event', 'form')
      },
      handleResponse: function (event, xhr) {
        this.$dispatch('unblockBody')
        this.$dispatch('wizard.step.requestNext')
        this.$dispatch('wizard.landmark.pass', 'location')
      }
    }
  })
})