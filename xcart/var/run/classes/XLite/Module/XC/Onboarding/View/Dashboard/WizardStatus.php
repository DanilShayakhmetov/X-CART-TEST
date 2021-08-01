<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\Dashboard;

use XLite\Core\Auth;
use XLite\Core\Request;
use XLite\Model\Role\Permission;
use XLite\Module\XC\Onboarding\Core\WizardState;

/**
 * Wizard mini informer on Dashboard
 *
 * @ListChild(list="dashboard-sidebar", weight="50", zone="admin")
 */
class WizardStatus extends \XLite\Module\XC\Onboarding\View\MiniWizardStatus
{
    /**
     * Add widget specific CSS file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] ='modules/XC/Onboarding/dashboard/wizard/style.less';

        return $list;
    }

    /**
     * @return string
     */
    protected function isTargetIsAllowed()
    {
        return \XLite::getController()->getTarget() === 'main';
    }
}