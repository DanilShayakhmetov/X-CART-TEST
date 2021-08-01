<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\Model\Role\Permission;
use XLite\Module\XC\Onboarding\Core\WizardState;


/**
 * Abstract admin-zone controller
 */
abstract class AAdmin extends \XLite\Controller\Admin\AAdmin implements \XLite\Base\IDecorator
{
    public function handleRequest()
    {
        if (Config::getInstance()->XC->Onboarding->wizard_state !== 'disabled'
            && Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS)
            && WizardState::getInstance()->getWizardProgress() >= 100) {

            WizardState::getInstance()->updateConfigOption('wizard_state', 'disabled');
            \XLite\Core\Config::updateInstance();
        }

        parent::handleRequest();
    }
}
