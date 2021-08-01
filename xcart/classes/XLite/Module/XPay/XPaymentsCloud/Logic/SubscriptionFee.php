<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Logic;

/**
 * Subscription Fee
 */
class SubscriptionFee extends \XLite\Logic\ALogic
{
    /**
     * Check modifier - apply or not
     *
     * @param \XLite\Model\AEntity $model Model
     * @param string $property Model's property
     * @param array $behaviors Behaviors
     * @param string $purpose Purpose
     *
     * @return boolean
     */
    public static function isApply(\XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        return (
                $model instanceof \XLite\Model\Product
                && $model->hasXpaymentsSubscriptionPlan()
            )
            || (
                $model instanceof \XLite\Model\OrderItem
                && $model->isXpaymentsSubscription()
                && $model->getOrder()
                && !$model->getOrder()->isXpaymentsSubscriptionPayment()
            );
    }

    /**
     * Modify money
     *
     * @param float $value Value
     * @param \XLite\Model\AEntity $model Model
     * @param string $property Model's property
     * @param array $behaviors Behaviors
     * @param string $purpose Purpose
     *
     * @return float
     */
    public static function modifyMoney($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        return $value + $model->getXpaymentsNetFeePrice();
    }

}
