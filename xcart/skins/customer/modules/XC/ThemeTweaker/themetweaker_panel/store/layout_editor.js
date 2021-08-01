/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/store/layout_editor', [], function () {
  return {
    state: {
      changeset: {},
      resetAvailable: false,
      images: {
        logo: null,
        favicon: null,
        appleIcon: null
      }
    },

    mutations: {
      LAYOUT_EDITOR_UPDATE_CHANGESET: function (state, key, value) {
        Vue.set(state.changeset, key, value);
      },

      LAYOUT_EDITOR_UPDATE_IMAGE: function (state, key, value) {
        if (typeof(state.images[key]) === 'undefined') {
          return;
        }

        Vue.set(state.images, key, value);
      },

      LAYOUT_EDITOR_CLEAR_CHANGES: function (state) {
        state.images = {
          logo: null,
          favicon: null,
          appleIcon: null
        }
        state.changeset = {};
      },

      LAYOUT_EDITOR_SET_RESET_AVAILABLE: function (state, value) {
        state.resetAvailable = value;
      }
    }
  }
});
