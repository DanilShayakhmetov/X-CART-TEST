<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\Model;
/**
 * Class SubscriptionPlan
 * @Decorator\Depend({"XPay\XPaymentsCloud", "QSL\XPaymentsSubscriptions"})
 */
class SubscriptionPlan extends \XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan implements \XLite\Base\IDecorator
{
    /**
     * Update params of current QSL/XPaymentsSubscriptions plan by corresponding params of XPay\XPaymentsCloud plan
     *
     * @param array $data
     * @param \XLite\Model\Product $product
     * @return void
     */
    public function updateByXpaymentsCloudData(array $data, \XLite\Model\Product $product)
    {
        $this->setSubscription($data['is_subscription'])
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
