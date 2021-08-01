<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;

use Includes\Utils\URLManager;

class CompanyLogoCloud extends CompanyLogo
{
    /**
     * @return string|null
     */
    protected function getDefaultLogoUrl()
    {
        return \XLite\Core\Layout::getInstance()->getLogo();
    }

    /**
     * @return string
     */
    protected function getStorefrontUrl()
    {
        return URLManager::getShopURL('', null, [
            'activate_mode' =>'layout_editor'
        ]);
    }
}