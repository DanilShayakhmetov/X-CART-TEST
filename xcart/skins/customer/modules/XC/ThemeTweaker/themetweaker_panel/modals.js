/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * store.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('themetweaker/modals',
      [],
      function() {

      var modals = {
        data: function () {
          return {
            isExitConfirmVisible: false,
            isSaveConfirmVisible: false,
            isErrorDialogVisible: false,
            isResetLayoutConfirmVisible: false,
            errorMessage: '',
            callbacks: {
              errorDialog: {
                ok: null,
                cancel: null
              },
              resetLayoutConfirm: {
                ok: null,
                cancel: null
              },
              saveConfirm: {
                ok: null,
                cancel: null
              },
              exitConfirm: {
                save: null,
                cancel: null,
                discard: null
              }
            },
          }
        },


        events: {
          showErrorDialog: function (message, ok, cancel) {
            this.errorMessage = message;
            this.errorDialog(ok, cancel);
          },

          showResetLayoutConfirm: function (ok, cancel) {
            this.callbacks.resetLayoutConfirm.ok = ok;
            this.callbacks.resetLayoutConfirm.cancel = cancel;
            this.isResetLayoutConfirmVisible = true;
          },

          'exitConfirm.cancel': function () {
            this.isExitConfirmVisible = false;
            if (this.callbacks.exitConfirm.cancel) {
              this.callbacks.exitConfirm.cancel.apply(this);
            }
          },

          'saveConfirm.cancel': function () {
            this.isSaveConfirmVisible = false;
          },

          'saveConfirm.ok': function () {
            this.isSaveConfirmVisible = false;
            if (this.callbacks.saveConfirm.ok) {
              this.callbacks.saveConfirm.ok.apply(this);
            }
          },

          'resetLayoutConfirm.cancel': function () {
            this.isResetLayoutConfirmVisible = false;
            if (this.callbacks.resetLayoutConfirm.cancel) {
              this.callbacks.resetLayoutConfirm.cancel.apply(this);
            }
          },

          'resetLayoutConfirm.ok': function () {
            this.isResetLayoutConfirmVisible = false;
            if (this.callbacks.resetLayoutConfirm.ok) {
              this.callbacks.resetLayoutConfirm.ok.apply(this);
            }
          }
        },

        methods: {
          exitConfirm: function (onSave, onDiscard, onCancel) {
            this.callbacks.exitConfirm.save = onSave;
            this.callbacks.exitConfirm.discard = onDiscard;
            this.callbacks.exitConfirm.cancel = onCancel;
            this.isExitConfirmVisible = true;
          },

          onExitConfirmSave: function () {
            this.isExitConfirmVisible = false;
            if (this.callbacks.exitConfirm.save) {
              this.callbacks.exitConfirm.save.apply(this);
            }
          },

          onExitConfirmDiscard: function () {
            this.isExitConfirmVisible = false;
            if (this.callbacks.exitConfirm.discard) {
              this.callbacks.exitConfirm.discard.apply(this);
            }
          },

          onErrorDialogOk: function () {
            this.isErrorDialogVisible = false;
            if (this.callbacks.errorDialog.ok) {
              this.callbacks.errorDialog.ok.apply(this);
            }
          },

          onErrorDialogCancel: function () {
            this.isErrorDialogVisible = false;
            if (this.callbacks.errorDialog.cancel) {
              this.callbacks.errorDialog.cancel.apply(this);
            }
          },

          saveConfirm: function (onOk, onCancel) {
            this.callbacks.saveConfirm.ok = onOk;
            this.callbacks.saveConfirm.cancel = onCancel;
            this.isSaveConfirmVisible = true;
          },

          errorDialog: function (onOk, onCancel) {
            this.callbacks.errorDialog.ok = onOk;
            this.callbacks.errorDialog.cancel = onCancel;
            this.isErrorDialogVisible = true;
          },
        }
      }

      return modals;
});