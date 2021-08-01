<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\Payment;

/**
 * Payment method
 */
 class Method extends \XLite\View\Payment\MethodAbstract implements \XLite\Base\IDecorator
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
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->isBraintreePaymentMethod()) {
            $list[] = 'modules/QSL/BraintreeVZ/config/style.css';
        }

        return $list;
    }
}
