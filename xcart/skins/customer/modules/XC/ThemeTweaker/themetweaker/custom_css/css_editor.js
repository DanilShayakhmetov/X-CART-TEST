/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Css hot editor panel
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/custom_css', ['js/vue/vue'], function (XLiteVue) {
    XLiteVue.component('xlite-custom-css', {
        props: ['initial'],

        vuex: {
            getters: {
                switcher: function(state) {
                    return state.actions.switcher;
                },

                content: function(state) {
                    if (state.customCss.currentState) {
                        return state.customCss.currentState['content'];
                    }

                    return null;
                },

                use: function(state) {
                    if (state.customCss.currentState) {
                        return state.customCss.currentState['use'];
                    }

                    return null;
                }
            },

            actions: {
                updateStoreState: function(state, value, updateOriginal) {
                    state.dispatch('CUSTOM_CSS_UPDATE_STATE', value, updateOriginal);
                },
                setSwitcherState: function(state, value) {
                    state.dispatch('TOGGLE_SWITCHER', value);
                },
            }
        },

        activate: function (done) {
            core.trigger('custom-css.activate', this);
            done();
        },

        ready: function() {
            core.trigger('custom-css.ready', this);
            this.setSwitcherState(this.initial);
            this.findElements();
            this.initTextarea();
            this.enableLiveReloading();
        },

        events: {
            'panel.resize': function() {
                this.resizeTextarea();
            },

            'action.save': function() {
                var params = {
                    use: this.use ? 1 : 0,
                    code: this.content
                };

                params[xliteConfig.form_id_name] = xliteConfig.form_id;

                core.post(
                    {
                        base: xliteConfig.admin_script,
                        target: 'custom_css',
                        action: 'save',
                    },
                    undefined, params, { timeout: 45000, rpc: true }
                )
                    .done(_.bind(this.onSaveSuccess, this))
                    .fail(_.bind(this.onSaveFail, this));
            },
        },

        watch: {
            switcher: function(value, oldValue) {
                if (oldValue !== null) {
                    var text = this.$css.text();

                    if (value) {
                        this.$css.replaceWith('<style rel="stylesheet" type="text/css" media="screen" data-custom-css>');
                    } else {
                        this.$css.replaceWith('<script type="text/css" data-custom-css>');
                    }

                    this.updateStoreState({use: value});
                    this.$css = $('[data-custom-css]').text(text);
                }
            },
            content: function(value, oldValue) {
                if (oldValue !== null) {
                    this.$css.text(value);
                }
            }
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
                get: function() {
                    return jQuery('[data-css-editor]').data('CodeMirror');
                }
            },

            isTextareaInitialized: {
                cache: false,
                get: function() {
                    return 'undefined' !== typeof(this.codeMirrorInstance);
                }
            }
        },

        methods: {
            findElements: function() {
                this.$css = $('[data-custom-css]');
            },
            enableLiveReloading: function() {
                $('body').addClass('live-css-reloading');
            },
            disableLiveReloading: function() {
                $('body').removeClass('live-css-reloading');
            },
            initTextarea: function() {
                var textarea = this.$el.querySelector('[data-css-editor]');
                var content = this.$el.querySelector('[data-css-content]');
                var text = _.unescape(content.innerHTML);
                textarea.value = text;
                this.updateStoreState({use: this.switcher, content: text}, true);

                var self = this;
                jQuery(document).ready(function () {
                    self.resizeTextarea();
                    self.codeMirrorInstance.on('change', _.bind(self.onCodeMirrorChange, this));
                });
            },

            resizeTextarea: function() {
                if (this.isTextareaInitialized) {
                    var width = $(this.$el).width();
                    var height = $(this.$el).height();
                    this.codeMirrorInstance.setSize(width, height);
                }
            },

            onCodeMirrorChange: _.debounce(function (instance) {
                this.updateStoreState({content: instance.getValue()});
            }, 300),

            onSaveSuccess: function() {
                this.$dispatch('completed.save');
                this.updateStoreState({use: this.use, content: this.content}, true);
            },

            onSaveFail: function() {
                this.$dispatch('failed.save');
            }
        }
    });
});
