/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * place-order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/sections/included_modifiers',
 ['vue/vue',
  'checkout_fastlane/sections'],
  function(Vue, Sections){

  var IncludedModifiers = Vue.extend({
    name: 'included-modifiers',

    replace: false,

    vuex: {
      getters: {
        included_modifiers_data: function(state) {
          return state.order.includedModifiersData;
        },
      },
    },


  });

  Vue.registerComponent(Sections, IncludedModifiers);

  return IncludedModifiers;
});
