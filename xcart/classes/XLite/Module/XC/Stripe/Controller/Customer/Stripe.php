<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Controller\Customer;

/**
 * Stripe OAuth endpoint
 */
class Stripe extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Confirm method
     *
     * @return void
     */
    protected function doActionConfirm()
    {
        header('Content-Type: application/json');

        $cart = $this->getCart();

        $payment = $this->getPaymentMethod()->getProcessor();
        $payment->confirmPayment($cart);
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'Stripe'));
    }
}