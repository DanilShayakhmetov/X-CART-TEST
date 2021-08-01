/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/labels_editor', ['js/vue/vue'], function (XLiteVue) {
    XLiteVue.component('xlite-labels-editor', {
        ready: function() {
            core.trigger('labels-editor.ready', this);

            if (!this.hasState()) {
                this.setState(true);
            }

            this.hideSaveButton();
            this.initializePopovers();
            this.setSwitcherState(this.getState());
        },

        vuex: {
            getters: {
                switcher: function(state) {
                    return state.actions.switcher;
                },
            },

            actions: {
                hideSaveButton: function(state) {
                    state.dispatch('SET_SAVE_AVAILABILITY', false);
                },

                setSwitcherState: function(state, value) {
                    state.dispatch('TOGGLE_SWITCHER', value);
                },
            }
        },

        watch: {
            'switcher': function(value, oldValue) {
                if (value) {
                    core.trigger('editable-label.enable');
                } else {
                    core.trigger('editable-label.disable');
                }

                this.setState(value);
            }
        },

        events: {
        },

        methods: {
            hasState: function() {
                var key = 'labels-editor-mode';
                return sessionStorage.getItem(key) !== null;
            },
            getState: function() {
                var key = 'labels-editor-mode';
                return sessionStorage.getItem(key) === 'true';
            },
            setState: function(value) {
                var key = 'labels-editor-mode';
                sessionStorage.setItem(key, value);
            },
            initializePopovers: function() {
                $(document).on('click', EditableLabel.closePopovers);
                core.autoload(EditableLabel, '.xlite-translation-label');
            }
        }
    });
});
