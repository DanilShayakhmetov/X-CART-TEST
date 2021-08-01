/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add product form
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('modules/XC/Onboarding/form_model/product/info', ['js/vue/vue', 'form_model'], function (XLiteVue) {
  XLiteVue.component('xlite-form-model', {
    methods: {
      isChanged: function (model, event) {
        var result = !this.$form.invalid;

        this.changed = result;
        return result;
      },
    }
  });
});