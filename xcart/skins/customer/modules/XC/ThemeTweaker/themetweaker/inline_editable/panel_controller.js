/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Inline editor mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/inline_editor', ['js/vue/vue'], function (XLiteVue) {
    XLiteVue.component('xlite-inline-editor', {
        props: {
            showTinymceWarning: {
                coerce: function (val) {
                    return val == 'true';
                }
            }
        },

        ready: function() {
            core.trigger('inline-editor.ready', this);
            jQuery(this.$el).removeClass('inline-editor--initial');
            this.assignHandlers();
            this.setSwitcherState(true);
            this.hideSwitcher();
            core.autoload(InlineEditableController);
        },

        data: function() {
            return {
                isTinymceWarningVisible: false,
                callbacks: {
                    tinymceWarning: {
                        ok: null,
                        cancel: null
                    }
                }
            }
        },

        vuex: {
            getters: {
                switcher: function(state) {
                    return state.actions.switcher;
                },

                changeset: function(state) {
                    return state.inlineEditor.changeset;
                },

                images: function(state) {
                    return state.inlineEditor.images;
                },

                videos: function(state) {
                    return state.inlineEditor.videos;
                }
            },

            actions: {
                setSwitcherState: function(state, value) {
                    state.dispatch('TOGGLE_SWITCHER', value);
                },

                hideSwitcher: function(state) {
                    state.dispatch('SET_SWITCHER_AVAILABILITY', false);
                },

                clearChangeset: function(state) {
                    state.dispatch('INLINE_EDITOR_CLEAR_CHANGESET');
                },

                updateChangeset: function(state, key, value) {
                    state.dispatch('INLINE_EDITOR_UPDATE_CHANGESET', key, value);
                },

                clearImages: function(state) {
                    state.dispatch('INLINE_EDITOR_CLEAR_IMAGES');
                },

                clearVideos: function(state) {
                    state.dispatch('INLINE_EDITOR_CLEAR_VIDEOS');
                },

                updateImages: function(state, key, value) {
                    state.dispatch('INLINE_EDITOR_UPDATE_IMAGES', key, value);
                },

                updateVideos: function(state, key, value) {
                    state.dispatch('INLINE_EDITOR_UPDATE_VIDEOS', key, value);
                },
            }
        },

        watch: {
            'switcher': function(value, oldValue) {
                if (oldValue !== null) {
                    this.toggleEditor(value);
                }
            }
        },

        events: {
            'action.save': function() {
                var self = this;

                var onOk = function() {
                    localStorage.setItem('inline_editor_ignore_incompatible_mode', true);
                    self.submitChanges();
                };

                if (this.showTinymceWarning && !localStorage.getItem('inline_editor_ignore_incompatible_mode')) {
                    this.confirmSave(onOk);
                } else {
                    onOk.apply(this);
                }
            },

            'tinymceWarning.ok': function() {
                this.hideWarning();
                if (this.callbacks.tinymceWarning.ok) {
                    this.callbacks.tinymceWarning.ok.apply(this);
                }
            },

            'tinymceWarning.cancel': function() {
                this.hideWarning();
                if (this.callbacks.tinymceWarning.cancel) {
                    this.callbacks.tinymceWarning.cancel.apply(this);
                }
            }
        },

        methods: {
            assignHandlers: function() {
                core.bind('inline_editor.image.inserted', _.bind(this.onImageInserted, this));
                core.bind('inline_editor.video.inserted', _.bind(this.onVideoInserted, this));
                core.bind('inline_editor.changed', _.bind(this.onChanged, this));
            },

            submitChanges: function() {
                var params = {
                    changeset: this.changeset,
                    videos: _.keys(this.videos),
                    images: _.keys(this.images)
                };

                params[xliteConfig.form_id_name] = xliteConfig.form_id;

                core.post(
                    {
                        base: xliteConfig.admin_script,
                        target: 'inline_editable',
                        action: 'update_field'
                    },
                    null,
                    params,
                    {
                        dataType: 'json',
                    }
                )
                    .done(_.bind(this.onSaveSuccess, this))
                    .fail(_.bind(this.onSaveFail, this));
            },

            onChanged: function (event, data) {
                this.updateChangeset(data.fieldId, data.change);
            },

            onImageInserted: function (event, data) {
                this.updateImages(data.imageId, data.imageElement[0]);
            },

            onVideoInserted: function (event, data) {
                this.updateVideos(data.videoId, data.videoElement[0]);
            },

            onSaveSuccess: function (event, status, xhr) {
                core.trigger('message', {type: 'info', message: core.t('Changes were successfully saved')});

                var updatedImageUrls = xhr.responseJSON.imageUrls;
                var updatedVideoUrls = xhr.responseJSON.videoUrls;

                var self = this;

                _.each(_.keys(updatedImageUrls), function(imageId) {
                    if (self.images[imageId]) {
                        self.images[imageId].src = updatedImageUrls[imageId];
                    }
                });

                _.each(_.keys(updatedVideoUrls), function(videoId) {
                    if (self.videos[videoId]) {
                        jQuery(self.videos[videoId]).find('video').prop('src', updatedVideoUrls[videoId]);
                    }
                });

                this.clearChangeset();
                this.clearImages();
                this.clearVideos();
                this.$dispatch('completed.save');
            },

            onSaveFail: function (event) {
                core.trigger('message', {type: 'error', message: core.t('Unable to save changes')});
                this.$dispatch('failed.save');
            },

            toggleEditor: function (state) {
                if (state) {
                    core.trigger('inline_editor.enable', this);
                } else {
                    core.trigger('inline_editor.disable', this);
                }
            },

            confirmSave: function(onOk, onCancel) {
                this.callbacks.tinymceWarning.ok = onOk;
                this.callbacks.tinymceWarning.cancel = onCancel;
                this.showWarning();
            },

            showWarning: function() {
                this.isTinymceWarningVisible = true;
            },

            hideWarning: function() {
                this.isTinymceWarningVisible = false;
            }
        }
    });
});
