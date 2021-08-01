<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Logic;

/**
 * Subscription fee modificator: add attribute surcharge
 */
class SubscriptionFeeModifier extends \XLite\Logic\ALogic
{
    /**
     * Check modificator - apply or not
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
        return $model instanceOf \XLite\Model\OrderItem || $model instanceOf \XLite\Model\Product;
    }

    /**
     * Modify money
     *
     * @param float                $value     Value
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return void
     */
    public static function modifyMoney($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        foreach (static::getAttributeValues($model) as $attributeValue) {
            if (
                $attributeValue instanceOf \XLite\Model\OrderItem\AttributeValue
                && $attributeValue->getAttributeValue()
            ) {
                $attributeValue = $attributeValue->getAttributeValue();
            }

            if (
                is_object($attributeValue)
                && $attributeValue instanceof \XLite\Model\AttributeValue\Multiple
            ) {
                $value += $attributeValue->getAbsoluteValue('xpaymentsSubscriptionFee');
            }
        }

        return $value;
    }

    /**
     * Return attribute values
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return array
     */
    protected static function getAttributeValues(\XLite\Model\AEntity $model)
    {
        return $model instanceOf \XLite\Model\Product
            ? $model->getAttrValues()
            : $model->getAttributeValues();
    }

}
