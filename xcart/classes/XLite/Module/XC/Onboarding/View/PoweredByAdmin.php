<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

use XLite\Module\XC\Onboarding\Controller\Admin\OnboardingWizard;

/**
 * 'Powered by' widget
 *
 */
class PoweredByAdmin extends \XLite\View\PoweredByAdmin implements \XLite\Base\IDecorator
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'powered_by.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && ! (\XLite::getController() instanceof OnboardingWizard);
    }
}
