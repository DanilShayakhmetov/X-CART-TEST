<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\Form\Payment\Method\Admin;

/**
 * Payment method settings form 
 */
 class Settings extends \XLite\View\Form\Payment\Method\Admin\SettingsAbstract implements \XLite\Base\IDecorator 
{
    /**
     * Is Braintree flag 
     */
    protected $isBraintree = null;

    /**
     * Check if this is Braintree payment method
     *
     * @return bool
     */
    protected function isBraintreePaymentMethod()
    {
        if (is_null($this->isBraintree)) {

            $paymentMethod = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->find($this->getPaymentMethodId());

            $this->isBraintree = $paymentMethod
                && (\XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::BRAINTREE_CLASS == $paymentMethod->getClass());
        }

        return $this->isBraintree;
    }

    /**
     * JavaScript: this value will be returned on form submit
     * NOTE - this function is designed for easy switch on/off via AJAX
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        if ($this->isBraintreePaymentMethod()) {

            $result = 'checkBraintreeMerhantAccountId();';

        } else {

            $result = parent::getOnSubmitResult();
        }

        return $result;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isBraintreePaymentMethod()) {

            $list[] = $this->getDir() . '/settings.js';
        }

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/BraintreeVZ/config';
    }
}
