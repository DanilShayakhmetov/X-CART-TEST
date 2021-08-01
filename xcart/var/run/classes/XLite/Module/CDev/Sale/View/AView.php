<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Viewer
 */
abstract class AView extends \XLite\Module\CDev\SimpleCMS\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Sale/css/lc.less';

        return $list;
    }

    /**
     * Return sale participation flag
     *
     * @param \XLite\Model\Product $product Product model
     *
     * @return boolean
     */
    protected function participateSaleAdmin(\XLite\Model\Product $product)
    {
        return $product->getParticipateSale() ||
            ($product->hasParticipateSale() && empty($product->getApplicableSaleDiscounts()));
    }

    /**
     * @param \XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount
     * @return string
     */
    protected function getSaleDiscountEditLink(\XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount)
    {
        return $this->buildURL('sale_discount', '', ['id' => $saleDiscount->getId()]);
    }
}
