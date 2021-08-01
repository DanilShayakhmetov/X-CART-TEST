/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Webmaster mode component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('themetweaker/panel/modal', ['js/vue/vue'], function (XLiteVue) {
    XLiteVue.component('xlite-themetweaker-modal', {
        template: document.querySelector('#themetweaker-modal-template').innerHTML,
        props: {
            namespace: {
                type: String
            },
            show: {
                type: Boolean,
                required: true,
            }
        },

        activate: function (done) {
            core.trigger('modal.activate', this);
            done();
        },

        ready: function() {
            core.trigger('modal.ready', this);
        },

        methods: {
            sendEvent: function(name) {
                this.$dispatch(this.namespace + '.' + name, this);
            },
            sendCancelEvent: function() {
                this.$dispatch(this.namespace + '.cancel', this);
            },
        }
    });
});
