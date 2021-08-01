/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/layout_editor', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-layout-editor', {
    consts: {
      TYPE_MOVE: 1,
      TYPE_HIDE: 2
    },

    props: ['preset'],

    ready: function () {
      core.trigger('layout-editor.ready', this);
      jQuery(this.$el).removeClass('layout-editor--initial');
      this.assignHandlers();
      this.setInitialState();
    },

    vuex: {
      getters: {
        switcher: function (state) {
          return state.actions.switcher;
        },

        changeset: function (state) {
          return state.layoutEditor.changeset;
        },

        images: function (state) {
          return state.layoutEditor.images;
        }
      },

      actions: {
        setSwitcherState: function (state, value) {
          state.dispatch('TOGGLE_SWITCHER', value);
        },

        clearChanges: function (state) {
          state.dispatch('LAYOUT_EDITOR_CLEAR_CHANGES');
        },

        updateChangeset: function (state, key, value) {
          state.dispatch('LAYOUT_EDITOR_UPDATE_CHANGESET', key, value);
        },

        setResetAvailable: function (state, value) {
          state.dispatch('LAYOUT_EDITOR_SET_RESET_AVAILABLE', value);
        }
      }
    },

    data: function () {
      return {
        hiddenBlocks: this.getHiddenBlocks()
      }
    },

    computed: {
      hiddenCount: {
        cache: false,
        get: function () {
          return Object.keys(this.hiddenBlocks).length
        }
      },

      panelClasses: function () {
        return {
          'layout-editor--disabled': !this.switcher
        }
      }
    },

    watch: {
      'switcher': function (value, oldValue) {
        if (oldValue !== null) {
          this.toggleEditor(value);
        }
      }
    },

    events: {
      'action.save': function () {
        var params = {
          'preset': this.preset,
          'changes': this.changeset,
          'returnURL': window.location.href
        };

        for (var type in this.images) {
          if (this.images[type] !== null) {
            params[type] = this.images[type];
          }
        }

        params[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            base: xliteConfig.admin_script,
            target: 'layout_edit',
            action: 'apply_changes'
          },
          null,
          params
          )
          .done(_.bind(this.onSaveSuccess, this))
          .fail(_.bind(this.onSaveFail, this));
      }
    },

    methods: {
      assignHandlers: function () {
        core.bind('layout.dragStart', _.bind(this.onDragStart, this));
        core.bind('layout.dragStop', _.bind(this.onDragStop, this));
        core.bind('layout.moved', _.bind(this.onListChange, this));
        core.bind('layout.rearranged', _.bind(this.onListChange, this));
        core.bind('layout.hide', _.bind(this.onItemHide, this));
        core.bind('layout.unhide', _.bind(this.onItemUnhide, this));
      },

      onDragStart: function () {
        $('#themetweaker-panel').addClass('collapsed');
      },

      onDragStop: function () {
        $('#themetweaker-panel').removeClass('collapsed');
      },

      getHiddenBlocks: function () {
        return jQuery('.list-item__hidden').toArray().reduce(function (blocks, elem) {
          var item = jQuery(elem);
          blocks[item.data('id')] = {
            id: item.data('id'),
            name: item.data('blockname'),
            element: item
          };
          return blocks;
        }, {});
      },

      setInitialState: function () {
        var state = null;
        var persistedState = this.accessState();

        if (persistedState === null) {
          state = true;
        } else {
          state = (persistedState === "true");
        }

        this.setSwitcherState(state);
      },

      onListChange: function (event, data) {
        this.addChange(data.id, data.list, data.position, 1);
      },

      onItemHide: function (event, sender) {
        var block = $(sender).closest('.list-item');
        this.hideBlock(block);
      },

      onItemUnhide: function (event, sender) {
        var block = $(sender).closest('.list-item');
        this.unhideBlock(block);
      },

      hideBlock: function (block) {
        var list = block.closest('.list-items-group');
        block.listItem('hide');
        this.addChange(block.data('id'), list.data('list'), block.data('weight'), 2);

        Vue.set(this.hiddenBlocks, block.data('id'), {
          id: block.data('id'),
          name: block.data('blockname'),
          element: block
        });
      },

      unhideBlock: function (block) {
        block.blur();
        var list = block.closest('.list-items-group');
        this.addChange(block.data('id'), list.data('list'), block.data('weight'), 1);

        Vue.delete(this.hiddenBlocks, block.data('id'));
      },

      showBlock: function (block) {
        if (!block.listItem('isHidden')) {
          return;
        }

        if (block.listItem('isSidebarHidden')) {
          this.showSidebarHiddenBlock(block);
        } else {
          block.listItem('show');
          this.unhideBlock(block);
          block.listItem('focus');
        }
      },

      getCenterListContainer: function () {
        return $('.list-container[data-group="center"]');
      },

      showSidebarHiddenBlock: function (block) {
        this.getCenterListContainer().each(function() {
          $(this).data('controller').move(block);
        });
        block.listItem('show');
        block.listItem('focus');

        Vue.delete(this.hiddenBlocks, block.data('id'));
      },

      addChange: function (id, list, weight, mode) {
        this.updateChangeset(id, {
          id: id,
          list: list,
          weight: weight,
          mode: mode
        });
      },

      onSaveSuccess: function (event) {
        this.$nextTick(function () {
          if (!TopMessages.instance.hasVisibleMessages()) {
            core.trigger('message', {type: 'info', message: core.t('Changes were successfully saved')});
          }
        });

        this.clearChanges();
        this.setResetAvailable(true);
        this.$dispatch('completed.save', function () {
          window.location.reload();
        });
      },

      onSaveFail: function (event) {
        core.trigger('message', {type: 'error', message: core.t('Unable to save changes')});
        this.$dispatch('failed.save');
      },

      toggleEditor: function (state) {
        $('.list-container').each(function () {
          $(this).data('controller').toggle(state);
        });

        this.persistState(state);
      },

      getBlockName: function (item) {
        return (item.name || core.t('Layout block'));
      },

      accessState: function () {
        var key = 'layout-editor-mode';
        return sessionStorage.getItem(key);
      },

      persistState: function (value) {
        var key = 'layout-editor-mode';
        sessionStorage.setItem(key, value);
      }
    }
  });
});