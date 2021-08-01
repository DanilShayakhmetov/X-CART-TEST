/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/layout_type_select', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-layout-type', {
    props: ['group'],

    ready: function() {
      var self = this;

      $(document).click(function(event){
        var target = $(event.target);
        if (target.closest(self.$el).length === 0) {
          self.showMenu = false;
        }
      });
    },

    data: function() {
      return {
        showMenu: false,
        options: {},
        selected: null
      }
    },

    watch: {
      'selected': function(value, oldValue) {
        if (oldValue !== null) {
          this.$dispatch('layout-type.selected', value, this);
        }
      }
    },

    methods: {
      getLayoutTypeOptionMarkup: function(key) {
        var container = $('.option-image-container', this.$el);
        return container.find('.option-image.' + key).html();
      },
      toggleMenu: function(event) {
        this.showMenu = !this.showMenu;
      },
      onSelect: function(option) {
        this.selected = option;
        // this.showMenu = false;
      }
    }
  });
});
