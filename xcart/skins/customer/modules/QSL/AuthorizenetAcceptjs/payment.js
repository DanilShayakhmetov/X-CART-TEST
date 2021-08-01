/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Authorize.net accept.js initialize
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
    'checkout.main.initialize',
    function () {
        core.bind(
            'checkout.paymentTpl.postprocess',
            function (event, data) {
                var box = jQuery('.anetjs-box');
                if (box.length && typeof(window.Accept) != 'undefined')
                {
                    if (!box.find('#cc_name').val() && box.data('name')) {
                        box.find('#cc_name').val(box.data('name'));
                    }

                    // Update payment template by change of cart total
                    PaymentTplView.prototype.handleUpdateCartAuthorizenetAcceptjs = function (event, data) {
                        if (!this.isLoading && 'undefined' != typeof(data.total)) {
                            this.load();
                        }
                    };

                    if (data) {
                        core.bind(
                          'updateCart',
                          _.bind(data.widget.handleUpdateCartAuthorizenetAcceptjs, data.widget)
                        );
                    }
                }
            }
        );

        core.bind(
            'checkout.common.ready',
            function (event, state) {
                var data;
                var box = jQuery('.anetjs-box');
                if (box.length && !box.find('.data-descriptor').val()) {
                    data = {
                        cardData: {
                            cardNumber: box.find('#cc_number').val().replace(/[^0-9]/g, ''),
                            month:      box.find('#cc_expire_month').val(),
                            year:       box.find('#cc_expire_year').val(),
                            cardCode:   box.find('#cc_cvv2').val().replace(/[^0-9]/g, ''),
                            fullName:   box.find('#cc_name').val()
                        },
                        authData: {
                            clientKey:  box.data('public-key'),
                            apiLoginID: box.data('api-login-id')
                        }
                    };

                    var checkoutBox = jQuery('.checkout-block .steps').get(0);
                    if (checkoutBox) {
                        checkoutBox.loadable.shade();
                    }

                    Accept.dispatchData(data,'anetjsHandler');

                    state.state = false;
                }
            }
        );

    }
);

/**
 * AuthorizeNet accept.js response handler
 *
 * @param {object} response
 */
function anetjsHandler(response)
{
    if (response.messages.resultCode === 'Error') {
        var checkoutBox = jQuery('.checkout-block .steps').get(0);
        if (checkoutBox) {
            checkoutBox.loadable.unshade();
        }

        for (var i = 0; i < response.messages.message.length; i++) {
            console.log(response.messages.message[i].code + ':' + response.messages.message[i].text);
        }

        setTimeout(
            function() {
                for (var i = 0; i < response.messages.message.length; i++) {
                    core.trigger('message', {type: 'error', message: response.messages.message[i].text});
                }
            },
            500
        );

    } else {
        var box = jQuery('.anetjs-box');

        box.find('.data-descriptor').val(response.opaqueData.dataDescriptor);
        box.find('.data-value').val(response.opaqueData.dataValue);

        jQuery('form.place').submit();
    }
}