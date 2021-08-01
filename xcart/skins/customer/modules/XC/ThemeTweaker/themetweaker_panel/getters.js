/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * store.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('themetweaker/getters',
      [],
      function() {

      var getters = {
        isSaveActive: function(state) {
          return getters.isWebmasterModeChanged(state)
              || getters.isLayoutEditorChanged(state)
              || getters.isCustomCssChanged(state)
              || getters.isInlineEditorChanged(state);
        },
        isWebmasterModeChanged: function(state) {
          return state.webmaster.isNewTemplate
              || state.webmaster.currentState !== state.webmaster.originalState
              || state.webmaster.reverted.length > 0;
        },
        isLayoutEditorChanged: function(state) {
          return Object.keys(state.layoutEditor.changeset).length > 0
              || state.layoutEditor.images.logo !== null
              || state.layoutEditor.images.favicon !== null
              || state.layoutEditor.images.appleIcon !== null;
        },
        isCustomCssChanged: function(state) {
          return objectHash.sha1(state.customCss.originalState) !== objectHash.sha1(state.customCss.currentState);
        },
        isInlineEditorChanged: function(state) {
          return Object.keys(state.inlineEditor.changeset).length > 0
              || Object.keys(state.inlineEditor.videos).length > 0
              || Object.keys(state.inlineEditor.images).length > 0;
        }
      }

      return getters;
});