/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * File manager line app
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('file_manager_line', [
  'vue/vue',
  'js/vue/vue'
], function (Vue, XLiteVue) {
  var XLiteFileManager = {
    props: {
    },

    watch: {
    },

    data: function () {
      return {
        type: null,
        loadLink: null,
        path: null,
        loadable: null,
        blocked: null
      };
    },
    
    events: {
      'expandOne': function () {
        this.expand();

        return this.getChildrenCount() === 1;
      },
      'expand': function () {
        this.expand();

        return true;
      },
      'collapse': function () {
        this.collapse();

        return true;
      }
    },

    ready: function () {
      if (this.type === 'dir' && this.getChildrenCount() === 0 && !this.loadable) {
        this.getRootElement().addClass('blocked empty');
        this.blocked = true;
      }
    },

    methods: {
      getRootElement: function () {
        return $(this.$el).closest('.line');
      },
      getChildrenCount: function () {
        return this.getRootElement().find('> .children > .line').length;
      },
      isExpanded: function () {
        return this.getRootElement().is('.expanded');
      },
      toggleDir: function () {
        if (this.blocked) {
          return false;
        }

        if (this.isExpanded()) {
          this.collapse();
          this.$broadcast('collapse');
        } else {
          this.expand();

          if (this.getChildrenCount() === 1) {
            this.$broadcast('expandOne');
          }
        }
      },
      expand: function () {
        // TODO: add loadability if this.loadable
        this.getRootElement().addClass('expanded');
      },
      collapse: function () {
        this.getRootElement().removeClass('expanded');
      }
    }
  };

  XLiteVue.component('xlite-file-manager-line', XLiteFileManager);

  return XLiteFileManager;
});