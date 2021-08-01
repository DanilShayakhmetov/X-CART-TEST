<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;

class ProductAddedCloud extends ProductAdded
{
    protected function getDir()
    {
        return 'modules/XC/Onboarding/wizard_steps/product_added';
    }

    protected function getDefaultTemplate()
    {
        return  $this->getDir() . '/body_cloud.twig';
    }
}