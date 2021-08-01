/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * navigation.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/navigation',
 ['vue/vue'],
  function(Vue){

  var Navigation = Vue.extend({
    name: 'navigation',
    replace: false,

    vuex: {
      getters: {
        sections: function(state) {
          return state.sections;
        }
      },
      actions: {
        dispatchSwitch: function(state, target) {
          state.dispatch('SWITCH_SECTION', target);
        },
        toggleSection: function(state, name, value) {
          state.dispatch('TOGGLE_SECTION', name, value);
        }
      }
    },

    data: function() {
      return {
        start_with: null,
      }
    },

    computed: {
      currentIndex: function() {
        var item = this.$children.find(function(item) {
          return item.isActive;
        });

        return item ? item.index : 0;
      }
    },

    methods: {
      getStartSection: function() {
        var target = null;

        if (this.start_with !== null) {
          target = this.$children.find(function (item) {
            return item.name === this.start_with;
          }.bind(this));

        } else if (sessionStorage.getItem('flc_last_uid') === this.$root.uid
          && sessionStorage.getItem('flc_last_visited_section')) {
          target = this.$children.find(function (item) {
            return item.name === sessionStorage.getItem('flc_last_visited_section');
          });
        }

        return target || this.$children[0];
      }
    },

    events: {
      requestNext: function() {
        if (this.sections.current.complete) {

          var self = this;
          var next = this.$children.find(function(item) {
            return item.index > self.currentIndex;
          });

          if (!next.isEnabled) {
            this.toggleSection(next.name, true);
          }

          this.$nextTick(function() {
            this.$emit('switchTo', next);
          })
        }
      },
      switchTo: function(target) {
        if (_.isObject(target)) {
          target = target.name;
        }

        this.dispatchSwitch(target);

        this.$nextTick(function(){
          this.$root.$broadcast('sectionSwitch', target);
          // noinspection JSCheckFunctionSignatures
          sessionStorage.setItem('flc_last_uid', this.$root.uid);
          sessionStorage.setItem('flc_last_visited_section', target);
        })
      },
      checkStartSection: function() {
        var target = this.getStartSection();

        var sectionsToEnable = this.$children.filter(function (item) {
          return item.index <= target.index;
        });

        sectionsToEnable.forEach(function (item) {
          if (!item.isEnabled) {
            this.toggleSection(item.name, true);
          }
        }.bind(this));

        this.$emit('switchTo', target);
      }
    },
  });

  return Navigation;
});