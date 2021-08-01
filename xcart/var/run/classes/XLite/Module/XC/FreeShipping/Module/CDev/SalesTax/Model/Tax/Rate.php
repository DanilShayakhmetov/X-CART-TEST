<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Module\CDev\SalesTax\Model\Tax;


/**
 * Rate
 *
 * @Decorator\Depend("CDev\SalesTax")
 */
 class Rate extends \XLite\Module\CDev\SalesTax\Model\Tax\RateAbstract implements \XLite\Base\IDecorator
{
    protected function getItemBasis($item)
    {
        $result = parent::getItemBasis($item);

        $formulaParts = explode('+', $this->getTaxableBaseType());

        if (
            in_array('SH', $formulaParts, true)
            && $this->isIgnoreProductsWithFixedFee()
        ) {
            $result += $item->getObject()
                ? $item->getObject()->getFreightFixedFee() * $item->getAmount()
                : 0;
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isIgnoreProductsWithFixedFee()
    {
        return \XLite\Core\Config::getInstance()->XC->FreeShipping->freight_shipping_calc_mode
            === \XLite\Module\XC\FreeShipping\View\FormField\FreightMode::FREIGHT_ONLY;
    }
}