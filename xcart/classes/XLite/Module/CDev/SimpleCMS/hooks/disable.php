<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->deleteRootMenu();
    }
);
