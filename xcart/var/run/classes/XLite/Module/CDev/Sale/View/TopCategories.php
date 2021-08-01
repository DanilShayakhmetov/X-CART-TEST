<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Top categories
 */
 class TopCategories extends \XLite\Module\QSL\FlyoutCategoriesMenu\View\TopCategories implements \XLite\Base\IDecorator
{
    /**
     * Get cache oarameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->getCategoryId();

        $controller = \XLite::getController();
        if ($controller instanceof \XLite\Module\CDev\Sale\Controller\Customer\SaleDiscount) {
            $list[] = $controller->getSaleDiscountId();
        }

        return $list;
    }

}
