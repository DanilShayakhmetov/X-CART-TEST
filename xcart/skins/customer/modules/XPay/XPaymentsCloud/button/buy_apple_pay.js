/* Buy with Apple Pay */

function BuyWithApplePayWrapper()
{
    BuyWithApplePayWrapper.superclass.constructor.apply(this, arguments);

    this.readyForCallbacks = false;

    this.setReadyForCallbacks = function(value) {
        this.readyForCallbacks = !!('undefined' === typeof value || value);
    }

    this.whenReadyForCallbacks = function(method, args) {
        if (!this.readyForCallbacks) {
            var promise = new Promise((function(resolve, reject) {
                var counter = 0;
                var checkReady = (function() {
                    counter++;
                    if (this.readyForCallbacks) {
                        resolve();
                    } else if (counter < 300) {
                        setTimeout(checkReady, 100);
                    }
                }).bind(this);
                checkReady();
            }).bind(this));

            promise.then(
                (function() {
                    (method.bind(this))(args);
                }).bind(this)
            );

        } else {
            (method.bind(this))(args);
        }
    }

    this.commonData = Object.assign({}, this.commonData, { xpaymentsBuyWithApplePay: '1'});
}

extend(BuyWithApplePayWrapper, CheckoutWithApplePayWrapper);

decorate('BuyWithApplePayWrapper', 'initialize', function(settings) {
    arguments.callee.previousMethod.apply(this, arguments);

    this.getWidget()
        .on('applepay.paymentauthorized', function(payment) {
            this.whenReadyForCallbacks(this.paymentHandler, payment);
        }, this)
        .on('applepay.shippingcontactselected', function(shippingContact) {
            this.whenReadyForCallbacks(this.shippingContactHandler, shippingContact);
        }, this)
        .on('applepay.shippingmethodselected', function(shippingMethod) {
            this.whenReadyForCallbacks(this.shippingMethodHandler, shippingMethod);
        }, this);
});

function getBuyWithApplePayStartForm() {
    return jQuery('.apple-pay-button-container').closest('form').get(0);
}

function assignBuyWithApplePayHandlers() {
    core.bind('xpaymentsBuyWithApplePayReady', function (event, data) {
        var widget = xpaymentsBuyWithApplePay.getWidget();
        widget.setOrder(data.total, data.currency);
        widget.config.applePay.shippingMethods = data.shippingMethods;
        widget.config.applePay.requiredShippingFields = data.requiredShippingFields;
        widget.config.applePay.requiredBillingFields = data.requiredBillingFields;
        xpaymentsBuyWithApplePay.setReadyForCallbacks();
    })
}

function startBuyWithApplePayWidget() {
    var form = getBuyWithApplePayStartForm();
    if (form) {
        showAdd2CartPopup = false;
        form.commonController.backgroundSubmit = true;
        form['xpaymentsBuyWithApplePay'].value = '1';
        jQuery(form).submit();
        xpaymentsBuyWithApplePay.setReadyForCallbacks(false);
        xpaymentsBuyWithApplePay.begin();
    }
}

// WA to prevent add2cart animation
if (
    'function' == typeof window.getProductRepresentationFor
    && 'undefined' == typeof window._getProductRepresentationFor
) {
    window._getProductRepresentationFor = window.getProductRepresentationFor;
    window.getProductRepresentationFor = function (element) {
        var form = getBuyWithApplePayStartForm();
        if (form && '1' == form['xpaymentsBuyWithApplePay'].value) {
            return {
                element: null,
                view: null
            };
        } else {
            return window._getProductRepresentationFor(element);
        }
    }
}

// Avoid reset of selected product option
define(['ProductDetails'], function () {
    decorate(
        'ProductDetailsView',
        'postprocessAdd2Cart',
        function (event, data) {
            var form = getBuyWithApplePayStartForm();
            if (form && '1' == form['xpaymentsBuyWithApplePay'].value) {
                form['xpaymentsBuyWithApplePay'].value = '';
            } else {
                arguments.callee.previousMethod.apply(this, arguments);
            }
        }
    );
});