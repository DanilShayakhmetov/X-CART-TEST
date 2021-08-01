/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Company logo added
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/company-logo-added', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-company-logo-added', {
    ready: function () {
      this.$form = $(this.$el).find('.logo-upload-form')
    },

    computed: {},

    vuex: {
      getters: {
        logoUrl: function (state) {
          return state.logo
        },
        logoParams: function (state) {
          return state.logoParams
        }
      },
      actions: {
        updateLogo: function (state, url) {
          state.dispatch('UPDATE_LOGO', url)
        },
        updateLogoFormParams: function (state, params) {
          state.dispatch('UPDATE_LOGO_PARAMS', params)
        }
      }
    },

    methods: {
      save: function () {
        if (!this.logoParams) {
          console.log('Something wrong happened')
          this.$dispatch('wizard.step.requestNext')
          this.$dispatch('wizard.landmark.pass', 'company')
        }

        this.$dispatch('blockBody')

        core.post(
          {
            target: 'onboarding_wizard',
            action: 'upload_company_logo'
          },
          null,
          this.logoParams,
          {
            dataType: 'json',
            rpc: true
          })
          .done(_.bind(this.onUploadSuccess, this))
          .fail(_.bind(this.onUploadFail, this))
      },

      onUploadSuccess: function (data) {
        if (data.logo) {
          this.updateLogo(data.logo)
        }
        this.$dispatch('wizard.tracking.event', 'form')
        this.$dispatch('wizard.step.requestNext')
        this.$dispatch('wizard.landmark.pass', 'company')
        this.$dispatch('unblockBody')
      },

      onUploadFail: function () {
        this.$dispatch('unblockBody')
      },

      goToNextStep: function () {
        this.$dispatch('wizard.tracking.event', 'link', '(skipped)')
        this.$dispatch('wizard.step.requestNext')
      },

      reuploadLogo: function () {
        this.$dispatch('wizard.step.requestPrevious')
      },

      openLayoutEditorVideo: function () {
        console.error('not implemented yey')
      },

      visitTemplateStore: function () {
        this.$dispatch('wizard.tracking.event', 'link', 'Visit the template store')
        window.open('service.php#/templates')
      }
    }
  })
})