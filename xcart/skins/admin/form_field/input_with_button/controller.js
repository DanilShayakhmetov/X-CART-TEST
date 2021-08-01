/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Password with button handler
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
    {
        pattern: '.input-passwordwithbutton .input-widget',
        handler: function () {

            var field = jQuery(this);
            var input = field.find('input[type="password"]');
            var btn   = field.find('.btn');
            var undo  = field.find('.undo');

            btn.click(function(){
                input.parent().removeClass('hidden');
                undo.removeClass('hidden');
                btn.addClass('hidden');
            });

            undo.click(function(){
                input.parent().addClass('hidden');
                undo.addClass('hidden');
                btn.removeClass('hidden');
                input.val('');
            })

        }
    }
);