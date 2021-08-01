<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;


/**
 * ShippingDone
 */
class ShippingDone extends \XLite\Module\XC\Onboarding\View\AWizardStep
{
    /**
     * @return string
     */
    protected function getAdvancedShippingSettingsLink()
    {
        return $this->buildURL('shipping_methods');
    }
}