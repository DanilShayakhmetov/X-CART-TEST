/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/file_uploader/onboarding', ['js/vue/vue', 'file_uploader'], function (XLiteVue) {

  var fileUploadedDefer = null;
  XLiteVue.component('xlite-file-uploader', {
    props: {
      'uploadedMessage': '',
      uploadingTarget: {
        type: String,
        default: function () {
          return 'files'
        }
      }
    },
    ready: function () {
      this.showsMessages = true;
      this.commonData.target = this.uploadingTarget;
      if (fileUploadedDefer !== null) {
        fileUploadedDefer.resolve();
      }
    },
    computed: {
      message: function () {
        if (this.hasFile && this.uploadedMessage) {
          return this.uploadedMessage;
        }

        return this.helpMessage;
      }
    },
    methods: {
      reset: function () {
        this.temp_id = false;
        this.$reload();
      },
      assignWait: function () {
        var parent = this.$options.methods.assignWait.parent;
        this.$dispatch('file-uploader-overlay', parent, this, arguments);
      },
      doRequest: function () {
        fileUploadedDefer = new $.Deferred();
        var promise = this.$options.methods.doRequest.parent.apply(this, arguments);
        return promise.done(this.onUploadSuccess).fail(this.onUploadError);
      },
      onUploadSuccess: function (data, textStatus, jqXHR) {
        var error = jqXHR.getResponseHeader('X-Upload-Error');
        if (error) {
          this.realErrorMessage = error;
          this.$dispatch('file-uploader-error', error, this);
          return;
        }

        fileUploadedDefer.then(_.bind(function () {
          this.$dispatch('file-uploader-success', data, this);
        }, this));
      },
      onUploadError: function (jqXHR, textStatus, textResponse) {
        this.$dispatch('file-uploader-error', '', this);
      }
    }
  });

});
