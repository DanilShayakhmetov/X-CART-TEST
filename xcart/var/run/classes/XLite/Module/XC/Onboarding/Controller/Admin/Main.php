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
 * Main page controller
 */
 class Main extends \XLite\Controller\Admin\MainAbstract implements \XLite\Base\IDecorator
{
    /**
     * Run controller
     *
     * @return void
     */
    protected function doNoAction()
    {
        if ($this->shouldRedirectToOnboarding()) {
            $this->redirect($this->buildURL('onboarding_wizard'));
        } else {
            parent::doNoAction();
        }
    }

    /**
     * Check if the user should be redirected to onboarding wizard
     */
    protected function shouldRedirectToOnboarding()
    {
        return !$this->isAJAX()
            && Config::getInstance()->XC->Onboarding->wizard_state === 'visible'
            && Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS)
            && WizardState::getInstance()->getWizardProgress() < 100;
    }
}
