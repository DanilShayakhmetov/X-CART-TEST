/* Checkout with Apple Pay */

function CheckoutWithApplePayWrapper()
{
    this.widget = new XPaymentsWidget();

    this.commonData = {};
    this.commonData[xliteConfig.form_id_name] = xliteConfig.form_id;

    this.blockPage = function() {
        assignShadeOverlay(jQuery('#page-wrapper'));
    }
    this.unblockPage = function() {
        unassignShadeOverlay(jQuery('#page-wrapper'));
    }
    this.waitPage = function() {
        assignWaitOverlay(jQuery('#page-wrapper'));
        jQuery('.lc-minicart').hide();
    }
    this.load = function() {
        this.getWidget().load();
    }
    this.begin = function() {
        this.getWidget().beginCheckoutWithApplePay();
    }
    this.isSupported = function() {
        return this.getWidget().isApplePaySupportedByDevice();
    }
    this.getWidget = function() {
        return this.widget;
    }
}

CheckoutWithApplePayWrapper.prototype.initialize = function(settings) {
    this.getWidget().initCheckoutWithApplePay(settings);

    // Assign handlers
    this.getWidget().on('fail', function () {
        this.unblockPage();
    }, this).on('applepay.start', function () {
        this.blockPage();
    }, this).on('alert', function (params) {
        setTimeout(function () {
            if ('popup' === params.type) {
                core.trigger('message', {type: 'info', message: params.message});
            } else {
                core.showError(params.message);
            }
        }, 500)
    })
    .on('applepay.paymentauthorized', this.paymentHandler, this)
    .on('applepay.shippingcontactselected', this.shippingContactHandler, this)
    .on('applepay.shippingmethodselected', this.shippingMethodHandler, this);
}

CheckoutWithApplePayWrapper.prototype.shippingContactHandler = function(shippingContact)
{
    var data = {
        destination_country: shippingContact.countryCode,
        destination_state: shippingContact.administrativeArea,
        destination_custom_state: shippingContact.administrativeArea,
        destination_zipcode: shippingContact.postalCode,
        destination_city: shippingContact.locality,
    }
    core.post(
        {
            target: 'apple_pay_shipping',
            action: 'set_destination'
        },
        (function (xhr) {
            var response = jQuery.parseJSON(xhr.responseText);
            response.newTotal.label = this.getWidget().config.company.name;
            if (response.errors) {
                response.errors.forEach(
                    function (error, i, arr) {
                        arr[i] = new ApplePayError(error.code, error.contactField, error.message);
                    }
                );
            }
            this.getWidget().completeApplePayShippingContactSelection(response);
        }).bind(this),
        Object.assign({}, data, this.commonData)
    );
}

CheckoutWithApplePayWrapper.prototype.shippingMethodHandler = function(shippingMethod)
{
    var data = {
        methodId: shippingMethod.identifier,
    }

    core.post(
        {
            target: 'apple_pay_shipping',
            action: 'change_method'
        },
        (function (xhr) {
            var response = jQuery.parseJSON(xhr.responseText);
            response.newTotal.label = this.getWidget().config.company.name;
            this.getWidget().completeApplePayShippingMethodSelection(response);
        }).bind(this),
        Object.assign({}, data, this.commonData)
    );
}

CheckoutWithApplePayWrapper.prototype.paymentHandler = function(payment)
{
    var data = {
        billingContact: payment.billingContact,
        shippingContact: payment.shippingContact,
    }

    core.post(
        {
            target: 'checkout',
            action: 'xpayments_apple_pay_prepare'
        },
        (function (xhr) {
            var response = jQuery.parseJSON(xhr.responseText);
            if (response.errors && response.errors.length) {
                response.errors.forEach(
                    function (error, i, arr) {
                        arr[i] = new ApplePayError(error.code, error.contactField, error.message);
                    }
                );
                this.getWidget().completeApplePayPayment({
                    status: ApplePaySession.STATUS_FAILURE,
                    errors: response.errors
                });
            } else {
                this.waitPage();
                this.getWidget().succeedApplePayPayment(payment);
            }
        }).bind(this),
        Object.assign({}, data, this.commonData)
    );
}

