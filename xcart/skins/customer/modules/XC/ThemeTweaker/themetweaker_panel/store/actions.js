/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/store/actions', [], function(){
    return {
        state: {
            switcher: null,
            saveAvailable: true,
            switcherAvailable: true,
        },

        mutations: {
            TOGGLE_SWITCHER: function (state, value) {
                state.switcher = value;
            },

            SET_SAVE_AVAILABILITY: function (state, value) {
                state.saveAvailable = value;
            },

            SET_SWITCHER_AVAILABILITY: function (state, value) {
                state.switcherAvailable = value;
            }
        }
    }
});
