/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order-notes.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/blocks/order_notes',
 ['vue/vue'],
  function(Vue) {

  var OrderNotes = Vue.extend({
    name: 'order-notes',
    replace: false,

    vuex: {
      getters: {
        notes: function(state) {
          return state.order.notes;
        },
      },
      actions: {
        updateNotes: function(state, event) {
          state.dispatch('UPDATE_NOTES', event.target.value);
          this.$nextTick(function() {
            $('order-notes textarea').trigger('change');
          });
        },
      }
    },
  });

  return OrderNotes;
});
