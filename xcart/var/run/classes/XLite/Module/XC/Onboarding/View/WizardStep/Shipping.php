<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;


/**
 * Shipping
 */
class Shipping extends \XLite\Module\XC\Onboarding\View\AWizardStep
{
    protected function getShippingEnabled()
    {
        return (integer)\XLite\Core\Config::getInstance()->General->requires_shipping_default;
    }
}