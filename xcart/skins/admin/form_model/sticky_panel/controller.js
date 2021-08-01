/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/sticky_panel', ['js/vue/vue'], function (XLiteVue) {

  XLiteVue.component('xlite-sticky-panel', {
    props: ['changed', 'formState'],
    activate: function (done) {
      done();

      this.$base = jQuery(this.$el);
      this.$panel = this.$base.find('.box').eq(0);

      setTimeout(_.bind(this.processReposition, this), 0);

    },
    watch: {
      'changed': function (newValue, oldValue) {
        this.processChange(newValue, oldValue);
      }
    },
    methods: {
      processChange: function(newValue, oldValue) {
        if (newValue !== oldValue) {
          const buttons = jQuery(this.$el).find('button').not('.always-enabled, .do-not-change-activation');

          if (newValue) {
            buttons
              .removeClass('disabled')
              .removeAttr('disabled');

          } else {
            buttons
              .addClass('disabled')
              .prop('disabled', 'disabled');
          }

        }
      },
      processReposition: function () {
        this.$base.height(this.$panel.outerHeight());
      },
    }
  });

});
