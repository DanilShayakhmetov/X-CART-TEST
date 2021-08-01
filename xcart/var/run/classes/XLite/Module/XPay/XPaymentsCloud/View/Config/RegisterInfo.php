<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Config;

class RegisterInfo extends \XLite\View\AView
{
    /**
     * Check if it's necessary to show info about registration 
     *
     * @return boolean
     */
    protected function isShowRegisterInfo()
    {
        $apiKey = \XLite\Module\XPay\XPaymentsCloud\Main::getPaymentMethod()
            ->getSetting('api_key');

        return empty($apiKey);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/register_info.twig';
    }

}
