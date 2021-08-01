/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/sticky_panel/non_sticky', ['js/vue/vue', 'form_model/sticky_panel'], function (XLiteVue) {

  XLiteVue.component('xlite-sticky-panel', {
    computed: {
      formFieldValues: function() {
        return this.$parent.form.default;
      },
      image: function() {
        return this.formFieldValues.images.temp_id;
      },
      name: function() {
        return this.formFieldValues.name;
      },
      price: function() {
        return this.formFieldValues.price;
      }
    },
    watch: {
      image: function () {
        this.formChange();
      },
      name: function () {
        this.formChange();
      },
      price: function () {
        this.formChange();
      }
    },
    ready: function() {
      this.formChange();
    },
    methods: {
      processChange: function(newValue, oldValue) {
      },
      formChange: function() {
        var buttons = jQuery(this.$el)
          .find('button')
          .not('.always-enabled');

        var nameFormGroup = jQuery(this.$parent.$el)
          .find('.form-default-name');

        if (this.name) {
          nameFormGroup.removeClass('has-error');

          buttons.each(function() {
            $(this)
              .removeClass('disabled')
              .removeAttr('disabled')
              .removeClass('pristine')
              .addClass('dirty')
              .text($(this).data('dirty'));
          });
        } else {
          if (!this.image && this.price == 0) {
            nameFormGroup.removeClass('has-error');

            buttons.each(function() {
              $(this)
                .removeClass('disabled')
                .removeAttr('disabled')
                .removeClass('dirty')
                .addClass('pristine')
                .text($(this).data('pristine'));
            });
          } else {
            nameFormGroup.addClass('has-error');

            buttons.each(function() {
              $(this)
                .addClass('disabled')
                .prop('disabled', 'disabled')
                .removeClass('pristine')
                .addClass('dirty')
                .text($(this).data('dirty'));
            });
          }
        }
      },
      processReposition: function () {
      },
      hideWizard: function () {
        this.$dispatch('wizard.hide');
      }
    }
  });

});
