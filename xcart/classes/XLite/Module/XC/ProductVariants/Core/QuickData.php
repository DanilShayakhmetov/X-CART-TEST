<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core;

/**
 * Class QuickData
 */
class QuickData extends \XLite\Core\QuickData implements \XLite\Base\IDecorator
{
    /**
     * Get memberships
     *
     * @param \XLite\Model\Product $product    Product
     * @param mixed                $membership Membership
     * @param mixed                $zone       Zone
     *
     * @return \XLite\Model\QuickData
     */
    public function updateDataWithZone(\XLite\Model\Product $product, $membership, $zone)
    {
        $data = parent::updateDataWithZone($product, $membership, $zone);

        if ($product->hasVariants()) {
            $minPrice = min($data->getPrice(), $this->getQuickDataMinPrice($product, $membership, $zone));
            $data->setMinPrice(\XLite::getInstance()->getCurrency()->roundValue($minPrice));

            $maxPrice = max($data->getPrice(), $this->getQuickDataMaxPrice($product, $membership, $zone));
            $data->setMaxPrice(\XLite::getInstance()->getCurrency()->roundValue($maxPrice));
        } else {
            $data->setMinPrice(\XLite::getInstance()->getCurrency()->roundValue($data->getPrice()));
            $data->setMaxPrice(\XLite::getInstance()->getCurrency()->roundValue($data->getPrice()));
        }

        return $data;
    }

    /**
     * @param \XLite\Model\Product $product
     * @param $membership
     * @param $zone
     * @return float
     */
    protected function getQuickDataMinPrice(\XLite\Model\Product $product, $membership, $zone)
    {
        return $product->getQuickDataMinPrice();
    }

    /**
     * @param \XLite\Model\Product $product
     * @param $membership
     * @param $zone
     * @return float
     */
    protected function getQuickDataMaxPrice(\XLite\Model\Product $product, $membership, $zone)
    {
        return $product->getQuickDataMaxPrice();
    }
}