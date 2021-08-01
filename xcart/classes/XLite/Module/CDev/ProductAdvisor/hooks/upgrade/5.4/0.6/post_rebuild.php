<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $option = \XLite\Core\Database::getRepo('XLite\Model\Config')
        ->findOneBy(
            [
                'name' => 'cs_display_date',
                'category' => 'CDev\ProductAdvisor'
            ]
        );

    if ($option) {
        \XLite\Core\Database::getEM()->remove($option);
    }

    \XLite\Core\Database::getEM()->flush();
};
