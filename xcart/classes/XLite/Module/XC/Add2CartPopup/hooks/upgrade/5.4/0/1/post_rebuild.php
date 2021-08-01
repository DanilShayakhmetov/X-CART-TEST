<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
    $optionsToRemove = $repo->findBy(
        array(
            'category'  => 'XC\Add2CartPopup',
            'name'      => ['a2cp_enable_for_dropping']
        )
    );

    $repo->deleteInBatch($optionsToRemove, false);

    \XLite\Core\Database::getEM()->flush();
};
