/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/layout_options', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-layout-options', {
    props: ['preset', 'initialResetAvailable'],

    ready: function () {
      this.setResetAvailable(this.initialResetAvailable);
    },

    vuex: {
      getters: {
        changeset: function (state) {
          return state.layoutEditor.changeset;
        },

        resetAvailable: function (state) {
          return state.layoutEditor.resetAvailable;
        }
      },

      actions: {
        updateImage: function (state, key, value) {
          state.dispatch('LAYOUT_EDITOR_UPDATE_IMAGE', key, value);
        },

        setResetAvailable: function (state, value) {
          state.dispatch('LAYOUT_EDITOR_SET_RESET_AVAILABLE', value);
        }
      }
    },

    computed: {
      isResetAvailable: function () {
        return this.resetAvailable || Object.keys(this.changeset).length > 0;
      },

      resetBtnClasses: {
        cache: false,
        get: function () {
          return {
            'disabled': !this.isResetAvailable,
          }
        }
      },
    },

    events: {
      'layout-type.selected': function(value, sender) {
        this.$dispatch('blockPanel');
        var params = {
          'group': sender.group,
          'type': value,
          'returnURL': window.location.href
        };

        params[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            base: xliteConfig.admin_script,
            target: 'layout_edit',
            action: 'switch_layout_type'
          },
          null,
          params
          )
          .done(_.bind(this.onSwitchSuccess, this))
          .fail(_.bind(this.onSwitchFail, this));
      },
      'action.reset': function () {
        this.$dispatch('blockPanel');
        var params = {
          'preset': this.preset,
          'returnURL': window.location.href
        };

        params[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            base: xliteConfig.admin_script,
            target: 'layout_edit',
            action: 'reset_layout'
          },
          null,
          params
          )
          .done(_.bind(this.onResetSuccess, this))
          .fail(_.bind(this.onResetFail, this));
      },
      'form-model-prop-updated': function(path, value, sender) {
        var updateData = {};
        if (typeof(sender.temp_id) !== 'undefined') {
          updateData.temp_id = sender.temp_id;

          if (typeof(sender.alt) !== 'undefined') {
            updateData.alt = sender.alt;
          }
        }
        if (typeof(sender.delete) !== 'undefined') {
          updateData.is_delete = sender.delete;
        }
        if (typeof(sender.alt) !== 'undefined'
          && sender.alt !== sender.initialAlt
        ) {
          updateData.alt = sender.alt;
        }

        if (_.isEqual(updateData, {is_delete: false})) {
          updateData = null;
        }

        this.updateImage(sender.basePath, updateData);
      },
    },

    methods: {
      onSwitchSuccess: function (event) {
        core.trigger('message', {type: 'info', message: core.t('Layout type was successfully changed')});
      },

      onSwitchFail: function (event) {
        core.trigger('message', {type: 'error', message: core.t('Unable to change the layout type')});
        this.$dispatch('unblockPanel');
      },

      onResetSuccess: function (event) {
        core.trigger('message', {type: 'info', message: core.t('Layout was successfully reset')});
      },

      onResetFail: function (event) {
        core.trigger('message', {type: 'error', message: core.t('Unable to reset layout')});
        this.$dispatch('unblockPanel');
      },

      resetLayout: function () {
        if (!this.isResetAvailable) {
          return;
        }
        var self = this;
        this.$dispatch('showResetLayoutConfirm',
          function() {
            self.$emit('action.reset');
          },
          null);
      }
    }
  });
});