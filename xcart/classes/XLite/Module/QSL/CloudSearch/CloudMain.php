<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch;

use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;

/**
 * Cloud Main decorator
 *
 * @Decorator\Depend ("XC\Cloud")
 */
abstract class CloudMain extends \XLite\Module\XC\Cloud\Main implements \XLite\Base\IDecorator
{
    public static function triggerTrialEvent()
    {
        parent::triggerTrialEvent();

        if (!Main::isConfigured()) {
            $apiClient = new ServiceApiClient();

            $apiClient->register();
        }
    }
}
