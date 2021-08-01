/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/store/custom_css', [], function(){
    return {
        state: {
            originalState: null,
            currentState: null,
        },

        mutations: {
            CUSTOM_CSS_UPDATE_STATE: function (state, value, updateOriginal) {
                if (state.originalState === null || updateOriginal) {
                    state.originalState = value;
                }

                if (state.currentState === null) {
                    state.currentState = value;
                } else {
                    state.currentState = _.extend({}, state.currentState, value);
                }
            },
        }
    }
});
