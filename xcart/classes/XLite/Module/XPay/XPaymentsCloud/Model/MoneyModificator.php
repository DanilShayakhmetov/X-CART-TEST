<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model;

use Includes\Utils\Module\Manager;

/**
 * Money modificator
 */
class MoneyModificator extends \XLite\Model\MoneyModificator implements \XLite\Base\IDecorator
{
    /**
     * Apply
     *
     * @param float                $value     Property value
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return float
     */
    public function apply($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        $xpSubscriptionsModifiers = [
            'XLite\Module\XPay\XPaymentsCloud\Logic\IncludedVAT',
            'XLite\Module\XPay\XPaymentsCloud\Logic\ExcludedVAT',
        ];

        if (
            !Manager::getRegistry()->isModuleEnabled('CDev\VAT')
            && in_array($this->getClass(), $xpSubscriptionsModifiers)
        ) {
            $result = $value;
        } else {
            $result = parent::apply($value, $model, $property, $behaviors, $purpose);
        }

        return $result;
    }

}
