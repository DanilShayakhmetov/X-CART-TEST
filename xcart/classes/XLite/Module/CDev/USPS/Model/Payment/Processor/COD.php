<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Payment\Processor;

/**
 * 'Cash on Delivery' payment method class
 */
class COD extends \XLite\Model\Payment\Processor\COD
{
    /**
     * Shipping method carrier code which is allowed to make COD payment method available at checkout
     *
     * @var string
     */
    protected $carrierCode = 'usps';

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Get the carrier code
     *
     * @return string
     */
    protected function getCarrierCode()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->USPS;

        return $config->dataProvider === 'pitneyBowes'
            ? 'pb_usps'
            : 'usps';
    }
}
