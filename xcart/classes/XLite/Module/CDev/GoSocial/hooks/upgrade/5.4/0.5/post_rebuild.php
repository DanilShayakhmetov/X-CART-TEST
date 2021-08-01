<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function removeGooglePlusConfig()
{
    $options = \XLite\Core\Database::getRepo('XLite\Model\Config')->findBy([
        'name' => [
            'gosocial_sep_4',
            'gplus_use',
            'gplus_page_id',
        ],
        'category' => 'CDev\GoSocial'
    ]);

    foreach ($options as $option) {
        \XLite\Core\Database::getEM()->remove($option);
    }
    \XLite\Core\Database::getEM()->flush();
}

return function () {
    removeGooglePlusConfig();
};