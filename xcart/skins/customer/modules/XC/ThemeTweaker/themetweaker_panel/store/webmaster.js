/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/store/webmaster', [], function () {
  return {
    state: {
      originalState: null,
      currentState: null,
      reverted: [],
      isNewTemplate: false
    },

    mutations: {
      WEBMASTER_MODE_UPDATE_STATE: function (state, value, updateOriginal) {
        if (state.originalState === null || updateOriginal) {
          state.originalState = value;
        }

        state.currentState = value;
      },
      WEBMASTER_MODE_ADD_TO_REVERTED: function (state, key) {
        if (state.reverted.indexOf(key) < 0) {
          state.reverted.push(key);
        }
      },
      WEBMASTER_MODE_REMOVE_FROM_REVERTED: function (state, key) {
        state.reverted = state.reverted.filter(function(item) {
          return item !== key;
        });
      },
      WEBMASTER_MODE_CLEAR_CHANGES: function (state) {
        state.originalState = null;
        state.currentState = null;
        state.reverted = [];
      },
      WEBMASTER_MODE_SET_NEW_TEMPLATE: function (state, value) {
        state.isNewTemplate = value;
      },
    }
  }
});
