<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

use XLite\Core\Database;
use XLite\Model\Config;


return function () {
    $repo = Database::getRepo('XLite\Model\Config');

    /** @var Config $setting */
    $setting = $repo->findOneBy([
        'name'     => 'isCloudFiltersEnabled',
        'category' => 'QSL\CloudSearch',
    ]);

    $setting->setType('hidden');

    Database::getEM()->flush($setting);
};
