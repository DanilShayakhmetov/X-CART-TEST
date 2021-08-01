/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Adding attributes to addtocart form
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
    $(document).on('click', '.add2cart', function () {
        var form_id = $('form.product-details').attr('id');
        $('.editable-attributes :input').each(function () {
            $(this).attr('form', form_id);
        });
    });
});