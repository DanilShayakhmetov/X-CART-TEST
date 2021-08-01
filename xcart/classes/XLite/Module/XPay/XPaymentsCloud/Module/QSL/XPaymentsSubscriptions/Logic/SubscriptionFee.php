<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\Logic;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Class SubscriptionFee
 * @Decorator\Depend({"QSL\XPaymentsSubscriptions"})
 */
class SubscriptionFee
    extends \XLite\Module\QSL\XPaymentsSubscriptions\Logic\SubscriptionFee
    implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    public static function isApply(\XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        $result = parent::isApply($model, $property, $behaviors, $purpose);
        if (XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()) {
            $result = false;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function modifyMoney($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        $result = parent::modifyMoney($value, $model, $property, $behaviors, $purpose);

        if (XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()) {
            $result = $value;
        }

        return $result;
    }
}
