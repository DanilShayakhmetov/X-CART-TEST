<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button;

use XLite\Module\CDev\Paypal\Controller\Admin\PaypalCommercePlatformSettings;

/**
 * Sign up button
 */
class PaypalCommercePlatformOnboarding extends \XLite\View\Button\SimpleLink
{
    public function getJSFiles()
    {
        $result = parent::getJSFiles();
        $result[] = 'modules/CDev/Paypal/settings/PaypalCommercePlatform/signup.js';

        return $result;
    }

    /**
     * Defines the default location path
     *
     * @return string
     */
    protected function getDefaultLocation()
    {
        $controller = new PaypalCommercePlatformSettings();

        return $controller->getSignUpUrl(
            $this->buildFullURL(
                'paypal_commerce_platform_settings',
                'onboarding_return',
                ['return' => 'onboarding_wizard']
            )
        );
    }

    /**
     * Get button css class
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn regular-button';
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        return [
            'target'                       => 'PPFrame',
            'data-paypal-onboard-complete' => 'PaypalCommercePlatformOnboardedCallback',
            'data-paypal-button'           => 'true',
        ];
    }
}
