<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Logic;

/**
 * Net price modifier: add attribute surcharge
 */
class AttributeSurcharge extends \XLite\Logic\AttributeSurcharge implements \XLite\Base\IDecorator
{
    /**
     * Check modifier - apply or not
     *
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return boolean
     */
    public static function isApply(\XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        if (
            $model instanceof \XLite\Model\OrderItem
            && $model->getOrder()
            && $model->getOrder()->isXpaymentsSubscriptionPayment()
        ) {
            $result = false;
        } else {
            $result = parent::isApply($model, $property, $behaviors, $purpose);
        }

        return $result;
    }

}
