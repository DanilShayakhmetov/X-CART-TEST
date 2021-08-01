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
      if (!this.logoUrl) {
        this.updateLogo(this.defaultLogo);
      }
    },

    props: {
      'defaultLogo': '',
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

        this.save()
      },
      'file-uploader-error': function (error, sender) {
        this.$dispatch('unblockBody')
      }
    },

    vuex: {
      getters: {
        logoUrl: function(state) {
          return state.logo;
        },
        logoParams: function (state) {
          return state.logoParams;
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
      skipStep: function () {
        this.$dispatch('wizard.step.switch', 'location', true)
      },
      save: function () {
        if (!this.logoParams) {
          console.log('Something wrong happened')
          return
        }

        this.$dispatch('wizard.tracking.event', 'link', 'logo uploading')

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
          this.$dispatch('wizard.landmark.pass', 'company')
          this.$dispatch('wizard.tracking.event', 'form')
        }
        this.$dispatch('unblockBody')
      },

      onUploadFail: function () {
        this.$dispatch('unblockBody')
      },
      openLayoutEditorVideo: function () {
        console.error('not implemented yey')
      },
      completeWizard: function () {
        this.$dispatch('wizard.close');
      },
    }
  })
})
