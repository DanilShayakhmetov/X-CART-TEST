<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Module\XC\Onboarding\Controller\Admin;

/**
 * OnboardingWizard
 *
 * @Decorator\Depend("XC\Onboarding")
 */
class OnboardingWizard extends \XLite\Module\XC\Onboarding\Controller\Admin\OnboardingWizard implements \XLite\Base\IDecorator
{
    public function doActionUpdateLocation()
    {
        parent::doActionUpdateLocation();

        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
            'category' => 'XC\Onboarding',
            'name'     => 'disable_geolocation',
            'value'    => true,
        ]);
    }
}