<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding;

use Includes\Utils\Module\Manager;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Returns module skin dir
     *
     * @return boolean
     */
    public static function getSkinDir()
    {
        return 'modules/XC/Onboarding/';
    }

    /**
     * Determines if some module is enabled
     *
     * @return boolean
     */
    public static function isModuleEnabled($name)
    {
        return Manager::getRegistry()->isModuleEnabled($name);
    }

    public static function getCloudDomainName()
    {
        return \XLite::getInstance()->getOptions(['host_details', 'http_host']);
    }
}
