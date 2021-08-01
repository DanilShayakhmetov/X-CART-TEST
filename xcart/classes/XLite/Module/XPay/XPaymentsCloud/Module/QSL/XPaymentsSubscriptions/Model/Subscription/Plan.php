<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\Model\Subscription;

/**
 * Class Plan
 * @Decorator\Depend({"XPay\XPaymentsCloud", "QSL\XPaymentsSubscriptions"})
 */
class Plan extends \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan implements \XLite\Base\IDecorator
{
    /**
     * Fill the fields of legacy subscription plan based on fields of the current subscription plan
     *
     * @param array $data
     * @param \XLite\Model\Product $product
     * @return void
     */
    public function updateByLegacyPlanData(array $data, \XLite\Model\Product $product)
    {
        $this->setIsSubscription($data['subscription'])
            ->setSetupFee($data['setup_fee'])
            ->setCalculateShipping('calculate_shipping')
            ->setType($data['plan']['type'])
            ->setNumber($data['plan']['number'])
            ->setPeriod($data['plan']['period'])
            ->setReverse($data['plan']['reverse'])
            ->setPeriods($data['periods'])
            ->setFee($data['fee'])
            ->setProduct($product);
    }

}
