/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/panel/actions', ['js/vue/vue', 'keymaster/key', 'themetweaker/getters'], function (XLiteVue, Key, Getters) {
  XLiteVue.component('xlite-panel-actions', {
    props: ['mode'],

    ready: function () {
      core.trigger('panel-actions.ready', this);
      this.assignHotkeys();
    },

    vuex: {
      getters: {
        switcher: function (state) {
          return state.actions.switcher;
        },

        isChanged: function (state) {
          return Getters.isSaveActive(state);
        },

        saveAvailable: function (state) {
          return state.actions.saveAvailable;
        },

        switcherAvailable: function (state) {
          return state.actions.switcherAvailable;
        }
      },

      actions: {
        toggleSwitcher: function (state, value) {
          state.dispatch('TOGGLE_SWITCHER', value);
        },
      }
    },

    watch: {},

    computed: {
      switcherLabel: function () {
        switch (this.mode) {
          case 'webmaster':
            return core.t('Pick templates from page');
          case 'layout_editor':
            return core.t('Drag-n-drop blocks');
          case 'custom_css':
            return core.t('Use custom CSS');
          case 'labels_editor':
            return core.t('Highlight labels');
        }
      },

      switcherClasses: function () {
        return {
          'hidden': !this.switcherAvailable,
        }
      },

      saveButtonClasses: {
        cache: false,
        get: function () {
          return {
            'disabled': !this.isChanged,
            'hidden': !this.saveAvailable
          }
        }
      },

      isSaveDisabled: {
        cache: false,
        get: function () {
          return !this.isChanged;
        }
      }
    },

    methods: {
      assignHotkeys: function () {
        Key.filter = function (event) {
          var element = event.target || event.srcElement;

          if ($(element).closest('.themetweaker-panel').length > 0) {
            return true;
          }

          return !(element.tagName == 'INPUT' || element.tagName == 'SELECT' || element.tagName == 'TEXTAREA');
        };

        Key('⌘+/, ctrl+/', _.bind(this.onSwitchShortcut, this));
        Key('⌘+s, ctrl+s', _.bind(this.onSaveShortcut, this));
      },

      onSaveClick: function () {
        this.$dispatch('triggerSave');
      },

      onSwitchChange: function (event) {
        this.toggleSwitcher(event.target.checked);
      },

      onSaveShortcut: function (event, handler) {
        this.onSaveClick();
        return false;
      },

      onSwitchShortcut: function (event, handler) {
        this.toggleSwitcher(!this.switcher);
        return false;
      },

      hidePanel: function () {
        this.$dispatch('triggerHide');
      },
    }
  });
});
