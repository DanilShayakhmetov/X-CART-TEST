<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Customer;

use XLite\Module\Amazon\PayWithAmazon\Model\Payment\Processor\PayWithAmazon;

/**
 * Checkout controller
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn implements \XLite\Base\IDecorator
{

    /**
     * @param $txn
     * @param $urlParams
     *
     * @return string
     */
    protected function getCheckoutReturnURL(\XLite\Model\Payment\Transaction $txn, $urlParams)
    {
        $url = parent::getCheckoutReturnURL($txn, $urlParams);

        $paymentMethod = $txn->getPaymentMethod();
        if ($paymentMethod->getServiceName() === 'PayWithAmazon') {
            $authorizationReason = '';
            foreach ($txn->getData() as $cell) {
                if ($cell->getName() == 'authorizationReason' || $cell->getName() == 'MFA_status') {
                    $authorizationReason = $cell->getValue();
                    break;
                }
            }

            if (in_array($authorizationReason, PayWithAmazon::getRedirectToCartReasons())) {
                $urlParams['redirect_to_cart'] = 1;
            }

            $urlParams['orderReference'] = $txn->getDetail('amazonOrderReferenceId');

            $url = $this->getShopURL(
                $this->buildURL('amazon_checkout', 'return', $urlParams),
                \XLite\Core\Request::getInstance()->isHTTPS() || \XLite\Core\Config::getInstance()->Security->customer_security
            );
        }

        return $url;
    }
}
