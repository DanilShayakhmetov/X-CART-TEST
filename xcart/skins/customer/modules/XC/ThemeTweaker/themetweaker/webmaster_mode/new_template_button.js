/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/webmaster_mode/new_template_button', ['js/vue/vue', 'keymaster/key'], function (XLiteVue, Key) {

  var templateRegex = /^\w+$/i
  var weightRegex = /^[0-9]+$/i

  return XLiteVue.component('xlite-new-template', {
    name: 'xlite-new-template',
    template:
    '<li class="jstree-new-node">' +
    '<a class="jstree-new-btn" @click="showInput" data-trigger="manual" data-toggle="tooltip" data-placement="right" title="{{ tooltip }}"><i class="fa fa-plus" role="presentation"></i>{{ label }}</a>' +
    '<div class="jstree-node-input" v-if="inputVisible">' +
      '<i class="jstree-icon jstree-themeicon"></i>' +
      '<input type="number" step="1" min="1" max="16777215" pattern="[0-9]" class="weight-input" :data-error="markedAsError.weight" placeholder="{{ placeholderWeight }}" v-model="weight">' +
      '<input class="filename-input" :data-error="markedAsError.filename" placeholder="{{ placeholderFilename }}" v-model="filename">' +
      '<span class="extension">{{ extension }}</span>' +
    '</div>' +
    '</li>',

    data: function () {
      return {
        inputVisible: false,
        weight: "",
        filename: "",
        list: "",
        markedAsError: {
          weight: false,
          filename: false
        }
      }
    },

    ready: function () {
      var self = this;
      this.assignHotkeys();

      $('[data-toggle="tooltip"]', this.$el).tooltip();

      $(document).click(function(event){
        var target = $(event.target);
        self.$nextTick(function () {
          if (target.closest(self.$el).length === 0 && !self.$parent.isSwitchConfirmVisible) {
            self.hideInput();
          }
        })
      });
    },

    computed: {
      path: function () {
        return this.filename !== ""
          ? 'theme_tweaker/customer/' + this.list + '/' + this.filename + this.extension
          : '';
      },
      extension: function () {
        return '.new.twig';
      },
      label: function () {
        return core.t("new template");
      },
      isTemplateValid: function () {
        return this.filename && templateRegex.test(this.filename) && this.filename.length < 245
      },
      isWeightValid: function () {
        return this.weight && weightRegex.test(this.weight) && Number(this.weight) > 0 && Number(this.weight) <= 16777215
      },
      placeholderWeight: function () {
        return core.t("template weight");
      },
      placeholderFilename: function () {
        return core.t("type filename and press Enter");
      }
    },

    watch: {
      filename: function () {
        this.markedAsError.filename = false;
      },
      weight: function () {
        this.markedAsError.weight = false;
      },
    },

    methods: {
      assignHotkeys: function () {
        var self = this;
        Key.filter = function (event) {
          var element = event.target || event.srcElement;

          if ($(element).closest('.themetweaker-panel').length > 0) {
            return true;
          }

          return !(element.tagName == 'INPUT' || element.tagName == 'SELECT' || element.tagName == 'TEXTAREA');
        };

        Key('escape', _.bind(this.hideInput, this));
        Key('enter', function () {
          if (self.inputVisible) {
            if (self.isTemplateValid && self.isWeightValid) {
              self.$dispatch('template.input.filled', self.compileData(), self);
            } else {
              self.markedAsError.filename = !self.isTemplateValid;
              self.markedAsError.weight = !self.isWeightValid;
            }
          }
        });
      },

      compileData: function () {
        var parent = $(this.$el).closest('.jstree-node');
        var minWeight = 1;
        this.weight = Math.max(minWeight, this.weight);
        return {
          filename: this.filename,
          weight: this.weight,
          list: this.list,
          path: this.path,
          parent: parent.attr('id'),
          id: parent.attr('id') + '_new',
          text: '<span class="template-weight">' + this.weight + '</span>' + this.path
        }
      },

      updateList: function () {
        var node = $(this.$el).closest('.jstree-node');
        this.list = node.data('template-path');
      },

      updateInitialWeight: function () {
        var maxWeight = 16777215;
        var minWeight = 1;
        var list = $(this.$el).closest('.jstree-children');
        this.weight = Math.min(maxWeight, list.children('.jstree-node:last-child').data('template-weight') + 100);
        this.weight = Math.max(minWeight, this.weight);
      },

      showInput: function() {
        this.updateList();
        this.updateInitialWeight();
        this.$dispatch('template.input.show', this);
        this.inputVisible = true;
        this.markedAsError.filename = false;
        this.markedAsError.weight = false;
      },

      hideInput: function() {
        this.weight = "";
        this.filename = "";
        this.inputVisible = false;
        this.$dispatch('template.input.hide', this);
      }
    }
  });
});
