<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\Controller\Customer;

/**
 * Abstract controller for Customer interface
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Assemble updateCart event
     *
     * @return boolean
     */
    protected function assembleEvent()
    {
        $result = parent::assembleEvent();

        if ($result) {
            \XLite\Core\Event::braintreeTotalUpdate(
                [
                    'total' => $this->getCart()->getTotal(),
                    'currency' => $this->getCart()->getCurrency()->getCode(),
                ]
            );
        }

        return $result;
    }

}
