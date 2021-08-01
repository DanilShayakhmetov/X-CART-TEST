<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Separator;

class AdditionalAccount extends \XLite\View\FormField\Separator\Regular
{
    /**
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() && $this->isConnectedOnboardingAvailable();
    }

    /**
     * @return string
     */
    public function isConnectedOnboardingAvailable()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
        );

        return $method->getSetting('email')
            && $method->getSetting('client_id')
            && $method->getSetting('secret')
            && $method->getSetting('partner_id')
            && $method->getSetting('bn_code');
    }
}
