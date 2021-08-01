/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('modules/XC/BulkEdit/form_model', ['js/vue/vue', 'form_model'], function (XLiteVue) {

  var state;
  var exitConfirmation = function (event) {
    event.returnValue = '';
    return '';
  };

  XLiteVue.component('xlite-form-model', {
    methods: {
      isChanged: function (model, event) {
        if (state === false) {
          state = objectHash.sha1(this.form);
          state = null;

          return true;
        }

        if (state === null) {
          state = objectHash.sha1(this.form);

          return false;
        }

        if (this.form.bulk_edit) {
          var edit = JSON.parse(this.form.bulk_edit);
          this.changed = edit.length > 0;
        }

        return this.changed;
      },
      setEdited: function (model, event) {
        var edit = JSON.parse(this.form.bulk_edit);

        var index = edit.indexOf(model);
        if (index != -1) {
          edit.splice(index, 1);
        }

        edit.push(model);
        this.form.bulk_edit = JSON.stringify(edit);

        window.onbeforeunload = exitConfirmation;

        var wrapper = $(event.target).siblings('.input-wrapper');

        Vue.nextTick(function () {
          wrapper.find('input:visible').eq(0).click().focus();
        });
      },
      setNotEdited: function (model, event) {
        var edit = JSON.parse(this.form.bulk_edit);

        var index = edit.indexOf(model);
        if (index != -1) {
          edit.splice(index, 1);
        }

        this.form.bulk_edit = JSON.stringify(edit);

        if (!this.isChanged()) {
          window.onbeforeunload = undefined;
        }
      },
      isEdited: function (model, event) {
        if (this.form.bulk_edit) {
          var edit = JSON.parse(this.form.bulk_edit);

          return edit.indexOf(model) != -1;
        }
      },
      onSubmit: function (event) {
        var self = this;
        if (!self.$form.invalid) {
          window.onbeforeunload = undefined;
        }

        this.$options.methods.onSubmit.parent.call(this, event);
      }
    }
  });
});
