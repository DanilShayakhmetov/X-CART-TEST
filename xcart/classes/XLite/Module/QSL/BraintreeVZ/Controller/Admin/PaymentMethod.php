<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\Controller\Admin;

/**
 * Payment method 
 */
class PaymentMethod extends \XLite\Controller\Admin\PaymentMethod implements \XLite\Base\IDecorator
{
    /**
     * Check if this is Braintree payment method
     *
     * @return bool 
     */
    protected function isBraintreePaymentMethod()
    {
        return $this->getPaymentMethod()
            && $this->getPaymentMethod()->getClass() == \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::BRAINTREE_CLASS;
    }

    /**
     * Redirect to the account connect tab if Braintree is not configured 
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            $this->isBraintreePaymentMethod()
            && !$this->isConfigured()
        ) {

            $url = $this->buildURL('braintree_account', '', array());

            $this->setReturnURL($url);

            $this->redirect();

        } else {

            parent::handleRequest();
        }
    }

    /**
     * Update payment method
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        if ($this->isBraintreePaymentMethod()) {

            $request = \XLite\Core\Request::getInstance();

            if ($request->externalCheck) {
                \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->checkAccount(true);
            }

            // Return back to the Braintree payment configurations page
            $this->setReturnURL(
                $this->buildURL(
                    'payment_method',
                    '',
                    array('method_id' => $request->method_id)
                )
            );
        }
    }

    /**
     * Check if this is Braintree payment method
     *
     * @return bool
     */
    public function isConfigured()
    {
        return $this->isBraintreePaymentMethod()
            && \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->isConfigured();
    }
}
