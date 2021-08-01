/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * File manager app
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('file_manager', [
  'vue/vue',
  'js/vue/vue'
], function (Vue, XLiteVue) {
  var XLiteFileManager = {
    props: {
    },

    watch: {
    },

    data: function () {
      return {
        openFirst: null,
        openLast: null
      };
    },

    ready: function () {
      if (
        this.openFirst
        && $(this.$el).find('.dir .line.file:first').get(0)
        && $(this.$el).find('.dir .line.file:first').get(0).__vue__
      ) {
        $(this.$el).find('.dir .line.file:first').get(0).__vue__.$dispatch('expand');
      }

      if (
        this.openLast
        && $(this.$el).find('.dir .line.file:last').get(0)
        && $(this.$el).find('.dir .line.file:last').get(0).__vue__
      ) {
        $(this.$el).find('.dir .line.file:last').get(0).__vue__.$dispatch('expand');
      }
    },

    methods: {

    }
  };

  XLiteVue.component('xlite-file-manager', XLiteFileManager);

  return XLiteFileManager;
});