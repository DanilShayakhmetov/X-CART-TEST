<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;


/**
 * Done
 */
class Done extends \XLite\Module\XC\Onboarding\View\AWizardStep
{
    /**
     * @return string
     */
    protected function getPhoneNumber()
    {
        return '1-800-657-7957';
    }

    /**
     * @return string
     */
    protected function getSalesEmail()
    {
        return 'sales@x-cart.com';
    }

    /**
     * @return string
     */
    protected function getSupportUrl()
    {
        return \XLite::getXCartURL('https://www.x-cart.com/contact-us.html');
    }

    /**
     * @return string
     */
    protected function getKBUrl()
    {
        return 'https://kb.x-cart.com/';
    }

    /**
     * @return string
     */
    protected function getDevDocsUrl()
    {
        return 'https://devs.x-cart.com/';
    }

    protected function getStorefrontUrl()
    {
        return \XLite::getController()->getShopURL();
    }
}