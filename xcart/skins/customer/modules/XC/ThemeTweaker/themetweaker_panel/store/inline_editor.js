/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/store/inline_editor', [], function () {

  return {
    state: {
      changeset: {},
      images: {},
      videos: {},
    },

    mutations: {
      INLINE_EDITOR_UPDATE_CHANGESET: function (state, key, value) {
        Vue.set(state.changeset, key, value);
      },

      INLINE_EDITOR_CLEAR_CHANGESET: function (state) {
        state.changeset = {};
      },

      INLINE_EDITOR_UPDATE_IMAGES: function (state, key, value) {
        Vue.set(state.images, key, value);
      },

      INLINE_EDITOR_UPDATE_VIDEOS: function (state, key, value) {
        Vue.set(state.videos, key, value);
      },

      INLINE_EDITOR_CLEAR_IMAGES: function (state) {
        state.images = {};
      },

      INLINE_EDITOR_CLEAR_VIDEOS: function (state) {
        state.videos = {};
      }
    }
  }
});
