/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * next_button.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/sections/section_change_button',
  ['vue/vue',
   'checkout_fastlane/sections'], 
  function(Vue, Sections){

  var SectionChangeButton = Vue.extend({
    name: 'section-change-button',
    replace: false,

    vuex: {
      getters: {
        current: function(state) {
          return state.sections.current;
        },
        total_text: function(state) {
          return state.order.total_text;
        },
      },
    },

    ready: function() {
      $('.checkout_fastlane_section-buttons', this.$el).removeClass('loading');

      setTimeout(_.bind(this.calculateMobilePadding, this), 1000);
      $(window).resize(_.debounce(_.bind(this.calculateMobilePadding, this), 200));
      core.bind('fastlane_section_switched', _.debounce(_.bind(this.calculateMobilePadding, this), 100));
    },

    methods: {
      scrollToDetails: function () {
        var top = $('.checkout_fastlane_details_box:visible').offset().top - 80;
        $(window).scrollTop( top );
      },

      calculateMobilePadding: function () {
        const fixedWidth = 768;
        var windowWidth = $(window).width();

        if (windowWidth < fixedWidth) {
          var $buttonsElem = $('.checkout_fastlane_section-buttons');
          var $mobilePadding = $('.checkout_fastlane_mobile_padding');

          var mobilePaddingValue = $buttonsElem.outerHeight() - 80;
          mobilePaddingValue = mobilePaddingValue > 0 ? mobilePaddingValue : 0;
          $mobilePadding.css({'height': mobilePaddingValue + 'px'});
        }
      },
    },

    computed: {
      showPlaceOrder: function() {
        if (this.current) {
          return this.current.name === 'payment';
        } else {
          return false;
        };
      },
      complete: function(state) {
        if (this.current) {
          return this.current.complete;
        } else {
          return false;
        };
      },
      index: function(state) {
        if (this.current) {
          return this.current.index;
        } else {
          return 0;
        };
      }
    },
  });

  Vue.registerComponent(Sections, SectionChangeButton);

  return SectionChangeButton;
});
