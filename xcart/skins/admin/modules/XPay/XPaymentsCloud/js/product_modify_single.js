/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Additional product page controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

    /**
     * Toggle validate rule to field
     *
     * @param field jQuery wrapped field
     * @param rule  rule string
     * @param force if force is defined, then add rule if force is true, remove - otherwise
     *
     * @return void
     */
    var toggleValidateRule = function (field, rule, force) {

        // get rules from class attribute
        var rules = /validate\[(.*)\]/.exec(field.attr('class'));
        var rulesStr = rules ? rules[1] : '';
        var rules_list = rulesStr.split(',');

        // check if required rule already exists
        var exists = rules_list.indexOf(rule) !== -1;

        // if force is undefined then toggle rule
        force = typeof force == 'undefined' ? !exists : force;

        // add rule
        if (force && !exists) {
            rules_list.push(rule);

            // remove rule
        } else if (!force && exists) {
            rules_list.splice(rules_list.indexOf(rule), 1);
        }

        // remove empty rules
        rules_list = jQuery.grep(rules_list, function(n) {return n;});

        // replace validate class
        field.removeClass('validate['+rulesStr+']');
        if (rules_list.length) {
            field.addClass('validate['+rules_list.join()+']');
        }
    };

    core.bind(
        'load',
        function () {
            var isSubscriptionPlan = jQuery('#product-is-xpayments-subscription-plan').length;
            if (isSubscriptionPlan) {
                var priceField = jQuery('#product-price');
                toggleValidateRule(priceField, 'required', false);
                jQuery('.star', priceField.closest('tr')).empty();
            }
        }
    );

})();
