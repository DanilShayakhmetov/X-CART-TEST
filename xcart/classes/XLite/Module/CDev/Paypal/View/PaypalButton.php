<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

/**
 * Paypal button settings dialog
 */
class PaypalButton extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'paypal_button';
        $list[] = 'paypal_commerce_platform_button';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/paypal_button';
    }

    /**
     * @return bool
     */
    protected function isPaypalForMarketplaces()
    {
        return $this->getPaymentMethod()
               && $this->getPaymentMethod()->getServiceName() === \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM;
    }
}
