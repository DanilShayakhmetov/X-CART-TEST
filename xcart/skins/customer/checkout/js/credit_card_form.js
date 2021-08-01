/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Credit card form
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var ccTypes = {
    'VISA'    : /^4[0-9]{0,18}$/,
    'MC'      : /^5$|^5[1-5][0-9]{0,14}$/,
    'AMEX'    : /^3$|^3[47][0-9]{0,13}$/,
    'JCB'     : /^2[1]?$|^21[3]?$|^1[8]?$|^18[0]?$|^(?:2131|1800)[0-9]{0,11}$|^3[5]?$|^35[0-9]{0,14}$/,
    'DICL'    : /^3$|^3[068]$|^3(?:0[0-5]|[68][0-9])[0-9]{0,11}$/,
    'CB'      : /^39/,
    'DC'      : /^6$|^6[05]$|^601[1]?$|^65[0-9][0-9]?$|^6(?:011|5[0-9]{2})[0-9]{0,12}$/,
    'BC'      : /^56[01]/,
    'SW'      : /^(5018|5020|5038|6304|6759|6761|6763|4903|4905|4911|4936|564182|633110|6333|6759)/,
    'SOLO'    : /^(6334|6767)/,
    'DELTA'   : /^401171/,
    'UKE'     : /^(417500|4917|4913|4508|4844)/,
    'LASER'   : /^6[37]/,
    'DANKORT' : /^4571/,
    'ER'      : /^2[01]/,
    'CUP'     : /^622[1-9]/
};

var ccTypeSprites = {
    'VISA'    : 'visa',
    'MC'      : 'mc',
    'AMEX'    : 'amex',
    'DICL'    : 'dicl',
    'DC'      : 'dc',
    'JCB'     : 'jcb',
    'BC'      : 'bc',
    'SW'      : 'sw',
    'SOLO'    : 'sw',
    'DELTA'   : 'visa',
    'CUP'     : 'cup'
};

/**
 * JS functions
 */

function trim(str) {
    return str.replace(/^[ ]+/, '').replace(/[ ]+$/, '');
}

function setCardType(type) {
    jQuery('#cvv2-hint').popover({ placement: 'top'});
    jQuery('#cc_cvv2')
        .focus(function() { $('#cvv2-hint').popover('show') })
        .blur(function() { $('#cvv2-hint').popover('hide') });

    jQuery('.cardType .icon').attr('class', 'dropdown-toggle icon');
    jQuery('.cardType .icon .card').attr('class', 'card');
    jQuery('.cardCVV2').attr('class', 'cardCVV2');
    jQuery('.cardCVV2 .right-text').attr('class', 'right-text').find('.default-text').show().siblings().hide();

    jQuery('#card_type').val('');

    if (typeof type == 'undefined') {
        var ccNumberField = jQuery('#cc_number');
        var accountNumber = ccNumberField.length
            ? ccNumberField.val().replace(/[^0-9]/g, '')
            : '';

        if ('' == accountNumber) {
            jQuery('.cardType .icon').addClass('blank');
            jQuery('.cardCVV2 .right-text').addClass('blank');
            return;
        }

        for (var t in ccTypes) {
            if (ccTypes[t].test(accountNumber)) {
                type = t;
                break;
            }
        }

    }

    if (typeof type == 'undefined') {
        jQuery('.cardType .icon').addClass('unknown');
        jQuery('.cardCVV2 .right-text').addClass('unknown').find('.default-text').show().siblings().hide();
        return;
    }

    jQuery('#card_type').val(type);

    if (ccTypeSprites[type]) {
        jQuery('.cardType .icon .card').addClass(ccTypeSprites[type]);
        jQuery('.cardCVV2').removeClass('blank').addClass(ccTypeSprites[type]);
    }


    var textSpan = jQuery('.cardCVV2 .right-text').find('.'+type);
    if (textSpan.length) {
        textSpan.show().siblings().hide();
    } else {
        jQuery('.cardCVV2 .right-text').attr('class', 'right-text').find('.default-text').show().siblings().hide();
    }
}

function setCardName() {
    var ccName = jQuery('#cc_name');

    if (ccName.length) {
      var firstname = jQuery('#shippingaddress-firstname').val();
      var lastname = jQuery('#shippingaddress-lastname').val();

      if (!jQuery('#same_address').prop('checked')) {
        firstname = jQuery('#billingaddress-firstname').val();
        lastname = jQuery('#billingaddress-lastname').val();
      }

      if (!firstname && !lastname) {
        firstname = jQuery('.address-field_firstname:last').text();
        lastname = jQuery('.address-field_lastname:last').text();
      }

      ccName.val(lastname ? `${firstname} ${lastname}` : firstname);
    }
}

function cardTypeHandlersSetter() {
    if (jQuery('.cc-form').length) {
        var numberField = jQuery('#cc_number');
        if ('undefined' !== typeof(Payment)) {
          Payment.formatCardNumber(numberField);
        }
        numberField.change(function () { setCardType(); });
        numberField.keyup(function () { setCardType(); });
        setCardType();
        setCardName();
    }
}

core.bind(['checkout.main.initialize', 'checkout.paymentTpl.loaded'], cardTypeHandlersSetter);
