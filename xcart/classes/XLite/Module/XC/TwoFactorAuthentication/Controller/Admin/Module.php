<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Admin;

use Includes\Utils\Module\Manager;

/**
 * Module settings
 */
class Module extends \XLite\Controller\Admin\Module implements \XLite\Base\IDecorator
{
    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        if ($this->getModuleId() === \Includes\Utils\Module\Module::buildId('XC', 'TwoFactorAuthentication')) {
            $oldApiKey = \XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->api_key;

            $newApiKey = \XLite\Core\Request::getInstance()->api_key;
            if ($oldApiKey !== $newApiKey && '' !== $newApiKey) {
                \XLite\Core\Database::getRepo('\XLite\Model\Profile')
                    ->clearAuthyIds();
            }
        }
    }
}