<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Admin;

/**
 * Payment method
 */
 class PaymentMethod extends \XLite\Module\QSL\BraintreeVZ\Controller\Admin\PaymentMethod implements \XLite\Base\IDecorator
{
    /**
     * Update payment method
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        $method = $this->getPaymentMethod();

        if ($method && $method->getProcessor() instanceof \XLite\Module\Amazon\PayWithAmazon\Model\Payment\Processor\PayWithAmazon) {
            if (!$method->isConfigured() && !\XLite\Core\Config::getInstance()->Security->customer_security) {
                \XLite\Core\TopMessage::addWarning(
                    'The "Pay with Amazon" feature requires https to be properly set up for your store.',
                    [
                        'url' => \XLite\Core\Converter::buildURL('https_settings'),
                    ]
                );
            }
        }
    }
}
