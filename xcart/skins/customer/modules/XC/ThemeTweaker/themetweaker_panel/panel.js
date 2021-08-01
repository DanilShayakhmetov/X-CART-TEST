/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/panel_start', ['js/vue/vue', 'ready'], function (XLiteVue) {
  if (core.isDeveloperMode) {
    Vue.config.debug = true;
    Vue.config.devtools = true;
  }

  XLiteVue.start('#themetweaker-panel-loader-point');
});

define('themetweaker/panel', ['js/vue/vue', 'vue/vue.loadable', 'themetweaker/store', 'themetweaker/getters', 'themetweaker/modals'], function (XLiteVue, Loadable, Store, Getters, ModalsMixin) {
  XLiteVue.component('xlite-themetweaker-panel', {
    props: ['mode'],
    mixins: [Loadable, ModalsMixin],
    store: Store,

    activate: function (done) {
      core.trigger('themetweaker-panel.activate', this);
      done();
    },

    ready: function () {
      $('[data-toggle="tooltip"]', this.$el).tooltip();
      core.trigger('themetweaker-panel.ready', this);
      this.$panel = jQuery('.themetweaker-panel', this.$el);
      jQuery(this.$el).removeClass('themetweaker-panel--initial');

      var self = this;
      this.$nextTick(function () {

        self.initializePanelHeight();

        if (self.isExpanded === true) {
          self.$panel.removeClass('expand-transition');
          self.autoResizeContent();
          self.autoResizeFooter();
        }

        self.enablePanelInteractionWhileInPopup();
        self.enableResizing();
      });

      if (this.mode) {
        this.isExpanded = true;
      }

      this.$panel.find('.themetweaker-tab_hide').removeClass('disabled');
      if (!this.isExpanded) {
          this.$panel.find('.themetweaker-tab_hide').addClass('disabled');
      }
    },

    data: function () {
      return {
        isExpanded: this.getInitialExpandedState(),
      }
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

        hasWebmasterRevertedTemplates: function (state) {
          return state.webmaster.reverted.length > 0;
        }
      },

      actions: {
        toggleSwitcher: function (state, value) {
          state.dispatch('TOGGLE_SWITCHER', value);
        },
      }
    },

    computed: {
      MIN_HEIGHT: function () {
        return 30;
      },
      MAX_HEIGHT: function () {
        return 500;
      },
      PANEL_ENABLED_HEIGHT: function () {
        return 'ThemeTweaker.panelHeight.enabled';
      },
      PANEL_DISABLED_HEIGHT: function () {
        return this.MIN_HEIGHT;
      },
      panelClasses: function () {
        return {
          'reloading': this.$reloading,
          'reloading-animated': this.$reloading
        }
      },
      shouldShowSaveConfirm: function () {
        return this.mode === 'webmaster' && this.hasWebmasterRevertedTemplates;
      }
    },

    watch: {
      isExpanded: function (value) {
        sessionStorage.setItem('ThemeTweaker_isExpanded', value);

        this.$panel.find('.themetweaker-tab_hide').removeClass('disabled');
        if (!value) {
          this.$panel.find('.themetweaker-tab_hide').addClass('disabled');
        }
      }
    },

    transitions: {
      'expand': {
        beforeEnter: function (el) {
          this.$panel.addClass('expand-transition');
        },
        afterEnter: function (el) {
          this.$panel.removeClass('expand-transition');
          this.autoResizeContent();
          this.autoResizeFooter();
        },
        beforeLeave: function (el) {
          this.$panel.addClass('expand-transition');
        },
        afterLeave: function (el) {
          this.autoResizeFooter();
        },
      }
    },

    events: {
      blockPanel: function () {
        this.$reloading = true;
      },

      unblockPanel: function () {
        this.$reloading = false;
      },

      triggerSave: function () {
        var self = this;
        this.$once('completed.save', function (callback) {
          if (_.isFunction(callback)) {
            callback();
          } else {
            self.$emit('unblockPanel');
          }
        });
        this.$once('failed.save', function () {
          self.$emit('unblockPanel');
        });
        this.savePanel();
      },

      triggerHide: function () {
        this.hidePanel();
      },
    },

    methods: {
      getInitialExpandedState: function () {
        return !!this.mode;
      },

      initializePanelHeight: function () {
        var savedHeight = this.getPanelHeight();

        if (savedHeight) {
          this.$panel.css('height', savedHeight);
        }
      },

      enablePanelInteractionWhileInPopup: function () {
        jQuery.widget("ui.dialog", jQuery.ui.dialog, {
          _allowInteraction: function (event) {
            return !!$(event.target).closest(".themetweaker-panel").length || this._super(event);
          }
        });
      },

      enableResizing: function () {
        this.$panel.resizable({
          handles: "n",
          maxHeight: this.MAX_HEIGHT,
          minHeight: this.MIN_HEIGHT,
        });

        this.$panel.on("resize", _.bind(this.autoResizeContent, this));
        this.$panel.on("resizestop", _.bind(this.autoResizeContent, this));
        this.$panel.on("resizestop", _.bind(this.autoResizeFooter, this));
      },

      autoResizeContent: function () {
        var panelHeight = this.$panel.height();
        var tabs = this.$panel.find('[data-panel-tabs]');
        jQuery('[data-panel-content]', this.$panel).css('height', panelHeight - tabs.height());

        this.setPanelHeight(panelHeight);
        this.$broadcast('panel.resize');
      },

      autoResizeFooter: function () {
        var height = this.$panel.height();
        jQuery('#footer-area').css('margin-bottom', height);
      },

      sendSwitchRequest: function (mode) {
        var data = {};
        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        if (mode) {
          data['mode'] = mode;
        }

        return core.post(
          {
            base: xliteConfig.admin_script,
            target: 'theme_tweaker',
            action: 'switch_mode'
          },
          null,
          data
        );
      },

      sendCloseRequest: function () {
        var data = {};
        data[xliteConfig.form_id_name] = xliteConfig.form_id;
        return core.post(
          {
            base: xliteConfig.admin_script,
            target: 'theme_tweaker',
            action: 'disable'
          },
          null,
          data
        );
      },

      savePanel: function () {
        var performSave = function () {
          if (this.saveAvailable && this.isChanged) {
            this.$dispatch('blockPanel');
            this.$broadcast('action.save');
          }
        };

        if (this.shouldShowSaveConfirm) {
          this.saveConfirm(performSave, null);
        } else {
          performSave.apply(this);
        }
      },

      showPanel: function () {
        this.isExpanded = true;
      },

      hidePanel: function () {
        var self = this;

        var completion = function (callback) {

          this.$broadcast('panel.disable');

          if (_.isFunction(callback)) {
            this.disablePanel().always(callback);
          } else {
            this.disablePanel().always(function () {
              self.$emit('unblockPanel');
            });
          }

          this.isExpanded = false;
        };

        var onSave = function () {
          this.$once('completed.save', _.bind(completion, this));
          this.$once('failed.save', function () {
            self.$emit('unblockPanel');
          });
          this.savePanel();
        };

        var onDiscard = completion;

        if (this.mode && this.isChanged) {
          this.exitConfirm(onSave, onDiscard);
        } else {
          onDiscard.apply(this);
        }
      },

      disablePanel: function () {
        if (this.mode !== 'custom_css') {
          this.toggleSwitcher(false);
        }
        this.mode = null;
        $(this.$el).find('.themetweaker-tab.active').removeClass('active');
        this.initializePanelHeight();
        return this.sendCloseRequest();
      },

      switchTab: function (mode) {
        var self = this;
        var completion = function (callback) {
          // TODO: ignores callback right now because only webmaster mode is using it to reload the page, needs to be reimplemented
          this.$broadcast('panel.switch');
          this.sendSwitchRequest(mode);
        };

        var onSave = function () {
          this.$once('completed.save', _.bind(completion, this));
          this.$once('failed.save', function () {
            self.$emit('unblockPanel');
          });
          this.savePanel();
        };

        var onDiscard = function () {
          this.$dispatch('blockPanel');
          completion.apply(this);
        };

        if (this.mode && this.isChanged) {
          this.exitConfirm(onSave, onDiscard);
        } else {
          onDiscard.apply(this);
        }
      },

      getPanelHeight: function () {
        switch (this.mode) {
          case 'webmaster':
          case 'layout_editor':
          case 'custom_css':
            var value = sessionStorage.getItem(this.PANEL_ENABLED_HEIGHT);
            return value !== null
              ? Math.min(Math.max(this.MIN_HEIGHT, value), this.MAX_HEIGHT)
              : null;
          default:
            return this.PANEL_DISABLED_HEIGHT;
        }
      },

      setPanelHeight: function (value) {
        var height = Math.min(Math.max(this.MIN_HEIGHT, value), this.MAX_HEIGHT);
        switch (this.mode) {
          case 'webmaster':
          case 'layout_editor':
          case 'custom_css':
            sessionStorage.setItem(this.PANEL_ENABLED_HEIGHT, height);
            break;
        }
      },

      goAdminPanel: function () {
        window.location.href = xliteConfig.admin_script
      },
    }
  });
});
