<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Checkout failed page
 */
abstract class CheckoutFailedAbstract extends \XLite\Controller\Customer\Cart
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Order failed');
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->setReturnURL($this->buildURL('checkout'));

        if ($this->getCart()) {
            $this->assignFailureReason($this->getFailureReason());
        }
    }

    protected function assignFailureReason($reason)
    {
        if (is_array($reason)) {
            foreach ($reason as $realReason) {
                \XLite\Core\TopMessage::addError($realReason);
            }
        } else {
            \XLite\Core\TopMessage::addError($reason);
        }
    }

    /**
     * @return string|array
     */
    protected function getFailureReason()
    {
        $cart = $this->getFailedCart();
        $reason = $cart
            ? $cart->getFailureReason()
            : null;

        return $reason
            ?: $this->getDefaultFailureReason();
    }

    /**
     * Get failed cart object
     *
     * @return \XLite\Model\Cart
     */
    protected function getFailedCart()
    {
        return $this->getCart();
    }

    /**
     * Returns default fa
     *
     * @return string
     */
    protected function getDefaultFailureReason()
    {
        return \XLite\Model\Payment\Transaction::getDefaultFailedReason();
    }
}
