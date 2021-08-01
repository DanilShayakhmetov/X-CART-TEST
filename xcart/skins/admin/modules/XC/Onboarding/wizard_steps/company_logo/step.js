/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Company logo
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/steps/company-logo', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-step-company-logo', {
    ready: function () {
      this.$form = $(this.$el).find('.logo-upload-form')
    },

    events: {
      'file-uploader-overlay': function () {
        this.$dispatch('blockBody')
      },
      'file-uploader-success': function (data, sender) {
        var temp = this.$form.find('.preview img').attr('src')
        if (temp) {
          this.updateLogo(temp)
          this.updateLogoFormParams(this.$form.serialize())
        }

        this.$dispatch('wizard.step.requestNext')
        this.$dispatch('unblockBody')
      },
      'file-uploader-error': function (error, sender) {
        this.$dispatch('unblockBody')
      }
    },

    vuex: {
      getters: {},

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
      skipStep: function () {
        this.$dispatch('wizard.step.switch', 'location', true)
      },
      resetForm: function () {
        var fileUploader = $(this.$form).find('.file-uploader')
        if (fileUploader.length) {
          fileUploader.each(function () {
            this.__vue__.reset()
          })
        }
      },
      openLayoutEditorVideo: function () {
        console.error('not implemented yey')
      }
    }
  })
})
