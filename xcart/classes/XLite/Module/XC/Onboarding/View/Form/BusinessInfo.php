<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\Form;

class BusinessInfo extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'onboarding_wizard';
    }

    protected function getDefaultAction()
    {
        return 'update_business_info';
    }
}