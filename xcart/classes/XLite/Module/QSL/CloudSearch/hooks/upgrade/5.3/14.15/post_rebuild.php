<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Module\QSL\CloudSearch\Main;

return function () {
    $repo = Database::getRepo('XLite\Model\Config');

    $planFeatures = Main::getPlanFeatures();

    if ((bool)Config::getInstance()->QSL->CloudSearch->isCloudFiltersEnabled
        && !in_array('cloudFilters', $planFeatures)
    ) {
        $features = $repo->findOneBy([
            'name'     => 'planFeatures',
            'category' => 'QSL\CloudSearch',
        ]);

        $features->setValue(json_encode(array_merge($planFeatures, ['cloudFilters'])));

        Database::getEM()->flush();
    }
};
