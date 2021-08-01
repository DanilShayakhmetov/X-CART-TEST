<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    updateConfigVariable();

    \XLite\Core\Database::getEM()->flush();
};

function updateConfigVariable()
{
    $option = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy([
        'category' => 'Company',
        'name'     => 'cloud_domain',
    ]);

    if ($option) {
        $option->setType('XLite\View\FormField\CloudDomain');
    }
}
