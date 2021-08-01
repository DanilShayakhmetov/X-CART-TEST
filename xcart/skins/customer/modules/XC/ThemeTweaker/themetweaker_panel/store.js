/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * store.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('themetweaker/store',
      [
          'vue/vue',
          'vue/vuex',
          'themetweaker/store/actions',
          'themetweaker/store/layout_editor',
          'themetweaker/store/inline_editor',
          'themetweaker/store/webmaster',
          'themetweaker/store/custom_css'
      ],
      function(Vue, Vuex, Actions, LayoutEditor, InlineEditor, WebmasterMode, CustomCss) {

      return new Vuex.Store({
          modules: {
              actions: Actions,
              webmaster: WebmasterMode,
              layoutEditor: LayoutEditor,
              inlineEditor: InlineEditor,
              customCss: CustomCss
          }
      });
});