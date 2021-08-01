<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

/**
 * Checkout controller
 */
class CheckoutFailed extends \XLite\Controller\Customer\CheckoutFailed implements \XLite\Base\IDecorator
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (\XLite\Core\Session::getInstance()->cancelUrl) {
            $this->setReturnURL(\XLite\Core\Session::getInstance()->cancelUrl);

            unset(\XLite\Core\Session::getInstance()->cancelUrl);
        } else {
            parent::doNoAction();
        }
    }
}
