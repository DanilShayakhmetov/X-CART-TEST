/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/webmaster_mode/template_code', ['js/vue/vue', 'vue/vue.loadable'], function (XLiteVue, Loadable) {
  function createEscaper(map) {
    var escaper = function (match) {
      return map[match];
    };
    var source = '(?:' + _.keys(map).join('|') + ')';
    var testRegexp = RegExp(source);
    var replaceRegexp = RegExp(source, 'g');
    return function (string) {
      string = string == null ? '' : '' + string;
      return testRegexp.test(string) ? string.replace(replaceRegexp, escaper) : string;
    };
  }

  XLiteVue.component('xlite-template-code', {
    props: ['template', 'interface', 'weight', 'list'],
    mixins: [Loadable],

    loadable: {
      transferState: false,
      loader: function () {
        var params = {
          templatePath: this.template,
          interface: this.interface,
        };

        return core.get(
          _.extend({
            fromCustomer: true,
            base: xliteConfig.admin_script,
            target: 'theme_tweaker_template',
            widget: 'XLite\\Module\\XC\\ThemeTweaker\\View\\ThemeTweaker\\TemplateCode'
          }, params),
          undefined, undefined, {timeout: 45000}
        );
      },
      resolve: function () {
        var self = this;
        this.$nextTick(function () {
          self.initTextarea();
        });
      },
      reject: function () {
      }
    },

    vuex: {
      getters: {
        switcher: function (state) {
          return state.actions.switcher;
        },

        originalText: function (state) {
          return state.webmaster.originalState;
        },

        text: function (state) {
          return state.webmaster.currentState;
        },

        isNewTemplate: function (state) {
          return state.webmaster.isNewTemplate;
        },
      },

      actions: {
        updateStoreState: function (state, value, updateOriginal) {
          state.dispatch('WEBMASTER_MODE_UPDATE_STATE', value, updateOriginal);
        },
      }
    },

    data: function () {
      return {
        templateId: null,
        contentHeader: null
      }
    },

    ready: function () {
      core.bind('themetweaker.error', _.bind(this.onThemetweakerError, this));
      core.trigger('template-code.ready', this);
    },

    events: {
      'panel.resize': function () {
        this.resizeTextarea();
      },
    },

    watch: {
      templateId: function () {
        this.$dispatch('templateIdChanged', this.templateId);
      },
      template: function () {
        this.$reload();
      },
    },

    computed: {
      classes: function () {
        return {
          'reloading': this.$reloading,
          'reloading-animated': this.$reloading
        }
      },

      codeMirrorInstance: {
        cache: false,
        get: function () {
          return jQuery('[data-template-editor]').data('CodeMirror');
        }
      },

      isTextareaInitialized: {
        cache: false,
        get: function () {
          return 'undefined' !== typeof(this.codeMirrorInstance);
        }
      },
    },

    methods: {
      initTextarea: function () {
        this.loadInitialTextState();
        core.autoload(CodeMirrorWidget, '[data-template-editor]');

        var self = this;
        jQuery(document).ready(function () {
          self.resizeTextarea();
          self.codeMirrorInstance.on('change', _.bind(self.onCodeMirrorChange, this));
        });
      },

      loadInitialTextState: function () {
        var textarea = this.$el.querySelector('[data-template-editor]');
        var content = this.$el.querySelector('[data-template-content]');
        var fullText = this.unescape(_.unescape(content.innerHTML));

        var parts = this.splitTextToParts(fullText);

        if (parts) {
          if (typeof parts.header === 'undefined' && this.isNewTemplate) {
            parts.header = this.generateHeader();
          }

          textarea.value = parts.content;
          this.contentHeader = typeof parts.header === 'undefined' ? '' : parts.header;
          this.updateContent(parts.content, true);
        } else {
          console.error('[template_code] Could not split text to parts')
        }
      },

      splitTextToParts: function (text) {
        var re = /^([\s]*?{#[\s\S]*?#})?[\s]*([\s\S]*)/;

        var matches = text.match(re);

        if (matches) {
          return {
            header: matches[1],
            content: matches[2]
          }
        }

        return null;
      },

      generateHeader: function () {
        return "{##" + '\n' + ' # @ListChild (list="' + this.list + '", weight="' + this.weight + '")' + '\n' + ' #}'
      },

      resizeTextarea: function () {
        if (this.isTextareaInitialized) {
          var width = $(this.$el).width();
          var height = $(this.$el).height();
          this.codeMirrorInstance.setSize(width, height);
        }
      },

      updateContent: function (content, setAsUnchanged) {
        setAsUnchanged = typeof a !== 'undefined' ? setAsUnchanged : false;
        this.updateStoreState(this.contentHeader + '\n' + content, setAsUnchanged);
      },

      onCodeMirrorChange: _.debounce(function (instance) {
        this.updateContent(instance.getValue());
      }, 300),

      unescape: createEscaper({'&#039;': "'"}),

      escape: createEscaper({"'": '&#039;'}),

      onThemetweakerError: function (event, data) {
        var error = this.getErrorMessage(data);
        var onCancel = typeof data.line !== 'undefined'
          ? _.bind(this.onCancelClick, this)
          : null;

        this.$dispatch('showErrorDialog', error, null, onCancel);
      },

      getErrorMessage: function (data) {
        if (typeof data.line === 'undefined') {
          return data.message;
        }

        var replaceLast = function(str, find, replace) {
          find = String(find);
          replace = String(replace);
          var index = str.lastIndexOf(find);

          if (index >= 0) {
            return str.substring(0, index) + replace + str.substring(index + find.length);
          }

          return str.toString();
        };

        var header = String(this.contentHeader);
        var headerLineCount = header.length > 0
          ? (header.match(/\r?\n/g) || '').length + 1
          : 0;
        var lineNo = data.line - headerLineCount;

        return replaceLast(data.message, data.line, lineNo);
      },

      onCancelClick: function () {
        var textarea = this.$el.querySelector('[data-template-editor]');
        textarea.value = this.originalText;
        this.initTextarea();
      },
    }
  });
});