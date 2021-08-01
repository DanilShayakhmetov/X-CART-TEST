/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Additional product page controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {
    var
        repeatRequest = false,
        inProgress = false;

    function send_plan() {
        var plan = {
            type: jQuery('#plan-type').val(),
            number: jQuery('#plan-number').val(),
            period: jQuery('#plan-period').val(),
            reverse: jQuery('#plan-reverse').is(':checked') ? 1 : ''
        };

        if (inProgress) {
            repeatRequest = true;
        } else {
            inProgress = true;

            var shadeElmSelector = '#plan' + (($(this).attr('id') != 'plan-type') ? '-description' : '');
            jQuery(shadeElmSelector).append('<div class="single-progress-mark"><div></div></div>');

            core.post(
                URLHandler.buildURL(
                    {
                        target: 'product',
                        action: 'update_plan_field_view'
                    }
                ),
                function(XMLHttpRequest, textStatus, data, valid) {
                    inProgress = false;
                    jQuery(shadeElmSelector + ' .single-progress-mark').remove();
                    if (repeatRequest) {
                        repeatRequest = false;
                        send_plan();
                    } else {
                        update_plan(data);
                    }
                },
                {
                    target: 'product',
                    action: 'update_plan_field_view',
                    plan : plan
                },
                {
                    rpc: true
                }
            );
        }
    }

    function bind_callbacks() {
        jQuery(document.body).on('change', '#plan-type', send_plan);
        jQuery(document.body).on('change', '#plan-number', send_plan);
        jQuery(document.body).on('change', '#plan-period', send_plan);
        jQuery(document.body).on('change', '#plan-reverse', send_plan);
    }

    function update_plan(plan) {
        jQuery('#plan-type').replaceWith($('#plan-type', plan));
        jQuery('#plan-number').val($('#plan-number', plan).val());
        jQuery('#plan-number-suffix').replaceWith($('#plan-number-suffix', plan));
        jQuery('#plan-period').replaceWith($('#plan-period', plan));

        var plan_reverse_field = $('#plan-reverse-field', plan);
        if (plan_reverse_field.length) {
            jQuery('#plan-reverse-field').replaceWith($('#plan-reverse-field', plan));

        } else {
            jQuery('#plan-reverse-field').empty();
        }

        jQuery('#plan-description').replaceWith($('#plan-description', plan));

        jQuery('[id^=plan-]').each(function () {
            if ('undefined' != typeof this.commonController) {
                this.commonController.isChanged = function() { return true; }
            }
        });
    }

    core.bind('load', bind_callbacks);

})();
