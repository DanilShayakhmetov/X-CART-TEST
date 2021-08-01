/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * sections.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('checkout_fastlane/store/sections', ['vue/vue'], function(Vue){
	return {
    state: {
      current: null,
      enabled: [],
      list: {}
    },

    mutations: {
      REGISTER_SECTION: function (state, name, component) {
        Vue.set(state.list, name, {});
        Vue.set(state.list[name], 'fields',     {});
        Vue.set(state.list[name], 'complete',   false);
        Vue.set(state.list[name], 'name',       name);
        Vue.set(state.list[name], 'index',      component.index);
        Vue.set(state.list[name], 'nextLabel',  component.nextLabel);
      },

      SWITCH_SECTION: function (state, name) {
        var oldSection = state.current;
        state.current = state.list[name];

        core.trigger('fastlane_section_switched', {
          oldSection: oldSection,
          newSection: state.current
        });
      },

      TOGGLE_SECTION: function (state, name, value) {
        if (value) {
          state.enabled.push(name);
        } else {
          state.enabled.$remove(name);
        }
      },

      TOGGLE_COMPLETE: function (state, name, value) {
        state.list[name].complete = value;
      },

      UPDATE_SECTION_FIELDS: function (state, name, data) {
        state.list[name].fields = _.extend(state.list[name].fields, data);
      },
    }
	}
});
