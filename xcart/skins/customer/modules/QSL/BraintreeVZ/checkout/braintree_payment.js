/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Braintree widget 
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var braintreePayment = {

    /**
     * Client authorization from the server
     */
    token: '',

    /**
     * Merchant ID
     */
    merchantId: '',

    /**
     * Test mode flag
     */
    isTestMode: false,

    /**
     * 3-D Secure flag
     */
    is3dSecure: false,

    /**
     * 3-D Secure for not supported cards flag
     */
    isAcceptNo3dSecure: false,

    /**
     * Error message for the failed 3-D Secure
     */
    acceptNo3dSecureError: '3-D Secure failed',

    /**
     * 3-D Secure for the vaulted cards
     */
    is3dSecureForVault: false,

    /**
     * PayPal flag
     */
    isPayPal: false,

    /**
     * Apple Pay flag
     */
    isApplePay: false,

    /**
     * Google Pay flag
     */
    isGooglePay: false,

    /**
     * Flag indicating is it a PayPal express checkout button or complete braintree component
     */
    isButton: false,

    /**
     * Flag indicating is it an anonymous customer
     */
    isAnonymous: false,

    /**
     * PayPal button rendering attempts
     */
    paypalButtonRenderingAttempts: 0,

    /**
     * Maximum PayPal button rendering attempts
     */
    maxPaypalButtonRenderingAttempts: 100,

    /**
     * Kount merchant ID
     */
    kountMerchantId: '',

    /**
     * Company name
     */
    companyName: '',

    /**
     * Cart total
     */
    cartTotal: 0,

    /**
     * Currency code
     */
    currencyCode: '',

    /**
     * Payment method nonce
     */
    _nonce: '',
    get nonce() {
        return this._nonce;
    },
    set nonce(value) {
        this._nonce = value;
        this.log('Set nonce: ' + value);
        if (this.nonceElement) {
            this.nonceElement.value = value;
            this.log(this.nonceElement);
        }
    },

    /**
     * Credit card bin
     */
    bin: '',

    /**
     * Payment method nonce element
     */
    nonceElement: null,

    /**
     * Form element
     */
    formElement: null,

    /**
     * Braintree client instance
     */
    client: null,

    /**
     * Braintree hosted fields instance
     */
    hostedFields: null,

    /**
     * Braintree PayPal instance
     */
    paypal: null,

    /**
     * Braintree PayPal Checkout instance
     */
    paypalCheckout: null,

    /**
     * Braintree 3-D Secure instance
     */
    secure3d: null,

    /**
     * Braintree Apple Pay instance
     */
    applePay: null,

    /**
     * Apple Pay session instance
     */
    applePaySession: null,

    /**
     * Google Payment client
     */
    googlePaymentsClient: null,

    /**
     * Google Payment instance
     */
    googlePaymentInstance: null,

    /**
     * Credit card number selector
     */
    numberSelector: '',

    /**
     * Credit card number placeholder
     */
    numberPlaceholder: '',

    /**
     * CVV selector
     */
    cvvSelector: '',

    /**
     * CVV placeholder
     */
    cvvPlaceholder: '',

    /**
     * Expiration date selector
     */
    dateSelector: '',

    /**
     * Expiration date placeholder
     */
    datePlaceholder: '',

    /**
     * PayPal button style
     */
    paypalButtonStyle: {},

    /**
     * Google Payment button style
     */
    googlePaymentButtonStyle: {},

    /**
     * Flag indicating braintree is initialized
     */
    isInitialized: false,

    /**
     * Flag indicating something is in progress 
     */
    _isInProgress: false,
    get isInProgress() {
        return this._isInProgress;
    },
    set isInProgress(value) {
        this._isInProgress = value;
        this.checkout.processShadows();
    },

    /**
     * Flag indicating something is loading
     */
    _isLoading: false,
    get isLoading() {
        return this._isLoading;
    },
    set isLoading(value) {
        this._isLoading = value;
        this.checkout.processShadows();
    },

    /**
     * Credit card form fields
     */
    fields: {},

    /**
     * Credit card form CSS styles
     */
    styles: {},

    /**
     * Trigger error
     */
    triggerError: function triggerError(e) {
        console.error(e);

        var message = 'Unknown Braintree error';

        if (typeof e === 'string' || e instanceof String) {
            message = e;
        } else if ('undefined' != typeof e.message) {
            message = e.message;
        }
        else if ('undefined' != typeof e.statusMessage) {
            message = e.statusMessage;
        } else if ('undefined' != typeof e.statusCode) {
            if ('CANCELED' == e.statusCode) {
                message = 'Action was canceled by customer';
            } else {
                message = e.statusCode;
            }
        }

        this.nonce = '';
        this.bin = '';

        this.checkout.triggerError(message);

        this.isInProgress = false;
        this.isLoading = false;
        if (this.isFlc()) {
            Checkout.instance.$root.finishLoadAnimation();
        }
    },

    /**
     * Log something to console
     */
    log: function log(data) {
        if (core.isDeveloperMode) {
            console.log(data);
        }
    },

    /**
     * Check if Braintree is the current method
     */
    isCurrent: function isCurrent(includeSavedCards) {
        return this.checkout.isCurrent(includeSavedCards);
    },

    /**
     * Check if it is Fastlane checkout
     */
    isFlc: function isFlc() {
        return jQuery('.checkout_fastlane_container').length > 0;
    },

    /**
     * Create Braintree 3-D Secure instance callback
     */
    create3DSecureCallback: function create3DSecureCallback(error, instance) {
        if (error) {
            return this.triggerError(error);
        }

        this.secure3d = instance;

        this.checkout.create3DSecureCallback(instance);

        this.isInProgress = false;
        this.isInitialized = true;

        this.log('Braintree payment initialized');
    },

    /**
     * Verify card via 3-D Secure
     */
    verifyCard: function verifyCard(options) {
        if ('undefined' != options.nonce && options.nonce) {
            this.nonce = options.nonce;
        }

        var threeDSecureParameters = {
            amount: options.total,
            nonce: this.nonce,
            bin: this.bin,
            email: options.email,
            billingAddress: options.billingAddress,
            additionalInformation: options.additionalInformation,
            onLookupComplete: function (data, next) {
                next();
            }
        };

        this.secure3d.verifyCard(threeDSecureParameters, this.verifyCardCallback.bind(this));
    },

    /**
     * 3-D Secure verify card callback
     */
    verifyCardCallback: function verifyCardCallback(error, response) {
        if (error) {
            return this.triggerError(error);
        }

        if ('undefined' != typeof response.verificationDetails && response.verificationDetails.liabilityShiftPossible == false && response.verificationDetails.liabilityShifted == false && this.isAcceptNo3dSecure == false) {
            return this.triggerError(this.acceptNo3dSecureError);
        }

        this.checkout.verifyCardCallback(response);

        this.nonce = response.nonce;

        this.isInProgress = false;
        jQuery(this.formElement).submit();
    },

    /**
     * Create Braintree hosted fields instance callback
     */
    createHostedFieldsCallback: function createHostedFieldsCallback(error, instance) {
        if (error) {
            return this.triggerError(error);
        }

        // Clear nonce
        this.nonce = '';

        // Form submitter
        var submitEvent = 'submit';
        if (this.isFlc()) {
            submitEvent = 'beforeSubmit';
        }

        jQuery(this.formElement).bind(submitEvent, this.submitForm.bind(this));

        this.hostedFields = instance;

        this.checkout.createHostedFieldsCallback(instance);

        if (this.is3dSecure) {
            braintree.threeDSecure.create({
                version: 2,
                client: this.client
            }, this.create3DSecureCallback.bind(this));
        } else {
            this.isInProgress = false;
            this.isInitialized = true;

            this.log('Braintree payment initialized');
        }
    },

    /**
     * Get options for the Hosted Fields initialization 
     */
    getHostedFieldsOptions: function getHostedFieldsOptions() {
        return {
            client: this.client,
            styles: this.styles,
            fields: this.fields
        };
    },

    /**
     * PayPal button payment
     */
    paypalButtonPayment: function paypalButtonPayment() {
        var _this = this;

        return new Promise(function (resolve, reject) {
            _this.checkout.getPayPalData(function (options) {
                resolve(_this.paypalCheckout.createPayment(options));
            });
        });
    },

    /**
     * Callback for get PayPal data
     */
    getPayPalDataCallback: function getPayPalDataCallback(options) {
        return this.paypalCheckout.createPayment(options);
    },

    /**
     * PayPal button Authorize action
     */
    paypalButtonOnAuthorize: function paypalButtonOnAuthorize(data, actions) {
        return this.paypalCheckout.tokenizePayment(data).then(this.paypalButtonOnAuthorizeThen.bind(this));
    },

    /**
     * Then for PayPal button Authorize action
     */
    paypalButtonOnAuthorizeThen: function paypalButtonOnAuthorizeThen(payload) {
        this.nonce = payload.nonce;

        this.isInProgress = false;

        if (!this.isButton && !this.isAnonymous) {

            // Submit order
            jQuery(this.formElement).submit();
        } else {

            // Proceed to checkout
            this.checkout.continuePayPal(payload.details);
        }
    },

    /**
     * PayPal button Cancel action
     */
    paypalButtonOnCancel: function paypalButtonOnCancel(data) {
        this.log(data);
    },

    /**
     * Create PayPal Checkout calback
     */
    createPayPalCheckoutCallback: function createPayPalCheckoutCallback(error, instance) {
        if (!this.isButton) {
            // Regardless of the error create hosted fields
            braintree.hostedFields.create(this.getHostedFieldsOptions(), this.createHostedFieldsCallback.bind(this));
        }

        if (error) {
            return this.triggerError(error);
        }

        this.paypalCheckout = instance;

        setTimeout(this.renderPayPalButtons.bind(this), 1000);
    },

    /**
     * Render PayPal buttons
     */
    renderPayPalButtons: function renderPayPalButtons() {

        if ('undefined' == typeof paypal || 'undefined' == typeof paypal.Button) {

            if (this.maxPaypalButtonRenderingAttempts > this.paypalButtonRenderingAttempts) {
                this.paypalButtonRenderingAttempts++;
                setTimeout(this.renderPayPalButtons.bind(this), 1000);
            }

            return;
        }

        var options = {
            env: this.isTestMode ? 'sandbox' : 'production',
            commit: !this.isButton,
            payment: this.paypalButtonPayment.bind(this),
            onAuthorize: this.paypalButtonOnAuthorize.bind(this),
            onCancel: this.paypalButtonOnCancel.bind(this),
            onError: this.triggerError.bind(this),
            style: this.paypalButtonStyle
        };

        var _braintree = this;

        jQuery('.braintree-paypal-button').each(function(i) {
            this.id = 'braintree-paypal-button-' + (i + 1);

            paypal.Button.render(options, '#' + this.id).then(function () {
                // The PayPal button will be rendered in an html element with the id
                // `paypal-button`. This function will be called when the PayPal button
                // is set up and ready to be used.
            });

            if (_braintree.isButton) {
                _braintree.isInProgress = false;
            }

        });

    },

    /**
     * Create PayPal calback
     */
    createPayPalCallback: function createPayPalCallback(error, instance) {
        if (error) {

            if (!this.isButton) {
                // Regardless of the error create hosted fields
                braintree.hostedFields.create(this.getHostedFieldsOptions(), this.createHostedFieldsCallback.bind(this));
            }

            return this.triggerError(error);
        }

        this.paypal = instance;

        var options = {
            client: this.client
        };

        // Create a PayPal Checkout component
        braintree.paypalCheckout.create(options, this.createPayPalCheckoutCallback.bind(this));
    },

    /**
     * Create Braintree client instance callback
     */
    createClientCallback: function createClientCallback(error, instance) {
        if (error) {
            return this.triggerError(error);
        }

        this.client = instance;

        this.checkout.createClientCallback();

        if (this.isPayPal || this.isButton) {

            var options = {
                client: this.client
            };

            // Create PayPal. Hosted fields are created in callback if necessary 
            braintree.paypal.create(options, this.createPayPalCallback.bind(this));
        } else {

            braintree.hostedFields.create(this.getHostedFieldsOptions(), this.createHostedFieldsCallback.bind(this));
        }

        if (
            this.isApplePay
            && this.canMakeApplePayPayments()
            && 'undefined' !== typeof braintree.applePay
        ) {
            braintree.applePay.create(options, this.createApplePayCallback.bind(this));
        }

        if (this.isGooglePay
            && 'undefined' !== typeof google
            && 'undefined' !== typeof braintree.googlePayment
        ) {
            this.googlePaymentsClient = new google.payments.api.PaymentsClient({
                environment: this.isTestMode ? 'TEST' : 'PRODUCTION'
            });

            var googlePaymentOptions = {
                client: this.client,
                googlePayVersion: 2,
            };

            if (!this.isTestMode) {
                Object.assign(googlePaymentOptions, {googleMerchantId: this.googlePayMerchantId});
            }

            braintree.googlePayment.create(googlePaymentOptions, this.createGooglePaymentCallback.bind(this));
        }
    },

    /**
     * Create Apple Pay callback
     */
    createApplePayCallback: function createApplePayCallback(error, applePayInstance) {
        if (error) {
            return this.triggerError(error);
        }

        this.applePay = applePayInstance;

        var promise = ApplePaySession.canMakePaymentsWithActiveCard(this.applePay.merchantIdentifier);

        promise.then(
            function (canMakePaymentsWithActiveCard) {

                if (canMakePaymentsWithActiveCard) {
                    jQuery('.apple-pay-button-container').show();
                    jQuery('#apple-pay-button').on('click', this.onApplePayButtonClicked.bind(this));
                }
            }.bind(this)
        );
    },

    /**
     * @param event
     */
    onApplePayButtonClicked: function onApplePayButtonClicked(event) {
        event.preventDefault();

        var paymentRequest = this.applePay.createPaymentRequest({
            total: {
                label : this.companyName,
                amount: String(this.cartTotal)
            },
            requiredBillingContactFields: ['postalAddress']
        });

        this.applePaySession = new ApplePaySession(3, paymentRequest);

        this.applePaySession.onvalidatemerchant = this.onValidateApplePayMerchant.bind(this);
        this.applePaySession.onpaymentauthorized = this.onApplePayPaymentAuthorized.bind(this);
        this.applePaySession.begin();
    },

    /**
     * @see https://developers.braintreepayments.com/guides/apple-pay/client-side/javascript/v3#onvalidatemerchant-callback
     * @param event
     */
    onValidateApplePayMerchant: function onValidateApplePayMerchant(event) {

        this.applePay.performValidation(
            {
                validationURL: event.validationURL,
                displayName  : this.companyName
            },
            this.completeApplePayMerchantValidation.bind(this)
        );
    },

    /**
     *
     * @param error
     * @param merchantSession
     * @returns {*|void}
     */
    completeApplePayMerchantValidation: function completeApplePayMerchantValidation(error, merchantSession) {
        if (error) {
            return this.triggerError(error);
        }
        this.applePaySession.completeMerchantValidation(merchantSession);
    },

    /**
     * @see https://developers.braintreepayments.com/guides/apple-pay/client-side/javascript/v3#onpaymentauthorized-callback
     * @param event
     */
    onApplePayPaymentAuthorized: function onApplePayPaymentAuthorized(event) {
        this.applePay.tokenize(
            {token: event.payment.token},
            this.onApplePayTokenize.bind(this)
        );
    },

    /**
     *
     * @param error
     * @param payload
     */
    onApplePayTokenize: function onApplePayTokenize(error, payload) {
        if (error) {
            this.applePaySession.completePayment(ApplePaySession.STATUS_FAILURE);
            return this.triggerError(error);
        }

        this.nonce = payload.nonce;
        this.isInProgress = false;
        this.applePaySession.completePayment(ApplePaySession.STATUS_SUCCESS);
        jQuery(this.formElement).submit();
    },

    /**
     * On Google Payment created callback handler
     *
     * @param error
     * @param googlePaymentInstance
     */
    createGooglePaymentCallback: function createGooglePaymentCallback(error, googlePaymentInstance) {
        if (error) {
            return this.triggerError(error);
        }

        this.googlePaymentInstance = googlePaymentInstance;

        this.googlePaymentsClient.isReadyToPay({
            apiVersion                   : 2,
            apiVersionMinor              : 0,
            allowedPaymentMethods        : this.googlePaymentInstance.createPaymentDataRequest().allowedPaymentMethods,
            existingPaymentMethodRequired: true,
        })
            .then(this.onGooglePaymentReadyToPay.bind(this))
            .catch(function (error) {
                this.triggerError(error);
            }.bind(this));
    },

    /**
     * Set up Google Pay button
     *
     * @param isReadyToPay
     */
    onGooglePaymentReadyToPay: function onGooglePaymentReadyToPay(isReadyToPay) {
        if (isReadyToPay.result) {
            var googlePayBtnContainer = jQuery('.payment-tpl .google-pay-button-container');
            var btnOptions = this.googlePaymentButtonStyle;
            Object.assign(btnOptions, {onClick: this.onGooglePaymentButtonClicked.bind(this)});
            var googePayBtn = this.googlePaymentsClient.createButton(btnOptions);
            googlePayBtnContainer.append(googePayBtn);
        }
    },

    /**
     * onGooglePaymentButtonClicked callback handler
     *
     * @param event
     */
    onGooglePaymentButtonClicked: function onGooglePaymentButtonClicked(event) {
        event.preventDefault();

        var paymentDataRequest = this.googlePaymentInstance.createPaymentDataRequest({
            transactionInfo: {
                currencyCode    : this.currencyCode,
                totalPriceStatus: 'FINAL',
                totalPrice      : String(this.cartTotal),
            },
        });

        var cardPaymentMethod = paymentDataRequest.allowedPaymentMethods[0];
        cardPaymentMethod.parameters.billingAddressRequired = true;
        cardPaymentMethod.parameters.billingAddressParameters = {
            format             : 'FULL',
            phoneNumberRequired: true
        };

        this.googlePaymentsClient.loadPaymentData(paymentDataRequest)
            .then(this.onGooglePaymentLoadPaymentData.bind(this))
            .catch(function (error) {
                this.triggerError(error);
            }.bind(this));
    },

    /**
     * onGooglePaymentLoadPaymentData callback handler
     *
     * @param paymentData
     */
    onGooglePaymentLoadPaymentData: function onGooglePaymentLoadPaymentData(paymentData) {
        this.googlePaymentInstance.parseResponse(paymentData, this.onGooglePaymentParseResponse.bind(this));
    },

    /**
     * onGooglePaymentParseResponse callback handler
     *
     * @param error
     * @param result
     */
    onGooglePaymentParseResponse: function onGooglePaymentParseResponse(error, result) {
        if (error) {
            return this.triggerError(error);
        }

        this.nonce = result.nonce;
        this.isInProgress = false;
        jQuery(this.formElement).submit();
    },

    /**
     * Tokenize card callback
     */
    tokenizeCallback: function tokenizeCallback(error, payload) {
        if (error) {
            return this.triggerError(error);
        }

        this.nonce = payload.nonce;
        this.bin = payload.details.bin;

        this.checkout.tokenizeCallback(payload);

        if (this.is3dSecure && this.secure3d) {

            this.checkout.getCartTotal(this.verifyCard.bind(this));
        } else {

            this.isInProgress = false;
            jQuery(this.formElement).submit();
        }
    },

    /**
     * Form submitter
     */
    submitForm: function submitForm(event) {
        var allow = true;

        if (this.isInitialized && this.isCurrent(true) && 'undefined' != typeof braintree) {

            // This is one of the Braintree payment methods

            if (this.isInProgress) {

                // Form is being submitted, do nothing
                allow = false;
                event.preventDefault();
            } else if (this.nonce == '') {

                if (this.isCurrent(false)) {

                    // This is credit card payment
                    this.isInProgress = true;
                    allow = false;
                    event.preventDefault();
                    this.hostedFields.tokenize(this.tokenizeCallback.bind(this));
                } else {

                    // This is saved card payment
                    if (this.is3dSecureForVault && this.secure3d) {

                        // Process 3-D secure for the vault
                        this.isInProgress = true;
                        allow = false;
                        event.preventDefault();
                        this.checkout.getSavedCardNonce(this.verifyCard.bind(this));
                    }
                }

            } else {

                // For FLC, where values are copied before form submit
                $('input[name="payment_method_nonce"]', 'form.place').val(this.nonce);
            }

        }

        return allow;
    },

    /**
     * List of required properties
     */
    required: ['token', 'merchantId', 'nonceSelector', 'formSelector', 'numberSelector', 'cvvSelector', 'dateSelector'],

    /**
     * Init callback
     */
    initCallback: function initCallback(options) {
        // Copy options
        for (var i in options) {
            this[i] = options[i];
        }

        // Check options
        for (var i in this.required) {
            if (!this[this.required[i]]) {
                return this.triggerError('Empty field: ' + this.required[i]);
            }
        }

        this.nonceElement = document.querySelector(this.nonceSelector);
        this.formElement = document.querySelector(this.formSelector);

        if (!this.isButton) {

            // Details for the hosted fields
            this.fields = {
                'number': { selector: this.numberSelector, placeholder: this.numberPlaceholder },
                'cvv': { selector: this.cvvSelector, placeholder: this.cvvPlaceholder },
                'expirationDate': { selector: this.dateSelector, placeholder: this.datePlaceholder }
            };
        }

        var clientOptions = {
            authorization: this.token
        };

        braintree.client.create(clientOptions, this.createClientCallback.bind(this));
    },

    /**
     * Constructor/initializator
     */
    init: function init(counter) {

        if ('number' != typeof counter) {
            counter = 1;
        }

        this.log('Braintree payment initialization started. Attempt: ' + counter);

        if (!this.isCurrent()) {
            this.log('Braintree is not current payment method');
            this.teardown();
            return;
        }

        this.isLoading = true;

        if (
            'undefined' == typeof braintree
            || this.isPayPal && 'undefined' == typeof braintree.paypal
            || this.is3dSecure && 'undefined' == typeof braintree.threeDSecure
        ) {

            if (20 > counter) {
                this.log('Waiting for Braintree lib');
                setTimeout(this.init.bind(this, ++counter), 500);
            } else {
                this.triggerError('Unable to load Braintree lib');
            }

            return;
        }

        this.isLoading = false;

        if (this.isInitialized) {
            this.log('Braintree is already initialized');
            return
        };

        if (this.isInProgress) {
            this.log('Braintree initialization in progress');
            return
        };

        this.isInProgress = true;

        this.checkout.init(this.initCallback.bind(this));
    },

    /**
     * Destructor
     */
    teardown: function() {

        this.log('Braintree payment teardown');

        try {
            this.client.teardown();
            this.hostedFields.teardown();
            this.paypal.teardown();
            this.paypalCheckout.teardown();
            this.secure3d.teardown();
            this.applePay.teardown();
            this.googlePaymentsClient.teardown();
        } catch (err) {
            // Do nothing. Apparenty somethng was not defined
        }

        this.isInitialized = false;
        this.isInProgress = false;
        this.isLoading = false;
    },

    /**
     * Update cart total
     */
    updateCartTotal: function updateCartTotal(event, data) {
        this.cartTotal = data.total;
        this.currencyCode = data.currency;
    },

    /**
     * Whether the client device supports Apple Pay
     *
     * @returns {boolean}
     */
    isSupportApplePay: function isSupportApplePay() {
        return 'undefined' !== typeof window.ApplePaySession;
    },

    /**
     * Whether the client device can make Apple Pay payments
     *
     * @returns {boolean}
     */
    canMakeApplePayPayments: function canMakeApplePayPayments() {
        return this.isSupportApplePay() && window.ApplePaySession.canMakePayments();
    },

    /**
     * Some external methods which shoudld be processed by the store checkout page
     */
    checkout: {
        init: function init(callback) {
            callback();
        }, // @param callback Callback function 
        triggerError: function triggerError(message) {}, // @param message Error message
        create3DSecureCallback: function create3DSecureCallback() {}, // @param instance 3-D Secure object instance
        verifyCardCallback: function verifyCardCallback(response) {}, // @param response Braintree response of card verification
        createHostedFieldsCallback: function createHostedFieldsCallback() {},
        createClientCallback: function createClientCallback() {},
        tokenizeCallback: function tokenizeCallback(payload) {}, // @param payload Braintree payload with card tokenization nonce
        getCartTotal: function getCartTotal(callback) {
            callback();
        }, // @param callback Callback function
        getSavedCardNonce: function getSavedCardNonce(callback) {
            callback();
        }, // @param callback Callback function
        getPayPalData: function getPayPalData(callback) {
            callback();
        }, // @param callback Callback function
        continuePayPal: function continuePayPal(details) {
            var params = this.getUrlParams({
                target: 'checkout',
                action: 'continue_paypal'
            });

            var url = URLHandler.buildURL(params);

            var form = $('<form method="post" action="' + url + '"></form>');

            $('<input>').attr('type', 'hidden').attr('name', 'nonce').val(braintreePayment.nonce).appendTo(form);
            $('<input>').attr('type', 'hidden').attr('name', 'details').val(JSON.stringify(details)).appendTo(form);

            form.appendTo('body').submit();
        }, // @param details Some details from PayPal
        processShadows: function processShadows() {},
        isCurrent: function isCurrent() {
            return false;
        }
    }
};
