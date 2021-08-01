/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/webmaster_mode', ['js/vue/vue', 'vue/vue.loadable', 'themetweaker/webmaster_mode/new_template_button'], function (XLiteVue, Loadable, TemplateButton) {

  XLiteVue.component('xlite-webmaster-mode', {
    props: ['interface', 'treeKey'],
    mixins: [Loadable],

    data: function () {
      return {
        detachedTrees: {},
        templateId: null,
        template: null,
        weight: null,
        list: null,
        isSwitchConfirmVisible: false,
        pendingCreation: null,
        pendingSelection: null,
        pendingNode: null
      }
    },

    ready: function () {
      core.trigger('webmaster-mode.ready', this);

      this.findElements();
      this.initializeTree();
      this.assignHandlers();

      this.$tree.prepend(this.$controlPanel);
      this.$wrapper.show();

      this.$reloading = true;

      _.defer(_.bind(function () {
        this.initializeTemplateNavigator();
        this.$reloading = false;
      }, this));
    },

    vuex: {
      getters: {
        switcher: function (state) {
          return state.actions.switcher;
        },

        reverted: function (state) {
          return state.webmaster.reverted;
        },

        text: function (state) {
          return state.webmaster.currentState;
        },

        isTextChanged: function (state) {
          return state.webmaster.currentState !== state.webmaster.originalState;
        }
      },

      actions: {
        setSwitcherState: function (state, value) {
          state.dispatch('TOGGLE_SWITCHER', value);
        },

        addToReverted: function (state, key) {
          state.dispatch('WEBMASTER_MODE_ADD_TO_REVERTED', key);
        },

        removeFromReverted: function (state, key) {
          state.dispatch('WEBMASTER_MODE_REMOVE_FROM_REVERTED', key);
        },

        clearWebmasterChanges: function (state) {
          state.dispatch('WEBMASTER_MODE_CLEAR_CHANGES');
        },

        setNewTemplate: function (state, value) {
          state.dispatch('WEBMASTER_MODE_SET_NEW_TEMPLATE', value);
        },
      }
    },

    computed: {
      treeClasses: function () {
        return {
          'reloading': this.$reloading,
          'reloading-animated': this.$reloading
        }
      },
    },

    watch: {
      'switcher': function (value, oldValue) {
        if (oldValue !== null) {
          this.templateNavigator.toggleEnabled();
        }
      }
    },

    events: {
      'templateIdChanged': function (value) {
        this.templateId = value;
      },
      'panel.disable': function () {
        this.clearJstreeCache();
      },
      'panel.switch': function () {
        this.clearJstreeCache();
      },
      'template.input.filled': function (data) {
        if (this.isSwitchConfirmVisible) {
          return;
        }

        if (!this.pendingCreation && this.isTextChanged) {
          this.pendingCreation = data;
          this.isSwitchConfirmVisible = true;
          return;
        }

        this.setNewTemplate(true);
        this.createNode(data);
      },
      'switchConfirm.cancel': function () {
        this.isSwitchConfirmVisible = false;
        this.addNewTemplateButtons();
        this.addRevertButtons();
        this.pendingCreation = null;
        this.pendingSelection = null;
      },
      'switchConfirm.ok': function () {
        this.isSwitchConfirmVisible = false;
        if (this.pendingCreation) {
          this.createNode(this.pendingCreation);
        } else if (this.pendingSelection) {
          this.treeView.callMethod('activate_node', this.pendingSelection);
        }
      },
      'action.save': function () {
        var params = {
          reverted: this.reverted
        };

        params = _.extend(params, {
          templatePath: this.template,
          interface: this.interface,
          content: this.text
        });

        if (this.pendingNode) {
          params = _.extend(params, {
            weight: this.weight,
            list: this.list,
            pendingId: this.templateId
          })
        }

        params[xliteConfig.form_id_name] = xliteConfig.form_id;

        core.post(
          {
            fromCustomer: true,
            base: xliteConfig.admin_script,
            target: 'theme_tweaker_template',
            action: 'apply_changes',
          },
          undefined, params, {timeout: 45000}
          )
          .done(_.bind(this.onSaveSuccess, this))
          .fail(_.bind(this.onSaveFail, this));
      },
    },

    methods: {
      findElements: function () {
        this.$wrapper = jQuery('[data-editor-wrapper]', this.$el);
        this.$controlPanel = jQuery('[data-editor-control-panel]', this.$el);
      },

      initializeTree: function () {
        var treeElement = jQuery('[data-editor-tree]').detach();
        this.originalTree = treeElement.get(0).cloneNode(true);
        this.$wrapper.append(treeElement);
        this.$tree = jQuery('[data-editor-tree]', this.$el);
        this.treeView = new TreeView(this.$tree, this.getJstreeOptions());
        this.interface = this.$tree.data('interface');
        this.innerInterface = this.$tree.data('inner-interface');
      },

      getJstreeOptions: function () {
        var self = this;
        return {
          state: {
            key: self.treeKey,
            ttl: 86400000 // one day in msec
          },
          plugins: ["state", "types", "conditionalselect"],
          core: {
            multiple: false,
            check_callback: function (operation) {
              return operation === 'create_node' || operation === 'delete_node';
            }
          },
          conditionalselect: function (node) {
            if (self.treeView.preventEdit || self.isSwitchConfirmVisible || (typeof node.data.jstree !== 'undefined' && node.data.jstree.disabled)) {
              return false;
            }

            if (!self.pendingSelection && !self.pendingCreation && self.pendingNode && self.isTextChanged) {
              self.pendingSelection = node.id;
              self.isSwitchConfirmVisible = true;
              return false;
            }

            if (self.pendingNode && node.id !== self.pendingNode) {
              self.deleteTemporaryNode();
              self.setNewTemplate(false);
            }

            self.pendingSelection = null;

            return true;
          },
        };
      },

      clearJstreeCache: function () {
        var keys = Object.keys(localStorage);
        for (var index in keys) {
          if (keys[index].indexOf('jstree') === 0) {
            localStorage.removeItem(keys[index]);
          }
        }
      },

      initializeTemplateNavigator: function () {
        var options = {
          manual: true,
          filters: [
            ':not(#themetweaker-panel)',
            ':not(#themetweaker-panel *)'
          ]
        };

        this.templateNavigator = {};
        this.templateNavigator = new TemplateNavigator(this.$wrapper, options);
        this.setSwitcherState(this.templateNavigator.enabled);
      },

      createNode: function (data) {
        var duplicate = this.$tree.find('.jstree-node[data-template-path="' + data.path + '"]')

        if (duplicate.length > 0) {
          this.pendingCreation = null;

          if (_.contains(this.reverted, data.path)) {
            duplicate.removeClass('jstree-reverted');
          }

          return this.treeView.callMethod('activate_node', duplicate.attr('id'));
        }

        var nodeData = {
          id          : data.id,
          parent      : data.parent,
          text        : data.text,
          state       : {
            opened    : false,
            disabled  : false,
            selected  : false
          },
          li_attr     : {
            'data-pending-node': true,
            'data-template-id': data.id,
            'data-template-path': data.path,
            'data-template-weight': data.weight,
            'data-template-list': data.list,
            'data-user-generated': true,
            'data-added-via-editor': true
          },
          data        : {
            userGenerated: true,
            addedViaEditor: true,
            templateId: data.id,
            templatePath: data.path,
            templateWeight: data.weight,
            templateList: data.list,
          },
          a_attr      : {}
        };

        var self = this;
        this.treeView.callMethod('create_node', data.parent, nodeData, 'last', function () {
          self.treeView.callMethod('activate_node', data.id);
          self.pendingNode = data.id;
          self.addRevertButtons();
          self.pendingCreation = null;
        });
      },

      assignHandlers: function () {
        core.addXhrMiddleware(_.bind(this.processXhrTree, this));
        core.bind('popup.postprocess', _.bind(this.onPopupOpen, this));
        this.assignTreeHandlers();
      },

      assignTreeHandlers: function () {
        this.treeView.on('ready.jstree open_node.jstree', _.bind(this.addNewTemplateButtons, this));
        this.treeView.on('ready.jstree open_node.jstree', _.bind(this.addRevertButtons, this));
        this.treeView.on('select_node.jstree', _.bind(this.onTreeNodeSelect, this));
        this.treeView.on('hover_node.jstree', _.bind(this.onTreeNodeHover, this));
        this.treeView.on('dehover_node.jstree', _.bind(this.onTreeNodeHoverEnd, this));
      },

      addNewTemplateButtons: function () {
        var vm = this;
        $('.jstree-anchor.jstree-disabled + .jstree-children', this.$tree).each(function() {
          if ($('.jstree-new-node', this).length > 0
            || $('[data-pending-node="true"]', this).length > 0
            || $(this).closest('.jstree-node').data('template-path') === 'body') {
            return;
          }
          new TemplateButton({parent: vm}).$mount().$prependTo(this);
        });
      },

      deleteTemporaryNode: function () {
        this.treeView.callMethod('delete_node', this.pendingNode);
        this.addNewTemplateButtons();
        this.addRevertButtons();
        this.pendingNode = null;
      },

      addRevertButtons: function () {
        var self = this;
        $('.jstree-node[data-user-generated="true"]', this.$tree).each(function() {
          if ($('.jstree-revert', this).length > 0 || $('.jstree-disable', this).length > 0 || $(this).data('pending-node')) {
            return;
          }

          var id = this.getAttribute('id');
          var idAttr, btnClass, onClick, label;
          var path = $(this).data('template-path');

          if ($(this).data('added-via-editor')) {
            idAttr = id + '_disable';
            btnClass = "jstree-disable";
            label = core.t('Delete custom file');
          } else {
            idAttr = id + '_revert';
            btnClass = "jstree-revert";
            label = core.t('Revert to default');
          }

          if (_.contains(self.reverted, path)) {
            $(this).addClass('jstree-reverted');
          }

          onClick = _.bind(self.onRevertBtnClick, self);

          var buttonMarkup = $('<a tabindex="-1" data-trigger="hover" data-toggle="tooltip" data-placement="top" title="' + label + '" class="' + btnClass +'" id="' + idAttr + '" ><i class="jstree-icon jstree-themeicon" role="presentation"></i></a>');
          $(this).prepend(buttonMarkup);
          buttonMarkup.on('click', onClick);
        });

        $('[data-toggle="tooltip"]', this.$tree).tooltip();
      },

      onRevertBtnClick: function (event) {
        var target = $(event.currentTarget);
        var path = target.parent().data('template-path');

        if (target && path) {
          if (_.contains(this.reverted, path)) {
            this.removeFromReverted(path);
            target.parent().removeClass('jstree-reverted');
          } else {
            this.addToReverted(path);
            target.parent().addClass('jstree-reverted');
          }
        }
      },

      onSwitch: function () {
        this.templateNavigator.toggleEnabled();
      },

      processXhrTree: function(xhr, response) {
        var html = $(response);
        var id = html.attr('id');

        var newTree = html.find('[data-editor-tree]');

        if (newTree.length > 0) {
          newTree.detach();
          this.detachedTrees[id] = newTree;
        }

        return html.length > 0
          ? html.get(0).outerHTML
          : response;
      },

      onPopupOpen: function (event, data) {
        if (data && data.widget && data.widget.currentPopup) {
          var popup = data.widget.currentPopup.box;
          var id = popup.attr('id');

          if (this.lastProcessedPopup === id || typeof(this.detachedTrees[id]) === 'undefined') {
            return;
          }

          var newTree = this.detachedTrees[id];
          var nodes = newTree.children('ul').first().children('li').addClass('themetweaker-popup-node').detach();

          if (nodes) {
            this.treeView.destroy(true);
            this.$tree.append(this.originalTree.innerHTML);
            this.$tree.children('ul').first().append(nodes);
            this.treeView.mount();
            this.assignTreeHandlers();
            this.templateNavigator.processElementTree(popup.get(0));

            var templateId = nodes.first().data('template-id');
            this.treeView.selectTemplate(templateId, false);
          }

          this.lastProcessedPopup = id;
          this.detachedTrees[id] = undefined;
        }
      },

      onTreeNodeSelect: function (event, tree) {
        this.template = tree.node.data.templatePath;
        this.weight = tree.node.data.templateWeight;
        this.list = tree.node.data.templateList;
      },

      onTreeNodeHover: function (event, data) {
        this.templateNavigator.markTemplateById(data.node.data.templateId);
      },

      onTreeNodeHoverEnd: function () {
        this.templateNavigator.unMarkTemplate();
      },

      onSaveSuccess: function () {
        this.$dispatch('completed.save', function () {
          window.location.reload();
        });
        this.clearWebmasterChanges();
      },

      onSaveFail: function () {
        core.trigger('message', {type: 'error', message: core.t('Unable to save changes')});
        this.$dispatch('failed.save');
      },
    }
  });
});
