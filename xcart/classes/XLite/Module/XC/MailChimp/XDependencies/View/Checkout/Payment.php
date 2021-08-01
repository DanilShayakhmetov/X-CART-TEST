<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\XDependencies\View\Checkout;

/**
 * Checkout
 *
 * @Decorator\Depend ("CDev\Paypal")
 */
class Payment extends \XLite\View\Checkout\Payment implements \XLite\Base\IDecorator
{
    /**
     * TODO: remove in next major release
     * 
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (version_compare(\XLite\Module\CDev\Paypal\Main::getVersion(), '5.3.6', '<')) {
            foreach ($list as $i => $script) {
                if ($script == 'modules/CDev/Paypal/checkout/payment.js') {
                    $list[$i] = 'modules/XC/MailChimp/checkout/payment.js';
                }
            }
        }

        return $list;
    }
}
